# MRZ Theme Experience : règles pour l'IA

Thème WordPress classique de l'Agence Double A. Le guide d'utilisation est dans `README.md`.

## Principes
- Éditeur classique partout (Gutenberg désactivé par filtres dans `inc/setup.php`).
- Les pages se construisent avec le champ ACF Flexible Content `mrz_builder` (groupe `group_mrz_builder`, dans `acf-json/`).
- Le texte (blog, pages simples) passe par `the_content()` et la classe `.mrz-prose`.
- ACF Pro est obligatoire. Sans lui, les pages affichent seulement leur contenu classique.

## Construire une page
Remplir `mrz_builder` avec `update_field( 'mrz_builder', $rows, $post_id )`. Chaque ligne porte `acf_fc_layout` :

| Layout | Champs | Note |
| --- | --- | --- |
| `hero` | title, subtitle, image (ID), height (auto, large, full), align (center, left), buttons[] (link, style primary/outline) | En premier : active le header transparent si l'option est cochée |
| `text_image` | title, content (HTML), button (link), image_position (right, left), image (ID) | |
| `cards` | title, columns (2, 3, 4), intro, items[] (image, title, link, text) | |
| `gallery` | title, columns, images (IDs) | Liens `data-mrz-lightbox` pour la lightbox V2 |
| `cta` | title, text, button (link) | |
| `faq` | title, items[] (question, answer HTML) | Accordéon `details`, sans JS |
| `text` | title, content (HTML) | |
| `form` | title, form_id (ID Gravity Forms), intro | |
| `posts` | title, list_id (liste MRZ Display Post), intro | Shortcode `[mrzdpe]` |
| `map` | title, map_id (carte MRZ Maps), intro | Shortcode `[mrzme]` |
| `global_section` | section (ID `mrz_section`) | |

Tous les layouts sauf `global_section` ont un groupe `settings` : background (none, light, dark, primary), spacing (normal, compact, large, none), width (container, wide, full), anchor, background_image (ID).

Un champ `link` vaut `array( 'url' => '', 'title' => '', 'target' => '' )`.

## CSS
- Tailwind v4 compile seulement le thème parent : `npm run build`. Le CSS compilé `dist/main.css` est commité.
- Variables de design dans `src/css/tokens.css` (`@theme static`). Classes générées : `bg-primary`, `text-muted`, `font-title`, `rounded-base`, `py-section`…
- Les composants sont en CSS dans `src/css/components.css`, préfixe `.mrz-`, dans `@layer components`.
- **Thème enfant : CSS classique uniquement.** Redéfinir les variables dans `:root`. Ne jamais écrire de nouvelle classe Tailwind dans un template de l'enfant : elle n'existe pas dans `dist/main.css`.
- Une classe Tailwind utilisée dans le parent doit apparaître en entier dans un fichier scanné (pas de nom construit par concaténation).

## Options
- Customizer natif, panneau « MRZ Theme » (le logo natif y est déplacé, section Header), ouvert par le menu d'admin « MRZ Theme » (`inc/admin.php`), qui porte aussi les sections globales. Pas de page d'options sur mesure : garder les écrans natifs de WordPress. Lecture : `mrz_theme_option( 'nom' )`, valeurs par défaut dans `inc/options.php`.
- Le point de rupture mobile est un réglage : les classes `.mrz-desktop-only` et `.mrz-mobile-only` sont pilotées par du CSS en ligne, pas par Tailwind.

## Sections globales
CPT `mrz_section`. Meta `mrz_section_hook` (emplacement) et `mrz_section_priority`. Hooks du thème : `mrz_before_header`, `mrz_after_header`, `mrz_before_content`, `mrz_after_content`, `mrz_before_footer`, `mrz_after_footer`.

## WooCommerce
Aucune surcharge de template. `woocommerce.php` + hooks dans `inc/woocommerce.php` + `assets/css/woocommerce.css`. Panier et commande : pages en shortcodes classiques `[woocommerce_cart]` et `[woocommerce_checkout]`, pas en blocs.

## Code
- Préfixe `mrz_theme_` (fonctions), `mrz_` (theme_mods, CSS), textdomain `mrz-theme-exp`, chaînes source en anglais.
- Conventions WordPress, échappement de toutes les sorties, `wp_enqueue_*` pour tous les assets.
- JS vanilla, sans jQuery, dans `assets/js/` (pas de build JS).
- Surcharges depuis l'enfant : mêmes chemins (`layouts/`, `parts/`).

## Parent ou enfant
- Parent : comportements, accessibilité, JS, intégrations (WooCommerce, Gravity Forms, Polylang, plugins MRZ). Ne pas y ajouter de variantes d'apparence (headers, menus mobiles, effets de survol).
- Enfant : l'apparence du site, en CSS sur les classes `.mrz-` du parent. Copier un template dans l'enfant seulement en dernier recours : la copie ne reçoit plus les corrections du parent.
