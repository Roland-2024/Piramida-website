import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { runInNewContext } from 'node:vm';

const source = readFileSync(new URL('../../resources/js/public.js', import.meta.url), 'utf8');
const script = source.slice(source.indexOf('const spaceDots ='), source.indexOf("document.querySelectorAll('.leasing-form-carousel')"));
let offset = 0, reduced = false, update;
const buttons = Array.from({ length: 5 }, (_, index) => ({ dataset: { spaceTarget: String(index) }, setAttribute(key, value) { this[key] = value; }, addEventListener(event, callback) { this.click = callback; } }));
const panels = buttons.map((_, index) => ({ getBoundingClientRect: () => ({ top: index * 800 - offset, bottom: (index + 1) * 800 - offset }), scrollIntoView(options) { this.behavior = options.behavior; offset = index * 800; } }));
const nav = { querySelectorAll: () => buttons };
runInNewContext(script, {
    document: { querySelector: () => nav, getElementById: id => panels[Number(id)] },
    window: { innerHeight: 800, addEventListener(event, callback) { if (event === 'scroll') update = callback; } },
    requestAnimationFrame: callback => callback(),
    matchMedia: () => ({ matches: reduced }),
});
assert.equal(buttons[0]['aria-current'], 'true');
offset = 1600; update();
assert.equal(buttons[2]['aria-current'], 'true');
assert.equal(buttons[0]['aria-current'], 'false');
buttons[4].click(); update();
assert.equal(panels[4].behavior, 'smooth');
assert.equal(buttons[4]['aria-current'], 'true');
reduced = true; buttons[1].click(); update();
assert.equal(panels[1].behavior, 'instant');
offset = 5000; update();
assert.equal(nav.hidden, true);
