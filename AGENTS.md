# MRZ Theme Experience : règles pour l'IA

Thème WordPress classique de l'Agence Double A. Le guide d'utilisation est dans `README.md`.

## Principes
- Éditeur classique partout (Gutenberg désactivé par filtres dans `inc/setup.php`).
- Les pages et les sections globales se construisent avec le champ ACF Flexible Content `mrz_builder` (clé `field_mrz_builder`). Son groupe, `group_mrzchild_builder`, appartient au **thème enfant** (`acf-json/` de l'enfant) : chaque site a ses propres sections.
- Le texte (blog, pages simples) passe par `the_content()` et la classe `.mrz-prose`.
- ACF Pro est obligatoire. Sans lui, les pages affichent seulement leur contenu classique.

## Construire une page
Remplir `mrz_builder` avec `update_field( 'field_mrz_builder', $rows, $post_id )`. Chaque ligne porte `acf_fc_layout`.

### Sections du parent (injectées sur tous les sites)
Déclarées en PHP dans `inc/builder-sections.php`, ajoutées au constructeur de l'enfant au chargement.

| Section | Champs | Note |
| --- | --- | --- |
| `mrz_text` | title, content (HTML), settings | Texte libre |
| `mrz_html` | code | Affiché tel quel, scripts compris ; champ visible seulement avec le droit `unfiltered_html` |
| `mrz_global_section` | section (ID `mrz_section`) | |

`settings` : background (none, light, dark, primary), spacing (normal, compact, large, none), width (container, wide, full), anchor, background_image (ID). Lu par `mrz_theme_section_open()`.

### Sections sur mesure (thème enfant)
Une section par rangée de la maquette. Pour en créer une :
1. Ajouter le layout dans `acf-json/group_mrzchild_builder.json` de l'enfant, sur `field_mrz_builder`. Nom `mrzchild_<nom>`, clés `layout_mrzchild_<nom>` et `field_mrzchild_<nom>_<champ>`, chaque sous-champ avec `parent_layout`. Mettre à jour `modified`.
2. Pour les réglages communs, ajouter un sous-champ `clone` de `group_mrz_section_settings` (`display: seamless`, `prefix_name: 0`).
3. Créer `layouts/mrzchild-<nom>.php` dans l'enfant (le nom, `_` remplacés par `-`) : `mrz_theme_section_open( 'mrzchild-<nom>' )`, le contenu échappé, `mrz_theme_section_close()`.
4. Le style de la section va dans le `style.css` de l'enfant, en CSS classique.

Une section dont le nom contient `hero`, placée en premier, active le header transparent (si l'option est cochée).

Un champ `link` vaut `array( 'url' => '', 'title' => '', 'target' => '' )`. Formulaires, listes MRZ Display Post et cartes MRZ Maps : shortcodes (`[gravityform]`, `[mrzdpe]`, `[mrzme]`) dans un Texte libre ou dans un champ de section.

### Règles du constructeur
- Préfixes : `mrz_` pour le parent, `mrzchild_` pour l'enfant. Jamais de section `mrz_` dans l'enfant.
- La clé `field_mrz_builder` et le nom `mrz_builder` ne changent jamais.
- Une section du parent n'est jamais renommée (nom, clés de layout et de champs) : le contenu des pages y est rattaché.
- Une section du parent n'est jamais supprimée : la retirer du menu, garder son template pour les pages qui l'utilisent.
- Une section de l'enfant qui sert sur trois sites peut remonter dans le parent : renommer en `mrz_` et migrer le contenu des sites concernés.

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
CPT `mrz_section`. Meta `mrz_section_hook` (emplacement) et `mrz_section_priority`. Hooks du thème : `mrz_before_header`, `mrz_after_header`, `mrz_before_content`, `mrz_after_content`, `mrz_footer`, `mrz_before_footer`, `mrz_after_footer`.

### Footer
- Le parent n'a pas de footer par défaut (ni widgets, ni menu de footer, ni copyright). Le footer d'un site est une section globale à l'emplacement `mrz_footer`, que `footer.php` enveloppe dans `<footer class="mrz-footer">`.
- Le modèle d'enfant fournit la section `mrzchild_footer` : logo, réseaux sociaux (icône image + lien + nom), lien de contact, colonnes (titre + liste de liens en WYSIWYG), ligne du bas (WYSIWYG, balises `{year}` et `{site}` via `mrz_theme_text_tags()`).
- Les liens du footer sont écrits dans la section, pas dans des menus WordPress : une section globale se traduit en une fois avec Polylang (type « Global sections » à cocher dans ses réglages), au lieu d'un menu par emplacement et par langue.

## WooCommerce
Aucune surcharge de template. `woocommerce.php` + hooks dans `inc/woocommerce.php` + `assets/css/woocommerce.css`. Panier et commande : pages en shortcodes classiques `[woocommerce_cart]` et `[woocommerce_checkout]`, pas en blocs.

## Code
- Préfixe `mrz_theme_` (fonctions), `mrz_` (theme_mods, CSS), textdomain `mrz-theme-exp`, chaînes source en anglais.
- Conventions WordPress, échappement de toutes les sorties, `wp_enqueue_*` pour tous les assets.
- JS vanilla, sans jQuery, dans `assets/js/` (pas de build JS).
- Icônes SVG dans `assets/icons/`, affichées avec `mrz_theme_icon( 'nom' )` (en ligne, `fill="currentColor"`). L'enfant remplace une icône par un fichier du même nom dans son `assets/icons/`.
- Surcharges depuis l'enfant : mêmes chemins (`layouts/`, `parts/`).
- Le parent ne renomme ni ne supprime ce que les enfants utilisent (fonctions `mrz_theme_*`, classes `.mrz-`, hooks, emplacements de menus, sections du constructeur). Un changement qui casse ces noms impose une version majeure et des notes de release.

## Parent ou enfant
- Parent : comportements, accessibilité, JS, intégrations (WooCommerce, Gravity Forms, Polylang, plugins MRZ). Ne pas y ajouter de variantes d'apparence (headers, menus mobiles, effets de survol).
- Enfant : l'apparence du site, en CSS sur les classes `.mrz-` du parent. Copier un template dans l'enfant seulement en dernier recours : la copie ne reçoit plus les corrections du parent.
