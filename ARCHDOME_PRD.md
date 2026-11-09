# Archdome Real Estate Website — Product Requirements Document

## 1. Document Purpose

This PRD defines the product scope, content architecture, technical direction, and phased planning process for a new WordPress website for **Archdome**, a real estate development company.

The project should follow the strongest architectural patterns of the reference airport website while replacing all airport-specific concepts with a clean real estate domain model. This document is a planning source of truth. It does **not** authorize the AI coding agent to build the website yet.

The immediate output expected from an AI coding agent is a complete set of detailed project-planning Markdown files suitable for use inside an IDE. Code generation starts only after those plans have been reviewed and approved.

---

## 2. Product Vision

Create a premium, responsive, content-managed website that presents Archdome's brand, developments, properties, locations, and insights while enabling potential buyers or investors to discover relevant offerings and submit contextual inquiries.

The website should:

- Present Archdome as a credible and modern real estate developer.
- Make developments and properties easy to browse and understand.
- Give content editors control without requiring code changes for normal updates.
- Support reusable page composition through native Gutenberg blocks.
- Use structured real estate content rather than hardcoded page data.
- Be fast, accessible, SEO-friendly, secure, and maintainable.
- Be organized so an AI coding agent can implement it in controlled phases without architectural drift.

---

## 3. Target Users

- Prospective property buyers.
- Real estate investors.
- Existing clients looking for project information.
- Brokers and business partners.
- Job candidates and media visitors.
- Archdome content editors and website administrators.

---

## 4. Initial Website Scope

### Public pages

- Home
- About Archdome
- Developments archive
- Development details
- Properties archive
- Property details
- Locations
- News / Insights archive
- News / Insight details
- Careers, if approved for the initial release
- Contact
- Privacy Policy and other required legal pages
- 404 page

### Primary content models

- Developments
- Properties
- News / Insights
- Locations
- Property Types
- Amenities
- Property Statuses
- Agents or sales contacts, if required
- Global Site Settings

### Primary user actions

- Browse developments.
- Browse and filter properties.
- View a development or property's full details.
- Explore location and amenity information.
- Submit an inquiry related to a property or development.
- Contact Archdome.
- Read company news and market insights.

---

## 5. Technical Direction

The project should use the following architecture unless a later planning document provides a reviewed and justified change:

```text
Bedrock + Composer
        ↓
WordPress CMS
        ↓
Custom Archdome Classic PHP Theme
        ↓
CPTs + Taxonomies + Registered Post Meta
        ↓
Native Gutenberg Blocks
        ↓
PHP Server Rendering
        ↓
Scoped CSS + Vanilla JavaScript
        ↓
Production Website
```

### Required principles

- Use Roots Bedrock as the WordPress project foundation.
- Use Composer for PHP and WordPress dependency management.
- Build a custom classic PHP theme named `archdome`.
- Use the native WordPress template hierarchy.
- Use native Gutenberg blocks for reusable page sections.
- Render dynamic blocks on the server with PHP.
- Use custom post types, taxonomies, and registered post meta for structured content.
- Keep block editor code, PHP rendering, CSS, and optional frontend JavaScript colocated.
- Use vanilla JavaScript for required interactions unless a reviewed plan proves that a small dependency is necessary.
- Load page and block assets only where they are required.
- Keep content, presentation, and interactive behavior clearly separated.
- Store secrets only in environment configuration; never place credentials in the repository or planning documents.

### Not part of the default architecture

Do not introduce Sage, Blade, ACF, Elementor, React on the public frontend, Tailwind, Bootstrap, SCSS, Vite, Webpack, Bud, or a large JavaScript framework unless the planning phase demonstrates a real requirement and documents the tradeoff.

---

# 6. Twelve-Step Project Plan

## Step 1 — Define the Archdome Product and Information Architecture

Before implementation, define:

- Business goals and measurable outcomes.
- Target audiences and their main journeys.
- Sitemap and navigation hierarchy.
- Required pages and page purposes.
- Primary calls to action.
- Development, property, and inquiry workflows.
- Required languages and RTL behavior.
- Content ownership and editorial roles.
- Required third-party services.
- Phase-one scope versus future enhancements.

