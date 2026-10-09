document.querySelectorAll('[data-gallery-picker]').forEach(picker => {
    const single = picker.hasAttribute('data-single-image');
    const dialog = picker.querySelector('[data-gallery-dialog]');
    const library = picker.querySelector('[data-gallery-library]');
    const selected = picker.querySelector('[data-gallery-selected]');
    const status = picker.querySelector('[data-gallery-status]');
    let ids = [...selected.querySelectorAll('input')].map(input => input.value).filter(Boolean);
    const items = () => [...library.querySelectorAll('[data-media-id]')];
    const known = new Map(items().map(item => [item.dataset.mediaId, item]));
    let staged = new Set(ids);
    let nextUrl = null;
    let controller;
    const more = picker.querySelector('[data-gallery-more]');
    const feedback = picker.querySelector('[data-gallery-upload-status]');
    const addItem = result => {
        const id = String(result.id);
        const item = document.createElement('label'); item.className = 'gallery-library-item';
        Object.assign(item.dataset, {mediaId: id, mediaName: result.name, mediaUrl: result.url, mediaEdit: result.edit_url});
        const image = document.createElement('img'); image.src = result.url; image.alt = ''; image.loading = 'lazy';
        const text = document.createElement('span');
        const input = document.createElement('input'); input.type = single ? 'radio' : 'checkbox'; input.value = id;
        input.setAttribute('form', 'media-picker-controls');
        if (single) input.name = `picker_${picker.dataset.fieldName}`;
        input.checked = staged.has(id);
        text.append(input, document.createTextNode(` ${result.name}`)); item.append(image, text);
        known.set(id, item);
        return item;
    };
    const load = async (url, reset = false) => {
        controller?.abort();
        controller = new AbortController();
        more.disabled = true; feedback.textContent = 'Loading images…';
        try {
            const response = await fetch(url, {headers: {Accept: 'application/json'}, signal: controller.signal});
            if (!response.ok) throw new Error('Images could not be loaded. Please try again or sign in again.');
            const result = await response.json();
            if (reset) library.replaceChildren();
            result.images.forEach(image => library.append(addItem(image)));
            nextUrl = result.next_url; more.hidden = !nextUrl;
            feedback.textContent = library.children.length ? '' : 'No matching images.';
        } catch (error) {
            if (error.name !== 'AbortError') feedback.textContent = error.message;
        } finally { more.disabled = false; }
    };
    library.addEventListener('change', event => {
        if (!event.target.matches('input')) return;
        const id = event.target.closest('[data-media-id]').dataset.mediaId;
        if (single) staged.clear();
        if (event.target.checked) staged.add(id); else staged.delete(id);
    });
    more.addEventListener('click', () => { if (nextUrl) load(nextUrl); });
    const button = (text, label, action) => {
        const element = document.createElement('button');
        element.type = 'button'; element.textContent = text; element.setAttribute('aria-label', label);
        element.addEventListener('click', action);
        return element;
    };
    const render = (focusId, focusAction = 0) => {
        selected.replaceChildren();
        ids.forEach((id, index) => {
            const source = known.get(id);
            if (!source) return;
            const card = document.createElement('div');
            card.dataset.selectedId = id;
            const image = document.createElement('img');
            image.src = source.dataset.mediaUrl; image.alt = source.dataset.mediaName;
            const name = document.createElement('p'); name.textContent = source.dataset.mediaName;
            if (single) image.dataset.imagePreview = '';
            const input = document.createElement('input'); input.type = 'hidden'; input.name = single ? picker.dataset.fieldName : 'gallery_media_ids[]'; input.value = id;
            const controls = document.createElement('div'); controls.className = 'gallery-picker-controls';
            const move = offset => {
                [ids[index], ids[index + offset]] = [ids[index + offset], ids[index]];
                render(id, offset < 0 ? 1 : 0);
            };
            const previous = button('←', `Move ${name.textContent} earlier`, () => move(-1)); previous.disabled = index === 0;
            const next = button('→', `Move ${name.textContent} later`, () => move(1)); next.disabled = index === ids.length - 1;
            const remove = button('Remove', `Remove ${name.textContent}`, () => { ids.splice(index, 1); render(); picker.querySelector('[data-gallery-open]').focus(); });
            if (single) {
                const edit = document.createElement('a');
                edit.href = source.dataset.mediaEdit; edit.textContent = 'Edit image details ↗';
                edit.target = '_blank'; edit.rel = 'noopener noreferrer'; edit.setAttribute('aria-label', 'Edit image details (opens in a new tab)');
                controls.append(edit, remove);
            } else controls.append(previous, next, remove);
            card.append(image, name, input, controls); selected.append(card);
        });
        if (!ids.length) {
            selected.textContent = single ? 'No image selected.' : 'No gallery images selected.';
            if (single) {
                const input = document.createElement('input'); input.type = 'hidden'; input.name = picker.dataset.fieldName; input.value = '';
                selected.append(input);
            }
        }
        if (focusId) selected.querySelector(`[data-selected-id="${focusId}"]`)?.querySelectorAll('button')[focusAction]?.focus();
    };
    picker.querySelector('[data-gallery-open]').addEventListener('click', () => {
        staged = new Set(ids);
        items().forEach(item => { item.querySelector('input').checked = ids.includes(item.dataset.mediaId); });
        picker.querySelector('[data-gallery-search]').value = '';
        dialog.showModal();
        load(picker.dataset.libraryUrl, true);
    });
    picker.querySelector('[data-gallery-close]').addEventListener('click', () => dialog.close());
    picker.querySelector('[data-gallery-apply]').addEventListener('click', () => {
        const checked = [...staged];
        if (checked.length > 30) { picker.querySelector('[data-gallery-upload-status]').textContent = 'Select at most 30 gallery images.'; return; }
        ids = single ? checked.slice(0, 1) : [...ids.filter(id => checked.includes(id)), ...checked.filter(id => !ids.includes(id))];
        render(); status.textContent = ''; dialog.close();
    });
    let searchTimer;
    picker.querySelector('[data-gallery-search]').addEventListener('input', event => {
        clearTimeout(searchTimer);
        controller?.abort();
        const url = new URL(picker.dataset.libraryUrl, location.href);
        url.searchParams.set('search', event.target.value);
        searchTimer = setTimeout(() => load(url, true), 250);
    });
    // Enter in the library must not submit the surrounding content form.
    dialog.addEventListener('keydown', event => { if (event.key === 'Enter' && event.target.matches('input')) event.preventDefault(); });
    picker.querySelector('[data-gallery-upload]').addEventListener('change', async event => {
        const upload = event.target;
        const feedback = picker.querySelector('[data-gallery-upload-status]');
        upload.disabled = true;
        picker.querySelector('[data-gallery-apply]').disabled = true;
        for (const file of upload.files) {
            feedback.textContent = `Uploading ${file.name}…`;
            const data = new FormData(); data.append('file', file); data.append('gallery_upload', '1');
            data.append('_token', picker.closest('form').querySelector('[name="_token"]').value);
            try {
                const response = await fetch(picker.dataset.uploadUrl, {method: 'POST', headers: {'Accept': 'application/json'}, body: data});
                const result = await response.json();
                if (!response.ok) throw new Error(Object.values(result.errors || {}).flat().join(' ') || result.message || 'Upload failed.');
                if (single) {
                    staged.clear();
                    items().forEach(item => { item.querySelector('input').checked = false; });
                }
                staged.add(String(result.id));
                library.prepend(addItem(result));
                feedback.textContent = 'Uploaded. Apply your selection, then save the record.';
            } catch (error) { feedback.textContent = error.message; break; }
        }
        upload.disabled = false; upload.value = '';
        picker.querySelector('[data-gallery-apply]').disabled = false;
    });
    render();
});
