const sidebar = document.querySelector('[data-sidebar]');
const overlay = document.querySelector('[data-sidebar-overlay]');
const toggle = document.querySelector('[data-sidebar-toggle]');

const closeSidebar = () => {
    sidebar?.classList.add('hidden');
    sidebar?.classList.remove('flex');
    overlay?.classList.add('hidden');
};

toggle?.addEventListener('click', () => {
    sidebar?.classList.remove('hidden');
    sidebar?.classList.add('flex');
    overlay?.classList.remove('hidden');
});

overlay?.addEventListener('click', closeSidebar);

document.querySelectorAll('[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (!window.confirm(form.dataset.confirm)) {
            event.preventDefault();
        }
    });
});

document.querySelectorAll('textarea[data-rich-text]').forEach((textarea) => {
    const wrapper = document.createElement('div');
    wrapper.className = 'rich-text';

    const toolbar = document.createElement('div');
    toolbar.className = 'rich-text__toolbar';

    const editor = document.createElement('div');
    editor.className = 'rich-text__editor';
    editor.contentEditable = 'true';
    editor.innerHTML = textarea.value;

    const controls = [
        ['formatBlock', 'p', 'Paragraph'],
        ['formatBlock', 'h2', 'Heading'],
        ['bold', null, 'Bold'],
        ['italic', null, 'Italic'],
        ['insertUnorderedList', null, 'Bullets'],
        ['insertOrderedList', null, 'Numbered list'],
    ];

    controls.forEach(([command, value, label]) => {
        const button = document.createElement('button');
        button.type = 'button';
        button.textContent = label;
        button.addEventListener('click', () => {
            editor.focus();
            document.execCommand(command, false, value);
            textarea.value = editor.innerHTML;
        });
        toolbar.append(button);
    });

    const linkButton = document.createElement('button');
    linkButton.type = 'button';
    linkButton.textContent = 'Link';
    linkButton.addEventListener('click', () => {
        const url = window.prompt('Link URL');

        if (url) {
            editor.focus();
            document.execCommand('createLink', false, url);
            textarea.value = editor.innerHTML;
        }
    });
    toolbar.append(linkButton);

    editor.addEventListener('input', () => {
        textarea.value = editor.innerHTML;
    });

    textarea.hidden = true;
    wrapper.append(toolbar, editor);
    textarea.before(wrapper);
});
