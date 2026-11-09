# Testing and Release

Status: release quality plan

## Test layers

| Layer | Scope |
|---|---|
| Static | PHP syntax/style, metadata/schema, translations, asset references, secret scans |
| Unit/integration | Sanitizers, validation, queries, permissions, render callbacks, migrations |
| WordPress admin | Activation, editor panels, media, blocks, revisions, roles, bilingual workflow |
| Frontend functional | Navigation, filters, pagination, forms, galleries, search, 404, failures |
| Accessibility | Automated + keyboard + zoom + screen reader + RTL/LTR manual checks |
| Visual/responsive | Representative pages/content at agreed widths, browsers, directions |
| SEO/performance | Status/canonical/schema/sitemap and budgets/Core Web Vitals diagnostics |
| Security/privacy | Abuse, authorization, escaping, secrets, retention/consent/provider failures |

## Browser/device baseline

Proposed: current and previous major Chrome, Edge, Firefox, and Safari; current iOS Safari and Android Chrome. Confirm client analytics/support obligations before release. Test at 320, 375/390, 768, 1024, 1280, 1440, and wide desktop widths where useful, plus zoom/reflow rather than treating widths as device guarantees.

## Content fixtures

Maintain non-production Arabic and English test content covering long/short titles, missing media, mixed-direction values, all statuses/types, zero and large numeric values, unavailable price, galleries/floor plans, orphaned relationships, empty archives, multiple pages, and provider errors. Do not present fixtures as approved company facts.

## Regression scope

Each phase retests global shell, both directions, responsive layout, console/PHP logs, content save/render, relevant archives/singles/blocks, and performance-sensitive paths. Changes to tokens, shared helpers, queries, localization, or integrations trigger broader regression.

## Staging acceptance

- Production-like runtime and configuration, indexing disabled, HTTPS enforced.
- Approved content migration and media complete.
- Stakeholders review Arabic and English content/design/workflows.
- No severe console/PHP errors, broken links/assets, unhandled empty states, or placeholder content.
- Accessibility, SEO, performance, security, and privacy gates documented.
- Forms/email/CRM/maps/analytics tested with approved sandbox or controlled destinations.

## Deployment runbook

1. Confirm approvals, maintenance/communication plan, artifact, environment variables, and access.
2. Capture verified database/media/config backups and test restore procedure.
3. Put integrations in controlled mode if required.
4. Deploy the reviewed artifact and run Composer production install/process.
5. Run idempotent migrations, controlled rewrite flush if required, and cache invalidation.
6. Smoke-test home, archives, singles, search/404, both languages, forms, assets, admin login, and logs.
7. Enable indexing/analytics only after production validation and approval.
8. Monitor errors, uptime, deliverability, performance, and lead delivery.

Exact commands depend on hosting and must be documented before release.

## Rollback

Rollback triggers include fatal errors, broken editorial access, widespread rendering/navigation failure, data corruption, security/privacy exposure, failed lead delivery, or material performance regression. Restore the previous immutable artifact/config, reverse only explicitly reversible migrations, restore verified database/media backup when necessary, purge caches, smoke-test, and document the incident. Never improvise destructive database rollback.

## Release checklist

- Approved artifact and change log.
- Backup/restore verified and rollback owner present.
- Database/media migration rehearsed.
- DNS/TLS/canonical/site URL correct.
- Indexing/robots/sitemaps/hreflang correct.
- Forms and notifications reach controlled recipients.
- Analytics/consent behavior matches approval.
- Security headers/cache policies reviewed at infrastructure layer.
- Monitoring, alerting, ownership, and post-release observation window agreed.
- Content freeze/sign-off recorded.

## Current tool constraints

WP-CLI is not available on PATH and no Git repository exists in the inspected project tree. These do not block writing plans but must be resolved or replaced with documented alternatives before a reliable implementation/release workflow.