### Expected planning output

- Approved sitemap.
- Page inventory.
- User journeys.
- Functional scope.
- Non-functional requirements.
- Assumptions, dependencies, and open questions.

### Exit criteria

No theme or page code is generated until the site scope and information architecture are documented.

---

## Step 2 — Establish the Bedrock and WordPress Foundation

Plan a clean Bedrock installation rather than copying the airport project repository.

Recommended high-level structure:

```text
archdome/
├── composer.json
├── .env.example
├── config/
│   ├── application.php
│   └── environments/
└── web/
    ├── wp/
    └── app/
        ├── themes/
        │   └── archdome/
        ├── mu-plugins/
        ├── plugins/
        └── uploads/
```

The plan must cover:

- Local, staging, and production environments.
- Environment variables and secret handling.
- Composer dependency policy.
- WordPress core and content directory paths.
- URL, HTTPS, and environment configuration.
- Database setup and migration expectations.
- Media storage and backup strategy.
- Git ignore rules and deployable artifacts.

### Exit criteria

The foundation plan must be reproducible and must not contain real credentials.

---

## Step 3 — Design the Custom Theme Foundation

Plan the base `archdome` theme before planning individual pages.

Proposed foundation:

```text
web/app/themes/archdome/
├── style.css
├── functions.php
├── header.php
├── footer.php
├── page.php
├── index.php
├── 404.php
├── archive-development.php
├── single-development.php
├── archive-property.php
├── single-property.php
├── blocks/
└── assets/
    ├── css/
    ├── js/
    ├── fonts/
    └── images/
```

The theme foundation plan must define:

- Theme setup and feature support.
- Template hierarchy.
- Header, navigation, mobile menu, and footer.
- Menu locations.
- Design tokens and CSS custom properties.
- Typography, colors, spacing, grids, containers, and breakpoints.
- Buttons, links, form controls, cards, and shared UI states.
- Asset enqueue and cache-busting rules.
- Image size registration and media policy.
- Naming conventions and file responsibilities.

### Exit criteria

Every future section must be able to reuse one consistent design system and site shell.

---

## Step 4 — Define the Real Estate Content Model

Design all major records and relationships before Gutenberg blocks or page templates.

### Development model

High-level fields may include:

- Name and slug
- Short description
- Full overview
- Featured image and gallery
- Location
- Development type and status
- Key statistics
- Amenities
- Property types
- Master plan
- Brochure
- Map coordinates or embed data
- Related properties
- Inquiry contact context

### Property model

High-level fields may include:

- Name and slug
- Related development
- Property type
- Location
- Price or price-on-request status
- Bedrooms and bathrooms
- Area and measurement unit
- Availability status
- Featured image and gallery
- Floor plan
- Amenities
- Description and specifications
- Assigned agent or sales contact
- Inquiry context

### Relationship rules

- A property should reference its development rather than duplicate development information.
- Developments and properties should reference controlled location records.
- Property types, amenities, and statuses should use controlled taxonomies where appropriate.
- Relationships must have a single source of truth.
- The planning document must decide whether locations and agents require rich CPTs or simpler taxonomies/settings.

### Exit criteria

Produce a content model matrix showing fields, data types, required status, relationships, editorial source, REST exposure, and public visibility.

---

## Step 5 — Plan CPTs, Taxonomies, Meta, and Admin Editing

Plan the WordPress implementation of the content model.

Expected models:

- `development` custom post type.
- `property` custom post type.
- Core Posts presented as News / Insights, unless a separate CPT is justified.
- `property_type` taxonomy.
- `location` taxonomy or CPT, based on the approved content model.
- `amenity` taxonomy.
- `property_status` taxonomy.

Use native WordPress APIs by default:

```php
register_post_type();
register_taxonomy();
register_post_meta();
```

The plan must also define:

