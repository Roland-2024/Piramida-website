import './gallery-picker';

const sidebar = document.querySelector('[data-sidebar]');
const overlay = document.querySelector('[data-sidebar-overlay]');
const toggle = document.querySelector('[data-sidebar-toggle]');
const shell = document.querySelector('[data-admin-shell]');
const mobileNavigation = window.matchMedia('(max-width: 767px)');

const closeSidebar = (restoreFocus = true) => {
    sidebar?.classList.add('hidden');
    sidebar?.classList.remove('flex');
    overlay?.classList.add('hidden');
    toggle?.setAttribute('aria-expanded', 'false');
    if (shell) shell.inert = false;
    document.body.classList.remove('overflow-hidden');
    if (restoreFocus && mobileNavigation.matches) toggle?.focus();
};

toggle?.addEventListener('click', () => {
    sidebar?.classList.remove('hidden');
    sidebar?.classList.add('flex');
    overlay?.classList.remove('hidden');
    toggle?.setAttribute('aria-expanded', 'true');
    if (shell) shell.inert = true;
    document.body.classList.add('overflow-hidden');
    sidebar?.querySelector('[data-sidebar-close]')?.focus();
});

overlay?.addEventListener('click', () => closeSidebar());
document.querySelector('[data-sidebar-close]')?.addEventListener('click', () => closeSidebar());
mobileNavigation.addEventListener('change', () => closeSidebar(false));
document.addEventListener('keydown', event => {
    if (toggle?.getAttribute('aria-expanded') !== 'true') return;
    if (event.key === 'Escape') {
        event.preventDefault();
        closeSidebar();
    }
    if (event.key === 'Tab') {
        const controls = [...sidebar.querySelectorAll('a[href], button:not([disabled]), summary, [tabindex="0"]')]
            .filter(control => control.getClientRects().length);
        const first = controls[0];
        const last = controls.at(-1);
        if ((event.shiftKey && document.activeElement === first) || (!event.shiftKey && document.activeElement === last)) {
            event.preventDefault();
            (event.shiftKey ? last : first)?.focus();
        }
    }
});

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

// Compare actual submitted values, including rich text, gallery order and files.
const formSnapshot = form => JSON.stringify([...new FormData(form)].map(([name, value]) => [
    name, value instanceof File ? (value.name ? [value.name, value.size, value.lastModified] : null) : value,
]));
const editingForms = [...document.querySelectorAll('main form[method="POST"]')]
    .filter(form => form.querySelector('input:not([type="hidden"]), textarea, select'))
    .map(form => ({ form, initial: formSnapshot(form) }));
let submitting = false;
document.addEventListener('submit', event => {
    queueMicrotask(() => {
        if (!event.defaultPrevented && editingForms.some(({ form }) => form === event.target)) submitting = true;
    });
});
window.addEventListener('pageshow', () => { submitting = false; });
window.addEventListener('beforeunload', event => {
    if (!submitting && editingForms.some(({ form, initial }) => formSnapshot(form) !== initial)) {
        event.preventDefault();
        event.returnValue = '';
    }
});
