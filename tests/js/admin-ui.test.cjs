const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

const handlers = {};
const form = { entries: [['title', 'Original'], ['gallery_media_ids[]', '1']], querySelector: () => true };
class File {
    constructor(name = '', size = 0, lastModified = Date.now()) { Object.assign(this, { name, size, lastModified }); }
}
class FormData {
    constructor(form) { this.entries = [...form.entries, ['upload', form.file || new File()]]; }
    [Symbol.iterator]() { return this.entries[Symbol.iterator](); }
}
const listen = (name, callback) => { handlers[name] = callback; };
const document = {
    querySelector: () => null,
    querySelectorAll: selector => selector === 'main form[method="POST"]' ? [form] : [],
    addEventListener: listen,
};
vm.runInNewContext(fs.readFileSync('resources/js/app.js', 'utf8').replace(/^import .*;\r?\n/gm, ''), {
    document, window: { matchMedia: () => ({ addEventListener() {} }), addEventListener: listen },
    File, FormData, queueMicrotask: callback => callback(),
});
const warns = () => {
    let prevented = false;
    handlers.beforeunload({ preventDefault() { prevented = true; } });
    return prevented;
};
assert.equal(warns(), false, 'Empty file inputs must not make a pristine form dirty');
form.entries[0][1] = 'Changed';
assert.equal(warns(), true, 'Changed content must warn');
handlers.submit({ target: form, defaultPrevented: true });
assert.equal(warns(), true, 'Cancelled submission must retain the warning');
handlers.submit({ target: {}, defaultPrevented: false });
assert.equal(warns(), true, 'Other forms such as logout must not bypass the warning');
handlers.submit({ target: form, defaultPrevented: false });
assert.equal(warns(), false, 'Saving the editor form must not warn');
handlers.pageshow();
assert.equal(warns(), true, 'Back/forward navigation must restore protection');
form.entries[0][1] = 'Original';
assert.equal(warns(), false, 'Reverted changes must not warn');
form.entries.push(['gallery_media_ids[]', '2']);
assert.equal(warns(), true, 'Gallery selections must warn');
form.entries.pop();
form.file = new File('photo.jpg', 123, 1);
assert.equal(warns(), true, 'Selected uploads must warn');
console.log('Admin unsaved-change checks passed');
