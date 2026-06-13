# Plan de corrections — accessibilité WCAG 2.2 AA

Document compagnon de `audit-accessibilite.html`. Il détaille, pour chaque problème, **le fichier à modifier**, un extrait **avant / après** et la **justification**.

## Principes d'application

Ce thème se compile : les sources sont dans `src/` (Pug + SCSS) et la sortie dans `dist/`. **Modifier les sources puis recompiler** (`npm run build`) plutôt que d'éditer `dist/` directement, sinon les changements seront écrasés au prochain build.

| Type de changement | Fichier source |
|---|---|
| Structure HTML, ARIA, attributs, ordre des titres | `src/pug/index.pug` |
| Couleurs, contraste, voile du masthead | `src/scss/sections/_footer.scss`, `_masthead.scss`, `src/scss/variables/_colors.scss` |

Ordre recommandé : **critiques → majeurs → mineurs** (voir fin de document).

---

## 1 — Champs e-mail sans étiquette · `CRITIQUE`
**Fichier :** `src/pug/index.pug` · **Critères :** 1.3.1, 3.3.2, 4.1.2

Ajouter une vraie étiquette à chaque champ. Pour conserver le visuel actuel, on la masque visuellement avec la classe Bootstrap `visually-hidden` (elle reste lue par les lecteurs d'écran). On en profite pour ajouter `required` (voir problème 12).

**Avant** (masthead, ~ligne 58) :
```pug
input#emailAddress.form-control.form-control-lg(type='email' placeholder='Email Address' data-sb-validations='required,email')
```

**Après :**
```pug
label.visually-hidden(for='emailAddress') Adresse e-mail
input#emailAddress.form-control.form-control-lg(type='email' placeholder='Email Address' required autocomplete='email' aria-describedby='emailHelpTop' data-sb-validations='required,email')
```

Faire de même pour le second formulaire (`#emailAddressBelow`, ~ligne 175) avec `for='emailAddressBelow'`.

*Pourquoi :* un `placeholder` n'est pas une étiquette — il disparaît à la saisie et n'est pas fiablement restitué. `for`/`id` crée l'association programmatique attendue.

---

## 2 — Liens d'icônes sociales sans nom · `CRITIQUE`
**Fichier :** `src/pug/index.pug` (pied de page, ~lignes 223-231) · **Critères :** 2.4.4, 4.1.2

**Avant :**
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

**Après :**
```pug
li.list-inline-item.me-4
    a(href='https://facebook.com/votre-page' aria-label='Facebook (nouvelle fenêtre)' rel='noopener')
        i.bi-facebook.fs-3(aria-hidden='true')
li.list-inline-item.me-4
    a(href='https://twitter.com/votre-compte' aria-label='Twitter (nouvelle fenêtre)' rel='noopener')
        i.bi-twitter.fs-3(aria-hidden='true')
li.list-inline-item
    a(href='https://instagram.com/votre-compte' aria-label='Instagram (nouvelle fenêtre)' rel='noopener')
        i.bi-instagram.fs-3(aria-hidden='true')
```

*Pourquoi :* `aria-label` donne au lien un nom accessible ; `aria-hidden` sur l'icône évite tout doublon.

---

## 3 — Contraste du pied de page · `MAJEUR`
**Fichier :** `src/scss/sections/_footer.scss` · **Critère :** 1.4.3

Mesuré : liens **4.27:1** et copyright **4.45:1** sur fond `#f8f9fa` (seuil 4.5:1). Forcer des teintes plus sombres dans le pied de page.

**Ajouter :**
```scss
footer.footer {
    a {
        color: #0a58ca;            // bleu plus sombre — 6.1:1 (vérifié)
        &:hover { color: #094bb0; }
    }
    .text-muted {
        color: #515860 !important; // gris plus sombre — 6.8:1 (vérifié)
    }
}
```

*Pourquoi :* relever le contraste au-delà de 4.5:1 pour le texte normal. (Les classes utilitaires Bootstrap nécessitent ici `!important` pour être surchargées.)

---

## 4 — Landmark `<main>` manquant · `MAJEUR`
**Fichier :** `src/pug/index.pug` · **Critère :** 1.3.1

Envelopper tout le contenu (du masthead au call-to-action inclus, **hors** `nav` et `footer`) dans un `main` identifié.

**Structure cible :**
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

En Pug, il suffit d'indenter les blocs concernés sous `main#main`. *Pourquoi :* fournit le repère « contenu principal » et la cible du lien d'évitement (problème 5). Corrige aussi l'alerte axe `region`.

---

## 5 — Lien d'évitement · `MAJEUR`
**Fichier :** `src/pug/index.pug` (tout début du `body`) + `src/scss/_global.scss` · **Critère :** 2.4.1

**Pug — premier enfant du `body` :**
```pug
body
    a.visually-hidden-focusable.skip-link(href='#main') Aller au contenu principal
    nav.navbar...
```

**SCSS :**
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

*Pourquoi :* permet aux utilisateurs clavier/lecteur d'écran de sauter la navigation. La classe `visually-hidden-focusable` (Bootstrap) ne révèle le lien qu'au focus.

---

## 6 — Hiérarchie des titres · `MAJEUR`
**Fichier :** `src/pug/index.pug` · **Critère :** 1.3.1

Ne jamais sauter de niveau après le `h1` unique du masthead.

| Emplacement | Avant | Après |
|---|---|---|
| Titres des 3 fonctionnalités (~l. 94, 100, 106) | `h3` | `h2` |
| Noms des 3 témoignages (~l. 139, 144, 150) | `h5` | `h3` |

Exemple (fonctionnalités) :
```pug
// Avant
h3 Fully Responsive
// Après
h2 Fully Responsive
```
```pug
// Avant
h5 Margaret E.
// Après
h3 Margaret E.
```

*Pourquoi :* la section témoignages possède déjà un `h2` ; ses noms deviennent logiquement des `h3`. Si le style des titres change, l'ajuster en SCSS plutôt que par le niveau de titre.

---

## 7 — Bouton « Submit » faussement désactivé · `MAJEUR`
**Fichier :** `src/pug/index.pug` (~lignes 65 et 182) · **Critères :** 4.1.2, 1.4.3

**Avant :**
```pug
button#submitButton.btn.btn-primary.btn-lg.disabled(type='submit') Submit
```

**Après :**
```pug
button#submitButton.btn.btn-primary.btn-lg(type='submit' disabled) Submit
```

*Pourquoi :* l'attribut `disabled` désactive réellement le bouton et est annoncé « désactivé », contrairement à la classe `.disabled` (purement visuelle, le bouton restait activable au clavier). Le script SB Forms réactive le bouton lorsque le formulaire devient valide. (Renommer aussi l'`id` du second bouton — voir problème 10.)

