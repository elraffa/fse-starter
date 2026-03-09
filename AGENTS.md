# geller2026 — WordPress FSE Theme

Law firm theme for Estudio Geller. Full Site Editing, block-based, React build pipeline via `@wordpress/scripts`.

## Build

```bash
npm run build   # compile SCSS + JS → build/
npm run start   # watch mode
```

**Always run `npm run build` after editing any file in `src/`.**
The `build/` directory is committed to git (server cannot compile).

## Deploy

```bash
git add -A
git commit -m "message"
git push
```

Remote: SiteGround via SSH (`siteground-geller` alias in `~/.ssh/config`).
First push per session prompts for the SSH key passphrase.
`git push` updates live files directly — no deploy hook needed (`receive.denyCurrentBranch = updateInstead`).

SSH key: `~/.ssh/siteground_geller`
Server path: `/home/customer/www/federicor105.sg-host.com/public_html/wp-content/themes/geller2026/`

## Local dev

Site runs in Local by Flywheel. WordPress root is at:
`/Users/federico/Local Sites/geller2026/app/public/`

## File structure

```
geller2026/
├── src/blocks/
│   ├── site-header/        # PHP-rendered header block (main custom block)
│   │   ├── block.json
│   │   ├── render.php      # Server-side HTML output
│   │   ├── style.scss      # All header CSS (compiled → build/)
│   │   ├── view.js         # Frontend JS: drawer, overlay, accordion
│   │   └── index.js        # Editor registration (ServerSideRender)
│   └── hero/               # InnerBlocks hero section block
├── build/                  # wp-scripts output — committed to git
├── templates/              # FSE block templates (HTML)
├── parts/                  # header.html, footer.html
├── patterns/               # PHP block patterns
├── inc/
│   └── theme-options.php   # Settings API admin page + geller2026_option()
├── assets/
│   ├── css/
│   │   ├── global.css      # @font-face + body font-family (enqueued globally)
│   │   └── editor-style.css
│   └── fonts/
│       ├── manrope-latin.woff2
│       └── manrope-latin-ext.woff2
├── functions.php
├── theme.json              # Design system — layout, colors, spacing, typography
└── style.css               # Theme header comment only, no CSS
```

## Design system (theme.json)

| Token | Value |
|-------|-------|
| `contentSize` | 1200px (default block width) |
| `wideSize` | 1920px (wide-aligned blocks cap) |
| Root padding | `clamp(1.25rem, 4vw, 3rem)` left/right |
| Font | Manrope (self-hosted variable, 300–800) |
| `--accent` | `#1e3464` (deep navy) |
| `--base` | `#ffffff` |
| `--surface` | `#f5f3ef` (warm off-white for sections) |
| `--contrast` | `#0a0a0a` |
| `--muted` | `#6b7280` |
| `--border` | `#e2ded8` |

Spacing presets: `xs sm md lg xl 2xl 3xl` (lg→3xl are fluid `clamp()`).

Use `var(--wp--preset--color--accent)` etc. in CSS/SCSS.
Use `var:preset|spacing|lg` etc. in block template HTML attributes.

## Header block (`geller2026/site-header`)

- Sticky, frosted-glass (`backdrop-filter` on `::before` to avoid creating a containing block for `position:fixed` children)
- Layout: flexbox, logo `margin-right: auto` pushes nav + CTA right
- Desktop nav: hover = color change only, SVG chevron via CSS `mask`, hover bridge pseudo-element fixes dropdown gap
- Mobile drawer: slides in from right, starts at `top: 64px` (below header), no duplicate logo/close button
- Hamburger: 2 asymmetric lines in accent color, animates to X on open
- Overlay: `z-index: 99`, also starts at `top: 64px`

Nav menu locations registered in `functions.php`:
- `primary` — desktop nav + mobile drawer nav
- `header-cta` — CTA button (desktop) + CTA button (mobile drawer bottom)

## Theme options

Admin → Appearance → Theme Options

```php
geller2026_option( 'show_page_title' )  // bool, default true
geller2026_option( 'show_post_title' )  // bool, default true
```

**Adding a new option:**
1. Add key + default to `geller2026_option_defaults()` in `inc/theme-options.php`
2. `add_settings_field()` in `geller2026_register_settings()`
3. Sanitize rule in `geller2026_sanitize_options()`
4. Use `geller2026_option( 'key' )` anywhere in PHP

## Templates

All templates use `<!-- wp:geller2026/site-header /-->` (not the template-part block).

- `single.html` — prose constrained to 720px via inner Group (`contentSize: "720px"`)
- `page.html` — full 1200px width (marketing pages use section patterns)
- Others — index, archive, search, 404

## Key conventions

- SCSS lives in `src/`, compiled output in `build/` — never edit `build/` directly
- CSS custom properties from theme.json: `var(--wp--preset--color--{slug})`, `var(--wp--preset--font-size--{slug})`, `var(--wp--preset--spacing--{slug})`
- No jQuery. No Google Fonts. No emoji scripts.
- Per-block CSS loaded on demand via `add_theme_support( 'wp-block-styles' )`
