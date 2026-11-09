# Architectural Decision Log

Only approved decisions should move from Proposed to Accepted. Dates use ISO 8601.

## ADR-001: Bedrock with a classic PHP theme

- Date: 2026-09-20
- Status: Accepted by PRD
- Decision: Use existing Bedrock/Composer, WordPress, native template hierarchy, structured WordPress data, native Gutenberg blocks, PHP rendering, scoped CSS, and vanilla JavaScript.
- Alternatives: Sage/Blade, page builders, headless frontend.
- Consequences: Smaller dependency surface and direct WordPress conventions; the team owns PHP templates and block tooling.

## ADR-002: Existing theme identity

- Date: 2026-09-20
- Status: Accepted
- Decision: Retain folder `archdome_portal`, display name “Archdome Portal,” function prefix `archdome_`, block namespace `archdome`, and text domain `archdome-portal`.
- Alternatives: Rename folder to `archdome` to match early PRD examples.
- Rationale: The explicit project path and implementation prompt repeatedly identify `archdome_portal`.
- Consequences: Deployment scripts and documentation must use the retained folder name.

## ADR-003: Arabic-first bilingual model

- Date: 2026-09-20
- Status: Implemented
- Decision: Arabic `ar`/RTL is default; English `en`/LTR is secondary. Use one semantic theme and plugin-owned translation relationships.
- Alternatives: Separate sites/themes or custom multilingual storage.
- Implementation: Arabic and English are configured in Polylang, Arabic is the default, translated pages and menus are linked, and language-root home URLs render directly in the correct direction.

## ADR-004: Multilingual plugin

- Date: 2026-09-20
- Status: Implemented
- Decision: Prefer Polylang for a lean native workflow unless WPML-specific translation-management requirements justify WPML.
- Alternatives: WPML; custom system (rejected); no translated-content management (insufficient for release).
- Consequences: Polylang is a required Composer-managed runtime dependency; editorial and deployment workflows must preserve translation relationships and language settings.
- Implementation: Polylang 3.8 is Composer-managed and active. Arabic and English are configured; Arabic is default. The theme registers domain post types/taxonomies and renders switcher URLs through Polylang APIs.

## ADR-005: Locations as taxonomy

- Date: 2026-09-20
- Status: Accepted
- Decision: Start `location` as a hierarchical shared taxonomy.
- Alternatives: Rich `location` CPT.
- Consequences: Efficient filtering/shared vocabulary; promote to CPT before content migration if unique modular location pages become mandatory.

## ADR-006: Insights use core Posts

- Date: 2026-09-20
- Status: Accepted
- Decision: Present core Posts as News/Insights.
- Alternatives: Separate `insight` CPT.
- Consequences: Uses mature WordPress editorial features and avoids duplicate post-like models.

## ADR-007: Provisional visual direction

- Date: 2026-09-20
- Status: Accepted as the implementation baseline; official asset replacement remains allowed
- Decision: Use PRD purple/off-white palette, large photography, restrained portal-line motif, IBM Plex Sans Arabic, and Manrope pending verification/approval.
- Alternatives: Official brand typography/palette when supplied.
- Consequences: Foundation may use provisional tokens/system fallbacks; visual finalization waits for official assets and license/performance review.

## ADR-008: Planning gate before production code

- Date: 2026-09-20
- Status: Applied from PRD
- Decision: Complete the PRD-required planning set and obtain review before production code.
- Alternatives: Begin foundation immediately per pasted implementation prompt.
- Rationale: The prompt declares the PRD primary, and the PRD explicitly prohibits production code before planning review.
- Consequences: Phase 03 starts after approval; prevents architecture from being invented during implementation.

## ADR-009: Integration neutrality

- Date: 2026-09-20
- Status: Accepted by PRD
- Decision: Do not install/configure form, CRM, maps, analytics, consent, SEO, cache/CDN, or spam services without approval.
- Consequences: Define adapters/fallbacks; integration-dependent features remain gated.

## ADR-010: Theme foundation baseline

- Date: 2026-09-20
- Status: Implemented
- Decision: Activate `archdome_portal` 0.1.0 with a modular PHP bootstrap, semantic shell, accessible progressively enhanced navigation, provisional token system, core templates, and translation template.
- Alternatives: Continue using Twenty Twenty-Five until all feature phases are complete.
- Rationale: The foundation is independently useful, follows the approved architecture, and does not require domain or integration decisions.
- Consequences: Official branding, multilingual content relationships, CPTs, metadata, blocks, and feature templates remain later phases; provisional visual tokens must not be treated as final brand approval.

## ADR-011: Structured content and global blocks baseline

- Date: 2026-09-20
- Status: Implemented
- Decision: Register `development` and `property`, six shared taxonomies, registered REST-visible metadata, secure native admin panels, and the first five server-rendered global blocks.
- Consequences: Domain archives/singles and development/property query blocks can now depend on stable identifiers. Media IDs use native attachments; richer media pickers remain a later enhancement.

## Pending human approvals

1. Official logo assets and final self-hosted font files under the accepted visual baseline (ADR-007).
2. Permalink migration and final bilingual slug strategy.
3. Controlled status vocabularies and final public pricing rules.
4. Every integration/provider and associated privacy/security policy.
5. Approved Arabic/English production content, media, and legal copy.
