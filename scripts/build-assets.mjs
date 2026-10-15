import {build, transform} from 'esbuild';
import {readFile, writeFile, mkdir, copyFile, readdir} from 'node:fs/promises';
import {fileURLToPath} from 'node:url';
import path from 'node:path';
const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const read = file => readFile(path.join(root, file), 'utf8');
for (const dir of ['assets/build', 'assets/vendor', 'assets/fonts']) await mkdir(path.join(root, dir), {recursive: true});
const css = {
  site: (await read('assets/css/style.css')) + '\n' + (await read('assets/css/site-layout.css')),
  home: await read('assets/css/home.css'),
  chat: await read('assets/css/chat.css'),
};
for (const [name, source] of Object.entries(css)) {
  const result = await transform(source, {loader: 'css', minify: true, target: ['chrome110', 'safari16'], legalComments: 'inline'});
  await writeFile(path.join(root, `assets/build/${name}.min.css`), result.code);
  console.log(`${name}.css: ${Buffer.byteLength(source)} -> ${Buffer.byteLength(result.code)} bytes`);
}
for (const name of ['site', 'home', 'chat']) {
  await build({entryPoints: [path.join(root, `assets/js/${name}.js`)], outfile: path.join(root, `assets/build/${name}.min.js`), minify: true, target: ['chrome110', 'safari16'], legalComments: 'inline'});
}
// Keep the complete icon set: admin forms and dynamic rating/heart icons use it.
let icons = await read('node_modules/bootstrap-icons/font/bootstrap-icons.css');
icons = icons.replace(/url\([^)]+\.woff2[^)]*\)/, 'url("../fonts/bootstrap-icons-1.11.3.woff2")')
  .replace(/,\s*url\([^)]+\.woff[^)]*\)\s*format\("woff"\)/, '')
  .replace('font-display: block', 'font-display: swap');
await writeFile(path.join(root, 'assets/build/icons.min.css'), (await transform(icons, {loader:'css', minify:true, legalComments:'inline'})).code);
await copyFile(path.join(root, 'node_modules/bootstrap-icons/font/fonts/bootstrap-icons.woff2'), path.join(root, 'assets/fonts/bootstrap-icons-1.11.3.woff2'));
for (const name of ['gsap', 'ScrollTrigger']) {
  const vendor = (await read(`node_modules/gsap/dist/${name}.min.js`)).replace(/[\t ]+$/gm, '').trimEnd() + '\n';
  await writeFile(path.join(root, `assets/vendor/${name}-3.12.5.min.js`), vendor);
}
for (const [pkg, name] of [['bootstrap-icons', 'bootstrap-icons-LICENSE.txt'], ['gsap', 'gsap-LICENSE.txt']]) {
  const entries = await readdir(path.join(root, 'node_modules', pkg));
  const license = entries.find(file => /^licen[cs]e(?:\.|$)/i.test(file));
  if (license) await copyFile(path.join(root, 'node_modules', pkg, license), path.join(root, 'assets/vendor', name));
}
