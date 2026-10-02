const MONTHS = {
    jan: 1, january: 1,
    feb: 2, february: 2,
    mar: 3, march: 3,
    apr: 4, april: 4,
    may: 5,
    jun: 6, june: 6,
    jul: 7, july: 7,
    aug: 8, august: 8,
    sep: 9, sept: 9, september: 9,
    oct: 10, october: 10,
    nov: 11, november: 11,
    dec: 12, december: 12,
};

const DATE_SEPARATOR = String.raw`[\s./-]+`;
const MONTH_PATTERN = Object.keys(MONTHS)
    .sort((a, b) => b.length - a.length)
    .join('|');
const MONTH_FIRST = new RegExp(
    String.raw`(?:^|[^a-z0-9])(${MONTH_PATTERN})\.?${DATE_SEPARATOR}(\d{1,2})(?:st|nd|rd|th)?(?:,)?${DATE_SEPARATOR}(\d{4})(?!\d)`,
    'gi',
);
const DAY_FIRST = new RegExp(
    String.raw`(?:^|[^a-z0-9])(\d{1,2})(?:st|nd|rd|th)?${DATE_SEPARATOR}(${MONTH_PATTERN})\.?(?:,)?${DATE_SEPARATOR}(\d{4})(?!\d)`,
    'gi',
);
const NUMERIC_DATE = /(?:^|[^a-z0-9])([0-9OQIL|]{1,4})\s*[./-]\s*([0-9OQIL|]{1,2})\s*[./-]\s*([0-9OQIL|]{2,4})(?![a-z0-9])/gi;
const DOB_LABEL = /\b(?:d\s*[.\/-]?\s*[o0]\s*[.\/-]?\s*b|date\s+of\s+(?:birth|bath)|date\s+0f\s+birth|birth\s+date)\b/i;
const PLACE_OF_BIRTH = /\bplace\s+of\s+birth\b/i;

const validIsoDate = isoDate => {
    const match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(isoDate || '');
    if (!match) return null;
    const [, yearText, monthText, dayText] = match;
    const year = Number(yearText);
    const month = Number(monthText);
    const day = Number(dayText);
    const date = new Date(Date.UTC(year, month - 1, day));

    if (date.getUTCFullYear() !== year || date.getUTCMonth() !== month - 1 || date.getUTCDate() !== day) {
        return null;
    }

    return { year, month, day };
};

export function validateRegistrationDob(value, todayIso) {
    const isoDate = typeof value === 'string' ? value.trim() : '';
    if (!isoDate) {
        return { isoDate: '', message: 'Enter your date of birth.' };
    }
    const dob = validIsoDate(isoDate);
    if (!dob) return { isoDate: '', message: 'Choose a real date of birth.' };
    const today = validIsoDate(todayIso);
    if (!today) {
        return { isoDate: '', message: 'The current date could not be checked. Reload and try again.' };
    }
    if (isoDate > todayIso) {
        return { isoDate: '', message: 'Date of birth cannot be in the future.' };
    }

    let age = today.year - dob.year;
    if (today.month < dob.month || (today.month === dob.month && today.day < dob.day)) age -= 1;
    if (age < 12 || age > 100) {
        return { isoDate: '', message: 'You must be between 12 and 100 years old to register.' };
    }

    return { isoDate, message: '' };
}

const isMatch = (year, month, day, target) =>
    year === target.year && month === target.month && day === target.day;

const numericValue = value => Number(value.replace(/[OQ]/gi, '0').replace(/[IL|]/gi, '1'));