- Admin labels and menu organization.
- Capabilities and editorial permissions.
- Meta boxes and validation behavior.
- Sanitization, escaping, and nonce requirements.
- Rewrite slugs and permalink behavior.
- Archive availability.
- REST API exposure.
- Default, empty, and invalid states.

### Exit criteria

The admin content-entry experience must be understandable without requiring editors to modify source code.

---

## Step 6 — Design the Gutenberg Block System

Plan native reusable blocks for editorial page composition.

Initial candidate blocks:

- Hero
- About Archdome
- Featured Developments
- Development Grid
- Featured Properties
- Property Grid
- Property Search / Filter
- Locations
- Statistics
- Amenities
- Image Gallery
- Master Plan
- Testimonials
- News / Insights
- Map / Location
- Call to Action
- Contact / Inquiry

Recommended block structure:

```text
blocks/
└── block-name/
    ├── index.js
    ├── render.php
    ├── style.css
    └── frontend.js      # only when interaction is required
```

For every block, the plan must define:

- Purpose and supported page contexts.
- Attribute schema.
- Editor controls and preview behavior.
- Dynamic query behavior.
- PHP rendering contract.
- Empty, loading, missing-media, and error states.
- Responsive behavior.
- Accessibility requirements.
- Frontend JavaScript needs.
- Asset-loading conditions.
- Acceptance criteria.

### Exit criteria

Each block must have one clear responsibility and must not duplicate structured real estate data inside block attributes.

---

## Step 7 — Plan Dynamic Archive and Single Templates

Plan the query-driven templates after the content model and blocks are defined.

Required templates:

```text
archive-development.php
single-development.php
archive-property.php
single-property.php
home.php or approved Insights templates
single.php
```

### Development archive

Should support an introduction, development cards, relevant filters or categories, pagination, empty results, and a contact CTA.

### Development details

Recommended sequence:

```text
Hero
→ Overview
→ Key Statistics
→ Gallery
→ Available Properties
→ Amenities
→ Master Plan
→ Location / Map
→ Contextual Inquiry CTA
```

### Property archive

Should define filters, URL state, sorting, pagination, mobile filter behavior, query performance, no-results handling, and progressive enhancement.

### Property details

Should define gallery, key specifications, price/availability, development relationship, amenities, floor plan, location, related properties, and contextual inquiry.

### Exit criteria

Every template must have a documented data source, query strategy, hierarchy, responsive behavior, SEO rules, and acceptance criteria.

---

## Step 8 — Plan Homepage Composition

The homepage should be assembled from reusable Gutenberg blocks after the design system, content models, and blocks are defined.

Recommended initial composition:

```text
Hero
→ About Archdome
→ Featured Developments
→ Why Archdome
→ Company Statistics
→ Featured Properties
→ Featured Locations
→ Brand / Vision Story
→ Latest News / Insights
→ Primary CTA
→ Footer
```

The homepage plan must identify:

- Purpose of each section.
- Content source and editor controls.
- Default and fallback content behavior.
- Primary and secondary calls to action.
- Motion and interaction requirements.
- Mobile reordering or simplification.
- Performance priorities for above-the-fold content.

### Exit criteria

The homepage must be a composition of reusable blocks, not one hardcoded monolithic template.

---

## Step 9 — Plan Remaining Pages and Global Content

Create specifications for:

- About Archdome.
- Locations landing and optional location details.
- News / Insights archive and details.
- Contact.
- Careers, if included.
- Privacy and legal pages.
- Search results, if included.
- 404 page.
- Header, footer, and global settings.

Global settings may include:

- Logos and brand assets.
- Contact information.
- Social links.
- Office addresses.
- Footer content.
- Legal links.
- Default inquiry recipient/configuration.
- Approved analytics or tracking insertion points.

### Exit criteria

Each page must reuse the established theme, content, and block systems instead of creating a new architectural pattern.

---

## Step 10 — Plan Interactions and Integrations

Add interaction only after static content and rendering responsibilities are understood.

Potential interactions:

- Property filters and search.
- URL-based filter state.
- Galleries and carousels.
- Mobile navigation.
- Counters and scroll reveals.
- Map interactions.
- Inquiry modal or inline inquiry form.
- Form validation and submission states.

