/**
 * USB Barcode Scanner Detector for POS.
 * PRD Section 19: detects rapid scanner keystrokes ending with Enter.
 */
export function initBarcodeScanner(onBarcodeScanned) {
    let buffer = '';
    let lastKeyTime = Date.now();

    window.addEventListener('keydown', (e) => {
        // Ignore if user is typing in standard text inputs (unless target is the barcode scan input itself)
        const activeElement = document.activeElement;
        const isInputField = activeElement && (activeElement.tagName === 'INPUT' || activeElement.tagName === 'TEXTAREA');

        // Allow dedicated scanner input or general background scanning
        if (isInputField && activeElement.id !== 'pos-barcode-input') {
            return;
        }

        const currentTime = Date.now();
        const timeDiff = currentTime - lastKeyTime;
        lastKeyTime = currentTime;

        if (e.key === 'Enter') {
            if (buffer.length >= 3) {
                e.preventDefault();
                onBarcodeScanned(buffer.trim());
            }
            buffer = '';
            return;
        }

        // Only buffer printable characters
        if (e.key.length === 1) {
            // Scanner keys arrive very rapidly (< 50ms)
            if (timeDiff > 100 && buffer.length > 0 && !isInputField) {
                buffer = ''; // Reset if too slow (human typing)
            }
            buffer += e.key;
        }
    });
}
