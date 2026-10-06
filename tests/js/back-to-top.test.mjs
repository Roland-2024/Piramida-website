import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { runInNewContext } from 'node:vm';

const source = readFileSync(new URL('../../resources/js/public.js', import.meta.url), 'utf8');
const handler = source.slice(source.indexOf("document.querySelector('[data-back-to-top]')"), source.indexOf("document.querySelectorAll('[data-gallery]')"));
for (const reduced of [false, true]) {
    let click;
    let scroll;
    runInNewContext(handler, {
        document: { querySelector: () => ({ addEventListener: (event, callback) => { assert.equal(event, 'click'); click = callback; } }) },
        window: { scrollTo: options => { scroll = options; } },
        matchMedia: () => ({ matches: reduced }),
    });
    click();
    assert.equal(scroll.top, 0);
    assert.equal(scroll.behavior, reduced ? 'instant' : 'smooth');
}
const footer = readFileSync(new URL('../../resources/views/public/partials/footer.blade.php', import.meta.url), 'utf8');
assert.match(footer, /<button type="button" data-back-to-top/);
assert.ok(!footer.includes('href="#top"'));
