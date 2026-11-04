// Downloads the Wikimedia Commons photos used for the stock, crops them to
// 16:10 (1600x1000) with ffmpeg, and writes resources/data/photo-credits.json.
// One car, one colour: every photo in a set shows the same car.
// Usage: node tools/download-photos.mjs <raw-download-dir>
import { execFileSync } from 'node:child_process';
import { mkdirSync, writeFileSync, existsSync } from 'node:fs';
import { join } from 'node:path';

const PHOTOS = {
    'skoda-octavia-combi-2021': ['2020 Skoda Octavia SE L First Edition TDi Estate 2.0 Front.jpg', '2020 Skoda Octavia SE L First Edition TDi Estate 2.0 Rear.jpg'],
    'skoda-kodiaq-2026': ['Škoda Kodiaq II IMG 9433.jpg', 'Škoda Kodiaq II IMG 9428.jpg'],
    'skoda-fabia-2022': ['Skoda Fabia IV - front.jpg', 'Skoda Fabia IV - rear.jpg'],
    'volkswagen-golf-2022': ['2020 Volkswagen Golf Style 1.5 Front.jpg', '2020 Volkswagen Golf Style 1.5 Rear.jpg', '2020 Volkswagen Golf Style 1.5 Side.jpg'],
    'volkswagen-id4-2021': ['2021 Volkswagen ID.4 City Front.jpg', '2021 Volkswagen ID.4 City Rear.jpg'],
    'toyota-yaris-hybrid-2023': ['2020-2024 Toyota Yaris Hybrid.jpg'],
    'toyota-rav4-hybrid-2021': ['2019 Toyota RAV4 Excel Hybrid CVT 2.5 Front.jpg', '2019 Toyota RAV4 Excel Hybrid CVT 2.5 Rear.jpg'],
    'tesla-model-3-2022': ['2019 Tesla Model 3 Long Range AWD Front.jpg', '2019 Tesla Model 3 Long Range AWD Rear.jpg'],
    'bmw-320d-touring-2020': ['BMW 3-Series Touring (G21) 320i (2022) (53463745312).jpg'],
    'mercedes-benz-c220d-2019': ['Mercedes-Benz C200 AVANTGARDE (W205) front.JPG'],
    'hyundai-tucson-2022': ['Hyundai Tucson (NX4) 1X7A0424.jpg'],
    'renault-clio-2025': ['2024 Renault Clio Esprit Alpine E-Tech - 1598cc 1.6 (145PS) Petrol Hybrid - Flame Red - 05-2024, Front.jpg'],
    'skoda-superb-2020': ['2019 Skoda Superb SE L Executive TDi 2.0 Front.jpg'],
};

const rawDir = process.argv[2];
if (!rawDir) throw new Error('Pass a directory for the raw downloads');
mkdirSync(rawDir, { recursive: true });
const UA = { 'User-Agent': 'RivaMotorsStudentProject/1.0 (educational demo)' };
const wait = (ms) => new Promise((r) => setTimeout(r, ms));
const strip = (html) => (html || '').replace(/<[^>]+>/g, '').replace(/\s+/g, ' ').trim();

const jobs = Object.entries(PHOTOS).flatMap(([slug, titles]) =>
    titles.map((title, i) => ({ slug, title, file: `images/cars/${slug}/${i + 1}.jpg`, w: 1600, h: 1000 })),
);

const credits = [];
for (const { slug, title, file, w, h } of jobs) {
    {
        const out = join('public', file);
        mkdirSync(join(out, '..'), { recursive: true });
        await wait(6000);
        const api = 'https://commons.wikimedia.org/w/api.php?' + new URLSearchParams({
            action: 'query', format: 'json', titles: 'File:' + title, prop: 'imageinfo',
            iiprop: 'url|extmetadata', iiurlwidth: '2600',
        });
        let body;
        for (let attempt = 0; ; attempt++) {
            const text = await (await fetch(api, { headers: UA })).text();
            if (text.startsWith('{')) { body = JSON.parse(text); break; }
            if (attempt > 6) throw new Error('Rate limited for ' + title);
            await wait(30000);
        }
        const info = Object.values(body.query.pages)[0].imageinfo[0];
        const meta = info.extmetadata;
        credits.push({
            slug,
            file,
            title,
            source: info.descriptionurl,
            author: strip(meta.Artist?.value) || 'Unknown',
            license: meta.LicenseShortName?.value || 'See source',
            license_url: meta.LicenseUrl?.value || null,
        });
        if (existsSync(out)) continue;
        const raw = join(rawDir, file.replaceAll('/', '-'));
        await wait(6000);
        const res = await fetch(info.thumburl, { headers: UA });
        if (!res.ok) throw new Error(`${res.status} for ${title}`);
        writeFileSync(raw, Buffer.from(await res.arrayBuffer()));
        execFileSync('ffmpeg', ['-y', '-loglevel', 'error', '-i', raw,
            '-vf', `scale=${w}:${h}:force_original_aspect_ratio=increase,crop=${w}:${h}`,
            '-q:v', '4', out]);
        console.log('saved', out);
    }
}
writeFileSync('resources/data/photo-credits.json', JSON.stringify(credits, null, 2) + '\n');
console.log(credits.length, 'credits written');