---

## 8 — Texte alternatif des témoignages · `MINEUR`
**Fichier :** `src/pug/index.pug` (~lignes 138, 143, 149) · **Critère :** 1.1.1

**Avant :**
```pug
img.img-fluid.rounded-circle.mb-3(src='assets/img/testimonials-1.jpg', alt='...')
```

**Après (images décoratives, recommandé) :**
```pug
img.img-fluid.rounded-circle.mb-3(src='assets/img/testimonials-1.jpg', alt='')
```

Le nom de la personne est déjà en texte juste après ; un `alt` vide évite la redondance et le « points de suspension » lu à voix haute. Si ces photos doivent être informatives, mettre plutôt `alt='Portrait de Margaret E.'`.

---

## 9 — Icônes décoratives non masquées · `MINEUR`
**Fichier :** `src/pug/index.pug` (~lignes 93, 99, 105) · **Critère :** 1.1.1

**Avant / Après :**
```pug
i.bi-window.m-auto.text-primary                  // avant
i.bi-window.m-auto.text-primary(aria-hidden='true')  // après
```
Idem pour `bi-layers` et `bi-terminal`. *Pourquoi :* ces icônes n'apportent pas d'information ; on les retire de l'arbre d'accessibilité.

---

## 10 — Identifiants dupliqués · `MINEUR`
**Fichier :** `src/pug/index.pug` (second formulaire, ~lignes 182-200) · **Robustesse (ex-4.1.1)**

