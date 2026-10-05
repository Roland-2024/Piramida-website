import assert from 'node:assert/strict';
import { scrollCarousel } from '../../resources/js/carousel-scroll.js';

let now = 0;
let reduced = false;
let frame;
globalThis.performance = { now: () => now };
globalThis.matchMedia = () => ({ matches: reduced });
globalThis.requestAnimationFrame = callback => { frame = callback; return 1; };
globalThis.cancelAnimationFrame = () => { frame = undefined; };
const track = Object.assign(new EventTarget(), {
    scrollLeft: 0, scrollWidth: 1200, clientWidth: 400,
    style: { scrollBehavior: 'smooth', scrollSnapType: 'x mandatory' },
});

scrollCarousel(track, 600);
frame(375);
assert.ok(track.scrollLeft > 0 && track.scrollLeft < 600);
frame(750);
assert.equal(track.scrollLeft, 600);
assert.equal(track.style.scrollSnapType, 'x mandatory');
scrollCarousel(track, 800);
track.dispatchEvent(new Event('pointerdown'));
assert.equal(frame, undefined);
assert.equal(track.style.scrollBehavior, 'smooth');
reduced = true;
scrollCarousel(track, 2000);
assert.equal(track.scrollLeft, 800);
scrollCarousel(track, -100);
assert.equal(track.scrollLeft, 0);
console.log('Carousel checks passed: smooth movement, cancellation, reduced motion, bounds.');