Potential integrations requiring explicit decisions:

- Form provider.
- Email delivery.
- CRM or lead-management platform.
- Maps provider.
- Analytics and consent management.
- Spam protection.
- Multilingual plugin or approach.
- SEO plugin.
- Caching/performance plugin.
- CDN or external media storage.

### Interaction rules

- JavaScript must be small, isolated, and progressively enhanced.
- Do not create one large global interaction file.
- Respect keyboard access and `prefers-reduced-motion`.
- Do not expose secrets in browser code.
- Condition frontend assets on their actual use.
- Document failure, offline, loading, and empty states.

### Exit criteria

Each interaction and integration must have an owner, dependency, data-flow description, privacy impact, security considerations, and acceptance criteria.

---

## Step 11 — Define the AI Implementation Workflow

The AI coding agent must work in controlled phases rather than attempt the entire project in one prompt.

Recommended implementation sequence:

```text
01 Architecture
→ 02 Bedrock Setup
→ 03 Theme Foundation
→ 04 Design System
→ 05 CPTs and Taxonomies
→ 06 Meta Fields and Admin Editing
→ 07 Gutenberg Infrastructure
→ 08 Global Blocks
→ 09 Development Blocks
→ 10 Property Blocks
→ 11 Archive Templates
→ 12 Single Templates
→ 13 Homepage
→ 14 Remaining Pages
→ 15 Interactions and Integrations
→ 16 Responsive QA
→ 17 Accessibility QA
→ 18 SEO and Performance
→ 19 Security and Privacy Review
→ 20 Final Testing and Release
```

For every phase:

```text
Read source-of-truth documents
→ Confirm current phase and dependencies
→ Implement only the approved scope
→ Run checks and test affected flows
→ Fix failures
→ Review against acceptance criteria
→ Update documentation if approved behavior changed
→ Commit
→ Continue only after review
```

### AI operating rules

- Read all project documents before implementation.
- Treat approved planning files as the source of truth.
- Implement one requested phase at a time.
- Inspect existing code before editing.
- Do not silently add frameworks, plugins, packages, patterns, or services.
- Do not modify unrelated completed functionality.
- Do not copy airport-specific names, data, content, icons, or behaviors.
- Do not expose secrets or real environment values.
- Do not claim checks passed unless they were actually run.
- State assumptions and blockers clearly.
- Keep a decision log for approved architectural changes.

### Exit criteria

The implementation roadmap must map every phase to inputs, outputs, dependencies, verification commands, acceptance criteria, and a review checkpoint.

---

## Step 12 — Generate the Detailed Project Plan Documents

Before generating production code, the AI agent must create the following files:

```text
docs/
├── 01-PROJECT-OVERVIEW.md
├── 02-ARCHITECTURE.md
├── 03-DESIGN-SYSTEM.md
├── 04-CONTENT-MODEL.md
├── 05-GUTENBERG-BLOCKS.md
├── 06-PAGE-STRUCTURE.md
├── 07-DEVELOPMENT-RULES.md
├── 08-SEO-PERFORMANCE.md
├── 09-ACCESSIBILITY.md
├── 10-AI-IMPLEMENTATION-PLAN.md
├── 11-INTEGRATIONS-SECURITY.md
├── 12-TESTING-RELEASE.md
└── DECISIONS.md
```

### Required purpose of each document

