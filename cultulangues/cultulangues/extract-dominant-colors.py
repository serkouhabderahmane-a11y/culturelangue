import json
import os
from collections import Counter
from math import sqrt

from PIL import Image

IMG_DIR = os.path.join(os.path.dirname(__file__), "img")
OUTPUT = os.path.join(os.path.dirname(__file__), "service-colors.json")

SERVICE_IMAGES = {
    "parcours-linguistique": "hero-parcours-linguistiques.png",
    "cap-sur-l-oral": "home/banner-tcf-overview-new.png",
    "tcf-quebec": "home/banner-tcf-partiel-new.png",
    "tcf-canada": "home/banner-tcf-intensif-new.png",
    "lingo-test": "home/banner-cap-choice1-new.png",
    "preparation-tcf": "banner-tcf-preparation.png",
    "english-linguistic-pathway": "hero-english-new.png",
    "formation-solo": "hero-cours-solo-new.png",
    "ateliers": "hero-ateliers-new.png",
    "francais-express": "banner-francais-express.png",
    "soiree-linguo": "banner-soiree-linguo.png",
    "samedis-en-francais": "banner-samedi-francais.png",
    "english-express": "banner-english-express.png",
    "evening-lingo": "banner-evening-lingo.png",
    "saturdays-english": "banner-samedi-anglais.png",
    "oral-b-partiel": "banner-oral-b-partiel.png",
    "oral-b-intensif": "banner-oral-b-intensif.png",
    "oral-c-partiel": "banner-oral-c-partiel.png",
    "oral-c-intensif": "banner-oral-c-intensif.png",
    "tcf-quebec-partiel": "banner-tcf-quebec-partiel.png",
    "tcf-quebec-intensif": "banner-tcf-quebec-intensif.png",
    "tcf-canada-partiel": "banner-tcf-canada-partiel.png",
    "tcf-canada-intensif": "banner-tcf-canada-intensif.png",
    "solo-5h": "solo-5h.png",
    "solo-10h": "solo-10h.png",
    "solo-15h": "solo-15h.png",
    "solo-20h": "solo-20h.png",
    "atelier-conversation": "atelier-conversation.png",
    "atelier-culture": "atelier-culture-canada.png",
    "atelier-maintien": "atelier-maintien.png",
}


def color_distance(c1, c2):
    return sqrt(sum((a - b) ** 2 for a, b in zip(c1, c2)))


def rgb_to_hex(r, g, b):
    return f"#{r:02X}{g:02X}{b:02X}"


def hex_to_rgb(h):
    h = h.lstrip("#")
    return tuple(int(h[i:i + 2], 16) for i in (0, 2, 4))


def saturation(r, g, b):
    mx = max(r, g, b)
    mn = min(r, g, b)
    if mx == 0:
        return 0
    return (mx - mn) / mx


def brightness(r, g, b):
    return (r + g + b) / 3 / 255


def score_color(r, g, b):
    sat = saturation(r, g, b)
    bri = brightness(r, g, b)
    if sat < 0.08:
        return -1
    if bri < 0.1 or bri > 0.95:
        return -1
    white_dist = color_distance((r, g, b), (255, 255, 255))
    black_dist = color_distance((r, g, b), (0, 0, 0))
    if white_dist < 30 or black_dist < 30:
        return -1
    rg = abs(r - g)
    gb = abs(g - b)
    rb = abs(r - b)
    max_diff = max(rg, gb, rb)
    return sat * 100 + min(white_dist, black_dist) * 0.5 + max_diff * 0.5


def get_center_crop_coords(w, h, crop_ratio=0.5):
    cw, ch = int(w * crop_ratio), int(h * crop_ratio)
    left = (w - cw) // 2
    top = (h - ch) // 2
    return left, top, left + cw, top + ch


def get_dominant_color(image_path):
    img = Image.open(image_path).convert("RGB")
    w, h = img.size
    box = get_center_crop_coords(w, h, 0.6)
    img = img.crop(box)
    img = img.resize((100, 100))
    pixels = list(img.getdata())

    scored = [(score_color(*p), p) for p in pixels]
    scored = [s for s in scored if s[0] > 0]
    scored.sort(reverse=True)
    top_n = scored[:int(len(scored) * 0.15)]
    if not top_n:
        scored = [(score_color(*p), p) for p in pixels]
        scored.sort(reverse=True)
        top_n = scored[:max(50, int(len(scored) * 0.05))]

    merged = []
    for _, color in top_n:
        if not merged:
            merged.append([color, 1])
        else:
            found = False
            for i, (mcol, mcnt) in enumerate(merged):
                if color_distance(color, mcol) < 40:
                    new_r = (mcol[0] * mcnt + color[0]) // (mcnt + 1)
                    new_g = (mcol[1] * mcnt + color[1]) // (mcnt + 1)
                    new_b = (mcol[2] * mcnt + color[2]) // (mcnt + 1)
                    merged[i] = [(new_r, new_g, new_b), mcnt + 1]
                    found = True
                    break
            if not found:
                merged.append([color, 1])
                if len(merged) >= 5:
                    break

    merged.sort(key=lambda x: x[1], reverse=True)
    colors_with_scores = []
    for col, cnt in merged[:3]:
        sat = saturation(*col)
        bri = brightness(*col)
        score = sat * 100 + cnt
        colors_with_scores.append((score, col))

    colors_with_scores.sort(key=lambda x: x[0], reverse=True)
    primary = colors_with_scores[0][1] if colors_with_scores else (100, 100, 100)
    palette = [c[1] for c in colors_with_scores[:5]]
    if len(palette) < 5:
        palette.extend([primary] * (5 - len(palette)))

    return primary, palette


def main():
    result = {}

    for service_id, rel_path in SERVICE_IMAGES.items():
        full_path = os.path.join(IMG_DIR, rel_path)
        if not os.path.exists(full_path):
            print(f"[SKIP] {service_id}: {rel_path} not found")
            continue

        primary, palette = get_dominant_color(full_path)
        hex_p = rgb_to_hex(*primary)
        hex_pal = [rgb_to_hex(*c) for c in palette]
        result[service_id] = {"primary": hex_p, "palette": hex_pal}
        print(f"[OK] {service_id}: {hex_p}")

    with open(OUTPUT, "w", encoding="utf-8") as f:
        json.dump(result, f, indent=2, ensure_ascii=False)

    print(f"\nWritten to {OUTPUT} ({len(result)} services)")


if __name__ == "__main__":
    main()
