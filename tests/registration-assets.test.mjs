import assert from 'node:assert/strict';
import { existsSync, readFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import test from 'node:test';

const root = process.cwd();
const manifest = JSON.parse(readFileSync(resolve(root, 'public/build/manifest.json'), 'utf8'));
const buildRoot = resolve(root, 'public/build');

test('registration bundle exposes local OCR and emitted worker assets', () => {
    const registration = manifest['resources/js/register-screening.js'];
    const app = manifest['resources/js/app.js'];

    assert.ok(registration?.isEntry, 'registration screening is a Vite entry');
    assert.ok(app?.isEntry, 'the shared app bundle is a Vite entry');
    assert.ok(existsSync(resolve(buildRoot, registration.file)), 'registration bundle exists');
    assert.ok(existsSync(resolve(buildRoot, app.file)), 'app bundle exists');

    const screeningBundle = readFileSync(resolve(buildRoot, registration.file), 'utf8');
    assert.match(screeningBundle, /localStudentIdOcr/, 'bundle publishes the OCR bridge');
    const screeningSource = readFileSync(resolve(root, 'resources/js/register-screening.js'), 'utf8');
    assert.match(screeningSource, /tessedit_pageseg_mode: pageSegmentationMode/);
    assert.match(screeningSource, /tessedit_char_whitelist: characterWhitelist/);
    assert.match(screeningSource, /let recognitionQueue = Promise\.resolve\(\)/);
    assert.match(screeningSource, /result\.data\.words/, 'the OCR bridge exposes local word positions for screening');

    const localAssets = registration.assets || [];
    assert.ok(localAssets.length >= 3, 'worker, core and language assets are attached to the Vite entry');
    for (const asset of localAssets) {
        assert.ok(asset.startsWith('assets/'), `asset is emitted under public/build: ${asset}`);
        assert.ok(existsSync(resolve(buildRoot, asset)), `local asset exists: ${asset}`);
    }

    const languageAsset = localAssets.find(asset => asset.endsWith('/tesseract/eng.traineddata.gz'));
    assert.ok(languageAsset, 'language data has the local Tesseract language path');
    assert.equal(dirname(languageAsset).replaceAll('\\', '/'), 'assets/tesseract');
});

test('registration form uses semantic headings and explains screening limits', () => {
    const view = readFileSync(resolve(root, 'resources/views/auth/register.blade.php'), 'utf8');
    const adminCreateView = readFileSync(resolve(root, 'resources/views/admin/users/create.blade.php'), 'utf8');
    const adminEditView = readFileSync(resolve(root, 'resources/views/admin/users/edit.blade.php'), 'utf8');
    const screeningSource = readFileSync(resolve(root, 'resources/js/register-screening.js'), 'utf8');
    for (const heading of ['About you', 'Proof of student ID', 'Secure your account']) {
        assert.match(view, new RegExp(`<h2 class="section-label[^"]*">${heading}<\\/h2>`));
    }
    assert.match(view, /<p class="proof-warning">Required for registration: upload clear front and back images of your student ID\. The System may miss or misread text\. Blurry text may not be recognized\.<\/p>/);
    assert.doesNotMatch(view, /<p class="small text-muted">Your images are saved securely in private storage and can be viewed by you, admins, and the guidance associate assigned to your appointment\. Guidance associates cannot view images for high-severity cases\.<\/p>/);
    assert.match(view, /<p class="proof-intro">Upload a sharp, full-resolution photo with the printed details in focus\.<\/p>/);
    assert.doesNotMatch(view, /The local scanner enlarges small images before OCR|enlargement cannot restore details missing/);
    assert.doesNotMatch(view, /JPG, PNG, or WEBP up to 5 MB each\. Front: OCR checks/);
    assert.doesNotMatch(view, /Back: OCR checks the labeled date of birth above “Place of Birth”/);
    assert.match(view, /Blurry text may not be recognized/);
    assert.match(view, /<input type="date"[^>]*id="date_of_birth" name="date_of_birth"/);
    assert.doesNotMatch(view, /Choose your date of birth\. You must be 12–100 years old\./);
    assert.match(view, /Required student ID screening passed\. You may continue with registration\./);
    assert.match(view, /Registration cannot continue until both student ID images pass the required screening\./);
    assert.doesNotMatch(view, /These checks can be bypassed|Local OCR screening passed/);
    assert.match(view, /const ready = results\.front && results\.back;/);
    assert.match(view, /passed\.value = ready \? '1' : '0';/);
    assert.match(view, /submit\.disabled = !ready;/);
    assert.match(view, /setStatus\(statusId, 'Checking\.\.\.', 'scanning'\)/);
    assert.match(view, /setChecks\(side, 'Checking\.\.\.'\)/);
    assert.match(view, /if \(generation === generations\[side\] && progress\.status\) setChecks\(side, 'Checking\.\.\.'/);
    assert.doesNotMatch(view, /First name: scanning|middle initial: (?:scanning|not entered \(optional\))|last name: scanning|Labeled DOB above Place of Birth: scanning/);
    for (const status of [
        'Student ID (Front): PASSED',
        'Student ID (Front): FAILED',
        'Student ID (Back): PASSED',
        'Student ID (Back): FAILED',
    ]) {
        assert.ok(view.includes(status), `registration status is present: ${status}`);
    }
    assert.doesNotMatch(view, /Front-side screening checks only your name and student ID|School or program text such as BSBA does not count as a match/);
    assert.match(view, /makeOcrCanvas\(image, 1\.25, false, 0\.62\)/);
    assert.match(view, /localOcr\(details, '11'\)/);
    assert.match(view, /makeOcrCanvas\(image, 1\.25, false, 0\.6, 0\.08\)/);
    assert.match(view, /localOcr\(printedDetails, '6'\)/);
    assert.match(view, /makeSurnameOcrCanvas\(image, rotation\)/);
    assert.match(view, /localOcr\(surname, '7', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'\)/);
    assert.match(view, /pattern="\^\\d\{2\}-\\d-\\d\{4,5\}\$"/);
    assert.match(adminEditView, /pattern="\^\\d\{2\}-\\d-\\d\{4,5\}\$"/);
    assert.match(adminEditView, /YY-S-NNNN or YY-S-NNNNN/);
    assert.match(view, /setChecks\(side, 'Checked'\)/);
    assert.doesNotMatch(view, /Name: \$\{screen\.firstNameMatched|Date of birth: not matched/);
    assert.match(view, /studentId: studentIdInput\.value\.trim\(\)/);
    assert.match(view, /\[\.\.\.nameInputs, studentIdInput\]\.forEach/);
    assert.doesNotMatch(view, /First name: passed|last name: passed|middle initial:.*passed/i);
    assert.doesNotMatch(view, /Front name checks and the back labeled DOB check passed/);
    assert.doesNotMatch(view, /MM\/DD\/YYYY|Student ID beneath BSIS/);
    assert.doesNotMatch(view, /id="date_of_birth_display"|name="date_of_birth_display"/);
    assert.doesNotMatch(adminCreateView, /Student ID|student_id|Format: YY-N-NNNNN/);
    assert.doesNotMatch(adminCreateView, /Select School \(if student\)/);
    assert.match(adminCreateView, /<option value="">Select School \(for Guidance Associate\)<\/option>/);
});
