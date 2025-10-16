from fastapi import FastAPI, File, UploadFile, Form
from fastapi.responses import Response
from fastapi.middleware.cors import CORSMiddleware
import cv2
import numpy as np

app = FastAPI()

# Biar Laravel bisa panggil (jika beda host)
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_methods=["POST", "GET", "OPTIONS"],
    allow_headers=["*"],
)

@app.post("/smooth")
async def smooth(image: UploadFile = File(...), level: int = Form(...)):
    contents = await image.read()
    nparr = np.frombuffer(contents, np.uint8)
    img = cv2.imdecode(nparr, cv2.IMREAD_COLOR)
    if img is None:
        return Response(b'Invalid image', status_code=400, media_type="text/plain")

    # Kernel ganjil minimal 1
    kernel = int(level)
    if kernel < 1:
        kernel = 1
    if kernel % 2 == 0:
        kernel += 1

    smoothed = cv2.GaussianBlur(img, (kernel, kernel), 0)
    _, buffer = cv2.imencode(".png", smoothed)
    return Response(buffer.tobytes(), media_type="image/png")

def ensure_odd(n):
    n = int(n)
    return n if n % 2 == 1 else n+1

@app.post("/remove-background")
async def remove_background(image: UploadFile = File(...)):
    contents = await image.read()
    nparr = np.frombuffer(contents, np.uint8)

    # Baca dengan unchanged supaya alpha jika ada tetap terbaca
    img = cv2.imdecode(nparr, cv2.IMREAD_UNCHANGED)
    if img is None:
        return Response(b'Invalid image', status_code=400, media_type="text/plain")

    # Jika gambar memiliki alpha, hapus dulu alpha untuk proses
    if img.shape[2] == 4:
        bgr = img[:, :, :3].copy()
    else:
        bgr = img

    h0, w0 = bgr.shape[:2]

    # Resize pekerjaan untuk kestabilan (jika terlalu besar), simpan scale
    max_dim = 1000
    scale = 1.0
    if max(h0, w0) > max_dim:
        scale = max_dim / max(h0, w0)
        new_w = int(w0 * scale)
        new_h = int(h0 * scale)
        work = cv2.resize(bgr, (new_w, new_h), interpolation=cv2.INTER_AREA)
    else:
        work = bgr.copy()

    # Pra-pemrosesan: bilateral filter untuk menjaga tepi dan meredam noise
    work_blur = cv2.bilateralFilter(work, d=9, sigmaColor=75, sigmaSpace=75)

    # Konversi ke grayscale & deteksi tepi untuk mengestimasi area objek
    gray = cv2.cvtColor(work_blur, cv2.COLOR_BGR2GRAY)
    edges = cv2.Canny(gray, 50, 150)
    # Perbesar area tepi jadi mask kasar
    kernel = cv2.getStructuringElement(cv2.MORPH_ELLIPSE, (15, 15))
    edges_dil = cv2.dilate(edges, kernel, iterations=2)

    # Fill holes dan cari kontur terbesar (diasumsikan objek utama)
    contours, _ = cv2.findContours(edges_dil, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)
    if len(contours) == 0:
        # fallback: gunakan Otsu threshold jika tidak ada kontur
        _, mask_otsu = cv2.threshold(gray, 0, 255, cv2.THRESH_BINARY + cv2.THRESH_OTSU)
        mask_init = cv2.bitwise_not(mask_otsu)
        # convert mask to bounding rect
        x,y,w,h = 0,0,work.shape[1],work.shape[0]
    else:
        # ambil kontur terbesar berdasarkan area
        c = max(contours, key=cv2.contourArea)
        x,y,w,h = cv2.boundingRect(c)

        # jika bounding box mengecil (misal noise), gunakan full image
        min_area_threshold = 0.01 * (work.shape[0] * work.shape[1])
        if cv2.contourArea(c) < min_area_threshold:
            x,y,w,h = 0,0,work.shape[1],work.shape[0]

    # Expand rect sedikit supaya GrabCut punya margin
    pad = int(0.05 * max(work.shape[0], work.shape[1]))
    rx = max(0, x - pad)
    ry = max(0, y - pad)
    rw = min(work.shape[1] - rx, w + pad*2)
    rh = min(work.shape[0] - ry, h + pad*2)
    rect = (rx, ry, rw, rh)

    # Inisialisasi mask untuk grabCut
    mask_gc = np.zeros(work.shape[:2], np.uint8)
    bgdModel = np.zeros((1,65), np.float64)
    fgdModel = np.zeros((1,65), np.float64)

    # Jalankan GrabCut dengan rect inisialisasi
    try:
        cv2.grabCut(work, mask_gc, rect, bgdModel, fgdModel, 5, cv2.GC_INIT_WITH_RECT)
    except Exception:
        # fallback simple: threshold Otsu inverted
        _, mask_otsu = cv2.threshold(gray, 0, 255, cv2.THRESH_BINARY + cv2.THRESH_OTSU)
        mask_gc = cv2.bitwise_not(mask_otsu)
        mask_gc[mask_gc > 0] = cv2.GC_FGD

    # Buat final mask: pixel yang terdeteksi foreground (sure+probable)
    mask2 = np.where((mask_gc==cv2.GC_FGD) | (mask_gc==cv2.GC_PR_FGD), 255, 0).astype('uint8')

    # Morphological ops untuk memperhalus mask
    kernel2 = cv2.getStructuringElement(cv2.MORPH_ELLIPSE, (7,7))
    mask2 = cv2.morphologyEx(mask2, cv2.MORPH_CLOSE, kernel2, iterations=2)
    mask2 = cv2.medianBlur(mask2, 5)

    # Resize mask kembali ke ukuran asli jika sempat di-resize
    if scale != 1.0:
        mask_full = cv2.resize(mask2, (w0, h0), interpolation=cv2.INTER_LINEAR)
    else:
        mask_full = mask2

    # Feather / blur alpha untuk tepi halus
    alpha = mask_full.astype(float) / 255.0
    # kernel untuk feathering
    alpha = cv2.GaussianBlur(alpha, (15,15), 0)
    alpha = (alpha*255).astype(np.uint8)

    # Pastikan gambar asal dalam ukuran asli
    if img.shape[2] == 4:
        rgb_orig = img[:, :, :3]
    else:
        rgb_orig = img

    # Gabungkan RGB dan alpha (RGBA)
    bgr_orig = rgb_orig
    rgba = cv2.cvtColor(bgr_orig, cv2.COLOR_BGR2BGRA)
    rgba[:, :, 3] = alpha

    # Optional: trim small fully-transparent border (tidak wajib)

    # Encode ke PNG dan return
    _, buffer = cv2.imencode(".png", rgba)
    return Response(buffer.tobytes(), media_type="image/png")

    contents = await image.read()
    nparr = np.frombuffer(contents, np.uint8)
    img = cv2.imdecode(nparr, cv2.IMREAD_UNCHANGED)

    if img is None:
        return Response(b'Invalid image', status_code=400, media_type="text/plain")

    # Ubah ke grayscale
    gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)

    # Gunakan threshold adaptif sederhana
    _, mask = cv2.threshold(gray, 0, 255, cv2.THRESH_BINARY + cv2.THRESH_OTSU)

    # Pastikan objek utama tetap (jika terbalik)
    if np.mean(img[mask == 255]) > np.mean(img[mask == 0]):
        mask = cv2.bitwise_not(mask)

    # Sedikit perbaikan mask
    mask = cv2.medianBlur(mask, 5)

    # Buat alpha channel
    alpha = mask

    # Gabungkan RGB + Alpha
    rgba = cv2.cvtColor(img, cv2.COLOR_BGR2BGRA)
    rgba[:, :, 3] = alpha

    # Simpan hasil
    _, buffer = cv2.imencode(".png", rgba)
    return Response(buffer.tobytes(), media_type="image/png")
