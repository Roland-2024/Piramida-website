import assert from 'node:assert/strict';
import fs from 'node:fs';
import vm from 'node:vm';

class Element {
    constructor(tag = 'div') { this.tag = tag; this.children = []; this.dataset = {}; this.handlers = {}; this.value = ''; }
    append(...items) { items.forEach(item => { item.parent = this; this.children.push(item); }); }
    prepend(item) { item.parent = this; this.children.unshift(item); }
    replaceChildren() { this.children = []; }
    addEventListener(event, handler) { this.handlers[event] = handler; }
    setAttribute() {}
    focus() {}
    showModal() { this.open = true; }
    close() { this.open = false; }
    matches(selector) { return selector === this.tag; }
    closest() { return this.parent.closestMedia || this.parent.parent; }
    querySelectorAll(selector) {
        return this.children.flatMap(child => [
            ...((selector === '[data-media-id]' ? child.dataset.mediaId : child.tag === selector) ? [child] : []),
            ...child.querySelectorAll(selector),
        ]);
    }
    querySelector(selector) { return this.querySelectorAll(selector)[0]; }
}

const nodes = Object.fromEntries(['dialog', 'library', 'selected', 'status', 'more', 'upload-status', 'open', 'close', 'apply', 'search', 'upload'].map(key => [key, new Element()]));
const existing = new Element('label');
Object.assign(existing.dataset, {mediaId: '1', mediaName: 'Saved image', mediaUrl: '/1.jpg', mediaEdit: '/edit/1'});
existing.append(new Element('input')); nodes.library.append(existing);
const hidden = new Element('input'); hidden.value = '1'; nodes.selected.append(hidden);
const picker = {
    dataset: {libraryUrl: '/admin/media/picker', fieldName: 'gallery_media_ids'},
    hasAttribute: () => false,
    querySelector: selector => nodes[selector.replace('[data-gallery-', '').replace(']', '')],
};
let payload = {images: [{id: 2, name: 'Second', url: '/2.jpg', edit_url: '/edit/2'}], next_url: '/page2'};
const requests = [];
vm.runInNewContext(fs.readFileSync('resources/js/gallery-picker.js', 'utf8'), {
    document: {querySelectorAll: () => [picker], createElement: tag => new Element(tag), createTextNode: text => Object.assign(new Element('text'), {textContent: text})},
    fetch: async url => { requests.push(String(url)); return {ok: true, json: async () => payload}; },
    AbortController, URL, location: {href: 'http://localhost/admin/news/1/edit'},
    setTimeout: callback => { callback(); return 1; }, clearTimeout() {},
});
const settle = async () => { for (let i = 0; i < 6; i++) await Promise.resolve(); };
const ids = () => nodes.selected.querySelectorAll('input').map(input => input.value);
const choose = id => {
    const item = nodes.library.children.find(item => item.dataset.mediaId === id);
    const input = item.querySelector('input'); input.checked = true;
    nodes.library.handlers.change({target: input});
};
assert.deepEqual(ids(), ['1']);
assert.equal(requests.length, 0, 'Library must not load until opened');
nodes.open.handlers.click(); await settle(); choose('2');
payload = {images: [{id: 3, name: 'Third', url: '/3.jpg', edit_url: '/edit/3'}], next_url: null};
nodes.more.handlers.click(); await settle(); choose('3');
assert.equal(nodes.more.hidden, true);
payload = {images: [], next_url: null};
nodes.search.handlers.input({target: {value: 'no match'}}); await settle();
nodes.apply.handlers.click();
assert.deepEqual(ids(), ['1', '2', '3'], 'Search and pagination must preserve staged choices and saved order');
nodes.open.handlers.click(); await settle();
nodes.close.handlers.click();
assert.deepEqual(ids(), ['1', '2', '3'], 'Closing without Apply must preserve saved selections');
console.log('Gallery lazy loading, pagination, search, selection and cancel checks passed');
