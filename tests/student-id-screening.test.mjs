import assert from 'node:assert/strict';
import test from 'node:test';
import { screenStudentIdBack, screenStudentIdFront } from '../resources/js/student-id-screening.js';

const frontApplicant = {
    firstName: 'Taylor',
    middleName: '',
    lastName: 'Sample',
    studentId: '23-1-12345',
};

test('front screening requires applicant names and student ID, not school or program text', () => {
    assert.deepEqual(
        screenStudentIdFront({
            ocrText: 'Name: Taylor Q. Sample\nBSBA\nStudent ID: 23-1-12345',
            ...frontApplicant,
        }),
        {
            firstNameMatched: true,
            middleNameMatched: true,
            lastNameMatched: true,
            studentIdMatched: true,
            passed: true,
        },
    );
});

test('front screening passes when full-card OCR is partial but details-region OCR reads names and ID', () => {
    const result = screenStudentIdFront({
        ocrResults: [
            { text: 'BSBA\nTAYLOR Q.' },
            { text: 'SAMPLE\n23-1-1234' },
            { text: 'Taylor Q. Sample\n23-1-12345' },
        ],
        ...frontApplicant,
    });

    assert.deepEqual(result, {
        firstNameMatched: true,
        middleNameMatched: true,
        lastNameMatched: true,
        studentIdMatched: true,
        passed: true,
    });
});

test('front screening matches a full middle name when OCR joins the printed middle initial to it', () => {
    const result = screenStudentIdFront({
        ocrResults: [
            { text: 'AVERY MORGANB.\nBSBA\n25-1-0008' },
            { text: 'SAMPLE\nAVERY MORGANB.\nBSBA\n25-1 0000 "9' },
        ],
        firstName: 'Avery',
        middleName: 'Morgan',
        lastName: 'Sample',
        studentId: '25-1-0000',
    });

    assert.deepEqual(result, {
        firstNameMatched: true,
        middleNameMatched: true,
        lastNameMatched: true,
        studentIdMatched: true,
        passed: true,
    });
});

test('front screening matches two given names entered together when OCR appends the printed trailing initial', () => {
    const result = screenStudentIdFront({
        ocrResults: [
            { text: 'CILMAR\nHEART VALYRIE A.\nBSBA\n23-1 01372 "9' },
        ],
        firstName: 'Heart Valyrie',
        middleName: '',
        lastName: 'Cilmar',
        studentId: '23-1-01372',
    });

    assert.deepEqual(result, {
        firstNameMatched: true,
        middleNameMatched: true,
        lastNameMatched: true,
        studentIdMatched: true,
        passed: true,
    });
});

test('front screening matches the same card when its given names are split across first and middle name fields', () => {
    const result = screenStudentIdFront({
        ocrResults: [
            { text: 'CILMAR\nHEART VALYRIE A.\nBSBA\n23-1 01372 "9' },
        ],
        firstName: 'Heart',
        middleName: 'Valyrie',
        lastName: 'Cilmar',
        studentId: '23-1-01372',
    });

    assert.deepEqual(result, {
        firstNameMatched: true,
        middleNameMatched: true,
        lastNameMatched: true,
        studentIdMatched: true,
        passed: true,
    });
});

test('front screening supports two-name and three-name applicants', () => {
    const twoNames = screenStudentIdFront({
        ocrText: 'AVERY SAMPLE\n25-1-0000',
        firstName: 'Avery',
        middleName: '',
        lastName: 'Sample',
        studentId: '25-1-0000',
    });
    assert.equal(twoNames.passed, true);

    const threeNames = screenStudentIdFront({
        ocrText: 'JORDAN M CRUZ\n25-1-0001',
        firstName: 'Jordan',
        middleName: 'M',
        lastName: 'Cruz',
        studentId: '25-1-0001',
    });
    assert.equal(threeNames.passed, true);
});

test('front screening accepts a three-name applicant and student ID printed with spaces instead of hyphens', () => {
    const result = screenStudentIdFront({
        ocrResults: [
            { text: 'SAMETH MC DENVER\n25 1 02495' },
        ],
        firstName: 'Sameth',
        middleName: 'MC',
        lastName: 'Denver',
        studentId: '25-1-02495',
    });

    assert.deepEqual(result, {
        firstNameMatched: true,
        middleNameMatched: true,
        lastNameMatched: true,
        studentIdMatched: true,
        passed: true,
    });
});

test('front screening uses focused OCR text to match the printed Sameth MC Denver card', () => {
    const result = screenStudentIdFront({
        ocrResults: [
            { text: 'SAMETH Mc DENVER IL\nBSHM\n257 02495' },
            { text: 'SAMETH MC DENVER L.\nBSHM\n251 02495' },
        ],
        firstName: 'Sameth',
        middleName: 'MC',
        lastName: 'Denver',
        studentId: '25-1-02495',
    });

    assert.deepEqual(result, {
        firstNameMatched: true,
        middleNameMatched: true,
        lastNameMatched: true,
        studentIdMatched: true,
        passed: true,
    });
});

