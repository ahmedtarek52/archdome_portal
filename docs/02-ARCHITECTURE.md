# Architecture

Status: proposed implementation architecture  
Last updated: 2026-09-20

## Runtime model

```text
Request -> web/index.php -> Bedrock configuration -> WordPress
        -> template hierarchy -> Archdome theme modules
        -> WP_Query / metadata / taxonomies / block renderer
        -> semantic HTML + scoped CSS + small vanilla JS
```

Bedrock owns dependency and environment configuration. WordPress owns users, editorial content, media, routing, and the block editor. The classic theme owns presentation and real-estate-specific behavior. Secrets remain in environment configuration.

## Existing foundation

- Project root: `C:/laragon/www/archdome`
- Web root: `web`
- WordPress core: `web/wp`
- Content directory: `web/app`
- Theme: `web/app/themes/archdome_portal`
- WordPress: Composer package `roots/wordpress` 7.1.1
- PHP: Composer requires 8.3+; local CLI is 8.4.20
- Existing runtime configuration follows standard Bedrock environment loading.

No production/runtime Bedrock change is required for the theme-foundation phase. The real `.env` must never be copied into documentation or Git.

## Theme structure

```text
archdome_portal/
  style.css                 Theme metadata only
  functions.php             Focused module loader
  theme.json                Editor tokens and settings
  header.php footer.php
  index.php page.php home.php single.php search.php 404.php
  archive-development.php single-development.php
  archive-property.php single-property.php
  inc/
    setup.php assets.php helpers.php localization.php security.php
    post-types/ taxonomies/ meta/ blocks/ admin/
  template-parts/
    global/ cards/ content/
  blocks/<block-name>/
    block.json index.js render.php style.css editor.css [frontend.js]
  assets/css/ assets/js/ assets/fonts/ assets/icons/ assets/images/
  languages/archdome-portal.pot
  docs/
```

Only files needed by the active phase should be created.

## Dependency boundaries

- Use WordPress APIs instead of direct SQL when a suitable API exists.
- No Sage, Blade, ACF, Elementor, public React app, Tailwind, Bootstrap, jQuery, SCSS, or bundler by default.
- Gutenberg editor scripts may use APIs shipped by WordPress and declare dependencies through `block.json`/registered handles.
- Third-party services are adapters at integration boundaries, not embedded across templates.
- A multilingual plugin owns translation relationships. Theme code uses core locale, URL, and translation APIs and optional documented compatibility adapters.

## Data flow

1. Editors create CPT records, assign controlled taxonomies, and save registered metadata.
2. WordPress validates capabilities/nonces; callbacks sanitize and validate values.
3. Templates and dynamic blocks query canonical records by stable IDs and taxonomy terms.
4. Renderers escape every value for its output context.
5. CSS and optional JS load only on templates or blocks that need them.
6. Caches may optimize derived output but never become the source of truth.

## Environments and deployment

| Environment | Purpose | Indexing | Debug display | Deployment expectation |
|---|---|---|---|---|
| Local/development | Build and test | Off | Development config only | Composer install + local DB/media |
| Staging | Content and release QA | Off | Off | Production-like artifact/config |
| Production | Public site | On when approved | Off | Immutable deploy + backups/rollback |

Database and uploads are environment data, not deploy artifacts. Schema/content migrations must be idempotent and backed up. Production Composer installs should use an authoritative lock file, optimized autoloading, and no dev packages.

## Routing and locale

- Arabic is intended as default (`ar`, RTL); English is secondary (`en`, LTR).
- Use `language_attributes()`, `is_rtl()`, `get_locale()`, translated strings, and logical CSS properties.
- Never concatenate language prefixes or assume translated posts share IDs.
- Switcher URLs must come from the approved multilingual plugin.
- Rewrite slugs require a bilingual permalink decision; translated slugs must not be hardcoded in theme queries.

## Asset strategy

- Register versioned shared assets from file modification time in development and theme version in production.
- Load navigation JS only when the header is rendered; block assets use `block.json` fields.
- Use WordPress responsive images; only the true LCP candidate may load eagerly/high priority.
- Self-host fonts only after family, license, subsets, and weights are approved; preload at most critical WOFF2 files.

## Failure behavior

Missing images use a neutral semantic placeholder only where one aids comprehension. Empty queries show a translated next step. Missing related records omit the dependent section rather than emitting broken links. Integration failures preserve entered data where safe and present an accessible status message.

## Architecture conflicts and resolutions

- PRD examples use theme name `archdome`; the explicit project/prompt path is `archdome_portal`. Proposed resolution: keep the existing folder and use display name “Archdome Portal.”
- PRD requires plans and approval before code; the implementation prompt requests immediate foundation code. Resolution: complete and review this planning gate first because the PRD is declared primary.
- `PROJECT_ARCHITECTURE.md` is referenced but absent. This document derives only from the PRD and inspected Bedrock project; supply it before implementation if it contains additional constraints.

