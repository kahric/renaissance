#!/usr/bin/env node
// Release: bump version → build → commit + tag → dist/renaissance-<version>.zip
//
//   npm run bundle            patch  (1.1.0 → 1.1.1)
//   npm run bundle -- minor   minor  (1.1.0 → 1.2.0)
//   npm run bundle -- major   major  (1.1.0 → 2.0.0)
//   npm run bundle -- 1.4.0   exact version
//
// The zip holds the theme in a top-level `renaissance/` folder, ready for
// Appearance → Themes → Add New → Upload. Only git-tracked files ship, plus
// build/ and a production vendor/ (composer install --no-dev).

import { execSync } from 'node:child_process';
import { readFileSync, writeFileSync, rmSync, mkdirSync, cpSync, existsSync } from 'node:fs';
import { join } from 'node:path';

const ROOT = new URL('..', import.meta.url).pathname;
const SLUG = 'renaissance';
const DIST = join(ROOT, 'dist');
const STAGE = join(DIST, SLUG);

// The zip mirrors the layout of the theme as deployed on durance (server copy
// 2026-07-12): theme PHP, build/, resources/, src/, composer.json/lock, vendor/.
// Nothing that gives away the build tooling ships.
const DEV_ONLY = ['node_modules', '.claude', 'CLAUDE.md', 'package.json', 'package-lock.json', 'scripts', 'dist', '.gitignore', '.gitattributes', '.github', '.editorconfig', 'README.md'];
const FORBIDDEN = /(^|\/)(node_modules|\.claude|CLAUDE\.md|package(-lock)?\.json|scripts|\.git\w*|\.DS_Store)$|\.map$/;

const run = (cmd, opts = {}) => execSync(cmd, { cwd: ROOT, stdio: 'inherit', ...opts });
const out = (cmd) => execSync(cmd, { cwd: ROOT, encoding: 'utf8' }).trim();
const fail = (msg) => { console.error(`\n✖ ${msg}`); process.exit(1); };

// 1. Clean tree — the release commit must hold only the version bump.
if (out('git status --porcelain')) fail('Working tree is not clean. Commit or stash first.');

// 2. Next version.
const read = (f) => readFileSync(join(ROOT, f), 'utf8');
const current = read('style.css').match(/^Version:\s*(\S+)/m)?.[1];
if (!current) fail('No "Version:" line in style.css.');

const arg = process.argv[2] ?? 'patch';
let next;
if (/^\d+\.\d+\.\d+$/.test(arg)) {
	next = arg;
} else {
	const [maj, min, pat] = current.split('.').map(Number);
	next = { major: `${maj + 1}.0.0`, minor: `${maj}.${min + 1}.0`, patch: `${maj}.${min}.${pat + 1}` }[arg];
	if (!next) fail(`Unknown bump "${arg}". Use patch, minor, major or x.y.z.`);
}
if (out(`git tag -l v${next}`)) fail(`Tag v${next} already exists.`);
console.log(`\n→ ${current} → ${next}\n`);

// 3. Bump everywhere the version lives.
const bump = (file, re, replacement) => {
	const src = read(file);
	if (!re.test(src)) fail(`Version pattern not found in ${file}.`);
	writeFileSync(join(ROOT, file), src.replace(re, replacement));
};
bump('style.css', /^Version:\s*\S+/m, `Version: ${next}`);
bump('version.php', /define\(\s*'_THEME_VERSION',\s*'[^']*'\s*\)/, `define( '_THEME_VERSION', '${next}' )`);
run(`npm version ${next} --no-git-tag-version --allow-same-version`, { stdio: 'ignore' });

// 4. Production build. On failure, roll the bump back so nothing is half-done.
try {
	run('npm run build');
} catch {
	run('git checkout -- style.css version.php package.json package-lock.json');
	fail('Build failed — version bump reverted.');
}

// 5. Commit + tag.
run('git add style.css version.php package.json package-lock.json');
run(`git commit -m "Release ${next}"`);
run(`git tag v${next}`);

// 6. Stage: tracked files at the release commit, + build, + production vendor.
rmSync(STAGE, { recursive: true, force: true });
mkdirSync(STAGE, { recursive: true });
run(`git archive HEAD | tar -x -C "${STAGE}"`);
for (const f of DEV_ONLY) rmSync(join(STAGE, f), { recursive: true, force: true });
cpSync(join(ROOT, 'build'), join(STAGE, 'build'), { recursive: true, filter: (p) => !p.endsWith('.map') });
if (existsSync(join(STAGE, 'composer.json'))) {
	run('composer install --no-dev --optimize-autoloader --no-interaction --quiet', { cwd: STAGE });
}

// Guard: refuse to zip if any build-tooling file slipped into the stage.
const leaked = out(`cd "${STAGE}" && find . -mindepth 1 -not -path "./vendor/*"`).split('\n').filter((p) => FORBIDDEN.test(p));
if (leaked.length) fail(`Build files in the package: ${leaked.join(', ')}`);

// 7. Zip.
const zip = join(DIST, `${SLUG}-${next}.zip`);
rmSync(zip, { force: true });
run(`zip -rqX "${zip}" ${SLUG} -x "*.DS_Store"`, { cwd: DIST });
rmSync(STAGE, { recursive: true, force: true });

console.log(`\n✔ Released ${next}\n  commit + tag v${next} (not pushed — git push --follow-tags)\n  ${zip}\n`);