test('front screening accepts Sameth MC Denver, Gaviola, and a space-separated enrollment ID from the attached card OCR', () => {
    const ocrResults = [
        { text: 'SAMETH Mc DENVER IL\nBSHM\n257 02495' },
        { text: 'GAVIOLA\n' },
        { text: 'SAMETH MC DENVER L.\nBSHM\n251 02495' },
    ];
    const result = screenStudentIdFront({
        ocrResults,
        firstName: 'Sameth MC Denver',
        middleName: '',
        lastName: 'Gaviola',
        studentId: '25-1-02495',
    });

    assert.deepEqual(result, {
        firstNameMatched: true,
        middleNameMatched: true,
        lastNameMatched: true,
        studentIdMatched: true,
        passed: true,
    });

    assert.equal(screenStudentIdFront({
        ocrResults: [
            { text: 'NAVIOLA\nSAMETH MC DENVER L.\nBSHM\n251 02495' },
        ],
        firstName: 'Sameth MC Denver',
        middleName: '',
        lastName: 'Gaviola',
        studentId: '25-1-02495',
    }).lastNameMatched, false);
    assert.equal(screenStudentIdFront({
        ocrResults,
        firstName: 'Sameth MC Denver',
        middleName: '',
        lastName: 'Gaviola',
        studentId: '25-1-02496',
    }).studentIdMatched, false);
});

test('front screening rejects missing names or a missing/mismatched student ID', () => {
    assert.equal(screenStudentIdFront({
        ocrText: 'Taylor Q.\nStudent ID: 23-1-12345',
        ...frontApplicant,
    }).lastNameMatched, false);
    assert.equal(screenStudentIdFront({
        ocrText: 'Sample\nStudent ID: 23-1-12345',
        ...frontApplicant,
    }).firstNameMatched, false);
    assert.equal(screenStudentIdFront({
        ocrText: 'Taylor Sample\nBSBA',
        ...frontApplicant,
    }).passed, false);
    assert.equal(screenStudentIdFront({
        ocrText: 'Taylor Sample\nStudent ID: 23-1-12346',
        ...frontApplicant,
    }).studentIdMatched, false);
});

test('front screening requires a full entered middle name and only permits a one-letter initial when entered', () => {
    const matchingInitial = screenStudentIdFront({
        ocrText: 'Taylor E. Sample\n23-1-12345',
        ...frontApplicant,
        middleName: 'E.',
    });
    assert.equal(matchingInitial.middleNameMatched, true);
    assert.equal(matchingInitial.passed, true);

    const mismatchingInitial = screenStudentIdFront({
        ocrText: 'Taylor A. Sample\n23-1-12345',
        ...frontApplicant,
        middleName: 'E.',
    });
    assert.equal(mismatchingInitial.middleNameMatched, false);
    assert.equal(mismatchingInitial.passed, false);

    const fullMiddleName = screenStudentIdFront({
        ocrText: 'Taylor E. Sample\n23-1-12345',
        ...frontApplicant,
        middleName: 'Emilia',
    });
    assert.equal(fullMiddleName.middleNameMatched, false);
    assert.equal(fullMiddleName.passed, false);

    assert.equal(screenStudentIdFront({
        ocrText: 'Taylor Emilia Sample\n23-1-12345',
        ...frontApplicant,
        middleName: 'Emilia',
    }).middleNameMatched, true);
    assert.equal(screenStudentIdFront({
        ocrText: 'Taylor Enrollment Sample\n23-1-12345',
        ...frontApplicant,
        middleName: 'E.',
    }).middleNameMatched, false);

    const noMiddleName = screenStudentIdFront({
        ocrText: 'Taylor Sample\n23-1-12345',
        ...frontApplicant,
    });
    assert.equal(noMiddleName.middleNameMatched, true);
    assert.equal(noMiddleName.passed, true);
});

test('front names preserve existing OCR matching behavior', () => {
    assert.equal(screenStudentIdFront({
        ocrText: 'TayIor Sample\n23-1-12345',
        ...frontApplicant,
    }).firstNameMatched, true);
    assert.equal(screenStudentIdFront({
        ocrText: 'TayXor Sample\n23-1-12345',
        ...frontApplicant,
    }).firstNameMatched, false);
});

test('student ID OCR tolerates common character confusion but not partial IDs', () => {
    assert.equal(screenStudentIdFront({
        ocrText: 'Taylor Sample\nStudent ID: 23-I-I2345',
        ...frontApplicant,
    }).studentIdMatched, true);

    assert.equal(screenStudentIdFront({
        ocrText: 'Taylor Sample\nStudent ID: 23-1-1234',
        ...frontApplicant,
    }).passed, false);
    assert.equal(screenStudentIdFront({
        ocrText: 'Taylor Sample\nBSBA 23-1-12345',
        ...frontApplicant,
    }).studentIdMatched, true);
});

