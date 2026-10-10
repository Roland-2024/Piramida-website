import assert from 'node:assert/strict';
import { initEventsArchive } from '../../resources/js/events-archive.js';

function element(classes = []) {
    const values = new Set(classes);
    return {
        children: [], handlers: {}, textContent: '', hidden: false, dataset: {},
        classList: {
            add: name => values.add(name), remove: name => values.delete(name),
            contains: name => values.has(name),
            toggle(name, enabled) { enabled ? values.add(name) : values.delete(name); },
            replace(from, to) { if (values.delete(from)) values.add(to); },
        },
        querySelectorAll() { return []; },
        addEventListener(name, handler) { this.handlers[name] = handler; },
        append(...items) { this.children.push(...items); },
        setAttribute() {}, removeAttribute() {},
    };
}
const section = element(), track = element(), mobile = element(), info = element();
const status = element(), more = element(), archive = element();
more.href = '/events?page=2';
archive.dataset = { loading: 'Loading', error: 'Retry' };
track.children.push(element(['is-active']));
section.querySelector = selector => ({ '#eventsTrack': track, '#eventsInfo': info })[selector];
archive.querySelector = selector => ({ '#eventsSection': section, '#eventsMobile': mobile, '#eventsLoadStatus': status, '#eventsLoadMore': more })[selector];
globalThis.document = { querySelector: () => archive };
let mobileMode = false;
globalThis.matchMedia = query => ({ matches: query.includes('reduced-motion') || mobileMode });
let resolveRequest;
const requests = [];
globalThis.fetch = url => {
    requests.push(url);
    return new Promise(resolve => { resolveRequest = resolve; });
};
let batch;
globalThis.DOMParser = class {
    parseFromString() {
        return {
            querySelector: selector => selector === '#eventsArchive' ? {} : batch.next ? { href: batch.next } : null,
            querySelectorAll: selector => selector.includes('eventsTrack') ? [batch.slide] : batch.cards,
        };
    }
};
const flush = () => new Promise(resolve => setImmediate(resolve));
const wheel = direction => section.handlers.wheel({ deltaY: direction, deltaX: 0, preventDefault() {} });
const complete = async next => {
    batch = { next, slide: element(['is-active']), cards: [element(), element(), element()] };
    resolveRequest({ ok: true, text: async () => 'HTML' });
    await flush();
};

initEventsArchive();
assert.equal(more.hidden, true);
wheel(1);
wheel(1);
assert.equal(requests.length, 1, 'Concurrent scrolls must share one request');
await complete('/events?page=3');
assert.equal(track.children.length, 2);
assert.equal(mobile.children.length, 3);
assert.equal(track.children[1].classList.contains('is-active'), true);
assert.equal(track.children[0].inert, true);
wheel(-1);
assert.equal(track.children[0].classList.contains('is-active'), true);
assert.equal(requests.length, 1, 'Backwards scrolling must use loaded groups');
wheel(1);
wheel(1);
resolveRequest({ ok: false });
await flush();
assert.equal(more.hidden, false, 'Failed requests expose the retry link');
assert.equal(status.textContent, 'Retry');
wheel(1);
assert.equal(requests.length, 2, 'Scroll must not repeatedly retry a failed request');
more.handlers.click({ preventDefault() {} });
assert.equal(requests[2], '/events?page=3', 'Retry must not skip the failed page');
await complete('/events?page=4');
assert.equal(track.children.length, 3);
assert.equal(status.textContent, '');

mobileMode = true;
Object.assign(mobile, { scrollTop: 1700, clientHeight: 800, scrollHeight: 2400 });
mobile.handlers.scroll();
mobile.handlers.scroll();
assert.equal(requests.length, 4, 'Mobile near-end scroll loads a single batch');
await complete(null);
assert.equal(track.children.length, 4);
assert.equal(track.children[2].classList.contains('is-active'), true, 'Mobile loading must not move the desktop group');
mobile.handlers.scroll();
assert.equal(requests.length, 4, 'The final batch stops requests');
assert.equal(more.hidden, true);
console.log('Event archive scroll, retry, concurrency and mobile checks passed');
