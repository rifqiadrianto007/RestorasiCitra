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
