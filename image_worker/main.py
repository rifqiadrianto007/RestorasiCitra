from fastapi import FastAPI, File, UploadFile, Form
from fastapi.responses import Response
import cv2
import numpy as np

app = FastAPI()

@app.post("/smooth")
async def smooth(image: UploadFile = File(...), level: int = Form(...)):
    # Baca file dari upload
    contents = await image.read()
    nparr = np.frombuffer(contents, np.uint8)
    img = cv2.imdecode(nparr, cv2.IMREAD_COLOR)

    # Kernel size harus ganjil
    kernel = level if level % 2 == 1 else level + 1
    smoothed = cv2.GaussianBlur(img, (kernel, kernel), 0)

    # Encode ke PNG dan return
    _, buffer = cv2.imencode(".png", smoothed)
    return Response(buffer.tobytes(), media_type="image/png")
