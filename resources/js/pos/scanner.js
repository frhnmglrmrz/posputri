/**
 * Comprehensive Barcode Scanner Module for Berkah Mart POS.
 * Supports:
 * 1. External USB / Bluetooth hardware scanners (rapid keystroke keyboard emulation).
 * 2. Device camera scanner via HTML5 getUserMedia + BarcodeDetector API.
 * 3. Accidental double-scan prevention (1.5s debounce).
 * 4. Audio feedback (Web Audio API synthetic beep, zero assets required).
 */

// Global Debounce & Audio Tracker
let lastScannedCode = '';
let lastScanTimestamp = 0;
const DOUBLE_SCAN_THRESHOLD_MS = 1500;

/**
 * Play tactile audio feedback using native Web Audio API.
 */
export function playScanSound(isSuccess = true) {
    try {
        const AudioContext = window.AudioContext || window.webkitAudioContext;
        if (!AudioContext) return;
        const ctx = new AudioContext();

        if (isSuccess) {
            // High-pitched cheerful beep (880Hz / A5)
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(880, ctx.currentTime);
            gain.gain.setValueAtTime(0.15, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.1);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start();
            osc.stop(ctx.currentTime + 0.1);
        } else {
            // Low double buzz (300Hz)
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'sawtooth';
            osc.frequency.setValueAtTime(250, ctx.currentTime);
            gain.gain.setValueAtTime(0.12, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.2);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start();
            osc.stop(ctx.currentTime + 0.2);
        }
    } catch {
        // AudioContext might be blocked until user gesture, ignore silently
    }
}

/**
 * Filter duplicate scans within threshold.
 */
export function isDuplicateScan(code) {
    const now = Date.now();
    const cleanCode = code.trim().toLowerCase();
    if (cleanCode === lastScannedCode && (now - lastScanTimestamp) < DOUBLE_SCAN_THRESHOLD_MS) {
        return true;
    }
    lastScannedCode = cleanCode;
    lastScanTimestamp = now;
    return false;
}

/**
 * USB Barcode Scanner Detector for POS.
 * Detects rapid scanner keystrokes ending with Enter key.
 */
export function initBarcodeScanner(onBarcodeScanned) {
    let buffer = '';
    let lastKeyTime = Date.now();

    window.addEventListener('keydown', (e) => {
        const activeElement = document.activeElement;
        const isInputField = activeElement && (activeElement.tagName === 'INPUT' || activeElement.tagName === 'TEXTAREA');

        // Allow dedicated scanner input or general background scanning
        if (isInputField && activeElement.id !== 'pos-barcode-input' && activeElement.id !== 'pos-search-input') {
            return;
        }

        const currentTime = Date.now();
        const timeDiff = currentTime - lastKeyTime;
        lastKeyTime = currentTime;

        if (e.key === 'Enter') {
            if (buffer.length >= 3) {
                const scannedCode = buffer.trim();
                buffer = '';

                if (isDuplicateScan(scannedCode)) {
                    console.log('Barcode scan debounced (duplicate):', scannedCode);
                    return;
                }

                e.preventDefault();
                onBarcodeScanned(scannedCode);
            }
            buffer = '';
            return;
        }

        // Only buffer printable characters
        if (e.key.length === 1) {
            // Scanner keys arrive very rapidly (< 80ms)
            if (timeDiff > 100 && buffer.length > 0 && !isInputField) {
                buffer = ''; // Reset if too slow (human typing)
            }
            buffer += e.key;
        }
    });
}

/**
 * Camera Barcode Scanner Controller.
 * Manages device media stream & BarcodeDetector loop.
 */
export class CameraBarcodeScanner {
    constructor(onScannedCallback, statusCallback = null) {
        this.onScanned = onScannedCallback;
        this.onStatus = statusCallback || (() => {});
        this.stream = null;
        this.videoElement = null;
        this.scanningInterval = null;
        this.detector = null;
        this.isSupported = typeof window.BarcodeDetector !== 'undefined';

        if (this.isSupported) {
            try {
                this.detector = new window.BarcodeDetector({
                    formats: ['ean_13', 'ean_8', 'code_128', 'code_39', 'upc_a', 'upc_e', 'qr_code'],
                });
            } catch {
                this.isSupported = false;
            }
        }
    }

    async start(videoElement) {
        this.videoElement = videoElement;

        try {
            this.onStatus('Mengaktifkan kamera perangkat...');
            this.stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: { ideal: 'environment' },
                    width: { ideal: 1280 },
                    height: { ideal: 720 },
                },
                audio: false,
            });

            this.videoElement.srcObject = this.stream;
            await this.videoElement.play();

            this.onStatus('Kamera aktif. Arahkan ke barcode produk.');

            // Start detection loop
            this.startDetectionLoop();
            return true;
        } catch (err) {
            console.error('Camera access error:', err);
            this.onStatus('Gagal mengakses kamera: ' + (err.message || 'Izin ditolak atau kamera tidak ditemukan.'));
            return false;
        }
    }

    startDetectionLoop() {
        if (!this.detector || !this.videoElement) {
            this.onStatus('Gunakan input manual atau scanner USB di bawah.');
            return;
        }

        this.scanningInterval = setInterval(async () => {
            if (!this.videoElement || this.videoElement.readyState < 2) {
                return;
            }

            try {
                const barcodes = await this.detector.detect(this.videoElement);
                if (barcodes && barcodes.length > 0) {
                    const rawValue = barcodes[0].rawValue;
                    if (rawValue && !isDuplicateScan(rawValue)) {
                        playScanSound(true);
                        this.onScanned(rawValue.trim());
                    }
                }
            } catch {
                // Ignore transient frame detection error
            }
        }, 180);
    }

    stop() {
        if (this.scanningInterval) {
            clearInterval(this.scanningInterval);
            this.scanningInterval = null;
        }

        if (this.stream) {
            this.stream.getTracks().forEach((track) => track.stop());
            this.stream = null;
        }

        if (this.videoElement) {
            this.videoElement.srcObject = null;
        }

        this.onStatus('Kamera nonaktif.');
    }
}
