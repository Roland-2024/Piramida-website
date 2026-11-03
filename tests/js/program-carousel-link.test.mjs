import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { runInNewContext } from 'node:vm';
import test from 'node:test';

const source = readFileSync(new URL('../../resources/js/template-education.js', import.meta.url), 'utf8');
const update = source.match(/function updateCarouselUi\(\) \{[\s\S]*?(?=\n    function renderFrame)/)[0];

test('button follows active slide and removes stale links on unlinked slides', () => {
  const button = { hidden: true, removeAttribute(name) { delete this[name]; } };
  const context = {
    N: 3, activeIndex: 0, activeContentIndex: -1,
    items: [{ label: 'First', link: 'https://example.test/first' },
      { label: 'No link', link: '' }, { label: 'Last', link: 'https://example.test/last' }],
    activeSlideLink: button, activeSlideTitle: {}, activeSlideDescription: {}, dots: [],
  };
  const render = runInNewContext(`${update}; updateCarouselUi`, context);
  render();
  assert.equal(button.hidden, false);
  assert.equal(button.href, context.items[0].link);
  context.activeIndex = 1;
  render();
  assert.equal(button.hidden, true);
  assert.equal(button.href, undefined);
  context.activeIndex = 2;
  render();
  assert.equal(button.hidden, false);
  assert.equal(button.href, context.items[2].link);
  assert.equal(context.activeSlideTitle.textContent, 'Last');
  context.activeIndex = 3;
  render();
  assert.equal(button.href, context.items[0].link);
});

test('slide images are not links and CTA stays outside draggable track', () => {
  const view = readFileSync(new URL('../../resources/views/public/pages/education.blade.php', import.meta.url), 'utf8');
  assert.doesNotMatch(view.slice(view.indexOf('@foreach'), view.indexOf('@endforeach')), /<a\s/);
  assert.ok(view.indexOf('id="activeSlideLink"') > view.indexOf('id="activeSlideDescription"'));
});
