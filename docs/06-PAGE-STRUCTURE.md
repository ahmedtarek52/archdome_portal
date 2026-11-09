# Page and Template Structure

Status: proposed information architecture

## Global shell

Every public page includes skip link, semantic header/navigation, main landmark with one primary heading, and footer. Breadcrumbs appear on interior content where they aid orientation. Language switcher behavior waits for the multilingual plugin; its reserved location must not create a broken control.

## Page specifications

| Page/template | Purpose | Recommended sequence | Primary data |
|---|---|---|---|
| Home | Brand and portfolio gateway | Hero -> About -> Featured developments -> Why Archdome -> Statistics -> Featured properties -> Locations -> Brand story -> Insights -> CTA | Gutenberg blocks + queries |
| About | Trust and company story | Intro -> vision/values -> evidence -> leadership/history if supplied -> CTA | Page blocks |
| Developments archive | Browse projects | Intro -> filters if justified -> cards -> pagination -> CTA | Main `development` query |
| Development single | Explain one project | Hero -> overview -> key stats -> gallery -> properties -> amenities -> master plan -> location -> inquiry | CPT/taxonomy/meta |
| Properties archive | Find a suitable property | Intro -> GET filters/sort -> result summary -> grid -> pagination -> CTA | Main `property` query |
| Property single | Evaluate and enquire | Gallery -> title/status/price -> specs -> development -> description -> amenities -> floor plan -> location -> related -> inquiry | CPT/taxonomy/meta |
| Locations | Explore areas | Intro -> location cards/list -> related projects -> CTA | `location` terms and queries |
| Insights home | Browse editorial content | Intro -> featured/latest -> categories -> pagination | Core Posts |
| Insight single | Read/share an article | Article header -> media -> content -> related -> CTA | Core Post |
| Contact | Reach Archdome | Intro -> contact routes/offices -> approved form -> map if approved | Page/settings/integration |
| Careers | Recruitment route | Intro -> culture -> openings/provider -> contact | Approval required |
| Search | Find public content | Query heading -> grouped/list results -> pagination | WordPress search |
| Legal | Publish approved policy | Title -> last-updated -> content | Page |
| 404 | Recover from missing URL | Clear message -> search/nav links -> key destinations | Static UI |

## Template mapping

| Context | Template |
|---|---|
| Fallback | `index.php` |
| Standard page | `page.php` |
| Insights archive | `home.php` |
| Insight single | `single.php` |
| Search | `search.php` |
| Not found | `404.php` |
| Development archive/single | `archive-development.php`, `single-development.php` |
| Property archive/single | `archive-property.php`, `single-property.php` |

Reusable parts belong under `template-parts/global`, `template-parts/cards`, and `template-parts/content`. Templates coordinate; helpers/query functions own nontrivial logic.

## Archive behavior

- Use the main query whenever practical; alter approved filters through `pre_get_posts` with strict public-query and post-type guards.
- Filters submit with GET and remain usable without JavaScript.
- Only known taxonomy terms, numeric ranges, order values, and pagination values are accepted.
- Canonical/index rules for filtered URLs follow `08-SEO-PERFORMANCE.md` and the selected SEO integration.
- Pagination uses WordPress APIs, exposes current state, and preserves approved filter parameters.
- Empty results explain what happened and offer a filter reset/contact path.

## Single-template rules

- Omit absent optional sections rather than output blank headings.
- Relationship queries are bounded and exclude the current post.
- Media uses attachment metadata and responsive image APIs.
- Phone, email, reference, price, measurement, and coordinates use isolated direction when needed.
- Contextual inquiry includes a server-validated record identifier, never trusts a client-supplied email recipient, and provides a non-JS fallback.

## SEO and schema ownership

Templates provide semantic structure and breadcrumb data hooks. An approved SEO layer owns titles, descriptions, canonical tags, Open Graph, XML sitemap policy, and most JSON-LD. Theme schema must not duplicate plugin output. Only defensible properties may be represented as structured data.

## Responsive behavior

Content priority—not DOM reversal—controls mobile layouts. Cards become fewer columns, filters use an accessible disclosure, media remains prominent, data tables scroll or reflow with preserved headers, and headings wrap naturally in Arabic and English.

## Missing-content behavior

Missing featured media uses layout that does not reserve a broken image. Missing price shows the approved “price on request” state only when explicitly set. Missing location/map omits the section. Draft/private related records never appear publicly.

## Acceptance criteria

Each page has an explicit data source, single H1, logical landmarks/headings, translated UI, empty state, responsive rules, focus behavior, SEO ownership, and test cases. No template embeds production claims or fake company content.

