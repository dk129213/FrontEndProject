# Riva Motors

Storefront and backoffice for a car dealership, built for the ADP final project (Group 1, front end). Customers browse the lot, filter by fuel, gearbox, condition, mileage and price, choose extras and reserve a car with a simulated card payment. The sales team and admins manage stock, sales and users.

Built with **PHP 8.4, Laravel 13, Blade, plain CSS and JavaScript**, following the technology choice document and the Riva Motors design system.

The look is a car studio, inspired by premium dealer sites: every car is cut out of its photo and shown on a lit studio panel, on cool grey pages, with orange kept for actions. Dark mode is a graphite studio with a spotlight. The site follows the device's light or dark setting, and the sun/moon button in the header switches and remembers it.

## Try it

| Role | Email | Password |
| --- | --- | --- |
| Customer | `customer@rivamotors.example` | `riva-demo` |
| Sales team | `sales@rivamotors.example` | `riva-demo` |
| Admin | `admin@rivamotors.example` | `riva-demo` |

The login page also has one-click buttons for each demo account.

Payment is simulated, Stripe-style. No money moves and no card data leaves the browser:

| Card | Result |
| --- | --- |
| `4242 4242 4242 4242` | Payment succeeds |
| `4000 0000 0000 0002` | Card is declined |

Use any future expiry date and any 3-digit security code.

The **Open on your phone** page (`/scan`) shows a QR code that opens the login page on a phone.

## Two homepages

- **Standard** (`/`): a showroom turntable of cut-out cars.
- **Cinematic** (`/cinematic`): a 12-second car film in five chapters. Each scroll, swipe or arrow key plays the real video on to the next chapter and shows its caption; scrolling back up plays a reversed copy of the film. The last chapter ends on the search bar, then the page scrolls normally.

A small switch at the bottom of both lets the owner compare them. The film lives in `public/video` as a forward and a reversed MP4, in 1600px for desktop and 960px for phones. To use a different film:

```bash
ffmpeg -i film.mp4 -an -vf scale=1600:-2 -c:v libx264 -crf 25 -g 12 -movflags +faststart public/video/film-1600.mp4
ffmpeg -i film.mp4 -an -vf scale=1600:-2,reverse -c:v libx264 -crf 25 -g 12 -movflags +faststart public/video/film-1600-reverse.mp4
ffmpeg -i film.mp4 -an -vf scale=960:-2 -c:v libx264 -crf 26 -g 12 -movflags +faststart public/video/film-960.mp4
ffmpeg -i film.mp4 -an -vf scale=960:-2,reverse -c:v libx264 -crf 26 -g 12 -movflags +faststart public/video/film-960-reverse.mp4
```

Where each chapter stops is set with `data-at` (0 to 1 through the film) in `resources/views/storefront/partials/cinematic.blade.php`.

## API

A JSON API serves the same data as the website. Full documentation: **`/api-docs`** (live at https://dk129213.github.io/FrontEndProject/api-docs/), generated from the OpenAPI 3.1 file `public/api/openapi.yaml`.

| Endpoint | What it returns |
| --- | --- |
| `GET /api/v1/index.json` | API name, version and endpoint list |
| `GET /api/v1/cars.json` | Cars for sale, with filters (`brand`, `body`, `fuel`, `gearbox`, `condition`, `min_price`, `max_price`, `max_mileage`, `year_from`, `status`) and `sort` |
| `GET /api/v1/cars/{slug}.json` | One car with every detail, gallery, extras and a finance example |
| `GET /api/v1/cars/{slug}/additions.json` | Extras that fit that car |
| `GET /api/v1/cars/{slug}/finance.json` | Finance quote (`deposit_percent`, `months`) |
| `GET /api/v1/additions.json` | All extras on offer |
| `GET /api/v1/filters.json` | Filter values with counts |
| `POST /api/v1/sales` | Reserve a car with extras and a simulated card payment |

On the live site (GitHub Pages) every `GET` endpoint works as a published file and query parameters are ignored. Filters, custom finance terms and `POST /api/v1/sales` need the PHP server (`php artisan serve`). Code: `routes/api.php`, `app/Http/Controllers/Api/`, `app/Support/ApiFormat.php`.

Try it locally:

```bash
curl "http://localhost:8000/api/v1/cars.json?fuel=hybrid,electric&sort=price_asc"
curl -X POST http://localhost:8000/api/v1/sales -H "Content-Type: application/json" -d '{"car":"volkswagen-golf-2022","additions":[1],"buyer":{"first_name":"Ana","last_name":"Horvat","email":"ana@example.com","phone":"+385 98 000 2201"},"payment":{"card_number":"4242 4242 4242 4242","exp_month":12,"exp_year":2028,"cvc":"123"}}'
```

## Pages

| Who | Pages |
| --- | --- |
| Everyone | Home, cars for sale with filters, car detail with payment calculator and full-screen photos, compare up to three cars, sell your car, log in / create account, QR code, photo credits, 404 |
| Buying | Choose extras, sign in and pay, order confirmed |
| Customer | My account: orders and favourites |
| Sales team | Dashboard, my cars, add a car, my sales |
| Admin | Dashboard, all cars, add a car, sales, users and roles, extras |

## Run it locally

Needs PHP 8.3+, Composer and Node 20+.

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
npm run build
php artisan serve
```

Open http://localhost:8000.

## Publish on GitHub Pages

1. Push the project to a GitHub repository.
2. In the repository, open **Settings > Pages** and set **Source** to **GitHub Actions**.
3. Every push to `main` runs `.github/workflows/pages.yml`, which builds the assets, exports every page to static HTML and publishes it to `https://<your-username>.github.io/<repo>/`.

GitHub Pages cannot run PHP, so the site is exported to plain HTML with [spatie/laravel-export](https://github.com/spatie/laravel-export). That is why login, favourites, orders and backoffice edits are simulated in the browser (`localStorage`): they persist on the device you use, not for everyone.

To preview the exported site locally exactly as Pages serves it:

```bash
ASSET_URL=/RivaMotors npm run build
APP_BASE_PATH=/RivaMotors ASSET_URL=/RivaMotors php artisan export
cp dist/not-found/index.html dist/404.html
node tools/serve-dist.mjs /RivaMotors 8080
```

## Where things live

| Path | What |
| --- | --- |
| `resources/data/cars.php` | The stock, shaped like the backend `Cars` table |
| `resources/data/backoffice.php` | Users, roles, additions, sales |
| `app/Support/Catalog.php` | The only class pages read data from. Swap it for Group 2's API later |
| `resources/views` | Blade pages and components (`car-card`, `status-badge`, `order-summary`, ...) |
| `resources/css/app.css` | Design tokens (light and dark) and all styles |
| `resources/js` | Filters, gallery, simulated login, checkout and backoffice |
| `config/export.php` | Every page included in the static export |
| `tools/` | Photo download, page smoke test, static preview server |

## Photos

Car photos are from Wikimedia Commons under CC BY, CC BY-SA and CC0 licences. Each one is credited on `/photo-credits`. They show the same models as the stock but are not photos of the exact cars. Re-download them with:

```bash
node tools/download-photos.mjs /some/empty/folder
```

The studio cut-outs (`public/images/cars/<slug>/cutout.webp`) are made from those downloads with the open-source [rembg](https://github.com/danielgatis/rembg) background remover and the BiRefNet model:

```bash
pip install "rembg[cpu]"
python tools/cutout.py /some/empty/folder
```

## Checks

```bash
php tools/smoke.php
```

Renders every exported page through Laravel and reports anything that is not a 200.