test('four-digit enrollment sequence student IDs are accepted without accepting partial IDs', () => {
    const applicant = {
        firstName: 'Taylor',
        middleName: '',
        lastName: 'Sample',
        studentId: '25-1-0000',
    };
    assert.equal(screenStudentIdFront({
        ocrText: 'Taylor Sample\n25-1-0000',
        ...applicant,
    }).passed, true);
    assert.equal(screenStudentIdFront({
        ocrText: 'Taylor Sample\n25-1-000',
        ...applicant,
    }).studentIdMatched, false);
    assert.equal(screenStudentIdFront({
        ocrText: 'Taylor Sample\n25-1-00000',
        ...applicant,
    }).studentIdMatched, false);
});

test('back screening depends only on the matching labeled DOB, not any ID-looking number', () => {
    const matchingDateOfBirthTexts = [
        'DOB: April 5, 2005\nPlace of Birth: Sample City',
        'Student ID: 98-7-65432\nDOB: April 5, 2005\nPlace of Birth: Sample City',
        'Student ID: 98-7-65431\nDOB: April 5, 2005\nPlace of Birth: Sample City',
        'Registry 98-7-65432\nOther ID: 00-0-00000\nDOB: April 5, 2005\nPlace of Birth: Sample City',
    ];
    for (const ocrText of matchingDateOfBirthTexts) {
        assert.deepEqual(
            screenStudentIdBack({ ocrText, dateOfBirth: '2005-04-05' }),
            { dateOfBirthMatched: true, passed: true },
            ocrText,
        );
    }

    for (const ocrText of [
        'ID 98-7-65432\nDate of Birth: April 6, 2005\nPlace of Birth: Sample City',
        'Academic Year: 2005-04-05\nPlace of Birth: Sample City\nID 98-7-65432',
        'Student ID: 98-7-65432\nPlace of Birth: Sample City',
    ]) {
        assert.equal(
            screenStudentIdBack({ ocrText, dateOfBirth: '2005-04-05' }).passed,
            false,
            ocrText,
        );
    }
});

test('back screening accepts the date-of-birth label and separate date line shown on the supplied card', () => {
    const ocrText = 'Date of Birth\nNOVEMBER 4, 2004\nPlace of Birth\nMANILA';
    assert.deepEqual(screenStudentIdBack({
        ocrText,
        dateOfBirth: '2004-11-04',
    }), {
        dateOfBirthMatched: true,
        passed: true,
    });
});

test('back DOB matching preserves positioned labeled-DOB placement rules', () => {
    const word = (text, x0, y0, x1, y1) => ({ text, bbox: { x0, y0, x1, y1 } });
    const result = screenStudentIdBack({
        ocrResults: [{
            text: 'DOB April 5 2005 Place of Birth Sample City 98-7-65432',
            words: [
                word('DOB', 10, 10, 35, 20),
                word('April', 42, 10, 68, 20),
                word('5,', 72, 10, 79, 20),
                word('2005', 83, 10, 110, 20),
                word('Place', 10, 35, 38, 45),
                word('of', 42, 35, 51, 45),
                word('Birth', 55, 35, 82, 45),
                word('98-7-65432', 155, 75, 215, 85),
            ],
            width: 300,
            height: 100,
        }],
        dateOfBirth: '2005-04-05',
    });
    assert.equal(result.dateOfBirthMatched, true);
    assert.equal(result.passed, true);
});

test('back screening checks date placement relative to Place of Birth when OCR words are positioned', () => {
    const word = (text, x0, y0, x1, y1) => ({ text, bbox: { x0, y0, x1, y1 } });
    const valid = screenStudentIdBack({
        ocrResults: [{
            text: '98-7-65432 DOB April 5 2005 Place of Birth Sample City',
            words: [
                word('98-7-65432', 150, 5, 210, 15),
                word('DOB', 10, 10, 35, 20),
                word('April', 42, 10, 68, 20),
                word('5,', 72, 10, 79, 20),
                word('2005', 83, 10, 110, 20),
                word('Place', 10, 35, 38, 45),
                word('of', 42, 35, 51, 45),
                word('Birth', 55, 35, 82, 45),
            ],
            width: 300,
            height: 100,
        }],
        dateOfBirth: '2005-04-05',
    });
    assert.equal(valid.passed, true);

    const belowPlace = screenStudentIdBack({
        ocrResults: [{
            text: '98-7-65432 DOB Place of Birth April 5 2005',
            words: [
                word('98-7-65432', 150, 5, 210, 15),
                word('DOB', 10, 10, 35, 20),
                word('Place', 10, 25, 38, 35),
                word('of', 42, 25, 51, 35),
                word('Birth', 55, 25, 82, 35),
                word('April', 10, 45, 36, 55),
                word('5,', 40, 45, 47, 55),
                word('2005', 51, 45, 78, 55),
            ],
            width: 300,
            height: 100,
        }],
        dateOfBirth: '2005-04-05',
    });
    assert.equal(belowPlace.passed, false);
});
