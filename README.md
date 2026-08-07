# Choice Home Warranty (CHW) WordPress Theme

Custom WordPress theme for [Choice Home Warranty](https://www.choicehomewarranty.com), built with native Gutenberg blocks, a Gulp asset pipeline, and PHP render callbacks. The theme is designed for marketing pages, a blog, and reusable content blocks managed in the block editor.

---

## Requirements

| Requirement | Notes |
|---|---|
| **WordPress** | 6.0+ recommended (block editor, `block_categories_all`, native block APIs) |
| **PHP** | 8.0+ recommended |
| **Node.js** | 18+ (LTS recommended). Used for the Gulp build only — not required on production if compiled assets are deployed |
| **npm** | Comes with Node.js |

### Recommended plugins

| Plugin | Purpose |
|---|---|
| **Advanced Custom Fields (ACF) Pro** | Custom post types and taxonomies synced via `acf-json/` (e.g. Testimonials) |
| **Gravity Forms** | Form styling and defaults are configured in the theme |

Other plugins may be used on the site, but the theme does not depend on page builders or ACF for its custom blocks — all CHW blocks are native Gutenberg blocks.

---

## Getting started

### 1. Install the theme

Clone or copy this repository into your WordPress themes directory:

```
wp-content/themes/chw/
```

Activate **Choice Home Warranty** under **Appearance → Themes**.

### 2. Install Node dependencies

From the theme root:

```bash
npm install
```

### 3. Configure BrowserSync

Update the local site URL in `gulpconfig.json`:

```json
{
  "browserSyncOptions": {
    "proxy": "your-local-site.local/",
    ...
  }
}
```

The default proxy is `choice-home-warranty.local/` (Local by Flywheel).

### 4. Build assets

**One-time compile** (production build):

```bash
npm run compile
```

**Development with live reload** (watches `src/` and reloads on PHP/CSS/JS changes):

```bash
npm start
```

This runs Gulp’s default task: compiles all assets, starts BrowserSync, and watches for changes.

To run BrowserSync + watch without a full initial compile:

```bash
npx gulp watch-bs
```

### 5. Sync ACF data

If using ACF Pro, open **ACF → Tools** in wp-admin and sync any field groups, post types, or taxonomies that appear as out of sync. Definitions are version-controlled in `acf-json/`.

---

## Project structure

```
chw/
├── style.css                 # Theme header (required by WordPress)
├── functions.php             # Bootstraps all PHP includes
├── header.php / footer.php   # Global layout shell
├── front-page.php            # Homepage template
├── page.php / singular.php   # Default page & single post templates
├── archive.php / search.php  # Archive & search
├── 404.php
│
├── src/
│   ├── php/
│   │   ├── chw.php           # Core setup: enqueues, theme supports, nav menus
│   │   ├── theme.php         # Theme-specific hooks (extend here)
│   │   ├── customizer/       # WordPress Customizer panels & controls
│   │   ├── functions/        # Helpers (SVGs, social links, blog, etc.)
│   │   └── wp-customization/ # Admin/editor tweaks, Gravity Forms, widgets
│   ├── blocks/               # Custom Gutenberg blocks (PHP + SCSS + JS)
│   ├── js/
│   │   ├── theme.js          # Front-end entry
│   │   ├── components/       # Front-end component scripts
│   │   ├── layout/           # Layout scripts (nav, etc.)
│   │   └── editor/           # Block editor (Gutenberg) scripts
│   ├── scss/
│   │   ├── theme.scss        # Main stylesheet entry
│   │   ├── abstracts/        # Variables, mixins, fonts
│   │   ├── base/             # Typography, elements, utilities
│   │   ├── layout/           # Header, footer, nav, sidebar
│   │   ├── components/       # Reusable UI components
│   │   └── pages/            # Page-specific styles
│   ├── components/           # PHP template parts (masthead, announcement bar, etc.)
│   ├── templates/              # Custom page templates
│   └── admin/                  # Admin/login styles & scripts
│
├── dist/                     # Compiled CSS, JS, and webfonts (committed to git)
├── assets/                   # Static images and SVGs
├── acf-json/                 # ACF post types, taxonomies, options (JSON sync)
├── patterns/                 # Block pattern PHP files
├── gulpfile.js               # Gulp build configuration
├── gulpconfig.json           # BrowserSync proxy & paths
└── package.json
```

### Compiled output (`dist/`)

Gulp produces minified bundles:

| Output | Source |
|---|---|
| `dist/css/theme.min.css` | Swiper, Font Awesome, `src/scss/theme.scss` |
| `dist/css/blocks.min.css` | `src/blocks/blocks.scss` + block SCSS |
| `dist/css/admin.min.css` | Block + admin SCSS |
| `dist/js/theme.min.js` | ally.js, Swiper, components, layout, `theme.js` |
| `dist/js/blocks.min.js` | All `src/blocks/**/*.js` |
| `dist/js/admin.min.js` | `src/admin/admin.js` |

Compiled assets are tracked in git so production deployments do not require Node.js. Always run `npm run compile` before committing asset changes.

---

## Custom blocks

All CHW blocks are **native Gutenberg blocks** — no ACF block fields. They appear under the **Choice Home Warranty Custom Blocks** category in the inserter.

Each block lives in `src/blocks/<slug>/` and typically includes:

| File | Role |
|---|---|
| `register-<slug>-block.php` | Block registration, attributes, PHP render callback |
| `<slug>.php` | Render template (when markup is complex) |
| `<slug>.scss` | Block-specific styles (imported via `blocks.scss`) |
| `<slug>.js` | Front-end behavior (bundled into `blocks.min.js`) |
| `src/js/editor/<slug>.js` | Block editor UI (registered as `editor_script`) |

### Available blocks

- Accordions / Accordion (parent/child)
- Audience Section
- Awards & Recognition
- Badge
- CHW Buttons
- CHW Legal
- Comparison Table
- Tabbed Comparison Table
- CTA Banner, CTA Block, CTA Highlights, CTA Image Banner, CTA Subtle
- Fullwidth Cover Section
- Fullwidth Halfscreen
- Highlight Cards
- Highlights List
- Linkbank Section
- Plans Section
- Quick Actions
- Social Links
- Stats & Testimonials
- Steps Section
- Tabbed Panes
- Testimonials Slider
- Video Modal

New blocks must be registered in `functions.php` via their `register-*-block.php` file.

---

## Theme customization

Site-wide settings are managed through the **WordPress Customizer** (`src/php/customizer/`):

- Site logos
- Layout settings (mobile menu layout, etc.)
- Masthead defaults
- Announcement bar
- Blog settings
- Social links
- Legal text
- “As Seen In” logos

Customizer values are exposed as CSS custom properties where needed (see `src/components/root-customizer-vars.php`).

### Navigation

One menu location is registered:

- **Main Menu** (`main-nav`) — primary header navigation

Register additional locations in `chw_nav_menus()` inside `src/php/chw.php`.

### Block patterns

Reusable block patterns live in `patterns/` and are registered via `src/php/functions/block-patterns.php`. Use patterns for common layouts that editors can insert and customize.

---

## Development workflow

### Daily development

1. Start the dev server: `npm start`
2. Edit files in `src/` — **never edit `dist/` directly**
3. Gulp recompiles on save; BrowserSync reloads the browser
4. Test in the block editor and on the front end

### Before committing

```bash
npm run compile
```

Verify compiled output in `dist/` looks correct, then commit both source and compiled files.

### Performance auditing

A Lighthouse script is included for local audits:

```bash
npm run lighthouse
```

Update the URL in `package.json` if your local domain differs.

---

## Best practices

### PHP

- Add new functionality under `src/php/` and include it from `functions.php` or `src/php/chw.php` — keep the root bootstrap thin.
- Always guard files with `if ( ! defined( 'ABSPATH' ) ) exit;`.
- Use `chw_asset_version()` for cache-busting enqueued assets (filemtime-based).
- Escape output: `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()` as appropriate.
- Block render callbacks should build markup in PHP; keep presentation logic out of JavaScript where possible.

### Blocks

- **Editor JS** (`src/js/editor/`) handles the Gutenberg canvas and inspector controls.
- **PHP render callbacks** produce the front-end HTML — this is the source of truth for what visitors see.
- **Front-end JS** (`src/blocks/<slug>/<slug>.js`) is only for interactive behavior (sliders, toggles, modals).
- Follow existing naming: block namespace `chw/<slug>`, CSS classes use BEM-style prefixes (e.g. `_summary`, `-modifier`).
- When adding block SCSS, ensure it is picked up by the blocks bundle (via `src/blocks/blocks.scss` or co-located imports).

### SCSS

- Design tokens live in `src/scss/abstracts/_variables.scss` (brand colors, typography, breakpoints).
- Follow the existing import order in `theme.scss`: abstracts → vendors → base → layout → components → pages.
- Use the `$breakpoints` map and existing mixins from `abstracts/_mixins.scss` for responsive styles.
- Block-specific styles belong in `src/blocks/`, not in global component files.

### JavaScript

- Front-end scripts use vanilla JS; dependencies (Swiper, ally.js) are bundled via Gulp.
- Editor scripts use the WordPress `@wordpress/*` packages exposed on the `wp` global — no separate npm build for editor JS.
- Keep `theme.js` as the main front-end entry; add component scripts to `src/js/components/` or `src/js/layout/`.

### Assets & images

- Place theme images in `assets/images/` and SVGs in `assets/svg/`.
- Use `THEME_IMAGES` and `THEME_SVGS` constants (defined in `functions.php`) for URI references in PHP.
- Prefer WebP for photos; SVG for icons and logos.

### ACF JSON

- Post types and taxonomies defined in ACF are stored in `acf-json/` and should be committed to git.
- After pulling changes, sync in wp-admin if ACF reports differences.
- Custom blocks do **not** use ACF — reserve ACF for data models (CPTs, options pages, etc.).

### Accessibility

- The theme includes skip links, ARIA patterns on interactive blocks (accordions, tabs), and ally.js for focus management.
- Test keyboard navigation and screen reader behavior when building new interactive blocks.
- Use semantic HTML (`<details>`, `<nav>`, heading hierarchy) in render callbacks.

### WordPress conventions

- Template parts live in `src/components/` and are loaded with `get_template_part()`.
- Custom page templates go in `src/templates/` and are selected per-page in the editor.
- Avoid hard-coding URLs; use `home_url()`, `get_permalink()`, and WordPress APIs.
- The theme removes several default WordPress head tags and emoji scripts for performance — do not re-add without reason.

### Git

- Commit compiled `dist/` files alongside source changes.
- Do not commit `node_modules/` (already in `.gitignore`).
- Document notable changes in `CHANGELOG.md`.

---

## Deployment

1. Run `npm run compile` locally.
2. Deploy the theme directory (or push to the deployment branch).
3. Activate the theme if not already active.
4. Sync ACF JSON in production wp-admin if post types or fields changed.
5. Flush caches (object cache, CDN, page cache) after asset updates.

Production servers do not need Node.js if `dist/` is up to date in the deployed code.

---

## Troubleshooting

| Issue | Fix |
|---|---|
| Styles/scripts not updating | Run `npm run compile`. Hard-refresh the browser. Check that you edited `src/`, not `dist/`. |
| BrowserSync not connecting | Verify the `proxy` URL in `gulpconfig.json` matches your local site URL exactly (include trailing slash). |
| Block missing in editor | Confirm the block’s `register-*-block.php` is included in `functions.php`. Check for PHP errors in debug log. |
| ACF post types missing | Install/activate ACF Pro and sync JSON from **ACF → Tools**. |
| Font/icon issues | Re-run compile — Font Awesome and Hind webfonts are copied/bundled into `dist/webfonts/`. |

Enable WordPress debugging during development:

```php
// wp-config.php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
```

---

## Credits

- **Theme:** Choice Home Warranty
- **Author:** Mark Fenske / moveBuddha
- **Version:** See `style.css`

For change history, see [CHANGELOG.md](CHANGELOG.md).
