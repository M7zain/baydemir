"""Extract exact window masks from lights-on vs lights-off difference."""
from __future__ import annotations

import json
from pathlib import Path

import numpy as np
from PIL import Image, ImageDraw, ImageFilter

ROOT = Path(r"e:\cevizsoft\baydemir-theme")
ON_PATH = ROOT / "assets" / "lights-on.png"
OFF_PATH = ROOT / "assets" / "lights-off.png"
OUT_ASSETS = ROOT / "assets"
OUT_TOOLS = ROOT / "tools"
OUT_JS = ROOT / "assets" / "js"

OUT_TOOLS.mkdir(exist_ok=True)
OUT_JS.mkdir(exist_ok=True)


def lum(a: np.ndarray) -> np.ndarray:
    return 0.2126 * a[..., 0] + 0.7152 * a[..., 1] + 0.0722 * a[..., 2]


def main() -> None:
    on = Image.open(ON_PATH).convert("RGB")
    off = Image.open(OFF_PATH).convert("RGB")
    assert on.size == off.size
    w, h = on.size

    on_a = np.asarray(on, dtype=np.float32)
    off_a = np.asarray(off, dtype=np.float32)
    lon, loff = lum(on_a), lum(off_a)
    diff = lon - loff
    warm = (
        (on_a[..., 0] - off_a[..., 0])
        + 0.5 * (on_a[..., 1] - off_a[..., 1])
        - 0.3 * (on_a[..., 2] - off_a[..., 2])
    )
    score = diff + 0.35 * np.clip(warm, 0, None)
    thr = max(18.0, float(np.percentile(score, 88)))
    mask = score > thr

    # Sign column from shared bright pixels
    both_bright = (lon > 160) & (loff > 140) & (np.abs(diff) < 35)
    col_sum = both_bright.sum(axis=0)
    x0b, x1b = int(w * 0.48), int(w * 0.72)
    band = col_sum.copy()
    band[:x0b] = 0
    band[x1b:] = 0
    peak = int(np.argmax(band))
    left = right = peak
    thresh_col = max(1.0, band.max() * 0.25)
    while left > x0b and band[left] > thresh_col:
        left -= 1
    while right < x1b - 1 and band[right] > thresh_col:
        right += 1
    pad = int(w * 0.016)
    left = max(0, left - pad)
    right = min(w - 1, right + pad)
    ys = np.where(both_bright[:, left : right + 1].any(axis=1))[0]
    y_top = max(0, int(ys.min()) - int(h * 0.04)) if len(ys) else int(h * 0.16)
    y_bot = min(h - 1, int(ys.max()) + int(h * 0.04)) if len(ys) else int(h * 0.78)

    # Slightly widen exclude so adjacent uplights don't flash the pillar
    exclude = np.zeros_like(mask)
    exclude[y_top : y_bot + 1, left : right + 1] = True
    mask &= ~exclude

    # Building ROI — drop sky / far-left background / wet-street reflections
    roi = np.zeros_like(mask)
    roi[int(h * 0.16) : int(h * 0.86), int(w * 0.46) : int(w * 0.93)] = True
    mask &= roi

    mimg = Image.fromarray((mask.astype(np.uint8) * 255), mode="L")
    mimg = mimg.filter(ImageFilter.MaxFilter(3))
    mimg = mimg.filter(ImageFilter.MinFilter(3))
    mask = np.asarray(mimg) > 127

    visited = np.zeros_like(mask, dtype=bool)
    windows: list[dict] = []
    dirs = [(-1, 0), (1, 0), (0, -1), (0, 1), (-1, -1), (-1, 1), (1, -1), (1, 1)]
    min_area = int(w * h * 0.00035)

    def flood(sy: int, sx: int) -> list[tuple[int, int]]:
        stack = [(sy, sx)]
        visited[sy, sx] = True
        pixels: list[tuple[int, int]] = []
        while stack:
            y, x = stack.pop()
            pixels.append((x, y))
            for dy, dx in dirs:
                ny, nx = y + dy, x + dx
                if 0 <= ny < h and 0 <= nx < w and mask[ny, nx] and not visited[ny, nx]:
                    visited[ny, nx] = True
                    stack.append((ny, nx))
        return pixels

    for y in range(h):
        for x in range(w):
            if mask[y, x] and not visited[y, x]:
                pix = flood(y, x)
                if len(pix) < min_area:
                    continue
                xs = [p[0] for p in pix]
                ys_ = [p[1] for p in pix]
                xa, xb = min(xs), max(xs)
                ya, yb = min(ys_), max(ys_)
                bw, bh = xb - xa + 1, yb - ya + 1
                if bh < 10 or bw < 8:
                    continue
                # Skip thin vertical uplights hugging the sign
                cx = (xa + xb) / 2
                if left - w * 0.02 <= cx <= right + w * 0.02 and bw < w * 0.035:
                    continue
                if exclude[ya : yb + 1, xa : xb + 1].mean() > 0.25:
                    continue
                windows.append({"pixels": pix, "bbox": [xa, ya, xb, yb], "area": len(pix)})

    windows.sort(key=lambda item: (item["bbox"][1] // 18, item["bbox"][0]))
    print(f"windows={len(windows)} sign=({left/w:.3f}-{right/w:.3f})")

    label = Image.new("RGB", (w, h))
    label.paste(on)
    draw = ImageDraw.Draw(label, "RGBA")
    win_mask = np.zeros((h, w), dtype=np.uint8)
    overlay = np.zeros((h, w, 4), dtype=np.uint8)
    js_windows = []

    for i, item in enumerate(windows):
        xa, ya, xb, yb = item["bbox"]
        local = np.zeros((yb - ya + 1, xb - xa + 1), dtype=np.uint8)
        for x, y in item["pixels"]:
            local[y - ya, x - xa] = 255
            win_mask[y, x] = 255
            overlay[y, x] = [255, 210, 70, 180]

        edge: list[tuple[int, int]] = []
        lh = local.shape[0]
        for yy in range(lh):
            xs_on = np.where(local[yy] > 0)[0]
            if len(xs_on) == 0:
                continue
            edge.append((int(xs_on[0] + xa), int(yy + ya)))
        for yy in range(lh - 1, -1, -1):
            xs_on = np.where(local[yy] > 0)[0]
            if len(xs_on) == 0:
                continue
            edge.append((int(xs_on[-1] + xa), int(yy + ya)))
        if len(edge) > 48:
            step = max(1, len(edge) // 48)
            edge = edge[::step]
        if len(edge) < 3:
            continue

        poly = [{"x": round(px / w, 5), "y": round(py / h, 5)} for px, py in edge]
        js_windows.append(
            {
                "x0": round(xa / w, 5),
                "y0": round(ya / h, 5),
                "x1": round(xb / w, 5),
                "y1": round(yb / h, 5),
                "poly": poly,
            }
        )
        draw.polygon([(int(p["x"] * w), int(p["y"] * h)) for p in poly], outline=(80, 255, 120, 255))
        draw.text((xa + 3, ya + 2), str(i + 1), fill=(255, 255, 0, 255))

    draw.rectangle([left, y_top, right, y_bot], outline=(255, 0, 80, 255), width=3)

    soft = Image.fromarray(win_mask, mode="L").filter(ImageFilter.GaussianBlur(radius=0.6))
    soft.save(OUT_ASSETS / "lights-window-mask.png")
    Image.fromarray(overlay, mode="RGBA").save(OUT_TOOLS / "lights-windows-overlay.png")
    label.save(OUT_TOOLS / "lights-windows-debug.png")

    data = {
        "width": w,
        "height": h,
        "sign": {
            "x0": round(left / w, 5),
            "y0": round(y_top / h, 5),
            "x1": round(right / w, 5),
            "y1": round(y_bot / h, 5),
        },
        "windows": js_windows,
    }
    (OUT_JS / "lights-windows-data.json").write_text(json.dumps(data), encoding="utf-8")
    print(f"saved {len(js_windows)} window polygons")


if __name__ == "__main__":
    main()
