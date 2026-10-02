import { createWorker, PSM } from 'tesseract.js';
import {
    matchesStudentIdDateOfBirth,
    validateRegistrationDob,
} from './student-id-date.js';
import { screenStudentIdBack, screenStudentIdFront } from './student-id-screening.js';
import { getStudentIdOcrDimensions } from './student-id-ocr-image.js';
import workerPath from 'tesseract.js/dist/worker.min.js?url';
import corePath from 'tesseract.js-core/tesseract-core.wasm.js?url';
import languageDataPath from '@tesseract.js-data/eng/4.0.0/eng.traineddata.gz?url';

const languagePath = new URL('.', new URL(languageDataPath, window.location.href)).href;
let workerPromise = null;
let workerInstance = null;
let progressHandler = () => {};
let cleanupPromise = null;
let recognitionQueue = Promise.resolve();

const getWorker = () => {
    if (!workerPromise) {
        workerPromise = createWorker('eng', 1, {
            workerPath,
            corePath,
            langPath: languagePath,
            gzip: true,
            workerBlobURL: false,
            cacheMethod: 'none',
            logger: progress => progressHandler(progress),
        }).then(worker => {
            workerInstance = worker;
            return worker;
        }).catch(error => {
            workerPromise = null;
            throw error;
        });
    }

    return workerPromise;
};

const recognize = (image, pageSegmentationMode = PSM.AUTO, characterWhitelist = '') => {
    const recognition = recognitionQueue.then(async () => {
        if (cleanupPromise) {
            await cleanupPromise;
            cleanupPromise = null;
        }

        const worker = await getWorker();
        try {
            await worker.setParameters({
                tessedit_pageseg_mode: pageSegmentationMode,
                tessedit_char_whitelist: characterWhitelist,
            });
            const result = await worker.recognize(image);

            return {
                text: result.data.text || '',
                words: (result.data.words || []).map(word => ({
                    text: word.text || '',
                    bbox: word.bbox,
                })),
                width: image.width,
                height: image.height,
            };
        } catch (error) {
            try {
                await cleanup();
            } catch (cleanupError) {
                throw new AggregateError(
                    [error, cleanupError],
                    'Local OCR failed and its worker could not be stopped.'
                );
            }
            throw error;
        }
    });
    recognitionQueue = recognition.catch(() => {});

    return recognition;
};

const cleanup = () => {
    if (!cleanupPromise) {
        cleanupPromise = (async () => {
            const worker = workerInstance || (workerPromise ? await workerPromise : null);
            workerInstance = null;
            workerPromise = null;
            if (worker) await worker.terminate();
        })().finally(() => {
            cleanupPromise = null;
        });
    }

    return cleanupPromise;
};

window.localStudentIdOcr = recognize;
window.matchesStudentIdDateOfBirth = matchesStudentIdDateOfBirth;
window.validateRegistrationDob = validateRegistrationDob;
window.screenStudentIdFront = screenStudentIdFront;
window.screenStudentIdBack = screenStudentIdBack;
window.getStudentIdOcrDimensions = getStudentIdOcrDimensions;
window.setLocalStudentIdOcrProgressHandler = handler => {
    progressHandler = typeof handler === 'function' ? handler : () => {};
};
window.cleanupLocalStudentIdOcr = cleanup;

window.addEventListener('pagehide', () => {
    cleanup().catch(error => console.error('Unable to stop local ID OCR worker.', error));
}, { once: true });
