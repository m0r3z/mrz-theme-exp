# MRZ Theme Experience : règles pour l'IA

Thème WordPress classique de l'Agence Double A. Le guide d'utilisation est dans `README.md`.

## Principes
- Éditeur classique partout (Gutenberg désactivé par filtres dans `inc/setup.php`).
- Les pages et les sections globales se construisent avec le champ ACF Flexible Content `mrz_builder` (clé `field_mrz_builder`). Son groupe, `group_mrzchild_builder`, appartient au **thème enfant** (`acf-json/` de l'enfant) : chaque site a ses propres sections.
- Le texte (blog, pages simples) passe par `the_content()` et la classe `.mrz-prose`.
- ACF Pro est obligatoire. Sans lui, les pages affichent seulement leur contenu classique.

## Lighthouse : 100 partout
Chaque site vise **100 sur les quatre scores Lighthouse** (Performance, Accessibilité, Bonnes pratiques, SEO), en mobile. À chaque choix, prendre la solution la plus simple et la plus rapide à afficher.
- **CSS avant JS.** Pas de bibliothèque, pas de jQuery, pas de CDN. Un module JS (carrousel, lightbox) ne se charge que sur les pages qui s'en servent.
- **Image principale (LCP)** : une vraie image `<img>` avec `'fetchpriority' => 'high'` et `'loading' => 'eager'`, jamais une image de fond. Toutes les autres par `wp_get_attachment_image()` (dimensions, `srcset`, chargement différé).
- **Aucun décalage de mise en page (CLS)** : images avec leurs dimensions, polices en woff2 auto-hébergées avec `font-display: swap`, le moins de fichiers de police possible.
- **Accessibilité** : contraste suffisant (réglage « Texte sur … » si la charte l'impose), texte alternatif sur chaque image porteuse de sens, un seul `<h1>`, liens et boutons au texte explicite, champs avec un `<label>`.
- **SEO** : Rank Math pose le titre et la meta description. Le contenu des pages est dans les champs ACF, donc l'extrait automatique est vide : **écrire la meta description de chaque page** dans Rank Math.
- Hors du thème : durée de cache, CSS critique et formats WebP/AVIF relèvent de WP Rocket et de l'hébergement. En local, ces alertes de Lighthouse sont normales.

## Accessibilité : RGAA
Le thème et chaque site suivent les critères du RGAA : https://accessibilite.numerique.gouv.fr/methode/criteres-et-tests/. Un 100 en accessibilité sur Lighthouse n'en couvre qu'une petite partie : le reste se vérifie à la main.
- **Images** (thème 1) : texte alternatif pertinent pour une image porteuse de sens, `alt=""` pour une image décorative, jamais de texte important dans une image.
- **Couleurs** (thème 3) : contraste d'au moins 4,5:1 pour le texte (3:1 pour un grand texte et les composants d'interface) ; une information n'est jamais donnée par la couleur seule.
- **Liens** (thème 6) : intitulé explicite, ou complété par un texte pour lecteur d'écran (« Lire la suite » ne suffit pas seul).
- **Scripts et clavier** (thèmes 7 et 12) : tout se fait au clavier, focus toujours visible, ordre de tabulation logique, composants JS avec les rôles et états ARIA attendus.
- **Structure** (thèmes 8 et 9) : `lang` de la page juste, titres hiérarchisés sans saut, listes en `<ul>`/`<ol>`, zones `<header>`, `<nav>`, `<main>`, `<footer>`.
- **Formulaires** (thème 11) : chaque champ a une étiquette visible et liée, erreurs explicites. Gravity Forms : jamais le balisage hérité (« legacy markup ») ; étiquettes visibles, pas de simple texte indicatif dans le champ.
- **Contenu en mouvement** (thème 13) : rien ne défile tout seul sans bouton pause ; animations coupées avec `prefers-reduced-motion`.
- Pas de mention « conforme RGAA » ni de déclaration d'accessibilité sur un site, sauf demande d'Aurélien (clients publics).

## Construire une page
Remplir `mrz_builder` avec `update_field( 'field_mrz_builder', $rows, $post_id )`. Chaque ligne porte `acf_fc_layout`.

### Sections du parent (injectées sur tous les sites)
Déclarées en PHP dans `inc/builder-sections.php`, ajoutées au constructeur de l'enfant au chargement.

| Section | Champs | Note |
| --- | --- | --- |
| `mrz_text` | title, title_tag (`h2` par défaut, `h1` si la section ouvre la page), content (HTML, shortcodes compris), settings | Texte libre |
| `mrz_html` | code | Affiché tel quel, scripts compris ; champ visible seulement avec le droit `unfiltered_html` |
| `mrz_global_section` | section (ID `mrz_section`) | |

`settings` : background (none, light, dark, primary, secondary, tertiary, quaternary), spacing (normal, compact, large, none), width (container, narrow pour une largeur de lecture, wide, full), anchor, background_image (ID). Lu par `mrz_theme_section_open()`, qui accepte aussi `'class'` : plusieurs classes séparées par des espaces, ou un tableau.

### Sections sur mesure (thème enfant)
Une section par rangée de la maquette. Pour en créer une :
1. Ajouter le layout dans `acf-json/group_mrzchild_builder.json` de l'enfant, sur `field_mrz_builder`. Nom `mrzchild_<nom>`, clés `layout_mrzchild_<nom>` et `field_mrzchild_<nom>_<champ>`, chaque sous-champ avec `parent_layout`. Mettre à jour `modified`.
2. Pour les réglages communs, ajouter un sous-champ `clone` de `group_mrz_section_settings` (`display: seamless`, `prefix_name: 0`).
3. Créer `layouts/mrzchild-<nom>.php` dans l'enfant (le nom, `_` remplacés par `-`) : `mrz_theme_section_open( 'mrzchild-<nom>' )`, le contenu échappé, `mrz_theme_section_close()`.
4. Le style de la section va dans le fichier de sa partie, `css/<partie>.css` de l'enfant, en CSS classique et uniquement avec les variables du site (voir CSS).

Header transparent : automatique quand l'option est cochée et que la première section a `hero` dans son nom. Pour une autre section, ou pour le forcer ou le couper sur une page : meta box « MRZ Theme » de la page (`_mrz_transparent_header` à `on` ou `off`). Ne pas renommer une section pour l'obtenir.
Sous un header transparent, la première section reçoit en haut `--spacing-section` + `--mrz-header-offset` (place du header). Un hero dont l'image est dans le flux remet son `padding-top` à 0 et décale son texte de `var(--mrz-header-offset)`.

Un champ `link` vaut `array( 'url' => '', 'title' => '', 'target' => '' )`, affiché par `mrz_theme_button( $link, $style )` : `primary`, `outline` ou `link` (lien souligné suivi d'une flèche). Formulaires, listes MRZ Display Post et cartes MRZ Maps : shortcodes (`[gravityform]`, `[mrzdpe]`, `[mrzme]`) dans un Texte libre, qui les affiche sans filtrer leur balisage. Dans un champ de section sur mesure : `do_shortcode( wp_kses_post( $champ ) )`, jamais `wp_kses_post()` après le shortcode (il retire les `<script>` et les champs de formulaire).

### Code d'une section sur mesure (PHP)
- **Échapper toute sortie** : `esc_html()` pour un texte, `esc_url()` pour un lien, `esc_attr()` pour un attribut, `wp_kses_post()` pour un champ WYSIWYG. Jamais un `echo get_sub_field()` brut (sauf la section du parent `mrz_html`).
- **Images** : `wp_get_attachment_image( $id, $taille )`, jamais une balise `<img>` écrite à la main (WordPress ajoute `srcset`, dimensions et `loading`). Un texte alternatif vide (`'alt' => ''`) pour une image décorative.
- **Image principale d'un hero** : une vraie image (`<img>`, `object-fit: cover`) avec `'fetchpriority' => 'high'` et `'loading' => 'eager'`, pas une image de fond CSS ni le réglage « image de fond » : une image de fond se charge tard et pénalise le chargement de la page (LCP).
- **Aucun texte visible écrit en dur dans un template de l'enfant** : tout texte (titre, bouton, « Voir plus », texte pour lecteur d'écran) vient d'un champ ACF, qui se traduit avec la page dans chaque langue. Un `aria-label` se construit à partir d'un champ (le titre de la section, par exemple). L'enfant n'a pas de fichier de traduction.
- **Titres** : un seul `<h1>` par page, porté par la première section (souvent le hero) ; les autres sections commencent à `<h2>`.
- **Liens** : `target="_blank"` toujours avec `rel="noopener"`. Un lien sans texte visible (icône) porte un texte pour les lecteurs d'écran (`<span class="screen-reader-text">`).
- **Ouvrir et fermer la section** avec `mrz_theme_section_open( 'mrzchild-<nom>' )` et `mrz_theme_section_close()` : réglages communs, ancre et conteneur sont gérés par le parent.
- **Variables PHP préfixées** `$mrzchild_` dans les templates (portée globale de `get_template_part`).

### Règles du constructeur
- Préfixes : `mrz_` pour le parent, `mrzchild_` pour l'enfant. Jamais de section `mrz_` dans l'enfant.
- La clé `field_mrz_builder` et le nom `mrz_builder` ne changent jamais.
- Une section du parent n'est jamais renommée (nom, clés de layout et de champs) : le contenu des pages y est rattaché.
- Une section du parent n'est jamais supprimée : la retirer du menu, garder son template pour les pages qui l'utilisent.
- Une section de l'enfant qui sert sur trois sites peut remonter dans le parent : renommer en `mrz_` et migrer le contenu des sites concernés.

## CSS

### Règle d'or : un CSS minimal, entièrement fait de variables
Un site doit avoir **un seul** style de bouton, de champ, de titre et de texte, partout : un bouton « Envoyer » de Gravity Forms, « Ajouter au panier » de WooCommerce et un bouton d'une section ont la même police, la même taille, la même graisse, le même rayon. Seule variante : le lien fléché (`mrz_theme_button( $link, 'link' )`, classe `.mrz-btn--link`), pour les liens « Voir tout », « Télécharger ». Jamais de fonction de lien maison dans l'enfant.
- **Jamais de valeur en dur** (taille de police, graisse, couleur, rayon) dans un fichier de section ou de partie : toujours une variable. Une valeur qui manque devient une variable dans le `style.css` de l'enfant, puis sert partout.
- **Jamais de CSS propre à une page.** Pas de règle sur `.page-id-12`, pas de bouton retouché pour un seul formulaire. Si une page semble demander un style à part, c'est une section sur mesure qui le porte, avec les variables du site.
- **Le moins de CSS possible.** Avant d'écrire une règle, vérifier qu'une variable ne suffit pas. Ne pas redéfinir ce que le parent fait déjà (boutons, champs, titres, prose).
- Pas de `!important` : le CSS de l'enfant est hors couche et passe déjà devant celui du parent.

### Variables (parent : `src/css/tokens.css`, valeurs du site : `style.css` de l'enfant)
| Famille | Variables |
| --- | --- |
| Couleurs | Réglées dans le Customizer (MRZ Theme > Couleurs, theme_mods `mrz_color_*`), **jamais dans le CSS de l'enfant** : `--color-primary`, `--color-secondary`, `--color-tertiary`, `--color-quaternary` (marque), `--color-dark` (texte sur fond clair, sections « Sombre »), `--color-light` (texte sur fond sombre, sections « Clair »), `--color-border`, `--color-background` (fond du site, blanc si vide), « Texte courant » (`mrz_color_body` : paragraphes d'une autre couleur que les titres, sur le fond du site). Dérivées, à utiliser mais pas à régler : `--color-text`, `--color-muted`, `--color-*-contrast`, `--mrz-title-color`. Quand la charte contredit le contraste calculé (texte blanc sur un vert moyen), régler « Texte sur … » de la couleur (automatique, Clair, Sombre) dans le Customizer, jamais `--color-*-contrast` dans l'enfant. Ne jamais ajouter de couleur |
| Polices | `--font-title`, `--font-body`, `--font-accent` (troisième police facultative : mono, manuscrite… ; la police du texte par défaut). Jamais de `--mrzchild-font-*` |
| Texte | `--mrz-body-size`, `--mrz-body-line-height`, `--mrz-text-sm`, `--mrz-text-lg` |
| Titres | `--mrz-h1` à `--mrz-h6`, `--mrz-title-weight`, `--mrz-title-line-height`, `--mrz-title-letter-spacing` |
| Lien fléché | `--mrz-link-font-size`, `--mrz-link-arrow` (`url()` d'un SVG, utilisé en masque : la flèche prend la couleur du texte), `--mrz-link-arrow-size` (`0` pour aucune flèche) |
| Boutons | `--mrz-button-font-size`, `--mrz-button-font-weight`, `--mrz-button-padding`, `--mrz-button-border-width`, `--mrz-button-letter-spacing`, `--mrz-button-text-transform`, `--mrz-button-bg`, `--mrz-button-color`, `--radius-button` |
| Champs | `--mrz-field-font-size`, `--mrz-field-padding`, `--mrz-field-bg`, `--mrz-field-color`, `--mrz-field-border`, `--mrz-field-border-focus`, `--mrz-field-radius`, `--mrz-label-font-size`, `--mrz-label-font-weight` |
| Menus | `--mrz-menu-font-family`, `--mrz-menu-font-size`, `--mrz-menu-font-weight`, `--mrz-menu-text-transform`, `--mrz-menu-letter-spacing` |
| Sous-menus (desktop) | `--mrz-submenu-bg`, `--mrz-submenu-color`, `--mrz-submenu-width`, `--mrz-submenu-radius`, `--mrz-submenu-shadow`, `--mrz-submenu-indicator` (chevron en masque), `--mrz-submenu-indicator-size` (`0` pour aucun chevron) |
| Mise en page | `--mrz-gutter`, `--mrz-container-wide`, `--mrz-container-narrow`, `--spacing-section`, `--spacing-section-sm`, `--spacing-section-lg`, `--radius-base` |
| Focus | `--mrz-focus-color` : contour du focus clavier. Couleur principale sur le fond du site, couleur du texte de la section sur un fond de section, sur une image et dans le header transparent. Ne jamais retirer le contour (`outline: none`) |
| Header | `--mrz-header-padding`, `--mrz-header-bg`, `--mrz-header-color`, `--mrz-header-border` ; avant le défilement d'un header transparent : `--mrz-header-transparent-bg`, `--mrz-header-transparent-color`, `--mrz-header-transparent-border`. Lecture seule : `--mrz-header-offset` (place prise par le header) |
| Icônes en image | Les SVG fournis sont noirs. `filter: var(--mrz-icon-filter)` (couleur principale par défaut) ou `var(--mrz-icon-filter-light)` (blanc par défaut). Pour une autre couleur : `url(#mrz-icon-<couleur>)` (primary, secondary, tertiary, quaternary, dark, light, border, white), filtres SVG imprimés par le thème qui suivent le Customizer. Jamais de chaîne `invert() sepia() hue-rotate()…` en dur : elle ne suit pas un changement de couleur |

Les boutons et les champs du thème, de Gravity Forms (`assets/css/gravity-forms.css`) et de WooCommerce (`assets/css/woocommerce.css`) lisent tous ces variables.

### Site clair ou sombre : le texte suit le fond
- Le thème marche aussi bien sur un site clair que sombre. Le fond du site se règle dans le Customizer (« Fond du site ») ; `--color-text` y prend Sombre ou Clair, celle qui contraste le plus.
- Chaque fond de section (`.mrz-bg--light`, `--dark`, `--primary`…) redéfinit `--color-background`, `--color-text` et `--color-muted` pour tout ce qu'il contient. Les champs de formulaire suivent la section (`--mrz-field-bg`, `--mrz-field-color` et `--mrz-field-border` ne sont pas définies par défaut : ne les définir que pour fixer les champs sur tout le site).
- Dans le CSS de l'enfant : **toujours `var(--color-text)`, `var(--color-muted)`, `var(--color-background)`**, jamais `var(--color-dark)` ni un blanc pour colorer du texte. Ainsi, un bloc reste lisible quel que soit le fond où on le pose.
- Une carte qui change de fond dans une section : lui donner le même jeu de variables (`--color-background`, `--color-text`, `--color-muted`) plutôt que des couleurs.

### Fichiers du thème enfant
- `style.css` : en-tête du thème, `@font-face` des polices du site, et les variables du site dans `:root`. Rien d'autre.
- `css/<partie>.css` : un fichier par partie du site (`header.css`, `footer.css`, `sections.css`, `formulaires.css` si besoin…). Chargés automatiquement par le parent, après `style.css`, par ordre alphabétique : aucun `wp_enqueue_style` à écrire.
- Une section sur mesure a ses règles dans le fichier de sa partie, ciblées par sa classe `.mrz-section--mrzchild-<nom>`.

### Thème parent
- Tailwind v4 compile seulement le thème parent : `npm run build`. Le CSS compilé `dist/main.css` est commité.
- Variables de design dans `src/css/tokens.css` (`@theme static` pour couleurs, polices, rayons, espacements ; `:root` pour le reste). Classes générées : `bg-primary`, `text-muted`, `font-title`, `rounded-base`, `py-section`…
- Les composants sont en CSS dans `src/css/components.css`, préfixe `.mrz-`, dans `@layer components`. Les éléments HTML (titres, champs, boutons d'envoi) dans `src/css/base.css`.
- Le CSS de Gravity Forms et de WooCommerce est hors couche : il passe devant le CSS du thème. Leurs réglages vont dans `assets/css/gravity-forms.css` et `assets/css/woocommerce.css`, hors couche eux aussi, toujours avec les variables.
- **Thème enfant : CSS classique uniquement.** Ne jamais écrire de nouvelle classe Tailwind dans un template de l'enfant : elle n'existe pas dans `dist/main.css`.
- Une classe Tailwind utilisée dans le parent doit apparaître en entier dans un fichier scanné (pas de nom construit par concaténation).

## Options
- Customizer natif, panneau « MRZ Theme » (le logo natif y est déplacé, section Header), ouvert par le menu d'admin « MRZ Theme » (`inc/admin.php`), qui porte aussi les sections globales. Pas de page d'options sur mesure : garder les écrans natifs de WordPress. Lecture : `mrz_theme_option( 'nom' )`, valeurs par défaut dans `inc/options.php`.
- Le point de rupture mobile est un réglage : les classes `.mrz-desktop-only` et `.mrz-mobile-only` sont pilotées par du CSS en ligne, pas par Tailwind.

## Réglages par page
Meta box native « MRZ Theme » (`inc/page-options.php`), sans ACF, sur tous les types publics. Metas `_mrz_hide_header` (`'1'` ou absente) et `_mrz_transparent_header` (`on`, `off` ou absente). Lecture : `mrz_theme_page_option( '_mrz_hide_header' )`. Classe du body : `mrz-no-header`. Ne jamais écrire de CSS sur `.page-id-*` : passer par ces réglages.
- **Titre** : une page n'affiche jamais son titre ; sa première section (ou son contenu) porte le `<h1>`. Toujours prévoir un `<h1>` sur une page, y compris une page faite d'un seul shortcode (panier, commande, compte). Un article affiche toujours son titre.
- **Footer** : section globale ; on le retire d'une page par ses conditions, pas par un réglage de page.

## Sections globales
CPT `mrz_section`. Meta `mrz_section_hook` (emplacement), `mrz_section_priority`, et conditions : `mrz_section_display` (`all`, `include`, `exclude`), `mrz_section_post_types` (types publics : un type couvre ses contenus et ses listes), `mrz_section_posts` (IDs). Test : `mrz_theme_global_section_matches()`. Polylang : voir plus bas. Hooks du thème : `mrz_before_header`, `mrz_after_header`, `mrz_before_content`, `mrz_after_content`, `mrz_footer`, `mrz_before_footer`, `mrz_after_footer`.

### Footer
- Le parent n'a pas de footer par défaut (ni widgets, ni menu de footer, ni copyright). Le footer d'un site est une section globale à l'emplacement `mrz_footer`, que `footer.php` enveloppe dans `<footer class="mrz-footer">`.
- Le modèle d'enfant fournit la section `mrzchild_footer` : logo (facultatif, centré), phrase et bouton, colonnes (titre + liste de liens en WYSIWYG), réseaux sociaux (icône en image, lien, nom), ligne du bas (WYSIWYG, balises `{year}` et `{site}` via `mrz_theme_text_tags()`). Fond et espacement par les réglages de la section. Retirer du modèle ce que la maquette n'a pas.
- Les liens du footer sont écrits dans la section, pas dans des menus WordPress : une section globale se traduit en une fois avec Polylang, au lieu d'un menu par emplacement et par langue.

## Plugins MRZ : obligatoires
- **Toute liste de contenus** (articles, CPT, projets, équipe, références, derniers articles sur l'accueil) passe par **MRZ Display Post** (`[mrzdpe]`). **Toute carte** passe par **MRZ Maps** (`[mrzme]`). Jamais de requête `WP_Query` ni de filtre JS maison dans une section sur mesure pour ça.
- Plugin absent du site : l'installer, ou le signaler à Aurélien **avant** de coder quoi que ce soit.
- Il manque une fonction au plugin (filtre par taxonomie, gabarit de carte, tri manuel…) : la noter comme évolution du plugin dans le compte rendu, sans recoder la liste dans le thème.
- Un type de contenu propre au site (projets, équipe, ressources) se déclare dans un **mu-plugin du site** (`wp-content/mu-plugins/<site>-contenus.php`), pas dans l'enfant : le contenu doit survivre à un changement de thème. C'est la seule chose qu'on écrit hors de l'enfant. Son affichage en liste passe par MRZ Display Post.
- MRZ Display Post ne liste que les types et taxonomies publics : un type sans page à lui se déclare `'public' => true, 'publicly_queryable' => false`.
- Site multilingue : une liste MRZ Display Post par langue (ses libellés de filtres, de recherche et de bouton sont propres à la liste).
- Noter les plugins MRZ utilisés dans la fiche du site (`AGENTS.md` de l'enfant).

## Ce qui manque au parent
Ne jamais modifier le parent installé sur un site, même pour le dépanner : la prochaine mise à jour l'écrase. Contourner le moins possible dans l'enfant, et lister le manque dans le compte rendu (« Manques du parent ») : il sera corrigé dans le dépôt du parent.

## Polylang
- Sections globales traduisibles d'office (filtre `pll_get_post_types`) : une version par langue. À l'emplacement d'un hook, chaque langue affiche sa version. Insérée à la main (section `mrz_global_section`) ou choisie comme page 404 : le parent prend la traduction de la langue courante (`mrz_theme_translated_id()`).
- Menus : un menu par emplacement et par langue (Apparence > Menus, onglets de langue).
- Sélecteur de langue : header et menu mobile, codes, drapeaux ou les deux (Customizer > Header, `mrz_lang_switcher`).
- Accueil de la seconde langue servi en `/en/home/` : cocher dans Polylang « l'URL de la page d'accueil contient le code de langue », puis vider le cache des langues.
- Formulaires : un formulaire Gravity Forms par langue.

## Gravity Forms
Le thème CSS de Gravity Forms (`theme.min.css`) est désactivé par le parent (filtre `gform_disable_form_theme_css`) : les formulaires prennent les variables du thème (`assets/css/gravity-forms.css`). Ne pas renforcer les sélecteurs dans l'enfant pour passer devant Gravity Forms.

## WooCommerce
Aucune surcharge de template. `woocommerce.php` + hooks dans `inc/woocommerce.php` + `assets/css/woocommerce.css`. Panier et commande : pages en shortcodes classiques `[woocommerce_cart]` et `[woocommerce_checkout]`, pas en blocs.

## Code
- Préfixe `mrz_theme_` (fonctions), `mrz_` (theme_mods, CSS), textdomain `mrz-theme-exp`, chaînes source en anglais. Toute chaîne ajoutée ou modifiée : mettre à jour `languages/mrz-theme-exp.pot`, `fr_FR.po` et recompiler `fr_FR.mo` (commandes dans le README).
- Conventions WordPress, échappement de toutes les sorties, `wp_enqueue_*` pour tous les assets.
- JS vanilla, sans jQuery, dans `assets/js/` (pas de build JS).
- Icônes SVG dans `assets/icons/`, affichées avec `mrz_theme_icon( 'nom' )` (en ligne, `fill="currentColor"`). L'enfant remplace une icône par un fichier du même nom dans son `assets/icons/`.
- Surcharges depuis l'enfant : mêmes chemins (`layouts/`, `parts/`), en dernier recours. Préférer les hooks :
  - `mrz_header_tools_start`, `mrz_header_tools_end` : dans le header, avant et après les outils (recherche, compte, panier), pour un bouton ou un téléphone ;
  - `mrz_mobile_menu_end` : en bas du menu mobile (aussi emplacement de section globale) ;
  - les emplacements des sections globales (`mrz_before_header`… `mrz_after_footer`).
- Le parent ne renomme ni ne supprime ce que les enfants utilisent (fonctions `mrz_theme_*`, classes `.mrz-`, hooks, emplacements de menus, sections du constructeur). Un changement qui casse ces noms impose une version majeure et des notes de release.

## Parent ou enfant
- Parent : comportements, accessibilité, JS, intégrations (WooCommerce, Gravity Forms, Polylang, plugins MRZ). Ne pas y ajouter de variantes d'apparence (headers, menus mobiles, effets de survol).
- Enfant : l'apparence du site, en CSS sur les classes `.mrz-` du parent. Copier un template dans l'enfant seulement en dernier recours : la copie ne reçoit plus les corrections du parent.

## Carrousel
Pour tout ce qui défile (logos, témoignages, cartes) : jamais de bibliothèque ni de carrousel maison dans l'enfant.
```php
mrz_theme_carousel_open( array( 'label' => $mrzchild_title, 'autoplay' => 0, 'dots' => true ) );
foreach ( $mrzchild_items as $mrzchild_item ) {
	echo '<li class="mrz-carousel__slide">…</li>';
}
mrz_theme_carousel_close();
```
- Défilement natif du navigateur (scroll-snap) : glissé au doigt, trackpad, flèches du clavier sur la piste. `assets/js/carousel.js` (commandes) ne se charge que sur les pages qui appellent `mrz_theme_carousel_open()`.
- `label` : le titre de la section (nom du carrousel pour les lecteurs d'écran). `autoplay` : secondes entre deux défilements, 0 par défaut ; activé, un bouton pause est toujours affiché, et la lecture s'arrête au survol, au focus et si l'utilisateur limite les animations (RGAA 13.8).
- Précédent et Suivant avancent d'une page de cartes visibles ; en fin de liste, Suivant revient au début. Commandes masquées si tout tient à l'écran.
- Apparence dans l'enfant, par section : `--mrz-carousel-per-view` (décimales acceptées : `3.5` montre la moitié de la suivante), avec un point de rupture par écran, `--mrz-carousel-gap`, `--mrz-carousel-button-size`, `--mrz-carousel-dot-size`.

## Sous-menus du header
Menus principal et secondaire, jusqu'à trois niveaux. Ouverts au survol (fermeture retardée de 0.2s), au clavier (Tab entre dans le sous-menu, Échap le ferme et rend le focus au lien parent) et au toucher (premier appui ouvre, second suit le lien). Un sous-menu qui sortirait de l'écran s'ouvre vers la gauche (classe `.mrz-submenu--flip`). Tout est dans le parent (`src/css/components.css`, `assets/js/main.js`) : l'enfant ne règle que l'apparence, par les variables `--mrz-submenu-*`.

## Menu mobile
Quatre styles au choix (Customizer > Header mobile, theme_mod `mrz_mobile_menu_style` : `right`, `left`, `fullscreen`, `dropdown`), sur le même balisage `parts/mobile-menu/offcanvas.php`, classe `.mrz-offcanvas--<style>`. Le parent ne pose que la position et l'ouverture. L'apparence (fond, flou, opacité, survol des liens) se fait dans `css/mobile-menu.css` de l'enfant, en ciblant `.mrz-offcanvas--<style> .mrz-offcanvas__panel`. Ne pas copier le template.
