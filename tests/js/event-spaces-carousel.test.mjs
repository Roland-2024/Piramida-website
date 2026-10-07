import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { runInNewContext } from 'node:vm';

const source = readFileSync(new URL('../../resources/js/public.js', import.meta.url), 'utf8');
const script = source.slice(source.indexOf("document.querySelectorAll('.event-spaces-carousel')"), source.indexOf('const spaceDots ='));
const element = () => ({ attributes: {}, events: {}, children: [], setAttribute(key, value) { this.attributes[key] = value; }, getAttribute() { return 'Page'; }, addEventListener(key, callback) { this.events[key] = callback; }, replaceChildren(...children) { this.children = children; } });
const track = Object.assign(element(), { clientWidth: 1280, scrollWidth: 1280, scrollLeft: 0 });
const previous = element(), next = element(), pagination = element();
const carousel = { querySelector: selector => selector.includes('track') ? track : selector.endsWith('left') ? previous : next, parentElement: { querySelector: () => pagination } };
let resize;
runInNewContext(script, {
    document: { querySelectorAll: () => [carousel], createElement: element },
    ResizeObserver: class { constructor(callback) { resize = callback; } observe() { resize(); } },
    scrollCarousel: (target, position) => { target.scrollLeft = Math.max(0, Math.min(position, target.scrollWidth - target.clientWidth)); target.events.scroll(); },
});
assert.equal(next.hidden, true); // All four cards fit: no misleading controls.
assert.equal(pagination.hidden, true);
track.scrollWidth = 1900;
resize();
assert.equal(next.hidden, false);
assert.equal(previous.disabled, true);
assert.equal(pagination.children.length, 2);
next.events.click();
assert.equal(track.scrollLeft, 620);
assert.equal(next.disabled, true);
assert.equal(pagination.children[1].attributes['aria-current'], 'true');
pagination.children[0].events.click();
assert.equal(track.scrollLeft, 0);
next.events.click();
previous.events.click();
assert.equal(track.scrollLeft, 0);
track.clientWidth = 0;
resize(); // Hidden mobile desktop-track must not produce an infinite loop.
track.clientWidth = track.scrollWidth = 1280;
resize();
assert.equal(next.hidden, true);
