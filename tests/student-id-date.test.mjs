import assert from 'node:assert/strict';
import test from 'node:test';
import {
    matchesStudentIdDateOfBirth,
    validateRegistrationDob,
} from '../resources/js/student-id-date.js';

const dob = '2005-04-05';

test('matches labeled DOB formats used on student IDs', () => {
    for (const text of [
        'Date of Birth: April 5, 2005',
        'DOB 5 April 2005',
        'D.O.B.: Apr. 5th, 2005',
        'DOB 04/05/2005',
        'DOB 05-04-2005',
        'DOB 04/05/05',
        'DOB 2005.04.05',
        'DOB O4/05/2O05',
        'Date of Birth:\nApril 5, 2005\nPlace of Birth: Sample City',
    ]) {
        assert.equal(matchesStudentIdDateOfBirth(text, dob), true, text);
    }
});

test('matches a common OCR misread of the printed Date of Birth label', () => {
    assert.equal(
        matchesStudentIdDateOfBirth(
            'Date of Bath )\nNOVEMBER 4, 2004 | .\nPlace of Birth b\nMANILA',
            '2004-11-04',
        ),
        true,
    );
});

test('does not match unrelated dates or dates outside the DOB field', () => {
    for (const text of [
        'Academic Year: 2005-04-05\nPlace of Birth: Sample City',
        'Student ID: 25-1-12345',
        'Serial: 05042005',
        'Date of Birth: April 6, 2005',
        'Date of Birth:\nPlace of Birth: April 5, 2005',
        'Place of Birth: Sample City\nDate of Birth: April 5, 2005',
        'DOB 13/13/2005',
    ]) {
        assert.equal(matchesStudentIdDateOfBirth(text, dob), false, text);
    }

    assert.equal(matchesStudentIdDateOfBirth('DOB 2005-04-05', '2005-02-30'), false);
    assert.equal(matchesStudentIdDateOfBirth('DOB 2005-04-05', ''), false);
});

test('registration DOB validation accepts canonical ISO dates within the supported age range', () => {
    assert.deepEqual(validateRegistrationDob('2014-10-01', '2026-10-01'), {
        isoDate: '2014-10-01',
        message: '',
    });
    assert.deepEqual(validateRegistrationDob('1926-10-01', '2026-10-01'), {
        isoDate: '1926-10-01',
        message: '',
    });
});

test('registration DOB validation rejects empty, noncanonical, impossible, future, and out-of-range dates', () => {
    assert.match(validateRegistrationDob('', '2026-10-01').message, /Enter/);
    assert.match(validateRegistrationDob('10/01/2014', '2026-10-01').message, /real date/);
    assert.match(validateRegistrationDob('2014-2-01', '2026-10-01').message, /real date/);
    assert.match(validateRegistrationDob('2025-02-29', '2026-10-01').message, /real date/);
    assert.match(validateRegistrationDob('2014-10-02', '2026-10-01').message, /12 and 100/);
    assert.match(validateRegistrationDob('1925-09-30', '2026-10-01').message, /12 and 100/);
    assert.match(validateRegistrationDob('2026-10-02', '2026-10-01').message, /future/);
});