| File | Required contents |
|---|---|
| `01-PROJECT-OVERVIEW.md` | Vision, goals, users, sitemap, scope, user journeys, assumptions, exclusions, and success criteria |
| `02-ARCHITECTURE.md` | Bedrock structure, runtime flow, theme structure, data flow, environments, dependency boundaries, and architecture decisions |
| `03-DESIGN-SYSTEM.md` | Brand inputs, tokens, typography, color, spacing, layout, breakpoints, shared components, states, and responsive principles |
| `04-CONTENT-MODEL.md` | CPTs, taxonomies, meta fields, relationships, validation, admin editing, global settings, and migration considerations |
| `05-GUTENBERG-BLOCKS.md` | Block inventory, contracts, attributes, controls, rendering, states, accessibility, assets, and acceptance criteria |
| `06-PAGE-STRUCTURE.md` | Sitemap plus section-by-section specifications for every page, archive, and single template |
| `07-DEVELOPMENT-RULES.md` | Coding standards, naming, file boundaries, sanitization, escaping, asset loading, Git workflow, and prohibited patterns |
| `08-SEO-PERFORMANCE.md` | Metadata, schema, canonicals, sitemap, indexing, Core Web Vitals, image/font strategy, caching, budgets, and measurement |
| `09-ACCESSIBILITY.md` | WCAG target, semantics, navigation, forms, focus, contrast, media, reduced motion, and test checklist |
| `10-AI-IMPLEMENTATION-PLAN.md` | Ordered phases, dependencies, prompt boundaries, deliverables, commands, acceptance gates, and rollback points |
| `11-INTEGRATIONS-SECURITY.md` | Forms, CRM, maps, analytics, consent, spam prevention, roles, secrets, security controls, privacy, and retention |
| `12-TESTING-RELEASE.md` | Test matrix, browsers/devices, content QA, regression scope, staging approval, deployment, backup, rollback, and monitoring |
| `DECISIONS.md` | Dated architectural decisions, alternatives considered, selected direction, rationale, and consequences |

### Generation rules

- Read this PRD and the supplied reference architecture before writing the planning documents.
- Inspect the actual new project workspace and identify what already exists.
- Do not generate production feature code during this planning task.
- Do not assume branding, languages, services, plugins, or business rules that are not provided.
- Record unresolved choices under **Open Questions** with a recommended default.
- Distinguish confirmed requirements from recommendations and assumptions.
- Keep all documents consistent with one another.
- Use the exact same names for CPTs, taxonomies, templates, routes, blocks, and fields across every file.
- Add diagrams and tables only where they improve understanding.
- Include measurable acceptance criteria.
- Avoid low-level code dumps.
- Never include secrets, tokens, passwords, private URLs, or `.env` values.
- End with a cross-document consistency review and a prioritized list of decisions requiring human approval.

### Exit criteria

The planning set is complete only when all documents agree, all known requirements are traceable, unresolved questions are visible, and the implementation plan can be executed phase by phase without inventing architecture during coding.

---

## 7. Non-Functional Requirements

### Accessibility

- Target WCAG 2.2 AA.
- Full keyboard operation for navigation, dialogs, galleries, forms, and filters.
- Visible focus states.
- Semantic heading and landmark structure.
- Accessible labels, validation, and status messages.
- Reduced-motion alternatives.
- Sufficient color contrast.

### Performance

- Define measurable Lighthouse and Core Web Vitals targets in the performance plan.
- Prioritize the above-the-fold hero image and avoid unnecessary eager loading.
- Use responsive WordPress images and appropriate dimensions.
- Minimize globally loaded CSS and JavaScript.
- Bound database queries and avoid N+1 query patterns.
- Establish page-weight and JavaScript budgets before implementation.

### SEO

- Human-readable URLs.
- Editable page titles and descriptions.
- Canonical URLs.
- XML sitemap and robots rules.
- Open Graph and social metadata.
- Structured data appropriate to the organization, breadcrumbs, articles, developments, and properties where valid.
- Correct archive pagination and filter indexing rules.

### Security and privacy

- Sanitize input, validate data, escape output, and protect mutations with nonces and permissions.
- Apply least-privilege roles and capabilities.
- Keep WordPress, Composer packages, and approved plugins maintainable and updateable.
- Protect forms against spam and abuse.
- Document personal-data collection, consent, retention, and deletion requirements.
- Never store secrets in theme source, browser code, documentation, or Git.

### Maintainability

- Clear file and naming conventions.
- One responsibility per block or module.
- No duplicated domain data.
- Documented setup, verification, deployment, backup, and rollback procedures.
- Architectural changes require an entry in `DECISIONS.md`.

