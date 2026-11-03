import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { runInNewContext } from 'node:vm';
import test from 'node:test';

const source = readFileSync(new URL('../../resources/js/template-education.js', import.meta.url), 'utf8');
const handler = source.match(/el\.addEventListener\("click", \(event\) => \{([\s\S]*?)\n      \}\);/)[1];

for (const [label, dragged, detail, blocked] of [
  ['normal click opens link', false, 1, false],
  ['drag does not open link', true, 1, true],
  ['keyboard still opens link after a drag', true, 0, false],
]) {
  test(label, () => {
    let prevented = false;
    let frames = 0;
    runInNewContext(`(() => {${handler}})()`, {
      event: { detail, preventDefault() { prevented = true; } },
      hasDragged: dragged, index: 2, activeIndex: 0, snapTarget: 0,
      calculateOffset: index => index, wrapOffset() {},
      requestAnimationTick() { frames++; },
    });
    assert.equal(prevented, blocked);
    assert.equal(frames, blocked ? 0 : 1);
  });
}
