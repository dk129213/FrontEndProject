// Serves the exported site from dist/ under a base path, the way GitHub
// Pages does: /<base>/cars -> dist/cars/index.html, unknown paths -> 404.html.
// Usage: node tools/serve-dist.mjs [base=/RivaMotors] [port=8080]
import { createServer } from 'node:http';
import { existsSync, readFileSync, statSync } from 'node:fs';
import { extname, join, normalize } from 'node:path';

const base = process.argv[2] ?? '/RivaMotors';
const port = Number(process.argv[3] ?? 8080);
const root = join(process.cwd(), 'dist');
const types = {
    '.html': 'text/html; charset=utf-8', '.css': 'text/css', '.js': 'text/javascript',
    '.svg': 'image/svg+xml', '.jpg': 'image/jpeg', '.png': 'image/png', '.webp': 'image/webp',
    '.woff2': 'font/woff2', '.json': 'application/json', '.txt': 'text/plain', '.mp4': 'video/mp4',
};

createServer((req, res) => {
    const path = decodeURIComponent(new URL(req.url, 'http://x').pathname);
    if (path === '/' || path === base) {
        res.writeHead(302, { Location: base + '/' });
        return res.end();
    }
    let file = path.startsWith(base + '/') ? normalize(join(root, path.slice(base.length))) : null;
    if (file && file.startsWith(root) && existsSync(file) && statSync(file).isDirectory()) file = join(file, 'index.html');
    if (!file || !file.startsWith(root) || !existsSync(file)) {
        res.writeHead(404, { 'Content-Type': types['.html'] });
        return res.end(readFileSync(join(root, '404.html')));
    }
    res.writeHead(200, { 'Content-Type': types[extname(file)] ?? 'application/octet-stream' });
    res.end(readFileSync(file));
}).listen(port, () => console.log(`Serving dist at http://localhost:${port}${base}/`));