---

## 8. Out of Scope Until Explicitly Approved

- Property purchasing or reservation transactions.
- Customer accounts or authentication.
- Online payments.
- Broker portals or CRM dashboards.
- Automated inventory synchronization.
- Mobile applications.
- Headless WordPress or a separate SPA frontend.
- A bespoke multilingual engine.
- AI chatbots or recommendation engines.
- 3D virtual tours, unless supplied as an external embed requirement.

These can be planned as future phases but must not be silently added to the initial implementation.

---

## 9. Project-Level Acceptance Criteria

The project can be considered release-ready when:

- Approved pages and content models are implemented.
- Editors can manage developments, properties, insights, and global content without source-code changes.
- Reusable Gutenberg blocks behave correctly in the editor and public site.
- Archives, filters, details, and contextual inquiries work across supported devices.
- Accessibility, SEO, performance, security, and privacy acceptance criteria pass.
- No airport-specific content or implementation assumptions remain.
- Required automated and manual tests pass.
- Staging content and stakeholder review are complete.
- Deployment, backup, monitoring, and rollback procedures are documented and verified.

---

## 10. Master Prompt for the AI Planning Agent

Copy the prompt below into the IDE after placing this PRD and the reference `PROJECT_ARCHITECTURE.md` in the project workspace.

```text
Act as a Principal WordPress Architect, Product Engineer, and Technical Lead.

You are planning a new production WordPress website for Archdome, a real estate development company.

First, read these files completely:

1. ARCHDOME_PRD.md
2. PROJECT_ARCHITECTURE.md

Then inspect the current workspace to determine what files and configuration already exist. Do not make assumptions based only on filenames.

Your task is PLANNING ONLY. Do not generate production feature code, install dependencies, or change runtime configuration.

Create the following planning files under /docs:

- 01-PROJECT-OVERVIEW.md
- 02-ARCHITECTURE.md
- 03-DESIGN-SYSTEM.md
- 04-CONTENT-MODEL.md
- 05-GUTENBERG-BLOCKS.md
- 06-PAGE-STRUCTURE.md
- 07-DEVELOPMENT-RULES.md
- 08-SEO-PERFORMANCE.md
- 09-ACCESSIBILITY.md
- 10-AI-IMPLEMENTATION-PLAN.md
- 11-INTEGRATIONS-SECURITY.md
- 12-TESTING-RELEASE.md
- DECISIONS.md

Follow Step 12 and all other constraints in ARCHDOME_PRD.md.

Preserve the reference project's architectural principles where they suit the real estate product:

- Bedrock and Composer foundation
- Custom classic PHP theme
- Native WordPress template hierarchy
- Structured CPTs, taxonomies, and registered post meta
- Native Gutenberg page composition
- PHP server-rendered dynamic blocks
- Scoped CSS and vanilla JavaScript
- Conditional asset loading

Do not copy airport-specific functionality or content. Do not introduce ACF, Sage, Blade, Elementor, React on the public frontend, Tailwind, Bootstrap, SCSS, or a frontend build system unless an explicit requirement justifies it. If you recommend a deviation, document it as a proposed architectural decision with alternatives, tradeoffs, and required approval; do not apply it silently.

For missing business or brand information:

1. Do not invent facts.
2. Add the item to an Open Questions section.
3. Provide a clearly labeled recommended default when useful.
4. Mark affected specifications as provisional.

Across the documents, use consistent identifiers and terminology for CPTs, taxonomies, meta fields, templates, routes, blocks, settings, and integrations.

Every implementation phase must specify:

- Goal
- Inputs and dependencies
- Exact scope
- Expected files or deliverables
- Verification commands or manual checks
- Acceptance criteria
- Risks and rollback point
- Items that must not be changed

After creating the documents, perform a cross-document consistency review. Report:

- Files created
- Confirmed architectural decisions
- Assumptions made
- Open questions
- Conflicts found and resolved
- Decisions requiring human approval before implementation

Stop after planning. Wait for approval before generating project code.
```

---

