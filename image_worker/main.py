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

@app.post("/remove-background")
async def remove_background(image: UploadFile = File(...)):
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