const dateMatchesTarget = (text, target) => {
    NUMERIC_DATE.lastIndex = 0;
    for (const match of text.matchAll(NUMERIC_DATE)) {
        const [, firstText, secondText, thirdText] = match;
        const first = numericValue(firstText);
        const second = numericValue(secondText);
        const third = numericValue(thirdText);
        const firstLength = firstText.length;
        const thirdLength = thirdText.length;

        if (firstLength === 4 && isMatch(first, second, third, target)) return true;
        if (thirdLength === 4 && (
            isMatch(third, first, second, target)
            || isMatch(third, second, first, target)
        )) return true;
        if (thirdLength === 2 && third === target.year % 100 && (
            (first === target.month && second === target.day)
            || (second === target.month && first === target.day)
        )) return true;
    }

    MONTH_FIRST.lastIndex = 0;
    for (const match of text.matchAll(MONTH_FIRST)) {
        const [, monthName, day, year] = match;
        if (isMatch(Number(year), MONTHS[monthName.toLowerCase()], Number(day), target)) return true;
    }

    DAY_FIRST.lastIndex = 0;
    for (const match of text.matchAll(DAY_FIRST)) {
        const [, day, monthName, year] = match;
        if (isMatch(Number(year), MONTHS[monthName.toLowerCase()], Number(day), target)) return true;
    }

    return false;
};

const textLines = text => String(text || '')
    .split(/\r?\n/)
    .map(line => line.trim())
    .filter(Boolean)
    .map(text => ({ text }));

const wordLines = words => {
    const usableWords = (Array.isArray(words) ? words : [])
        .filter(word => word?.text && Number.isFinite(word?.bbox?.y0) && Number.isFinite(word?.bbox?.y1))
        .map(word => ({
            text: String(word.text),
            x: Number.isFinite(word.bbox.x0) ? word.bbox.x0 : 0,
            top: word.bbox.y0,
            bottom: word.bbox.y1,
        }))
        .sort((a, b) => a.top - b.top || a.x - b.x);
    const lines = [];

    for (const word of usableWords) {
        const height = Math.max(1, word.bottom - word.top);
        const existing = lines.find(line => Math.abs(line.center - (word.top + word.bottom) / 2) <= Math.max(4, height * 0.65));
        if (existing) {
            existing.words.push(word);
            existing.top = Math.min(existing.top, word.top);
            existing.bottom = Math.max(existing.bottom, word.bottom);
            existing.center = (existing.top + existing.bottom) / 2;
        } else {
            lines.push({
                words: [word],
                top: word.top,
                bottom: word.bottom,
                center: (word.top + word.bottom) / 2,
            });
        }
    }

    return lines
        .sort((a, b) => a.top - b.top)
        .map(line => ({
            text: line.words.sort((a, b) => a.x - b.x).map(word => word.text).join(' '),
            top: line.top,
            bottom: line.bottom,
        }));
};

const sourceLines = (text, words) => {
    const linesWithWords = wordLines(words);
    return linesWithWords.length ? linesWithWords : textLines(text);
};

const matchesDobLines = (lines, target) => {
    for (let index = 0; index < lines.length; index += 1) {
        const dobMatch = DOB_LABEL.exec(lines[index].text);
        if (!dobMatch) continue;
        if (lines.slice(0, index).some(line => PLACE_OF_BIRTH.test(line.text))) continue;

        const placeMatch = PLACE_OF_BIRTH.exec(lines[index].text);
        const sameLineEnd = placeMatch ? placeMatch.index : lines[index].text.length;
        const sameLineText = lines[index].text.slice(dobMatch.index + dobMatch[0].length, sameLineEnd);
        if (dateMatchesTarget(sameLineText, target)) return true;

        const nextLine = lines[index + 1];
        if (!nextLine || PLACE_OF_BIRTH.test(lines[index].text) || PLACE_OF_BIRTH.test(nextLine.text)) continue;
        if (dateMatchesTarget(nextLine.text, target)) return true;
    }

    return false;
};

export function matchesStudentIdDateOfBirth(ocrText, isoDate, ocrWords = null) {
    const target = validIsoDate(isoDate);
    if (!target || (typeof ocrText !== 'string' && !Array.isArray(ocrWords))) return false;

    return matchesDobLines(sourceLines(ocrText, ocrWords), target);
}