## 11. Final Architecture Summary

```text
                         ARCHDOME
                             │
                   Bedrock + Composer
                             │
                       WordPress CMS
                             │
             ┌───────────────┴───────────────┐
             │                               │
      Structured Content              Gutenberg Pages
             │                               │
   ┌─────────┴─────────┐              Reusable Blocks
   │                   │                     │
Developments       Properties           PHP Rendering
   │                   │                     │
Taxonomies          Post Meta          Scoped CSS + JS
   │                   │                     │
   └──────────┬────────┘                     │
              └──────── Theme Queries ───────┘
                              │
                       PHP Templates
                              │
                    Archdome Website
```

The core principle is simple: Bedrock provides the reproducible project foundation, WordPress manages content, the custom theme owns real estate behavior and presentation, Gutenberg controls editorial page composition, PHP renders public output, and JavaScript is used only where interaction genuinely requires it.


Step 13 — Define the Brand and Visual Design System

The website design must reflect Archdome’s existing logo and social-media identity. It should feel premium, architectural, modern, spacious, and suitable for a professional real estate development company.

Brand direction

Use the supplied logo and social-media designs as the primary visual references.

The visual style should communicate:

Premium real estate development.
Modern architectural precision.
Trust and professionalism.
Spaciousness and comfortable living.
Minimal, refined presentation.
Strong consistency between the website and Archdome’s social-media presence.

Avoid overly colorful interfaces, generic corporate templates, crowded layouts, excessive decoration, and visual effects that compete with property photography.

Initial color palette

The supplied references suggest the following approximate palette:

Token	Suggested color	Usage
Primary Purple	#4B2B63	Main brand sections, buttons, headings, navigation states, and key accents
Primary Purple Dark	#352044	Footer, overlays, dark sections, and hover states
Primary Purple Light	#6E4C82	Secondary accents, borders, tags, and subtle backgrounds
Light Gray	#CCCCCC	Section backgrounds and branded neutral surfaces
Warm Off-White	#F3F1EE	Main page background, cards, and light sections
White	#FFFFFF	Text on purple backgrounds and clean content surfaces
Charcoal	#262329	Primary body text on light backgrounds
Muted Gray	#727076	Secondary text, metadata, labels, and placeholders

These values are starting points extracted visually from the supplied brand references. The final values must be verified against the original logo or official brand files before implementation.

Color usage rules
Purple is the primary brand color and should create strong visual recognition.
Use purple for primary calls to action, selected states, important headings, and branded sections.
Use warm off-white or light gray as the dominant page background to keep the experience spacious.
Use dark charcoal for body copy instead of pure black.
Maintain sufficient contrast that meets WCAG 2.2 AA.
Do not use purple for large amounts of small body text on gray backgrounds unless the contrast is verified.
Property photography should remain the strongest visual element.
Avoid adding unrelated bright accent colors unless approved by the client.
Use transparent purple overlays carefully when text appears over photographs.
CSS design tokens

The design plan should define centralized tokens similar to:

:root {
  --color-brand-primary: #4b2b63;
  --color-brand-dark: #352044;
  --color-brand-light: #6e4c82;

  --color-background: #f3f1ee;
  --color-background-muted: #cccccc;
  --color-surface: #ffffff;

  --color-text-primary: #262329;
  --color-text-secondary: #727076;
  --color-text-on-brand: #ffffff;

  --color-border: rgba(75, 43, 99, 0.18);
  --color-overlay: rgba(53, 32, 68, 0.58);
}

The exact values may be refined during design validation, but all components must use centralized tokens rather than hardcoded colors.

Typography direction

Typography should complement the wide, geometric appearance of the Archdome logo.

Use:

A clean geometric sans-serif font for English headings.
A highly readable sans-serif font for English body content.
A compatible modern Arabic font if Arabic is required.
Spacious letter spacing for selected uppercase labels and display headings.
Normal letter spacing for paragraphs and long-form content.
Clear hierarchy between display headings, section headings, card titles, body copy, metadata, and labels.

