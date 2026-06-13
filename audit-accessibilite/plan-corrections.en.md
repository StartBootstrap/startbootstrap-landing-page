# Remediation plan — WCAG 2.2 AA accessibility

Companion document to `audit-accessibilite.en.html`. For each issue it details **the file to change**, a **before / after** snippet, and the **rationale**.

## How to apply

This theme is compiled: the sources live in `src/` (Pug + SCSS) and the output in `dist/`. **Edit the sources, then recompile** (`npm run build`) rather than editing `dist/` directly, otherwise the changes are overwritten on the next build.

| Type of change | Source file |
|---|---|
| HTML structure, ARIA, attributes, heading order | `src/pug/index.pug` |
| Colors, contrast, masthead overlay | `src/scss/sections/_footer.scss`, `_masthead.scss`, `src/scss/variables/_colors.scss` |

Recommended order: **critical → major → minor** (see end of document).

---

## 1 — Email fields without a label · `CRITICAL`
**File:** `src/pug/index.pug` · **Criteria:** 1.3.1, 3.3.2, 4.1.2

Add a real label to each field. To keep the current look, hide it visually with the Bootstrap `visually-hidden` class (it stays readable by screen readers). While here, also add `required` (see issue 12).

**Before** (masthead, ~line 58):
```pug
input#emailAddress.form-control.form-control-lg(type='email' placeholder='Email Address' data-sb-validations='required,email')
```

**After:**
```pug
label.visually-hidden(for='emailAddress') Email address
input#emailAddress.form-control.form-control-lg(type='email' placeholder='Email Address' required autocomplete='email' aria-describedby='emailHelpTop' data-sb-validations='required,email')
```

Do the same for the second form (`#emailAddressBelow`, ~line 175) with `for='emailAddressBelow'`.

*Why:* a `placeholder` is not a label — it disappears on input and is not reliably exposed. `for`/`id` creates the expected programmatic association.

---

## 2 — Social icon links with no name · `CRITICAL`
**File:** `src/pug/index.pug` (footer, ~lines 223-231) · **Criteria:** 2.4.4, 4.1.2

**Before:**
```pug
li.list-inline-item.me-4
    a(href='#!')
        i.bi-facebook.fs-3
li.list-inline-item.me-4
    a(href='#!')
        i.bi-twitter.fs-3
li.list-inline-item
    a(href='#!')
        i.bi-instagram.fs-3
```

**After:**
```pug
li.list-inline-item.me-4
    a(href='https://facebook.com/your-page' aria-label='Facebook (new window)' rel='noopener')
        i.bi-facebook.fs-3(aria-hidden='true')
li.list-inline-item.me-4
    a(href='https://twitter.com/your-account' aria-label='Twitter (new window)' rel='noopener')
        i.bi-twitter.fs-3(aria-hidden='true')
li.list-inline-item
    a(href='https://instagram.com/your-account' aria-label='Instagram (new window)' rel='noopener')
        i.bi-instagram.fs-3(aria-hidden='true')
```

*Why:* `aria-label` gives the link an accessible name; `aria-hidden` on the icon avoids any duplicate.

---

## 3 — Footer contrast · `MAJOR`
**File:** `src/scss/sections/_footer.scss` · **Criterion:** 1.4.3

Measured: links **4.27:1** and copyright **4.45:1** on a `#f8f9fa` background (threshold 4.5:1). Force darker shades in the footer.

**Add:**
```scss
footer.footer {
    a {
        color: #0a58ca;            // darker blue — 6.1:1 (verified)
        &:hover { color: #094bb0; }
    }
    .text-muted {
        color: #515860 !important; // darker grey — 6.8:1 (verified)
    }
}
```

*Why:* raise contrast above 4.5:1 for normal text. (Bootstrap utility classes need `!important` here to be overridden.)

---

## 4 — Missing `<main>` landmark · `MAJOR`
**File:** `src/pug/index.pug` · **Criterion:** 1.3.1

Wrap all the content (from the masthead through the call-to-action, **excluding** `nav` and `footer`) in an identified `main`.

**Target structure:**
```pug
nav.navbar...
    // ...

main#main
    header.masthead
        // ...
    section.features-icons...
    section.showcase...
    section.testimonials...
    section#signup.call-to-action...

footer.footer...
```

In Pug, just indent the relevant blocks under `main#main`. *Why:* provides the "main content" landmark and the target for the skip link (issue 5). Also clears the axe `region` warning.

---

## 5 — Skip link · `MAJOR`
**File:** `src/pug/index.pug` (very start of `body`) + `src/scss/_global.scss` · **Criterion:** 2.4.1

**Pug — first child of `body`:**
```pug
body
    a.visually-hidden-focusable.skip-link(href='#main') Skip to main content
    nav.navbar...
```

**SCSS:**
```scss
.skip-link {
    position: absolute;
    top: 0; left: 0;
    z-index: 2000;
    padding: .5rem 1rem;
    background: #fff;
    color: #0a58ca;
}
```

*Why:* lets keyboard / screen-reader users skip the navigation. The `visually-hidden-focusable` class (Bootstrap) only reveals the link on focus.

---

## 6 — Heading hierarchy · `MAJOR`
**File:** `src/pug/index.pug` · **Criterion:** 1.3.1

Never skip a level after the single masthead `h1`.

| Location | Before | After |
|---|---|---|
| The 3 feature titles (~l. 94, 100, 106) | `h3` | `h2` |
| The 3 testimonial names (~l. 139, 144, 150) | `h5` | `h3` |

