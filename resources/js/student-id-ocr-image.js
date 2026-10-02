const MAX_OCR_EDGE = 3200;
const MAX_OCR_PIXELS = 8_000_000;
const MAX_UPSCALE = 3.5;

export function getStudentIdOcrDimensions(sourceWidth, sourceHeight) {
    if (!Number.isFinite(sourceWidth) || !Number.isFinite(sourceHeight) || sourceWidth <= 0 || sourceHeight <= 0) {
        throw new RangeError('Image dimensions must be positive finite numbers.');
    }

    const scale = Math.min(
        MAX_UPSCALE,
        MAX_OCR_EDGE / Math.max(sourceWidth, sourceHeight),
        Math.sqrt(MAX_OCR_PIXELS / (sourceWidth * sourceHeight)),
    );

    return {
        width: Math.max(1, Math.round(sourceWidth * scale)),
        height: Math.max(1, Math.round(sourceHeight * scale)),
    };
}
