// Lists Wikimedia Commons photo candidates for each search term (metadata only).
const terms = process.argv.slice(2);
for (const term of terms) {
  await new Promise(r => setTimeout(r, 4000));
  const url = 'https://commons.wikimedia.org/w/api.php?' + new URLSearchParams({
    action: 'query', format: 'json', generator: 'search', gsrnamespace: '6',
    gsrsearch: term + ' filetype:bitmap', gsrlimit: '12', prop: 'imageinfo',
    iiprop: 'url|size|extmetadata', iiurlwidth: '1600',
  });
  const res = await fetch(url, { headers: { 'User-Agent': 'RivaMotorsStudentProject/1.0' } });
  const data = await res.json();
  console.log('\n## ' + term);
  for (const p of Object.values(data.query?.pages ?? {}).sort((a, b) => a.index - b.index)) {
    const ii = p.imageinfo[0];
    const lic = ii.extmetadata?.LicenseShortName?.value ?? '?';
    console.log(`${ii.width}x${ii.height} | ${lic} | ${p.title}`);
  }
}
