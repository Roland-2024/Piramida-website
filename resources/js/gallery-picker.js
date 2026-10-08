document.querySelectorAll('[data-gallery-picker]').forEach(picker => {
    const single = picker.hasAttribute('data-single-image');
    const dialog = picker.querySelector('[data-gallery-dialog]');
    const library = picker.querySelector('[data-gallery-library]');
    const selected = picker.querySelector('[data-gallery-selected]');
    const status = picker.querySelector('[data-gallery-status]');
    let ids = [...selected.querySelectorAll('input')].map(input => input.value).filter(Boolean);
    const items = () => [...library.querySelectorAll('[data-media-id]')];
    const button = (text, label, action) => {
        const element = document.createElement('button');
        element.type = 'button'; element.textContent = text; element.setAttribute('aria-label', label);
        element.addEventListener('click', action);
        return element;
    };
    const render = (focusId, focusAction = 0) => {
        selected.replaceChildren();
        ids.forEach((id, index) => {
            const source = items().find(item => item.dataset.mediaId === id);
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
        items().forEach(item => { item.querySelector('input').checked = ids.includes(item.dataset.mediaId); });
        dialog.showModal();
    });
    picker.querySelector('[data-gallery-close]').addEventListener('click', () => dialog.close());
    picker.querySelector('[data-gallery-apply]').addEventListener('click', () => {
        const checked = items().filter(item => item.querySelector('input').checked).map(item => item.dataset.mediaId);
        if (checked.length > 30) { picker.querySelector('[data-gallery-upload-status]').textContent = 'Select at most 30 gallery images.'; return; }
        ids = single ? checked.slice(0, 1) : [...ids.filter(id => checked.includes(id)), ...checked.filter(id => !ids.includes(id))];
        render(); status.textContent = ''; dialog.close();
    });
    picker.querySelector('[data-gallery-search]').addEventListener('input', event => {
        const term = event.target.value.toLocaleLowerCase();
        items().forEach(item => { item.hidden = !item.dataset.mediaName.toLocaleLowerCase().includes(term); });
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
                const item = document.createElement('label'); item.className = 'gallery-library-item';
                Object.assign(item.dataset, {mediaId: String(result.id), mediaName: result.name, mediaUrl: result.url, mediaEdit: result.edit_url});
                const image = document.createElement('img'); image.src = result.url; image.alt = '';
                const text = document.createElement('span');
                const checkbox = document.createElement('input'); checkbox.type = single ? 'radio' : 'checkbox';
                if (single) {
                    items().forEach(item => { item.querySelector('input').checked = false; });
                    checkbox.name = `picker_${picker.dataset.fieldName}`; checkbox.setAttribute('form', 'media-picker-controls');
                }
                checkbox.checked = true;
                text.append(checkbox, document.createTextNode(` ${result.name}`)); item.append(image, text); library.prepend(item);
                feedback.textContent = 'Uploaded. Apply your selection, then save the record.';
            } catch (error) { feedback.textContent = error.message; break; }
        }
        upload.disabled = false; upload.value = '';
        picker.querySelector('[data-gallery-apply]').disabled = false;
    });
    render();
});
