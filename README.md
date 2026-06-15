# Spendvest WordPress Theme

Custom WordPress theme for the [Spendvest](https://spendvest.com) marketing website. The theme powers the landing page, blog, legal pages, waitlist flow, and mobile deep-link landing pages for the Spendvest mobile app.

**Version:** 1.0.0  
**Text domain:** `spendvest`  
**License:** GPL v2 or later

---

## Requirements

| Requirement | Notes |
|-------------|-------|
| WordPress | 6.0+ recommended |
| PHP | 7.4+ (8.x recommended) |
| [Advanced Custom Fields (ACF)](https://www.advancedcustomfields.com/) | Required — page content and theme options are managed via ACF fields |
| [Contact Form 7](https://contactform7.com/) | Required for the waitlist / newsletter form |
| Permalinks | Must be enabled (not “Plain”) for deep-link routes to work |

### Optional

- SVG uploads are allowed for administrators only (configured in `functions.php`).

---

## Installation

1. Copy the `spendvest` folder into `wp-content/themes/`.
2. In **Appearance → Themes**, activate **Spendvest**.
3. Go to **Settings → Permalinks** and click **Save Changes** to flush rewrite rules (also done automatically on theme activation).
4. Install and activate **ACF** and **Contact Form 7**.
5. Import or recreate ACF field groups used by the theme (home page sections, theme options, simple page fields, blog hero).
6. Assign menus under **Appearance → Menus**:
   - **Main Menu** — primary header navigation
   - **Footer Menu** — footer links

---

## Page Templates

Create WordPress pages and assign the appropriate template under **Page Attributes → Template**.

| Template | File | Purpose |
|----------|------|---------|
| **Home Page** | `template/template-home.php` | Main marketing landing page. Content is driven entirely by ACF fields; the block editor is disabled for this template. |
| **Thank You** | `template/template-thank-you.php` | Post-submission confirmation page (`/thank-you/`). Fires a Meta Pixel `Lead` event. Uses `noindex,nofollow`. |
| **Simple Page** | `template/template-simple.php` | Legal and informational pages (Privacy Policy, Terms, etc.) with a hero and optional metadata fields. |

### Recommended site structure

| Page | Template | Slug (example) |
|------|----------|------------------|
| Front page | Home Page | `/` (set as static front page) |
| Blog index | — (uses `home.php`) | `/blog/` (set as **Posts page**) |
| Thank you | Thank You | `thank-you` |
| Legal pages | Simple Page | `privacy-policy`, `terms`, etc. |

---

## Theme Options & Content (ACF)

Global settings are stored in ACF **Options** pages (referenced in `header.php` and `footer.php`):

- `header_logo`, `dark_logo`, `button_text`
- `footer_logo`, `copyright`, `socials`, `social_text`, `email`, `disclaimer`

The home page template reads multiple ACF field groups, including:

- `hero` — title, subtitle, background image
- `works` — feature cards and copy
- `contact` — waitlist section (also used in the footer fallback)

The blog index (`home.php`) uses an options-level `hero` field group.

Simple pages support: `page_info`, `last_updated`, `summary_date`.

> Field group definitions are not bundled with the theme. Export them from an existing environment or recreate them to match the `get_field()` calls in the templates.

---

## Features

### Investment calculator

Loaded only on the Home Page template (`assets/js/calculator.js`). Interactive calculator section on the landing page.

### Animations

GSAP 3.12.5 (ScrollTrigger, MotionPathPlugin) is loaded from jsDelivr CDN. Custom scroll animations and interactions live in `assets/js/main.js` and `assets/css/animations.css`.

### Meta Pixel (Facebook)

- **PageView** — tracked on every page via `wp_head`.
- **Lead** — tracked on the Thank You template, with optional `eventID` from `sessionStorage` for deduplication with server-side events.

Pixel ID is defined in `functions.php` (`spendvest_meta_pixel_head`).

### Contact Form 7 → Beehiiv

On successful submission of Contact Form 7 form **ID 121**, the theme sends the subscriber email (and UTM / Facebook tracking fields) to the Beehiiv API.

Hidden fields expected in the form:

| Field name | Purpose |
|------------|---------|
| `sv_fbc` | Facebook click ID |
| `sv_fbp` | Facebook browser ID |
| `sv_utm_source` | UTM source |
| `sv_utm_medium` | UTM medium |
| `sv_utm_campaign` | UTM campaign |
| `sv_utm_content` | UTM content |
| `sv_event_id` | Event ID for pixel deduplication |

The thank-you redirect URL is exposed to JavaScript as `spendvestTheme.thankYouUrl` (filterable via `spendvest_thank_you_url`).

### Mobile deep links (`/dl/` and `/ref/`)

Custom rewrite rules route mobile visitors to a lightweight app-open landing page:

- `/dl/` and `/dl/{path}` — download / open app links
- `/ref/` and `/ref/{path}` — referral links

Desktop visitors are redirected to the home page. Deep links use the `spendvest://` URL scheme.

**Filters for store URLs** (add to a child theme or mu-plugin):

```php
add_filter('spendvest_dl_app_store_url', function () {
    return 'https://apps.apple.com/app/…';
});
add_filter('spendvest_dl_play_store_url', function () {
    return 'https://play.google.com/store/apps/details?id=…';
});
```

### Universal links & App Links

The theme serves JSON documents for mobile app association:

| URL | Source file |
|-----|-------------|
| `/.well-known/apple-app-site-association` | `assets/doc/apple-app-site-association.json` |
| `/.well-known/assetlinks.json` | `assets/doc/assetlinks.json` |

Update these files when the app bundle ID, team ID, or SHA-256 fingerprints change.

### Blog

- `home.php` — blog index with category filter (`?category=slug`)
- `single.php` — single post with auto-generated table of contents from `<h2>` headings and estimated reading time

### Block editor

The Gutenberg block editor is disabled site-wide. Content is managed through ACF and the classic editor (where enabled).

---

## File Structure

```
spendvest/
├── style.css                 # Theme metadata & minimal base styles
├── functions.php             # Enqueues, integrations, rewrite rules
├── header.php
├── footer.php
├── index.php
├── home.php                  # Blog index
├── single.php                # Single post
├── 404.php
├── template/
│   ├── template-home.php     # Home Page
│   ├── template-thank-you.php
│   ├── template-simple.php   # Simple Page
│   └── template-dl.php       # Deep-link landing (mobile)
└── assets/
    ├── css/
    │   ├── main.css
    │   ├── animations.css
    │   └── responsive.css
    ├── js/
    │   ├── main.js
    │   └── calculator.js
    ├── fonts/
    ├── images/
    └── doc/                  # App association JSON files
```

---

## Development

### Asset cache busting

Styles and scripts use `filemtime()` for versioning in `spendvest_scripts()`, so changes to CSS/JS are picked up without manual version bumps.

### Constants

| Constant | Value |
|----------|-------|
| `SPENDVEST_VERSION` | `1.0.0` |
| `SPENDVEST_DIR` | Absolute path to the theme directory |
| `SPENDVEST_URI` | Theme directory URL |

### Useful filters

| Filter | Description |
|--------|-------------|
| `spendvest_thank_you_url` | Override the thank-you page URL |
| `spendvest_dl_deep_link` | Customize the `spendvest://` deep link |
| `spendvest_dl_app_store_url` | Apple App Store fallback URL |
| `spendvest_dl_play_store_url` | Google Play Store fallback URL |

### After deployment

1. Activate the theme (or re-save Permalinks) so `/dl/`, `/ref/`, and `/.well-known/` routes are registered.
2. Verify Contact Form 7 form ID matches the Beehiiv integration in `functions.php` (currently form `121`).
3. Confirm ACF field groups are present and assigned to the correct templates/options pages.
4. Test the waitlist flow end-to-end: form submit → thank-you page → Meta Pixel Lead event.

---

## Credits

- **Font:** Inter (see `assets/fonts/`)
- **Animation:** [GSAP](https://greensock.com/gsap/) via jsDelivr CDN

---

## License

This theme is licensed under the [GNU General Public License v2 or later](http://www.gnu.org/licenses/gpl-2.0.html).
