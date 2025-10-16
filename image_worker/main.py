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
async def smooth(image: UploadFile = File(...), level: int = Form(...)) :
    contents = await image.read()  # Mendeteksi image yang diupload
    nparr = np.frombuffer(contents, np.uint8) # Mengubah ke array numpy
    img = cv2.imdecode(nparr, cv2.IMREAD_COLOR) # Mendeteksi gambar yang sudah berbentuk array numpy
    # Error handling jika yang diuopload bukan gambar
    if img is None :
        return Response(b'Invalid image', status_code = 400, media_type = "text/plain")

    # Mengecek nilai kernel ganjil dan >= 1
    kernel = int(level)
    if kernel < 1 :
        kernel = 1
    if kernel % 2 == 0 :
        kernel += 1

    smoothed = cv2.GaussianBlur(img, (kernel, kernel), 0) # Melakukan smoothing
    _, buffer = cv2.imencode(".png", smoothed) # Mengubah gambar ke format PNG
    return Response(buffer.tobytes(), media_type = "image/png")

# Fungsi untuk memastikan bilangan ganjil
def ensure_odd(n):
    n = int(n)
    return n if n % 2 == 1 else n+1
