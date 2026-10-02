import { matchesStudentIdDateOfBirth } from './student-id-date.js';

const nameTokens = value => String(value || '')
    .toLocaleLowerCase()
    .normalize('NFKD')
    .replace(/[\u0300-\u036f]/g, '')
    .match(/[a-z]+/g) || [];
const textTokens = value => String(value || '').toLocaleLowerCase()
    .normalize('NFKD')
    .replace(/[\u0300-\u036f]/g, '')
    .match(/[a-z]+/g) || [];

const ocrNameTokenMatches = (expected, actual) => {
    if (expected === actual) return true;
    if (expected.length < 4 || expected.length !== actual.length) return false;

    let differences = 0;
    for (let index = 0; index < expected.length; index += 1) {
        if (expected[index] === actual[index]) continue;
        if (![['i', 'l'], ['o', 'q']].some(pair =>
            pair.includes(expected[index]) && pair.includes(actual[index]))) return false;
        differences += 1;
        if (differences > 1) return false;
    }
    return differences === 1;
};

const ocrGivenNameTokenMatches = (expected, actual) =>
    ocrNameTokenMatches(expected, actual)
    || (
        expected.length >= 4
        && actual.length === expected.length + 1
        && actual.startsWith(expected)
    );

const nameMatches = (expected, ocrText) => {
    const expectedTokens = nameTokens(expected);
    const recognizedTokens = textTokens(ocrText);
    if (expectedTokens.length === 0 || expectedTokens.some(token => token.length < 2)) return false;
    return expectedTokens.every(token => recognizedTokens.some(recognized =>
        ocrGivenNameTokenMatches(token, recognized)));
};

const middleNameMatches = (expected, ocrText) => {
    const expectedTokens = nameTokens(expected);
    if (expectedTokens.length === 0) return true;
    const recognizedTokens = textTokens(ocrText);
    return expectedTokens.every(expectedToken => recognizedTokens.some(token => {
        if (expectedToken.length === 1) return token === expectedToken;

        return ocrGivenNameTokenMatches(expectedToken, token);
    }));
};

const idDigitPattern = digit => ({
    0: '[0oOqQ]',
    1: '[1iIlL|]',
}[digit] || digit);

const studentIdMatches = (expected, ocrText) => {
    if (typeof expected !== 'string' || !/^\d{2}-\d-\d{4,5}$/.test(expected)) return false;

    const pattern = expected
        .replace(/-/g, '')
        .split('')
        .map((digit, index, digits) => `${idDigitPattern(digit)}${index < digits.length - 1 ? '[\\s-]*' : ''}`)
        .join('');
    const matcher = new RegExp(`(?:^|[^0-9])${pattern}(?:$|[^0-9])`, 'im');

    return String(ocrText || '').split(/\r?\n/).some(line => matcher.test(line));
};

const getSources = ({ ocrText, ocrWords, ocrResults }) => {
    if (Array.isArray(ocrResults) && ocrResults.length) {
        return ocrResults.map(result => ({
            text: typeof result?.text === 'string' ? result.text : '',
            words: result?.words,
        }));
    }
    return [{
        text: typeof ocrText === 'string' ? ocrText : '',
        words: ocrWords,
    }];
};

export function screenStudentIdFront({ ocrText, ocrWords, ocrResults, firstName, middleName, lastName, studentId }) {
    const sources = getSources({ ocrText, ocrWords, ocrResults });
    const allText = sources.map(source => source.text).join('\n');
    const firstNameMatched = nameMatches(firstName, allText);
    const middleNameMatched = middleNameMatches(middleName, allText);
    const lastNameMatched = nameMatches(lastName, allText);
    const studentIdMatched = sources.some(source => studentIdMatches(studentId, source.text));

    return {
        firstNameMatched,
        middleNameMatched,
        lastNameMatched,
        studentIdMatched,
        passed: firstNameMatched && middleNameMatched && lastNameMatched && studentIdMatched,
    };
}

export function screenStudentIdBack({ ocrText, ocrWords, ocrResults, dateOfBirth }) {
    const sources = getSources({ ocrText, ocrWords, ocrResults });
    const dateOfBirthMatched = sources.some(source =>
        matchesStudentIdDateOfBirth(source.text, dateOfBirth, source.words));

    return {
        dateOfBirthMatched,
        passed: dateOfBirthMatched,
    };
}
