import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { runInNewContext } from 'node:vm';
import test from 'node:test';

const view = readFileSync(new URL('../../resources/views/public/submissions/_leasing-form.blade.php', import.meta.url), 'utf8');
const script = view.match(/<script>([\s\S]*?)<\/script>/)[1];

test('monthly offer preview uses ALL and clears invalid totals', () => {
  let update;
  const offer = { validity: { valid: true }, valueAsNumber: 1500, validationMessage: 'Below minimum',
    setAttribute() {}, addEventListener(_, handler) { update = handler; } };
  const output = {}, error = {};
  const form = { dataset: { area: '110.20' },
    querySelector: selector => ({ '#leasing_offer_per_sqm': offer, '#leasing_monthly_rent': output, '#leasing_offer_error': error })[selector] };
  runInNewContext(script, { document: { querySelector: () => form } });
  assert.equal(output.textContent, '165300.00 ALL');
  offer.valueAsNumber = 1600.25;
  update();
  assert.equal(output.textContent, '176347.55 ALL');
  offer.validity.valid = false;
  update();
  assert.equal(output.textContent, '—');
  assert.equal(error.hidden, false);
});
