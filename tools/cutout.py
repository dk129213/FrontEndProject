"""Cuts each car out of its main photo (rembg, BiRefNet lite) and saves a
trimmed transparent WebP to public/images/cars/<slug>/cutout.webp.

Usage: python tools/cutout.py <raw-photo-dir>
Raw photos are the full-size downloads from tools/download-photos.mjs.
"""

import os
import sys
from pathlib import Path

import numpy as np
from PIL import Image
from rembg import new_session, remove
from scipy import ndimage

# slug -> raw file holding the photo used as the car's main image
SOURCES = {
    'skoda-octavia-combi-2021': 'skoda-octavia-combi-2021-1.jpg',
    'skoda-kodiaq-2026': 'skoda-kodiaq-2026-1.jpg',
    'skoda-fabia-2022': 'skoda-fabia-2022-1.jpg',
    'volkswagen-golf-2022': 'volkswagen-golf-2022-1.jpg',
    'volkswagen-id4-2021': 'volkswagen-id4-2021-1.jpg',
    'toyota-yaris-hybrid-2023': 'toyota-yaris-hybrid-2023-1.jpg',
    'toyota-rav4-hybrid-2021': 'toyota-rav4-hybrid-2021-1.jpg',
    'tesla-model-3-2022': 'tesla-model-3-2022-1.jpg',
    'bmw-320d-touring-2020': 'bmw-320d-touring-2020-2.jpg',
    'mercedes-benz-c220d-2019': 'mercedes-benz-c220d-2019-2.jpg',
    'hyundai-tucson-2022': 'hyundai-tucson-2022-2.jpg',
    'renault-clio-2025': 'renault-clio-2025-1.jpg',
    'skoda-superb-2020': 'skoda-superb-2020-1.jpg',
}

raw = Path(sys.argv[1])
session = new_session(os.environ.get('CUTOUT_MODEL', 'birefnet-general-lite'))
only = set(sys.argv[2:])

for slug, name in SOURCES.items():
    if only and slug not in only:
        continue
    image = Image.open(raw / name).convert('RGB')
    cut = remove(image, session=session, post_process_mask=True)
    # Drop faint halo pixels and keep only the biggest shape (the car), so
    # pieces of neighbouring cars do not come along.
    alpha = np.array(cut.getchannel('A'))
    alpha[alpha < 24] = 0
    labels, count = ndimage.label(alpha > 0)
    if count > 1:
        sizes = ndimage.sum(np.ones_like(labels), labels, range(1, count + 1))
        alpha[labels != (np.argmax(sizes) + 1)] = 0
    alpha = Image.fromarray(alpha)
    cut.putalpha(alpha)
    cut = cut.crop(alpha.getbbox())
    cut.thumbnail((1400, 1400), Image.LANCZOS)
    out = Path('public/images/cars') / slug / 'cutout.webp'
    cut.save(out, 'WEBP', quality=86, method=6)
    print('saved', out, cut.size)
