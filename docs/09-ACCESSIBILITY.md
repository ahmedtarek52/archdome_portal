# Accessibility

Target: WCAG 2.2 Level AA

## Baseline requirements

- Semantic landmarks, one primary page heading, and logical heading order.
- A visible skip link moves focus to the main content.
- All functions work by keyboard without traps; focus order follows meaning.
- Focus indicators are visible, unobscured, and sufficiently distinct.
- Text and meaningful UI meet AA contrast; non-text controls/states meet applicable contrast.
- Text can resize/reflow without loss at required zoom and narrow widths.
- Status/error/loading changes are announced appropriately without excessive interruption.
- Motion has a reduced-motion path; no unsafe flashing.
- Arabic screen order and spoken labels remain natural; mixed-direction values are isolated correctly.

## Component behavior

### Navigation

Use a real navigation landmark and lists. Dropdown and mobile controls are buttons with accessible names and accurate expanded state. Escape closes an open surface and returns focus when appropriate. Current page is exposed with `aria-current="page"`. Pointer hover is never the only route.

### Forms

Every field has a persistent programmatic label. Instructions precede the field or are referenced with `aria-describedby`. Errors identify the field and corrective action; submission summaries receive focus or an appropriate live announcement. Required/invalid state is not color-only. Autocomplete tokens are used for personal data where applicable. Do not clear valid input after a failed submission.

### Filters

GET filters work without JavaScript. Controls have visible labels and a clear reset. Results announce updated counts after enhanced submission, focus does not jump unexpectedly, and browser history/URL state stays meaningful.

### Galleries and dialogs

Native scrolling/grid is preferred to a carousel. If a dialog is approved, it has a label, close control, focus containment/return, Escape support, background inertness, and correct media alternatives. Carousel controls expose purpose and current position and pause movement; autoplay is off by default.

### Cards and media

Avoid multiple duplicate links to the same destination. Decorative images use empty alt; informative images have concise purpose-based alt maintained in the media library. Floor-plan alternatives explain material information not otherwise available. Captions are associated with their media.

### Maps

A map never becomes the only way to obtain an address/location. Provide textual address/directions, keyboard-safe controls, a consent/loading fallback, and a link to the external map when approved.

## RTL/LTR checklist

- Document `lang` and `dir` match active locale.
- Visual and DOM order remain coherent in both directions.
- Only directional icons mirror.
- Phone, email, URLs, references, coordinates, prices, and measurements read correctly in mixed content.
- Arabic labels, errors, headings, navigation, pagination, breadcrumbs, and dialogs are tested with realistic copy.
- No tracking/letter spacing is applied to Arabic text.

## Content guidance

Editors need guidance for heading structure, meaningful links, image alternatives, transcript/caption needs, table headers, plain-language error text, and avoiding text embedded in images. Empty alt is a deliberate editorial choice, not a missing value.

## Test matrix

- Keyboard-only pass at 320 px, desktop, 200% zoom, and 400% reflow where applicable.
- Screen readers: NVDA + current Firefox/Chrome on Windows; VoiceOver + Safari on macOS/iOS if in supported matrix; TalkBack + Chrome on Android if supported.
- Automated checks: axe-core or approved equivalent plus HTML validation; manual review remains required.
- Forced-colors/high-contrast, reduced motion, text spacing, orientation, touch targets, and focus-not-obscured checks.
- Arabic and English passes on every representative template and interactive state.

## Definition of done

No known WCAG A/AA failure remains without a documented, approved exception and remediation date. Automated scans have no serious/critical unresolved issues, and manual keyboard, zoom, screen-reader, direction, errors, and content checks are recorded.