Do not imitate the logo lettering for long paragraphs. The logo remains a brand asset, while website typography must prioritize readability.

The AI planning agent must recommend actual fonts and confirm:

Web licensing and availability.
English and Arabic compatibility.
Font weights.
Fallback fonts.
Loading strategy.
Performance impact.
RTL appearance.
Layout and spacing

The design should use:

Generous whitespace.
Large architectural images.
Strong alignment and grid structure.
Wide desktop containers.
Clear separation between content sections.
Balanced asymmetric layouts where appropriate.
Simple property and development cards.
Comfortable mobile spacing.
Minimal borders and restrained shadows.

Avoid placing too much content inside a single viewport or card. The design should feel editorial and architectural rather than like a crowded property marketplace.

Image direction

Use high-quality imagery as a central design element:

Architectural exteriors.
Interior spaces.
Master plans and floor plans.
Lifestyle photography.
Location imagery.
Construction and project-progress photography.

Image treatments should include:

Large full-width or split-layout presentations.
Consistent aspect ratios for listing cards.
Clear floor-plan presentation without unnecessary cropping.
Subtle purple overlays only when required for readable text.
Responsive WordPress image sizes.
Meaningful alternative text.
Optimized formats and lazy loading below the fold.

Floor plans must remain clear and legible. Users should be able to enlarge them when necessary.

Component styling direction
Buttons
Primary: purple background with white text.
Secondary: transparent or light background with a purple border.
Dark-section alternative: white background with purple text.
Include clear hover, focus, active, loading, and disabled states.
Avoid excessive rounding unless supported by the final brand direction.
Property and development cards
Use large imagery and clear content hierarchy.
Display only important summary information.
Use purple for controlled accents rather than covering the entire card.
Maintain consistent image ratios.
Include accessible links and visible focus states.
Avoid heavy shadows and excessive metadata.
Forms
Use clean, spacious fields.
Clearly distinguish labels from placeholders.
Use purple for focus and confirmation states.
Provide visible validation messages.
Ensure inquiry forms show the related property or development context.
Header and navigation
Use a clean, minimal header.
Display the official Archdome logo without modifying its proportions.
Support a transparent variant over suitable hero imagery and a solid variant after scrolling.
Use clear active, hover, and keyboard-focus states.
Ensure the mobile menu maintains the same premium visual identity.
Footer
A deep-purple footer is recommended.
Use the light or white logo variation.
Include navigation, contact details, social links, and legal links.
Maintain sufficient spacing and avoid overcrowding.
Motion and interaction direction

Motion should be subtle and architectural:

Gentle image reveals.
Controlled fade and vertical transitions.
Smooth but short navigation transitions.
Refined gallery and carousel movement.
Minimal hover feedback on cards and buttons.

Avoid excessive parallax, continuous animation, dramatic rotations, or effects that reduce usability. All motion must respect prefers-reduced-motion.

Responsive design requirements

The design must be planned for:

Large desktop.
Standard desktop and laptop.
Tablet landscape and portrait.
Mobile devices.
English LTR and Arabic RTL layouts if Arabic is included.

Responsive behavior must be intentionally designed rather than created by simply stacking desktop elements. Typography, spacing, imagery, navigation, galleries, filters, floor plans, and forms should be reviewed at every breakpoint.

Required design deliverables

The AI planning agent must document:

Final verified color palette.
Typography system.
Spacing scale.
Grid and container system.
Responsive breakpoints.
Button and form styles.
Card patterns.
Header and footer variants.
Image and floor-plan treatments.
Interaction and motion principles.
English and Arabic behavior.
Accessibility requirements.
Design tokens and component states.
Examples of correct and incorrect brand usage.
Exit criteria

The design phase is complete when:

The final colors are verified against official brand assets.
Typography is approved for all required languages.
Desktop, tablet, mobile, LTR, and RTL behavior is defined.
All shared components have documented states.
Color contrast meets WCAG 2.2 AA.
The visual language matches Archdome’s logo and social-media identity.
The design system can guide every page and Gutenberg block without introducing inconsistent styles.
