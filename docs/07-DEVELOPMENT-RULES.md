# Development Rules

Status: implementation standard

## Naming and ownership

- PHP functions/options/meta use prefix `_archdome_` where private storage is intended and `archdome_` for functions/hooks.
- Text domain: `archdome-portal`.
- Block namespace: `archdome`.
- CSS classes: component-oriented `archdome-*`; WordPress core classes remain intact.
- JavaScript modules use one behavior per file and `data-*` hooks rather than visual class names.
- `functions.php` only loads focused modules.

## PHP and WordPress

- Follow WordPress PHP coding standards and support the Composer PHP floor (8.3).
- Protect theme PHP files from unintended direct execution where they are not template entry points.
- Use hooks, template hierarchy, `WP_Query`, taxonomy/meta/settings/media APIs, and prepared core APIs.
- Never flush rewrites during a normal request.
- Never edit WordPress core, vendor packages, `.env`, or unrelated Bedrock configuration.
- Avoid side effects at file-load time beyond registering hooks.

## Input and output security

- Validate type/range/allowlist before sanitizing saved input.
- Require current-user capability and a purpose-specific nonce for admin mutations.
- Escape late with `esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`, or the correct contextual function.
- Never trust block attributes, query parameters, metadata, options, REST requests, or integration responses.
- Form destinations and secrets are server-owned; do not expose credentials or privileged IDs.

## Translation and direction

- All UI strings use translation functions with `archdome-portal`; dynamic content is not wrapped as a source string.
- Translator comments precede placeholder strings where context is unclear.
- Use numbered placeholders when translation can reorder values.
- Use `language_attributes()`, `is_rtl()`, and locale-aware number/date formatting.
- Prefer CSS logical properties. Physical properties require a comment explaining the genuinely physical relationship.
- Maintain `languages/archdome-portal.pot` using an approved extraction command/process.

## HTML, CSS, and JavaScript

- Semantic HTML and native controls come first.
- One H1 per rendered page in normal templates; headings follow content hierarchy.
- Mobile-first CSS uses tokens; do not hardcode repeated color/spacing/type values.
- Visible `:focus-visible` states are mandatory; never remove outlines without a stronger replacement.
- Vanilla JS progressively enhances complete server HTML.
- Disclosures use buttons and accurate `aria-expanded`; Escape and focus behavior are intentional.
- Respect reduced motion and avoid scroll/resize work without throttling or observers.

## Assets

- Enqueue through WordPress; do not print ad-hoc script/style tags in templates.
- Block assets are metadata-declared and conditional.
- Shared assets use deterministic versioning.
- Images use registered sizes, dimensions, `srcset`, and appropriate loading priority.
- Fonts are local only after license/weights/subsets are approved.

## Git and change discipline

No repository currently exists. Before production development, initialize or connect the intended repository and approve branch/review conventions. Expected workflow: one scoped phase/change, review diff, run checks, update decisions/docs where behavior changed, then commit. Never commit secrets, uploads, cache, built dependencies not defined as deploy artifacts, or local IDE files.

## Required checks per phase

1. PHP syntax on every changed PHP file.
2. Coding-style command when configured.
3. Focused automated tests and full available suite.
4. File/enqueue/block metadata existence.
5. Duplicate function and text-domain searches.
6. Hardcoded user-facing string and physical-direction searches.
7. Secret and unrelated-file review.
8. Browser console, keyboard, responsive RTL/LTR, and empty-state checks where a runnable page exists.

Never claim a check passed unless it ran. Record unavailable tools and exact manual checks.

## Prohibited patterns

Monolithic `functions.php`, direct database access without justification, unbounded queries, raw meta output, language-specific URL concatenation, matching translation IDs by assumption, duplicated Arabic/English templates, frontend framework introduction, global jQuery, inline production credentials, silent plugin installation, fake approved content, and placeholder directories without a current use.

