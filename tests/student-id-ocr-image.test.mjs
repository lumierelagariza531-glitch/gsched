import assert from 'node:assert/strict';
import test from 'node:test';
import { getStudentIdOcrDimensions } from '../resources/js/student-id-ocr-image.js';

test('small ID images are enlarged substantially for OCR', () => {
    assert.deepEqual(getStudentIdOcrDimensions(300, 200), {
        width: 1050,
        height: 700,
    });
});

test('medium ID images are enlarged until the OCR edge limit is reached', () => {
    assert.deepEqual(getStudentIdOcrDimensions(1000, 600), {
        width: 3200,
        height: 1920,
    });
});

test('large images are reduced to safe edge and pixel limits', () => {
    const dimensions = getStudentIdOcrDimensions(6000, 4000);

    assert.ok(Math.max(dimensions.width, dimensions.height) <= 3200);
    assert.ok(dimensions.width * dimensions.height <= 8_000_000);
});

test('invalid source dimensions are rejected', () => {
    assert.throws(() => getStudentIdOcrDimensions(0, 300), RangeError);
    assert.throws(() => getStudentIdOcrDimensions(Number.NaN, 300), RangeError);
});
