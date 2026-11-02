---
paths:
  - "scripts/**"
  - "package.json"
  - ".gitignore"
---

# Release package layout

`npm run bundle [patch|minor|major|x.y.z]` (scripts/release.mjs): clean tree → bump `style.css` + `version.php` + `package.json` → `npm run build` → commit `Release x.y.z` + tag `vx.y.z` (no push) → `dist/renaissance-x.y.z.zip`.

The zip must look exactly like the theme as packaged before (server copy on `durance`, `new.durance.hr/wp-content/themes/renaissance`, 2026-07-12): theme PHP files, `build/`, `resources/`, `src/`, `templates/`, `email/`, `template-parts/`, `woocommerce/`, `screenshot.png`, `.htaccess`, `composer.json`/`composer.lock`, `vendor/`. Owner's rule (2026-11-02): **no file that gives away the build tooling or would be public** — no `package.json`/lock, `scripts/`, `node_modules`, `.gitignore`/`.git*`, `.claude`/`CLAUDE.md`, `.map`, `.DS_Store`, README. `DEV_ONLY` strips them, `FORBIDDEN` aborts the release if one slips through. Any new dev-only file at repo root goes into both lists.

- `vendor/` in the zip comes from `composer install --no-dev` (the old deployed vendor carried dev packages; the code only uses the `#[NoReturn]` attribute, which needs no class at runtime). Server runs PHP 8.3.
- Tailwind is pinned to exactly 4.0.0 (cli, node, oxide via `overrides`): the CLI's own deps float otherwise and break with `Missing field negated` / `toSourceMap`.
- Tailwind v4 scans everything not gitignored — `vendor/` and `build/` must stay in `.gitignore`, or app.css bloats (~+27 KB).
- The server build had ~50 extra classes (old `before:` accordion, `xl:grid-cols-*`, `bg-surface`…) that no current source or staging DB content uses; the rebuilt CSS is a strict subset. If a style is missing on some page, look there first.
