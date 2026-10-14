import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { runInNewContext } from 'node:vm';

const source = readFileSync(new URL('../../resources/js/public.js', import.meta.url), 'utf8');
const script = source.slice(source.indexOf('const intro ='), source.indexOf('// Enhance real request sections'));

function visit(jar, { width = 1365, reduced = false, protocol = 'https:', homepage = true } = {}) {
    const stripeNodes = [];
    const timers = [];
    const cancelled = [];
    let keydown;
    let scrolls = 0;
    const swipe = { hidden: true };
    const intro = {
        hidden: true,
        querySelector: () => ({ append: node => stripeNodes.push(node), style: { setProperty() {} } }),
        querySelectorAll: () => [],
    };
    const document = {
        get cookie() { return jar.value; },
        set cookie(value) { jar.writes.push(value); jar.value = value.split(';')[0]; },
        querySelector: selector => selector === '[data-intro]' ? (homepage ? intro : null) : swipe,
        createElement: () => ({ style: { setProperty() {} } }),
        addEventListener: (event, callback) => { assert.equal(event, 'keydown'); keydown = callback; },
    };
    runInNewContext(script, {
        document,
        window: { innerWidth: width, scrollTo: () => { scrolls++; } },
        location: { protocol },
        matchMedia: () => ({ matches: reduced }),
        performance: { now: () => 0 },
        requestAnimationFrame() {},
        setTimeout: (callback, delay) => { timers.push({ callback, delay }); return delay; },
        clearTimeout: id => cancelled.push(id),
    });
    return { intro, swipe, stripeNodes, timers, cancelled, scrolls, keydown };
}

const jar = { value: 'unrelated=1', writes: [] };
const first = visit(jar);
assert.equal(first.intro.hidden, false);
assert.equal(first.stripeNodes.length, 9);
assert.equal(first.scrolls, 1);
assert.equal(jar.writes[0], 'piramida_intro_seen=1; Path=/; SameSite=Lax; Secure');
assert.ok(!/Expires|Max-Age|Domain/.test(jar.writes[0]), 'Use a host-only browser-session cookie');
assert.deepEqual(first.timers.map(timer => timer.delay), [4000, 4754, 5300]);
first.timers.forEach(timer => timer.callback());
assert.equal(first.intro.hidden, true);
assert.equal(first.swipe.hidden, true);

// New VM contexts represent refreshes, AL/EN navigation and other tabs sharing the cookie.
for (let i = 0; i < 3; i++) {
    const repeat = visit(jar);
    assert.equal(repeat.intro.hidden, true);
    assert.equal(repeat.swipe.hidden, true);
    assert.equal(repeat.timers.length, 0);
    assert.equal(repeat.scrolls, 0);
}
assert.equal(jar.writes.length, 1);

for (const options of [{ width: 375 }, { width: 1024 }, { reduced: true }, { homepage: false }]) {
    const skippedJar = { value: '', writes: [] };
    const skipped = visit(skippedJar, options);
    assert.equal(skipped.intro.hidden, true);
    assert.equal(skipped.timers.length, 0);
    assert.equal(skippedJar.writes.length, 0);
}
const mobileFirst = { value: '', writes: [] };
visit(mobileFirst, { width: 375 });
assert.equal(visit(mobileFirst).intro.hidden, false, 'Only a displayed intro marks the session');

for (const key of ['Escape', 'Tab']) {
    const skipJar = { value: '', writes: [] };
    const skipped = visit(skipJar);
    skipped.keydown({ key });
    assert.equal(skipped.intro.hidden, true);
    assert.equal(skipped.swipe.hidden, true);
    assert.equal(skipped.cancelled.length, 3);
    assert.equal(visit(skipJar).intro.hidden, true, 'Skipping still counts as the first display');
}

const httpJar = { value: 'other=1; piramida_intro_seen=10', writes: [] };
assert.equal(visit(httpJar, { protocol: 'http:' }).intro.hidden, false);
assert.equal(httpJar.writes[0], 'piramida_intro_seen=1; Path=/; SameSite=Lax');
const existingJar = { value: 'other=1; piramida_intro_seen=1; another=2', writes: [] };
assert.equal(visit(existingJar).intro.hidden, true);
assert.equal(existingJar.writes.length, 0);
assert.equal(visit({ value: '', writes: [] }).intro.hidden, false, 'A fresh session can show the intro again');

console.log('Homepage intro session, timing, accessibility and protocol checks passed.');