Renommer les `id` dupliqués du formulaire de l'appel à l'action :

| Avant | Après |
|---|---|
| `#submitButton` | `#submitButtonFooter` |
| `#submitSuccessMessage` | `#submitSuccessMessageFooter` |
| `#submitErrorMessage` | `#submitErrorMessageFooter` |

*Pourquoi :* des `id` uniques sont nécessaires pour des associations `label/for` et ARIA fiables et pour le ciblage par script.

---

## 11 — Liens factices `href="#!"` · `MINEUR`
**Fichier :** `src/pug/index.pug` (navbar ~l. 30, pied de page ~l. 210-216) · **Critère :** 2.4.4

Remplacer chaque `href='#!'` par une vraie URL (ou `#section` existante). À défaut de page de destination, retirer le lien plutôt que de laisser une ancre morte. Exemple :
```pug
a(href='/a-propos') About   // au lieu de a(href='#!') About
```

---

## 12 — Validation native de secours · `MINEUR`
**Fichier :** `src/pug/index.pug` · **Critère :** 3.3.1

Les attributs `required` (ajoutés au problème 1) et `type='email'` fournissent une identification d'erreur native par le navigateur, indépendante du script SB Forms. Vérifier que le message d'erreur visible n'est pas uniquement en `text-white` sur fond clair : le bloc `.invalid-feedback.text-white` n'est lisible que sur le masthead/CTA sombres — acceptable ici, mais à revoir si réutilisé sur fond clair.

---

## 13 — Voile de contraste du masthead · `MINEUR`
**Fichier :** `src/scss/sections/_masthead.scss` (~ligne 21) · **Critère :** 1.4.3

Le texte blanc dépend de l'image. Renforcer le voile pour garantir ≥ 4.5:1 quelle que soit la photo :

**Avant :**
```scss
&:before {
    background-color: mix($gray-900, $primary, 75%);
    opacity: 0.5;
}
```
**Après :**
```scss
&:before {
    background-color: mix($gray-900, $primary, 75%);
    opacity: 0.6;   // voile plus opaque, marge de sécurité
}
```

---

## 14 — Métadonnées · `MINEUR (bonne pratique)`
**Fichier :** `src/pug/index.pug` (~lignes 8-11) · **SEO / qualité**

**Avant :**
```pug
meta(name='description', content='')
meta(name='author', content='')
title Landing Page - Start Bootstrap Theme
```
**Après :**
```pug
meta(name='description', content='Votre proposition de valeur en une phrase claire.')
meta(name='author', content='Votre organisation')
title Votre marque — Générez plus de leads
```

Si l'audience est francophone, penser aussi à passer `html(lang='en')` à `html(lang='fr')` et à traduire les contenus.

---

## Récapitulatif d'exécution

| Priorité | Problèmes | Effort estimé |
|---|---|---|
| 🔴 Critique | 1, 2 | ~30 min |
| 🟠 Majeur | 3, 4, 5, 6, 7 | ~1 h 30 |
| 🔵 Mineur | 8, 9, 10, 11, 12, 13, 14 | ~1 h |

**Après corrections :**
1. `npm install` (si nécessaire) puis `npm run build` pour régénérer `dist/`.
2. Re-tester : scan automatique (axe / Lighthouse), navigation clavier complète, et idéalement un lecteur d'écran (VoiceOver ou NVDA).
3. Vérifier qu'aucune nouvelle régression de contraste n'apparaît.

> Quand tu veux, je peux appliquer ces corrections directement dans les sources et lancer le build — dis-moi par quel niveau de priorité commencer.
