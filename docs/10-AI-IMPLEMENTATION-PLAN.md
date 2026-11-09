# AI Implementation Plan

Status: ordered execution plan; each phase requires review before architecture-dependent continuation

## Operating loop

For every phase: re-read relevant plans, inspect current files, state exact scope/files, implement only that scope, run available checks, fix failures, compare acceptance criteria, report changes/assumptions/unavailable tests, update `DECISIONS.md` for approved changes, and preserve a rollback point.

## Phases

| # | Phase | Dependencies | Deliverables | Acceptance gate / rollback |
|---:|---|---|---|---|
| 01 | Architecture/planning | PRD, actual workspace | This `docs/` set | Cross-doc consistency and human approval; remove docs if rejected |
| 02 | Bedrock readiness | Phase 01 | Environment/deploy checklist; no secret changes | Composer validate/install audit; revert scoped config only |
| 03 | Theme foundation | 01–02, logo/font provisional allowed | Theme metadata, loader, setup, localization, shell, nav, base assets, `theme.json`, fallbacks, POT | PHP syntax, activation/manual shell, RTL/LTR, keyboard nav; switch back to prior theme |
| 04 | Design system | Brand assets/font approval | Final tokens, fonts, shared components/states | Contrast/responsive visual approval; revert asset commit |
| 05 | CPTs/taxonomies | Content model approval | Registrations, labels, rewrite/REST policy | Admin/REST/permalink tests; unregister/revert before content entry |
| 06 | Meta/admin editing | Fields/business rules | Registered meta, panels, validation, migrations/tests | Save/auth/sanitize/revision tests; DB backup + versioned rollback |
| 07 | Gutenberg infrastructure | Theme/design foundations | Registration loader, category, shared editor utilities | Insert/save/render smoke tests |
| 08 | Global blocks | 07 | Hero, About, Statistics, Insights, CTA | Per-block acceptance/asset tests |
| 09 | Development blocks | 05–08 | Featured/grid, amenities, gallery/master plan/location as approved | Query/empty/a11y/RTL tests |
| 10 | Property blocks | 05–09 | Featured/grid/search/filter and contextual blocks | GET fallback, sanitization, performance tests |
| 11 | Archive templates | Models/blocks | Development/property/Insights archives, pagination/filter state | Main-query, empty, SEO, RTL/mobile tests |
| 12 | Single templates | Meta/archives | Development/property/Insight singles | Relationship/media/missing-data tests |
| 13 | Homepage | Global/query blocks | Approved block composition and content setup guide | LCP, responsive, editorial tests |
| 14 | Remaining pages | Shell/blocks | About, Locations, Contact, Search, 404, legal, approved Careers | Per-page data/empty/accessibility tests |
| 15 | Integrations | Provider/privacy decisions | Inquiry, email/CRM, map, analytics/consent adapters | Sandbox/failure/privacy/security tests; feature flags/adapter rollback |
| 16 | Responsive QA | Feature complete | Defect fixes across matrix | Recorded visual/interaction pass |
| 17 | Accessibility QA | 16 | Audit and remediations | `09-ACCESSIBILITY.md` definition of done |
| 18 | SEO/performance | 15–17 | Metadata/schema/caching/media optimizations | `08-SEO-PERFORMANCE.md` targets |
| 19 | Security/privacy | Complete integrations | Threat/privacy review and fixes | No unresolved critical/high issues; owner approval |
| 20 | Release | All gates | Staging sign-off, backup/deploy/rollback/monitoring | Release checklist approved; restore prior artifact/DB backup |

## Phase 03 expected files

`style.css`, `functions.php`, `theme.json`, `header.php`, `footer.php`, `index.php`, `page.php`, `404.php`, focused files under `inc/`, global template parts, base/component/layout CSS, navigation JS, and `languages/archdome-portal.pot`. Create only those actively used.

Foundation must not include CPTs, taxonomies, meta fields, final homepage, complete domain templates, filters, galleries, maps, forms/inquiry integrations, large animation systems, or plugins.

## Standard verification commands

Adapt to available tooling and report omissions:

```text
composer validate --strict
composer lint
composer test
php -l <each changed PHP file>
```

Add targeted searches for text domain, hardcoded UI strings, physical CSS direction properties, duplicate prefixed functions, secrets, and invalid asset paths. Use a browser for activation, console, keyboard, responsive, RTL/LTR, and visual checks when available.

## Decisions that block later phases

- Phase 04 finalization: official brand assets and fonts.
- Translated content, switcher, and translated URLs: Polylang/WPML decision.
- Phase 06: field definitions and editorial workflow.
- Phase 10 filters: approved searchable attributes and public price rules.
- Phase 15: form/email/CRM, map, analytics/consent, spam, and retention decisions.
- Release: content, legal, browser support, hosting, deployment, backup, and ownership approvals.

## Items never changed silently

Bedrock/runtime versions, environment configuration, dependencies/plugins, identifiers/slugs, data schema, multilingual architecture, public URLs, analytics/privacy behavior, or previously approved visual tokens.

