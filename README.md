# Andreia Philosophy Theme

## Customizable areas

Quick reference for everything you can change without editing theme code.

| Area | Where to edit | What it controls |
|------|---------------|------------------|
| Site identity | **Appearance → Customize → Site Identity** | Site title, tagline, site icon, main logo |
| Layout | **Appearance → Customize → Layout Settings** | Container width, gutter, mobile menu style |
| Social links | **Appearance → Customize → Social Links** | Header social icons (Facebook, X, Instagram, LinkedIn) |
| Main navigation | **Appearance → Menus** → assign to **Main Menu** | Header nav (desktop + mobile offcanvas) |
| Footer navigation | **Appearance → Menus** → assign to **Footer Menu** | Footer link row |
| Blog/post sidebar | **Appearance → Widgets → Sidebar** | Widgets beside single posts and sidebar page template |
| Homepage CTA | **Appearance → Widgets → Homepage Latest Sidebar** | CTA blocks under the Random slideshow on Latest Entries |
| Footer widgets | **Appearance → Widgets → Footer** | Footer widget area (hidden on the homepage) |
| Homepage layout | Edit the **static homepage** in the block editor | Andreian Post blocks, patterns, page content |
| Pages | Edit each **page** in the block editor | Full-width or sidebar page templates |
| Posts | Edit each **post** in the Classic Editor | Post body; sidebar comes from the Sidebar widget area |

---

## Appearance → Customize

Open **Appearance → Customize** in WordPress admin.

### Site Identity

- **Site title** and **Tagline** — used for the header/footer wordmark text and SEO defaults
- **Site icon** — browser tab / mobile bookmark icon
- **Main logo** — optional image logo (theme also displays the text wordmark in the header)

### Layout Settings

- **Container Gutter** — horizontal padding for site containers (pixels, default `16`)
- **Container Width** — max content width (pixels, default `1200`)
- **Mobile Menu Layout** — `Expansion Dropdowns` (default) or `Slide-in Panels` for nested mobile menu items

### Social Links

Add one row per profile:

1. Choose platform (Facebook, X, Instagram, LinkedIn)
2. Paste the full profile URL
3. Drag rows to reorder

Links render automatically in the header. You can also place them elsewhere with the **Social Links** block or `[andreian_social_links]` / `[social_links]` shortcode.

---

## Appearance → Menus

Create or edit menus under **Appearance → Menus**, then assign them:

### Main Menu (`main-nav`)

- Primary header navigation (desktop center nav + mobile offcanvas)
- Supports dropdown submenus

### Footer Menu (`footer-nav`)

- Single-level links in the footer lower bar (beside copyright)

If no menu is assigned, the theme falls back to a page list for the main nav.

---

## Appearance → Widgets

### Sidebar

Used on:

- Single blog posts (Classic Editor layout)
- Pages using the **Sidebar** page template

Add standard WordPress widgets (search, categories, custom HTML, block widgets, etc.).

### Homepage Latest Sidebar

Used only on the homepage **Latest Entries** section when **Show Random sidebar** is enabled on that Andreian Post block.

- The **Random** slideshow is automatic (block-driven)
- Add CTA content here: buttons, text, images, newsletter forms, etc.
- On desktop, this column stays visible while scrolling through Latest Entries

### Footer

Widget area above the footer menu on **internal pages only**. It is hidden on the homepage front page.

---

## Homepage

### Assign the homepage

1. **Settings → Reading**
2. Set **Your homepage displays** to **A static page**
3. Choose the homepage page

### Edit homepage content

1. Edit the assigned homepage page in the block editor
2. Insert **Patterns → Andreian Editorial Homepage** if starting fresh
3. Configure each **Andreian Post** block in the block sidebar

**Andreian Post block settings:**

- **Category**, post count, order, offset, exclude IDs
- **Layout** — Grid, List, Full, Hero Tiles, Category Tiles
- **Show excerpts** — teaser text under titles
- **Show Random sidebar** — List layout only; slideshow + Homepage Latest Sidebar widgets
- **Random slides** / **Random sidebar title** — when Random sidebar is on
- **Prioritize first image** — LCP preload for the first hero image

The pattern is an insertion template. After inserting, each block on the page is independent.

If the homepage has no saved content, the theme falls back to the default editorial pattern from the theme files.

### Homepage post counts (desktop)

| Section | Layout | Posts |
|---------|--------|-------|
| Featured lead | Hero Tiles | 5 |
| Latest Entries | List + Random sidebar | 9 (offset `5` skips lead posts) |
| Body / Mind / Spirit | Category Tiles | 9 each (3×3 grid) |

Random slideshow defaults to **9 slides**.

---

## Pages and posts

### Page templates

When editing a page, choose **Template** in the page sidebar:

- **Default** — standard page with optional entry header
- **Page Builder (Full Width)** — full-width block content, no sidebar
- **Sidebar** — block content with the **Sidebar** widget area

### Posts

Posts use the **Classic Editor** and the **Sidebar** widget area. Metadata (date, reading time, categories) is rendered by the theme.

---

## Blocks and shortcodes

| Name | Use |
|------|-----|
| **Andreian Post** (`andreian/post`) | Dynamic post grids/lists on pages |
| **Social Links** (`andreian/social-links`) | Output Customizer social links anywhere |
| `[andreian_social_links]` or `[social_links]` | Same as Social Links block |

---

## Development

Compile theme assets after SCSS/JS changes:

```bash
npm run compile
```

Social icons are SVG files in `assets/svg` (`ico-facebook`, `ico-x`, `ico-instagram`, `ico-linkedin`).