Example (features):
```pug
// Before
h3 Fully Responsive
// After
h2 Fully Responsive
```
```pug
// Before
h5 Margaret E.
// After
h3 Margaret E.
```

*Why:* the testimonials section already has an `h2`; its names logically become `h3`. If the heading styling changes, adjust it in SCSS rather than via the heading level.

---

## 7 — Fake-disabled "Submit" button · `MAJOR`
**File:** `src/pug/index.pug` (~lines 65 and 182) · **Criteria:** 4.1.2, 1.4.3

**Before:**
```pug
button#submitButton.btn.btn-primary.btn-lg.disabled(type='submit') Submit
```

**After:**
```pug
button#submitButton.btn.btn-primary.btn-lg(type='submit' disabled) Submit
```

*Why:* the `disabled` attribute genuinely disables the button and is announced "dimmed/disabled", unlike the `.disabled` class (purely visual — the button stayed keyboard-activable). The SB Forms script re-enables the button when the form becomes valid. (Also rename the second button's `id` — see issue 10.)

---

## 8 — Testimonial alt text · `MINOR`
**File:** `src/pug/index.pug` (~lines 138, 143, 149) · **Criterion:** 1.1.1

**Before:**
```pug
img.img-fluid.rounded-circle.mb-3(src='assets/img/testimonials-1.jpg', alt='...')
```

**After (decorative images, recommended):**
```pug
img.img-fluid.rounded-circle.mb-3(src='assets/img/testimonials-1.jpg', alt='')
```

The person's name is already in text right after; an empty `alt` avoids the redundancy and the "dot dot dot" read aloud. If these photos must be informative, use `alt='Portrait of Margaret E.'` instead.

---

## 9 — Decorative icons not hidden · `MINOR`
**File:** `src/pug/index.pug` (~lines 93, 99, 105) · **Criterion:** 1.1.1

**Before / After:**
```pug
i.bi-window.m-auto.text-primary                  // before
i.bi-window.m-auto.text-primary(aria-hidden='true')  // after
```
Same for `bi-layers` and `bi-terminal`. *Why:* these icons carry no information; remove them from the accessibility tree.

---

## 10 — Duplicate identifiers · `MINOR`
**File:** `src/pug/index.pug` (second form, ~lines 182-200) · **Robustness (former 4.1.1)**

Rename the duplicate `id`s of the call-to-action form:

| Before | After |
|---|---|
| `#submitButton` | `#submitButtonFooter` |
| `#submitSuccessMessage` | `#submitSuccessMessageFooter` |
| `#submitErrorMessage` | `#submitErrorMessageFooter` |

*Why:* unique `id`s are required for reliable `label/for` and ARIA associations and for script targeting.

---

## 11 — Placeholder links `href="#!"` · `MINOR`
**File:** `src/pug/index.pug` (navbar ~l. 30, footer ~l. 210-216) · **Criterion:** 2.4.4

Replace each `href='#!'` with a real URL (or an existing `#section`). If there is no destination page, remove the link rather than leaving a dead anchor. Example:
```pug
a(href='/about') About   // instead of a(href='#!') About
```

---

## 12 — Native fallback validation · `MINOR`
**File:** `src/pug/index.pug` · **Criterion:** 3.3.1

The `required` attributes (added in issue 1) and `type='email'` provide native browser error identification, independent of the SB Forms script. Make sure the visible error message is not only `text-white` on a light background: the `.invalid-feedback.text-white` block is only legible on the dark masthead/CTA — acceptable here, but reconsider if reused on a light background.

---

## 13 — Masthead contrast overlay · `MINOR`
**File:** `src/scss/sections/_masthead.scss` (~line 21) · **Criterion:** 1.4.3

The white text depends on the image. Strengthen the overlay to guarantee ≥ 4.5:1 regardless of the photo:

**Before:**
```scss
&:before {
    background-color: mix($gray-900, $primary, 75%);
    opacity: 0.5;
}
```
**After:**
```scss
&:before {
    background-color: mix($gray-900, $primary, 75%);
    opacity: 0.6;   // more opaque overlay, safety margin
}
```

---

## 14 — Metadata · `MINOR (best practice)`
**File:** `src/pug/index.pug` (~lines 8-11) · **SEO / quality**

**Before:**
```pug
meta(name='description', content='')
meta(name='author', content='')
title Landing Page - Start Bootstrap Theme
```
**After:**
```pug
meta(name='description', content='Your value proposition in one clear sentence.')
meta(name='author', content='Your organization')
title Your brand — Generate more leads
```

If the audience is, say, French-speaking, also switch `html(lang='en')` to the right language and translate the content.

---

## Execution summary

| Priority | Issues | Estimated effort |
|---|---|---|
| 🔴 Critical | 1, 2 | ~30 min |
| 🟠 Major | 3, 4, 5, 6, 7 | ~1 h 30 |
| 🔵 Minor | 8, 9, 10, 11, 12, 13, 14 | ~1 h |

**After fixing:**
1. `npm install` (if needed) then `npm run build` to regenerate `dist/`.
2. Re-test: automated scan (axe / Lighthouse), full keyboard navigation, and ideally a screen reader (VoiceOver or NVDA).
3. Check that no new contrast regression appears.

> Status: all 14 fixes have been applied to the source, the project was rebuilt, and re-testing shows 0 axe violations with all contrast checks passing. The VoiceOver pass was validated. See `audit-avant-apres.en.html` for the before/after comparison.
