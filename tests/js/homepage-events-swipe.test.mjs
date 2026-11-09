import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { runInNewContext } from 'node:vm';
import test from 'node:test';

const source = readFileSync(new URL('../../resources/js/template-home.js', import.meta.url), 'utf8');

function setup() {
  const frames = [], timers = new Map(), calls = [];
  let timerId = 0;
  const element = () => ({
    events: {}, style: {},
    addEventListener(type, handler, options) { (this.events[type] ??= []).push({ handler, options }); },
    fire(type, event = {}) { for (const { handler } of this.events[type] ?? []) handler(event); },
  });
  const track = Object.assign(element(), {
    children: [], scrollLeft: 0,
    get firstChild() { return this.children[0]; },
    appendChild(card) { this.children.push(card); },
    insertBefore(card) { this.children.unshift(card); },
    querySelector() { return this.children[0]; },
    querySelectorAll(selector) { return selector === '.card-snap' ? this.children : []; },
    getBoundingClientRect() { return { left: 0, width: 100 }; },
  });
  const card = () => ({
    cloneNode: card, querySelectorAll: () => [], classList: { toggle() {} },
    getBoundingClientRect() { return { width: 100, left: track.children.indexOf(this) * 116 - track.scrollLeft }; },
  });
  track.children = [card(), card(), card()];
  const previous = element(), next = element();
  const window = element();
  const document = {
    querySelector: () => track.children[0],
    getElementById: id => ({ cardTrack: track, prevBtn: previous, nextBtn: next })[id],
  };
  runInNewContext(source.replace(/^import .*\n/, '').replace('export function', 'function') + '\ninitHome();', {
    document, window, getComputedStyle: () => ({ columnGap: '16px' }),
    requestAnimationFrame: callback => frames.push(callback),
    setTimeout: callback => { timers.set(++timerId, callback); return timerId; },
    clearTimeout: id => timers.delete(id),
    scrollCarousel: (_, left, smooth) => { calls.push({ left, smooth }); track.scrollLeft = left; },
  });
  frames.splice(0).forEach(callback => callback());
  return { track, next, previous, calls, settle() { const pending = [...timers.values()]; timers.clear(); pending.forEach(callback => callback()); } };
}

test('native touch is not blocked or advanced again by custom pointer swipe', () => {
  const { track, calls } = setup();
  const initial = calls.length;
  track.fire('pointerdown', { pointerType: 'touch', clientX: 200, clientY: 100 });
  track.fire('pointermove', { clientX: 40, clientY: 100, preventDefault() { assert.fail('native scrolling blocked'); } });
  track.fire('pointercancel');
  track.scrollLeft += 116; // Browser scrolls without a pointerup.
  track.fire('scroll');
  assert.equal(calls.length, initial);
  assert.equal(track.scrollLeft, 464);
  assert.equal(track.style.touchAction, undefined);
  const css = readFileSync(new URL('../../public/template/css/homepage.css', import.meta.url), 'utf8');
  assert.match(css, /touch-action: auto/);
  assert.match(css, /scroll-snap-type: x mandatory/);
});

test('loop waits for finger release and then wraps a clone', () => {
  const { track, calls, settle } = setup();
  track.fire('touchstart');
  track.scrollLeft = 6 * 116;
  track.fire('scroll');
  settle();
  assert.equal(calls.length, 1);
  track.fire('touchend', { touches: [] });
  settle();
  assert.equal(track.scrollLeft, 3 * 116);
  assert.equal(calls.at(-1).smooth, false);
  assert.equal(track.events.touchstart[0].options.passive, true);
});

test('touch cancellation settles and ordinary taps remain clickable', () => {
  const { track, settle } = setup();
  track.fire('touchstart');
  track.scrollLeft = 2 * 116;
  track.fire('touchcancel', { touches: [] });
  settle();
  assert.equal(track.scrollLeft, 5 * 116);
  track.fire('pointerdown', { pointerType: 'touch' });
  track.fire('click', { preventDefault() { assert.fail('tap blocked'); } });
});

test('desktop arrows and horizontal mouse swipes still navigate; vertical drags do not', () => {
  const { track, next, previous, calls } = setup();
  next.fire('click');
  assert.equal(track.scrollLeft, 4 * 116);
  previous.fire('click');
  assert.equal(track.scrollLeft, 3 * 116);
  track.fire('pointerdown', { pointerType: 'mouse', button: 0, clientX: 200, clientY: 100 });
  track.fire('pointerup', { clientX: 100, clientY: 105 });
  assert.equal(track.scrollLeft, 4 * 116);
  let prevented = false;
  track.fire('click', { preventDefault() { prevented = true; }, stopPropagation() {} });
  assert.equal(prevented, true);
  const count = calls.length;
  track.fire('pointerdown', { pointerType: 'mouse', button: 0, clientX: 200, clientY: 100 });
  track.fire('pointerup', { clientX: 100, clientY: 300 });
  assert.equal(calls.length, count);
});
