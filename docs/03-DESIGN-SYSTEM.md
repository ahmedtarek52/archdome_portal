# Design System

Status: provisional pending official brand assets and font approval  
Direction: Arabic-first, premium architectural restraint

## Design concept

The interface uses calm masonry-like planes, generous negative space, and large project photography. Its signature is a restrained “portal line”: a thin, offset purple frame used around selected hero imagery and focus moments, echoing an architectural opening without turning into decoration. It must never compete with photography or readability.

## Color tokens

These values come from the PRD’s approximate visual extraction and are not final brand values.

| Token | Value | Role |
|---|---:|---|
| `--color-brand-primary` | `#4b2b63` | Primary actions and selected states |
| `--color-brand-dark` | `#352044` | Footer, dark surfaces, hover |
| `--color-brand-light` | `#6e4c82` | Controlled secondary accent |
| `--color-canvas` | `#f3f1ee` | Main background |
| `--color-muted-surface` | `#cccccc` | Limited neutral surface |
| `--color-surface` | `#ffffff` | Cards/content surfaces |
| `--color-ink` | `#262329` | Primary text |
| `--color-muted-ink` | `#727076` | Secondary text where contrast passes |
| `--color-on-brand` | `#ffffff` | Text on dark brand surfaces |
| `--color-border` | `rgb(75 43 99 / 18%)` | Dividers and controls |
| `--color-overlay` | `rgb(53 32 68 / 58%)` | Image text protection |
| `--color-error` | provisional | Requires contrast validation |
| `--color-success` | provisional | Requires contrast validation |

Do not publish a final palette until colors are sampled from official assets and all foreground/background pairs are checked against WCAG 2.2 AA.

## Typography

Provisional recommendation:

- Arabic display/body: IBM Plex Sans Arabic, weights 400/500/600/700.
- Latin display: Manrope, weights 500/600/700.
- Latin body/utility: system sans stack or Manrope 400/500 when approved.
- Fallbacks: `Tahoma, Arial, sans-serif` for Arabic; `Arial, sans-serif` for Latin.

Final use depends on license verification, Arabic review, approved weights, WOFF2 availability, and page-weight impact. Arabic text must use normal letter spacing, generous line height, natural shaping, and realistic content during QA.

```css
:root {
  --font-family-display-arabic: "IBM Plex Sans Arabic", Tahoma, Arial, sans-serif;
  --font-family-body-arabic: "IBM Plex Sans Arabic", Tahoma, Arial, sans-serif;
  --font-family-display-latin: Manrope, Arial, sans-serif;
  --font-family-body-latin: Manrope, Arial, sans-serif;
  --step--1: clamp(.83rem, .81rem + .1vw, .9rem);
  --step-0: clamp(1rem, .96rem + .2vw, 1.125rem);
  --step-1: clamp(1.25rem, 1.13rem + .6vw, 1.6rem);
  --step-2: clamp(1.56rem, 1.32rem + 1.2vw, 2.25rem);
  --step-3: clamp(1.95rem, 1.48rem + 2.3vw, 3.25rem);
  --step-4: clamp(2.44rem, 1.65rem + 3.9vw, 4.75rem);
}
```

Body text targets 1.65–1.85 line height in Arabic and 1.55–1.7 in English. Display line heights should remain at least 1.1 and be tested for multi-line Arabic headings.

## Spacing, grid, and breakpoints

- Spacing scale: 4, 8, 12, 16, 24, 32, 48, 64, 96, 128 px expressed as `rem` tokens.
- Content container: `min(100% - 2 * var(--gutter), 90rem)`.
- Reading measure: approximately 65 Latin characters; verify equivalent comfortable Arabic measure visually.
- Gutter: 20 px small, 32 px medium, 48–64 px large.
- Breakpoint guidance: 30rem, 48rem, 64rem, 80rem. Components should break when content requires, not merely at device labels.
- Layout: 4 columns mobile, 8 tablet, 12 desktop; CSS Grid/Flexbox and logical properties.

## Component rules

### Header and navigation

The header supports solid and transparent visual variants without changing semantics. The logo keeps its intrinsic ratio. Desktop dropdowns work by keyboard and pointer. The mobile disclosure uses a native button with `aria-expanded`, manages focus predictably, closes on Escape, and does not trap focus unless implemented as a true modal.

### Buttons and links

Primary buttons use brand purple/white; secondary buttons use a transparent surface and purple border; dark surfaces may use white/purple. Each has hover, focus-visible, active, loading, and disabled states. Minimum target size is 44 by 44 CSS px where practical. Underlines or another non-color cue distinguish inline links.

### Cards

Project/property cards prioritize a consistent image ratio, concise title, location, essential facts, and one clear link target. Avoid nested interactive controls and excessive shadow. Focus must be visible around the actual interactive element.

### Forms

Labels remain visible. Help, error, and success text is programmatically associated. Required state is communicated in text, not color alone. Inputs are comfortable in Arabic and English, with `dir="ltr"` applied only to isolated email, telephone, reference, URL, or coordinate values.

### Images and plans

Photography is the dominant visual asset. Listing imagery uses consistent aspect ratios and responsive sources. Floor plans use contain behavior, retain legibility, and provide an accessible enlargement flow only after the gallery/dialog phase is approved.

## Directionality

- One semantic DOM adapts through the document `dir` value.
- Prefer `margin-inline`, `padding-inline`, `inset-inline`, `border-inline`, and `text-align: start/end`.
- Mirror only directional icons (next, previous, breadcrumbs, forward/back).
- Do not mirror logos, media controls, maps, or non-directional icons.
- Isolate LTR values with a semantic wrapper and `dir="ltr"`; do not reverse strings manually.

## Motion

Motion is brief and architectural: disclosure, opacity, and small block-axis movement where it explains change. No continuous animation or decorative parallax. `prefers-reduced-motion: reduce` removes nonessential movement and smooth scrolling.

## Correct and incorrect usage

| Correct | Avoid |
|---|---|
| Purple concentrated in actions and signature surfaces | Purple on every card and heading |
| Large imagery with protected text contrast | Text directly over busy images |
| Natural Arabic shaping and wrapping | Letter spacing or manual reversal in Arabic |
| Restrained portal-line motif on selected moments | Frames around every component |
| Logical CSS and direction-aware icons | Separate RTL/LTR templates |
| Token-based values | Repeated hardcoded colors and spacing |

## Approval gate

Before implementation is considered visually final, approve logo files, verified colors, font licenses/files and weights, photography direction, header variant, and representative Arabic/English responsive compositions.

