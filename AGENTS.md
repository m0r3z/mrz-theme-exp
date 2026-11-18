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
- **Image principale (LCP)** : une vraie image `<img>` avec `'fetchpriority' => 'high'` et `'loading' => 'eager'`, jamais une image de fond CSS. Le réglage « Image de fond » des sections le fait déjà. Toutes les autres par `wp_get_attachment_image()` (dimensions, `srcset`, chargement différé).
- **Aucun décalage de mise en page (CLS)** : images avec leurs dimensions, polices en woff2 auto-hébergées avec `font-display: swap`, le moins de fichiers de police possible.
- **Accessibilité** : voir RGAA ci-dessous, qui couvre largement ce que mesure Lighthouse.
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
- Pas de mention « conforme RGAA » ni de déclaration d'accessibilité sur un site, sauf demande expresse (clients publics soumis à l'obligation légale).

## Construire une page
Remplir `mrz_builder` avec `update_field( 'field_mrz_builder', $rows, $post_id )`. Chaque ligne porte `acf_fc_layout`.

### Sections du parent (injectées sur tous les sites)
Déclarées en PHP dans `inc/builder-sections.php`, ajoutées au constructeur de l'enfant au chargement.

| Section | Champs | Note |
| --- | --- | --- |
| `mrz_text` | eyebrow (surtitre), align (`left`, `center`), title, title_tag (`h2` par défaut ; `h1` si la section ouvre la page ; `h3` à `h6` pour suivre la hiérarchie), content (HTML, shortcodes compris), link (bouton plein), link_2 (bouton à contour), settings | Texte libre. Jamais de bouton, de surtitre ni de classe d'alignement écrits à la main dans le contenu : les champs le font |
| `mrz_html` | code | Affiché tel quel, scripts compris ; champ visible seulement avec le droit `unfiltered_html` |
| `mrz_cards` | title, display (`grid`, `carousel`), autoplay, per_row (`auto`, `2`, `3`, `4` : cartes par ligne sur grand écran), card_style (`default`, `figures` : chiffres clés, le titre de la carte est le chiffre), link_style (`link` lien fléché par défaut, `outline`, `primary`), count_up (compteur des chiffres clés : la valeur finale est dans la page, `assets/js/counter.js` la fait monter depuis 0), items (répéteur : image, title, background, text, link), settings | Cartes libres. Image SVG = icône à la couleur du texte. Titre des cartes en `h3` sous un titre de section, en `h2` sans titre de section. Variables `--mrz-cards-*`, `--mrz-card-*` |
| `mrz_split` | eyebrow, title, title_tag (`h1` à `h6`, `h2` par défaut), content, link, link_2, media_type (`image`, `content`), image, aside_background (`none` = second texte, une couleur = encadré), aside_content, media_side (`right`, `left`), ratio (`equal`, `text_wide`, `media_wide`), vertical_align (`center`, `top`), sticky (`none`, `media`, `text`), settings | Texte et média. Empilés sur mobile, texte en premier ; l'ordre du HTML ne change pas avec le côté (lecture et tabulation). Image prioritaire si la section ouvre la page. Variables `--mrz-split-*` |
| `mrz_accordion` | title, intro, items (répéteur : question, answer), open_first, single_open, settings | Accordéon en `<details>` natif, sans JS. Pas de titre dans `<summary>` (son rôle de bouton masque le titre aux lecteurs d'écran). Variables `--mrz-accordion-*` |
| `mrz_gallery` | title, intro, images (galerie ACF, IDs), display (`grid`, `masonry` : colonnes CSS, formats d'origine, ratio ignoré ; `carousel`), per_row (`auto`, `2` à `5`), ratio (`original`, `square`, `4-3`, `16-9`), captions, lightbox, settings | Galerie. Le texte alternatif décrit l'image, la légende de la médiathèque s'affiche dessous et dans la lightbox. Variables `--mrz-gallery-*` |
| `mrz_anchors` | items (répéteur : label, anchor), sticky, align (`left`, `center`), settings | Sous-menu d'ancres. Collé sous le header en option ; lien de la section affichée en `aria-current` (`assets/js/anchors.js`). Son espacement vient de `--mrz-anchors-padding`. Lien actif et survol : seulement la couleur (`--mrz-anchors-active-color`), comme le menu. Les ancres arrivent juste sous le header et la barre, en défilement doux (`scroll-behavior: smooth` sur `html`, coupé par `prefers-reduced-motion`). Variables `--mrz-anchors-*` |
| `mrz_marquee` | items (répéteur : text, image, link), speed (`slow`, `normal`, `fast`), direction (`left`, `right`), separator, settings | Bandeau défilant en CSS. Logo : le texte de l'élément devient son alternative (sinon celle de la médiathèque), à remplir quand le logo est un lien. Bouton pause toujours affiché, arrêt au survol et au focus ; rien ne bouge si le visiteur limite les animations. Copies masquées aux lecteurs d'écran. Variables `--mrz-marquee-*` |
| `mrz_footer` | logo, title, button, columns (répéteur : title, links), socials (répéteur : icon, url, name), free_title, free_content (colonne libre après les colonnes de liens : texte, formulaire d'inscription en shortcode), bottom, settings | Footer. Proposée seulement dans les sections globales. Variables `--mrz-footer-*` |
| `mrz_global_section` | section (ID `mrz_section`) | |

Titres des sections du parent (Texte libre, titre et cartes de Cartes) : `<em>mot</em>` colore un mot (`--mrz-title-highlight-color`), `<br>` force un retour à la ligne ; rien d'autre ne passe (`mrz_theme_title_html()`, à utiliser aussi dans les sections sur mesure).

`settings` : background (none, light, dark, primary, secondary, tertiary, quaternary), spacing (normal, compact, large, none), spacing_top et spacing_bottom (inherit, none, compact, normal, large), width (container, narrow pour une largeur de lecture, wide, full), anchor, height (auto, large, screen), background_image (ID), background_video (ID, MP4 ou WebM), video_mobile (booléen), image_overlay (dark, light, none), gradient (booléen), gradient_to (none = fond du site, light, dark, primary…), gradient_direction (bottom, right, diagonal), motion (none, fade_up, fade, zoom, cascade), parallax (none, light, medium). Lu par `mrz_theme_section_open()`, qui accepte aussi `'class'` : plusieurs classes séparées par des espaces, ou un tableau, et `'image'` (ID) pour une image de fond fournie par la section.

- **Image de fond** : rendue en vraie `<img class="mrz-section__bg">` derrière le contenu (`object-fit: cover`). La première de la page est en `fetchpriority="high"`, les autres en chargement différé. C'est le bon moyen d'avoir l'image principale d'une bannière (LCP).
- **Voile** : `dark` et `light` posent un voile (`--mrz-image-overlay`, `--mrz-image-overlay-light`) et passent le texte en Clair (classe `.mrz-has-image`) ; `none` laisse les couleurs de la section (image composée avec un côté blanc, motif). Vérifier le contraste du texte avec le voile léger et sans voile.
- **Vidéo de fond** (`background_video`) : toujours avec une image de fond, affichée d'abord (image d'attente et LCP). `assets/js/video.js` (chargé seulement si une section a une vidéo) pose la source après le chargement complet de la page ; rien sur mobile sauf `video_mobile`, rien si le visiteur limite les animations ou économise ses données. Bouton pause affiché pendant la lecture (RGAA 13.8). Jamais de vidéo de fond YouTube ou Vimeo : un fichier du site.
- **Animations au défilement** (`motion`) : déclenchées à l'entrée à l'écran par `assets/js/motion.js` (classe `.is-inview`, `IntersectionObserver`), puis jouées en entier en CSS (`--mrz-motion-duration`), sans dépendre de la vitesse de défilement. En remontant, jouées à l'envers dès que l'élément repasse sous la ligne de déclenchement (10 % au-dessus du bas de l'écran), sauf réglage « une seule fois » du Customizer (`motion_once`). Le module n'est chargé que si une section en a une ; sans JavaScript, contenu affiché normalement (rien n'est caché sans la classe `mrz-motion-js` sur `<html>`). Le surlignage animé du mot en couleur (Customizer) passe par le même module et le même réglage (`--mrz-highlight-duration`, `--mrz-highlight-delay`). La parallaxe (`parallax`) reste en CSS seul, liée au défilement (`animation-timeline: view()`). Coupées par `prefers-reduced-motion`. `motion` ne s'applique jamais à la première section de la page (LCP). La parallaxe ne joue que sur l'image de fond. Ne pas ajouter de bibliothèque d'animation (AOS, GSAP) dans l'enfant : régler `--mrz-motion-*` et `--mrz-parallax-*`.
- **Bannière de page** : Texte libre, `title_tag` à `h1`, image (ou vidéo) de fond, hauteur `large` ou `screen`. Le header transparent suit tout seul (voir plus bas). Une section sur mesure seulement si la bannière fait ce qu'un Texte libre ne sait pas faire.
- **Images des sections** : `mrz_theme_section_image_loading()` donne `loading`, `fetchpriority` et `decoding` (prioritaire dans la première section de la page, différé ailleurs) ; `mrz_theme_image_ratio_style()` pose le format réel de l'image (`--mrz-img-ratio`) pour réserver sa place avant chargement. Les reprendre dans une section sur mesure. Ne jamais écrire `aspect-ratio: auto` sur une image : il annule la place réservée par ses attributs `width` et `height` (décalage de mise en page).

### Sections sur mesure (thème enfant)
Une section par rangée de la maquette. Pour en créer une :
1. Ajouter le layout dans `acf-json/group_mrzchild_builder.json` de l'enfant, sur `field_mrz_builder`. Nom `mrzchild_<nom>`, clés `layout_mrzchild_<nom>` et `field_mrzchild_<nom>_<champ>`, chaque sous-champ avec `parent_layout`. Mettre à jour `modified`.
2. Pour les réglages communs, ajouter un sous-champ `clone` de `group_mrz_section_settings` (`display: seamless`, `prefix_name: 0`).
3. Créer `layouts/mrzchild-<nom>.php` dans l'enfant (le nom, `_` remplacés par `-`) : `mrz_theme_section_open( 'mrzchild-<nom>' )`, le contenu échappé, `mrz_theme_section_close()`.
4. Le style de la section va dans le fichier de sa partie, `css/<partie>.css` de l'enfant, en CSS classique et uniquement avec les variables du site (voir CSS).

Header transparent : automatique quand l'option du Customizer est cochée et que la première section de la page a une image ou une vidéo de fond (réglages communs). Une première section dont le nom contient `hero` compte aussi, pour les sites qui en ont une. Pour le forcer ou le couper sur une page : meta box « MRZ Theme » de la page (`_mrz_transparent_header` à `on` ou `off`).
Sous un header transparent, la première section reçoit en haut `--spacing-section` + `--mrz-header-offset` (place du header). Une section sur mesure dont l'image est dans le flux (pas en image de fond) remet son `padding-top` à 0 et décale son texte de `var(--mrz-header-offset)`.

Un champ `link` vaut `array( 'url' => '', 'title' => '', 'target' => '' )`, affiché par `mrz_theme_button( $link, $style )` : `primary`, `outline` ou `link` (lien souligné suivi d'une flèche). Formulaires, listes MRZ Display Post et cartes MRZ Maps : shortcodes (`[gravityform]`, `[mrzdpe]`, `[mrzme]`) dans un Texte libre, qui les affiche sans filtrer leur balisage. Dans un champ de section sur mesure : `do_shortcode( wp_kses_post( $champ ) )`, jamais `wp_kses_post()` après le shortcode (il retire les `<script>` et les champs de formulaire).

### Code d'une section sur mesure (PHP)
- **Échapper toute sortie** : `esc_html()` pour un texte, `esc_url()` pour un lien, `esc_attr()` pour un attribut, `wp_kses_post()` pour un champ WYSIWYG. Jamais un `echo get_sub_field()` brut (sauf la section du parent `mrz_html`).
- **Images** : `wp_get_attachment_image( $id, $taille )`, jamais une balise `<img>` écrite à la main (WordPress ajoute `srcset`, dimensions et `loading`), avec `mrz_theme_section_image_loading()` pour la priorité. Un texte alternatif vide (`'alt' => ''`) pour une image décorative.
- **Aucun texte visible écrit en dur dans un template de l'enfant** : tout texte (titre, bouton, « Voir plus », texte pour lecteur d'écran) vient d'un champ ACF, qui se traduit avec la page dans chaque langue. Un `aria-label` se construit à partir d'un champ (le titre de la section, par exemple). L'enfant n'a pas de fichier de traduction.
- **Titres** : un seul `<h1>` par page, porté par la première section ; les autres sections commencent à `<h2>`.
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
Un site doit avoir **un seul** style de bouton, de champ, de titre et de texte, partout : un bouton « Envoyer » de Gravity Forms, « Ajouter au panier » de WooCommerce et un bouton d'une section ont la même police, la même taille, la même graisse, le même rayon. Trois formes seulement, toutes par `mrz_theme_button( $link, $style )` : plein (`primary`), contour (`outline`) et lien fléché (`link`, classe `.mrz-btn--link`, pour « Voir tout », « Télécharger »). Les Cartes choisissent la leur (réglage « Style du lien »). Jamais de fonction de lien maison dans l'enfant.
- **Jamais de valeur en dur** (taille de police, graisse, couleur, rayon) dans un fichier de section ou de partie : toujours une variable. Une valeur qui manque devient une variable dans le `style.css` de l'enfant, puis sert partout.
- **Jamais de CSS propre à une page.** Pas de règle sur `.page-id-12`, pas de bouton retouché pour un seul formulaire. Si une page semble demander un style à part, c'est une section sur mesure qui le porte, avec les variables du site.
- **Le moins de CSS possible.** Avant d'écrire une règle, vérifier qu'une variable ne suffit pas. Ne pas redéfinir ce que le parent fait déjà (boutons, champs, titres, prose).
- Pas de `!important` : le CSS de l'enfant est hors couche et passe déjà devant celui du parent.

### Variables (parent : `src/css/tokens.css`, valeurs du site : `style.css` de l'enfant)
| Famille | Variables |
| --- | --- |
| Couleurs | Réglées dans le Customizer (MRZ Theme > Couleurs, theme_mods `mrz_color_*`), **jamais dans le CSS de l'enfant** : `--color-primary`, `--color-secondary`, `--color-tertiary`, `--color-quaternary` (marque), `--color-dark` (texte sur fond clair, sections « Sombre »), `--color-light` (texte sur fond sombre, sections « Clair »), `--color-border`, `--color-background` (fond du site, blanc si vide), « Texte courant » (`mrz_color_body` : paragraphes d'une autre couleur que les titres, sur le fond du site). Dérivées, à utiliser mais pas à régler : `--color-text`, `--color-muted`, `--color-*-contrast`, `--mrz-title-color`. Quand la charte contredit le contraste calculé (texte blanc sur un vert moyen), régler « Texte sur … » de la couleur (automatique, Clair, Sombre) dans le Customizer, jamais `--color-*-contrast` dans l'enfant. Ne jamais ajouter de couleur. Une teinte dérivée d'une couleur de la palette est permise (header translucide : `color-mix(in srgb, var(--color-dark) 85%, transparent)`), jamais un code couleur en dur. Lecture seule : `--mrz-site-background` (fond du site, même dans une section colorée) |
| Polices | `--font-title`, `--font-body`, `--font-accent` (troisième police facultative : mono, manuscrite… ; la police du texte par défaut). Jamais de `--mrzchild-font-*` |
| Texte | `--mrz-body-size`, `--mrz-body-line-height`, `--mrz-text-sm`, `--mrz-text-lg` |
| Titres | `--mrz-h1` à `--mrz-h6`, `--mrz-title-weight`, `--mrz-title-line-height`, `--mrz-title-letter-spacing`, `--mrz-title-text-transform` (`uppercase` : titres en capitales, titre du footer compris), `--mrz-title-highlight-color` (mot en `<em>` : principale, ou texte si elle n'atteint pas 3:1 ; texte de la section sur un fond de section), `--mrz-title-highlight-style` (`normal` ou `italic`), `--mrz-title-highlight-color-on-image` (mot en couleur sur une image de fond ; texte par défaut) |
| Surtitre | `--mrz-eyebrow-color` (couleur des liens par défaut), `--mrz-eyebrow-font-family` (police du texte par défaut), `--mrz-eyebrow-size`, `--mrz-eyebrow-weight`, `--mrz-eyebrow-text-transform`, `--mrz-eyebrow-letter-spacing` |
| Liens du texte | `--mrz-link-color` (liens de `.mrz-prose`) : couleur principale, ou couleur du texte si la principale n'atteint pas 4,5:1 sur le fond du site (calculé par le Customizer). Sur un fond de section, les liens prennent la couleur du texte. `--mrz-link-weight`, `--mrz-link-underline-thickness` |
| Lien fléché | `--mrz-link-font-size`, `--mrz-link-arrow` (`url()` d'un SVG, utilisé en masque : la flèche prend la couleur du texte), `--mrz-link-arrow-size` (`0` pour aucune flèche) |
| Boutons | `--mrz-button-font-family` (police du texte autour par défaut), `--mrz-button-font-size`, `--mrz-button-font-weight`, `--mrz-button-padding-y`, `--mrz-button-padding-x` (lus sur le bouton même : un bouton peut les redéfinir ; raccourci `--mrz-button-padding` facultatif, non déclaré), `--mrz-button-border-width`, `--mrz-button-letter-spacing`, `--mrz-button-text-transform`, `--mrz-button-icon` et `--mrz-button-icon-size` (icône après le texte des boutons du thème, en masque ; taille 0 par défaut : aucune), `--mrz-button-bg`, `--mrz-button-color`, `--radius-button` |
| Champs | `--mrz-field-font-size`, `--mrz-field-padding-y`, `--mrz-field-padding-x`, `--mrz-field-bg`, `--mrz-field-color`, `--mrz-field-border`, `--mrz-field-border-focus`, `--mrz-field-radius`, `--mrz-label-font-size`, `--mrz-label-font-weight` |
| Menus | `--mrz-menu-font-family`, `--mrz-menu-font-size`, `--mrz-menu-font-weight`, `--mrz-menu-text-transform`, `--mrz-menu-letter-spacing` (lues aussi par le sélecteur de langue), `--mrz-lang-inactive-opacity` (langues non affichées) |
| Cartes (`mrz_cards`) | `--mrz-cards-per-view`, `--mrz-cards-per-view-md` (640px), `--mrz-cards-per-view-lg` (1024px), `--mrz-card-padding`, `--mrz-card-gap`, `--mrz-card-radius`, `--mrz-card-border`, `--mrz-card-shadow`, `--mrz-card-icon-size`, `--mrz-card-title-size`, `--mrz-card-media-ratio` (format des images, recadrées : `16 / 9`, `1`…), `--mrz-card-figure-size` (chiffre du style « chiffres clés ») |
| Texte et média (`mrz_split`) | `--mrz-split-gap`, `--mrz-split-radius`, `--mrz-split-box-padding`, `--mrz-split-image-ratio` (format de l'image, recadrée ; format d'origine par défaut) |
| Galerie (`mrz_gallery`) | `--mrz-gallery-per-view`, `--mrz-gallery-per-view-md` (640px), `--mrz-gallery-per-view-lg` (1024px), `--mrz-gallery-gap`, `--mrz-gallery-radius` |
| Sous-menu d'ancres | `--mrz-anchors-padding`, `--mrz-anchors-gap`, `--mrz-anchors-active-color` (`--mrz-link-color` par défaut, comme le lien actif du menu) |
| Bandeau défilant | `--mrz-marquee-duration`, `-duration-slow`, `-duration-fast`, `--mrz-marquee-gap`, `--mrz-marquee-font-size`, `--mrz-marquee-logo-height` |
| Lightbox | `--mrz-lightbox-bg`, `--mrz-lightbox-color`, `--mrz-lightbox-duration` (fondu à l'ouverture et entre deux images) |
| Accordéon (`mrz_accordion`) | `--mrz-accordion-gap`, `--mrz-accordion-border`, `--mrz-accordion-padding`, `--mrz-accordion-question-size`, `--mrz-accordion-question-font-family` (police des titres par défaut), `--mrz-accordion-question-text-transform`, `--mrz-accordion-icon` (SVG en masque, pivote de 45° à l'ouverture : un plus devient une croix), `--mrz-accordion-icon-size`, `--mrz-accordion-duration` (ouverture et fermeture, 0,35 s) |
| Footer (`mrz_footer`) | `--mrz-footer-logo-height`, `--mrz-footer-logo-space`, `--mrz-footer-gap`, `--mrz-footer-title-size`, `--mrz-footer-columns-gap`, `--mrz-footer-heading-size`, `--mrz-footer-link-size`, `--mrz-footer-link-space`, `--mrz-footer-social-size`, `--mrz-footer-bottom-space`, `--mrz-footer-bottom-size`, `--mrz-footer-bottom-border`, `--mrz-footer-link-color` (couleur du texte de la section par défaut), `--mrz-footer-free-width` (colonne libre) |
| Mega menu | `--mrz-megamenu-columns` (`auto-fit` ou un nombre), `--mrz-megamenu-column-min`, `--mrz-megamenu-width` |
| Sous-menus (desktop) | `--mrz-submenu-bg` (fond du header par défaut), `--mrz-submenu-color`, `--mrz-submenu-width`, `--mrz-submenu-radius`, `--mrz-submenu-shadow`, `--mrz-submenu-indicator` (chevron en masque), `--mrz-submenu-indicator-size` (`0` pour aucun chevron) |
| Mise en page | `--mrz-gutter`, `--mrz-container-wide`, `--mrz-container-narrow`, `--spacing-section`, `--spacing-section-sm`, `--spacing-section-lg`, `--radius-base` |
| Focus | `--mrz-focus-color` : contour du focus clavier. Couleur principale sur le fond du site (couleur du texte si la principale n'atteint pas 3:1 sur ce fond, calculé par le Customizer), couleur du texte de la section sur un fond de section, sur une image et dans le header transparent (ses sous-menus, sur le fond du site, gardent celle du site). Lecture seule : `--mrz-site-focus-color` (focus du site, même dans le header). Ne jamais retirer le contour (`outline: none`) |
| Header | `--mrz-header-padding`, `--mrz-header-max-width` (`none` : header pleine largeur), `--mrz-header-bg`, `--mrz-header-color`, `--mrz-header-border`, `--mrz-header-backdrop` (flou derrière un header translucide : `blur(12px)`), `--mrz-logo-height-scrolled` (logo plus petit une fois la page défilée, sur ordinateur) ; avant le défilement d'un header transparent : `--mrz-header-transparent-bg`, `--mrz-header-transparent-color`, `--mrz-header-transparent-border`. Lecture seule : `--mrz-header-offset` (place prise par le header), `--mrz-topbar-visible` (partie visible du bandeau, posée par main.js sous un header transparent). Bandeau au-dessus du header : `--mrz-topbar-padding`, `--mrz-topbar-font-family`, `--mrz-topbar-font-size`, `--mrz-topbar-font-weight`, `--mrz-topbar-letter-spacing`, `--mrz-topbar-text-transform`. Blocs du header ciblables : `.mrz-header__bar`, `.mrz-header__start` (header centré), `.mrz-logo`, `.mrz-header__end` (menu et outils) |
| Sections | `--mrz-image-overlay` (voile sombre, `rgb(0 0 0 / 0.45)`), `--mrz-image-overlay-light` (voile léger), `--mrz-section-image-position` (cadrage de l'image de fond), `--mrz-section-height-large` (`70svh`), `--mrz-section-height-screen` (écran moins le header), `--mrz-motion-distance`, `--mrz-motion-scale`, `--mrz-motion-duration` (0,9 s), `--mrz-motion-stagger` (cascade : décalage entre deux éléments d'une ligne, 0,12 s), `--mrz-motion-easing`, `--mrz-parallax-light`, `--mrz-parallax-medium`, `--mrz-highlight-mark` et `--mrz-highlight-height` (surlignage animé du mot en couleur, réglage « Surlignage animé » du Customizer) |
| WooCommerce | `--mrz-product-title-font-family`, `--mrz-product-title-size`, `--mrz-product-title-text-transform` (titres des produits dans les listes), `--mrz-shop-gap` (grille des produits), `--mrz-product-thumbs` (miniatures de la galerie par ligne, 4), `--mrz-product-summary-gap` (sous le texte court de la fiche, 1,5 rem), `--mrz-shop-header-gap` (sous le titre et la description de la boutique ou d'une catégorie, 2,5 rem), `--mrz-account-form-width` (connexion, inscription, mot de passe perdu, 26 rem), `--mrz-order-width` (page de remerciement, 48 rem), `--mrz-account-nav-width` (menu de Mon compte, 15 rem), `--mrz-account-gap` (entre le menu et le contenu, 3 rem), `--mrz-swatch-gap`, `--mrz-swatch-padding`, `--mrz-swatch-radius`, `--mrz-swatch-size` (pastilles et ronds de couleur des variations, `inc/woocommerce-swatches.php` : type d'attribut `mrz_buttons` ou `mrz_colors`, couleur du terme en meta `mrz_color`), `--mrz-minicart-width`, `--mrz-minicart-bg`, `--mrz-minicart-padding`, `--mrz-minicart-backdrop`, `--mrz-minicart-duration` (glissement du panneau, 0,6 s), `--mrz-minicart-backdrop-duration` (fondu du voile, 0,5 s), `--mrz-minicart-remove-icon` (SVG en masque, à la couleur du texte : croix par défaut) et `--mrz-minicart-remove-icon-size` (0,875 rem), `--mrz-minicart-remove-size` (zone de clic de la croix, 1,75 rem : jamais sous 24 px), `--mrz-minicart-thumb` (côté de l'image carrée d'un article, 6 rem), `--mrz-minicart-item-gap`, typo des articles : `--mrz-minicart-name-font-family`, `-name-size`, `-name-weight`, `-name-text-transform`, `-name-letter-spacing` (titre), `--mrz-minicart-options-font-family`, `-options-size`, `-options-text-transform`, `-options-letter-spacing` (options de la variation), `--mrz-minicart-price-font-family`, `-price-size`, `-price-weight` (prix), `--mrz-minicart-qty-size` et `--mrz-minicart-qty-height` (quantité). Par défaut : titre et prix 15 px, options et quantité 13 px, quantité de 32 px de haut. Article du panneau : nom et options par `woocommerce_cart_item_name`, prix et quantité par `woocommerce_widget_cart_item_quantity`, seulement dans le mini-panier ; quantité envoyée à `wc-ajax=mrz_cart_quantity` (panneau du panier). Colonnes : réglage du Customizer, pas de variable à écrire. Informations du produit : accordéons de la section Accordéon (`--mrz-accordion-*`) |
| Icônes en image | Les SVG fournis sont noirs. `filter: var(--mrz-icon-filter)` (couleur principale par défaut) ou `var(--mrz-icon-filter-light)` (blanc par défaut). Pour une autre couleur : `url(#mrz-icon-<couleur>)` (primary, secondary, tertiary, quaternary, dark, light, border, white), filtres SVG imprimés par le thème qui suivent le Customizer. Jamais de chaîne `invert() sepia() hue-rotate()…` en dur : elle ne suit pas un changement de couleur |

Liste complète et valeurs par défaut : `src/css/tokens.css` du parent, livré avec le thème pour servir de référence.

Les boutons et les champs du thème, de Gravity Forms (`src/css/gravity-forms.css`, compilé dans `dist/main.css`) et de WooCommerce (`assets/css/woocommerce.css`) lisent tous ces variables.

### Site clair ou sombre : le texte suit le fond
- Le thème marche aussi bien sur un site clair que sombre. Le fond du site se règle dans le Customizer (« Fond du site ») ; `--color-text` y prend Sombre ou Clair, celle qui contraste le plus.
- Chaque fond de section (`.mrz-bg--light`, `--dark`, `--primary`…) redéfinit `--color-background`, `--color-text` et `--color-muted` pour tout ce qu'il contient. Les champs de formulaire suivent la section (`--mrz-field-bg`, `--mrz-field-color` et `--mrz-field-border` ne sont pas définies par défaut : ne les définir que pour fixer les champs sur tout le site).
- Dans le CSS de l'enfant : **toujours `var(--color-text)`, `var(--color-muted)`, `var(--color-background)`**, jamais `var(--color-dark)` ni un blanc pour colorer du texte. Ainsi, un bloc reste lisible quel que soit le fond où on le pose.
- Une carte qui change de fond dans une section : lui donner le même jeu de variables (`--color-background`, `--color-text`, `--color-muted`) plutôt que des couleurs.
- Redéfinir `--color-text` ou `--color-muted` sur une section ou un bloc est permis (liens gris d'un footer sur fond clair : `--color-text: var(--color-tertiary)`), à condition que le contraste reste suffisant.

### Fichiers du thème enfant
- `style.css` : en-tête du thème, `@font-face` des polices du site, et les variables du site dans `:root` (et dans des `@media` du `:root` pour les redéfinir sous un point de rupture). Rien d'autre.
- Une variable qui cite une autre variable (`--mrz-eyebrow-color: var(--color-text)`) est calculée dans `:root`, une fois pour toutes : elle ne suit pas une section qui redéfinit `--color-text`. Pour suivre le fond de la section, écrire `currentColor` ou redéfinir la variable sur la section.
- `css/<partie>.css` : un fichier par partie du site (`header.css`, `footer.css`, `sections.css`, `formulaires.css` si besoin…). Chargés automatiquement par le parent, après `style.css`, par ordre alphabétique : aucun `wp_enqueue_style` à écrire.
- Une section sur mesure a ses règles dans le fichier de sa partie, ciblées par sa classe `.mrz-section--mrzchild-<nom>`.

### Thème parent
- Tailwind v4 compile seulement le thème parent : `npm run build`. Le CSS compilé `dist/main.css` est commité.
- Variables de design dans `src/css/tokens.css` (`@theme static` pour couleurs, polices, rayons, espacements ; `:root` pour le reste). Classes générées : `bg-primary`, `text-muted`, `font-title`, `rounded-base`, `py-section`…
- Les composants sont en CSS dans `src/css/components.css`, préfixe `.mrz-`, dans `@layer components`. Les éléments HTML (titres, champs, boutons d'envoi) dans `src/css/base.css`.
- Le CSS de Gravity Forms et de WooCommerce est hors couche : il passe devant le CSS du thème. Leurs réglages vont dans `src/css/gravity-forms.css` (importé hors couche dans `main.css`) et `assets/css/woocommerce.css` (chargé dès que WooCommerce est actif), toujours avec les variables.
- **Thème enfant : CSS classique uniquement.** Ne jamais écrire de nouvelle classe Tailwind dans un template de l'enfant : elle n'existe pas dans `dist/main.css`.
- Une classe Tailwind utilisée dans le parent doit apparaître en entier dans un fichier scanné (pas de nom construit par concaténation).

## Options
- Customizer natif, panneau « MRZ Theme » (le logo natif y est déplacé, section Header), ouvert par le menu d'admin « MRZ Theme » (`inc/admin.php`), qui porte aussi les sections globales. Pas de page d'options sur mesure : garder les écrans natifs de WordPress. Lecture : `mrz_theme_option( 'nom' )`, valeurs par défaut dans `inc/options.php`.
- Le point de rupture mobile est un réglage : les classes `.mrz-desktop-only` et `.mrz-mobile-only` sont pilotées par du CSS en ligne, pas par Tailwind. Une variable ne peut pas servir dans `@media` : l'enfant recopie la valeur du réglage (`@media (max-width: <valeur>px)`) et la note dans sa fiche de site.

## Réglages par page
Meta box native « MRZ Theme » (`inc/page-options.php`), sans ACF, sur tous les types publics. Metas `_mrz_hide_header` (`'1'` ou absente) et `_mrz_transparent_header` (`on`, `off` ou absente). Lecture : `mrz_theme_page_option( '_mrz_hide_header' )`. Classe du body : `mrz-no-header`. Ne jamais écrire de CSS sur `.page-id-*` : passer par ces réglages.
- **Titre** : une page n'affiche jamais son titre ; sa première section (ou son contenu) porte le `<h1>`. Un article affiche toujours son titre.
- **Footer** : section globale ; on le retire d'une page par ses conditions, pas par un réglage de page.

## Sections globales
CPT `mrz_section`. Meta `mrz_section_hook` (emplacement), `mrz_section_priority`, et conditions : `mrz_section_display` (`all`, `include`, `exclude`), `mrz_section_post_types` (types publics : un type couvre ses contenus et ses listes), `mrz_section_posts` (IDs). Test : `mrz_theme_global_section_matches()`. Polylang : voir plus bas. Hooks du thème : `mrz_topbar` (bandeau au-dessus du header et en haut du panneau du panier, `mrz_theme_topbar( 'header' | 'cart' )` ; ses sections ne comptent pas dans le rang des sections et leurs titres deviennent des paragraphes, comme dans un mega menu), `mrz_before_header`, `mrz_after_header`, `mrz_before_content`, `mrz_after_content`, `mrz_footer`, `mrz_before_footer`, `mrz_after_footer`. Une section sur toutes les pages (bannière avant le footer) : emplacement `mrz_before_footer`, conditions `all`. Sur quelques pages seulement : conditions `include`, ou section insérée à la main dans le constructeur de ces pages.

### Footer
- Le parent n'a pas de footer par défaut (ni widgets, ni menu de footer, ni copyright). Le footer d'un site est une section globale à l'emplacement `mrz_footer`, que `footer.php` enveloppe dans `<footer class="mrz-footer">`.
- Le parent fournit la section `mrz_footer` : logo (facultatif, centré), phrase et bouton, réseaux sociaux (icône SVG à la couleur du texte, lien, nom), colonnes (titre + liste de liens en WYSIWYG), ligne du bas (balises `{year}` et `{site}`). Elle n'est proposée que dans les sections globales (filtre `mrz_theme_global_only_layouts`, que l'enfant peut compléter). Apparence : variables `--mrz-footer-*`. Quand une maquette s'en écarte trop, l'enfant crée sa propre section `mrzchild_footer`.
- Les liens du footer sont écrits dans la section, pas dans des menus WordPress : une section globale se traduit en une fois avec Polylang, au lieu d'un menu par emplacement et par langue.

## Plugins MRZ : obligatoires
- **Toute liste de contenus** (articles, CPT, projets, équipe, références, derniers articles sur l'accueil) passe par **MRZ Display Post** (`[mrzdpe]`). **Toute carte** passe par **MRZ Maps** (`[mrzme]`). Jamais de requête `WP_Query` ni de filtre JS maison dans une section sur mesure pour ça.
- Plugin absent du site : l'installer, ou le signaler **avant** de coder quoi que ce soit.
- Il manque une fonction au plugin (filtre par taxonomie, gabarit de carte, tri manuel…) : la noter comme évolution du plugin dans le compte rendu, sans recoder la liste dans le thème.
- Un type de contenu propre au site (projets, équipe, ressources) se déclare dans un **mu-plugin du site** (`wp-content/mu-plugins/<site>-contenus.php`, types et fonctions préfixés `aa_` : `aa_equipe`, `aa_register_contenus()`), pas dans l'enfant : le contenu doit survivre à un changement de thème. C'est la seule chose qu'on écrit hors de l'enfant. Son affichage en liste passe par MRZ Display Post.
- MRZ Display Post ne liste que les types et taxonomies publics : un type sans page à lui se déclare `'public' => true, 'publicly_queryable' => false`.
- Mettre en forme une liste : écrire ses propres classes `mrzchild-*` dans le gabarit de la liste (réglage du plugin) et styler celles-là dans l'enfant. Jamais les classes internes du plugin (`.mrz-display-post-exp-*`).
- Site multilingue : une liste MRZ Display Post par langue (ses libellés de filtres, de recherche et de bouton sont propres à la liste).
- Noter les plugins MRZ utilisés dans la fiche du site (`AGENTS.md` de l'enfant).

## Images SVG
Les icônes des Cartes et des réseaux du Footer sont des SVG : le plugin **Safe SVG** (prérequis du README) les autorise et les nettoie. Ne jamais écrire sa propre autorisation des SVG (filtre `upload_mimes` dans un mu-plugin ou l'enfant) : un SVG non nettoyé peut porter du script.

## Ce qui manque au parent
Ne jamais modifier le parent installé sur un site, même pour le dépanner : la prochaine mise à jour l'écrase. Contourner le moins possible dans l'enfant, et lister le manque dans le compte rendu (« Manques du parent ») : il sera corrigé dans le dépôt du parent.

## Polylang
- Sections globales traduisibles d'office (filtre `pll_get_post_types`) : une version par langue. À l'emplacement d'un hook, chaque langue affiche sa version. Insérée à la main (section `mrz_global_section`) ou choisie comme page 404 : le parent prend la traduction de la langue courante (`mrz_theme_translated_id()`).
- Menus : un menu par emplacement et par langue (Apparence > Menus, onglets de langue).
- Sélecteur de langue : header et menu mobile, codes, drapeaux ou les deux (Customizer > Header, `mrz_lang_switcher`).
- Accueil de la seconde langue servi en `/en/home/` : cocher dans Polylang « l'URL de la page d'accueil contient le code de langue », puis vider le cache des langues (supprimer le transient `pll_languages_list` : `wp transient delete pll_languages_list`).
- Le sélecteur de langue ne s'affiche qu'à partir de deux langues.
- Formulaires : un formulaire Gravity Forms par langue.

## Gravity Forms
- Thème de formulaire **Orbital** imposé à tous les formulaires (filtre `gform_form_theme_slug`) : c'est le seul qui gère tous les types de champs (choix d'image, fichiers, dates…) et qui se règle par variables CSS (`--gf-*`).
- `src/css/gravity-forms.css` (compilé dans `dist/main.css`) donne aux variables d'Orbital les valeurs du thème (`--color-*`, `--mrz-field-*`, `--mrz-label-*`, `--mrz-button-*`). Les formulaires suivent donc les variables du site et le fond de leur section, bouton inversé compris. Le focus clavier des champs et des boutons reprend `--mrz-focus-color`, en 2px.
- Dans l'enfant : régler les variables du thème, **jamais un sélecteur de Gravity Forms** ni une variable `--gf-*`. Ne pas régler de style de formulaire dans l'éditeur de blocs de Gravity Forms : il passerait devant le thème.
- Padding des champs et des boutons : `--mrz-field-padding-y`/`-x` et `--mrz-button-padding-y`/`-x` (Gravity Forms les lit séparément), pas le raccourci `--mrz-field-padding`.
- Poids : Orbital ajoute environ 37 Ko compressés, seulement sur les pages qui ont un formulaire.

## WooCommerce
Aucune surcharge de template. `woocommerce.php` + hooks dans `inc/woocommerce.php` + `assets/css/woocommerce.css` (chargé sur tout le site : une grille `[products]` peut être sur n'importe quelle page). Panier et commande : les blocs de WooCommerce (pages créées par WooCommerce), jamais remplacés par les shortcodes ; leurs boutons prennent ceux du thème. Les boutons + et − de quantité ne concernent que l'ancien panier en shortcode. Jamais de sélecteur de WooCommerce dans l'enfant : passer par les variables (`--mrz-product-title-*`, boutons, champs). Page Boutique : ses sections (constructeur ACF) s'affichent au-dessus des produits et portent le `h1` ; sans sections, WooCommerce affiche le titre « Boutique » en `h1`.

## Code
- Préfixe `mrz_theme_` (fonctions), `mrz_` (theme_mods, CSS), textdomain `mrz-theme-exp`, chaînes source en anglais. Toute chaîne ajoutée ou modifiée : mettre à jour `languages/mrz-theme-exp.pot`, `fr_FR.po` et recompiler `fr_FR.mo` (commandes dans le README).
- Conventions WordPress, échappement de toutes les sorties, `wp_enqueue_*` pour tous les assets.
- JS vanilla, sans jQuery, dans `assets/js/` (pas de build JS).
- Icônes SVG dans `assets/icons/`, affichées avec `mrz_theme_icon( 'nom' )` (en ligne, `fill="currentColor"`). L'enfant remplace une icône par un fichier du même nom dans son `assets/icons/`.
- Surcharges depuis l'enfant : mêmes chemins (`layouts/`, `parts/`), en dernier recours (la copie ne reçoit plus les corrections du parent). Préférer les hooks :
  - `mrz_header_tools_start`, `mrz_header_tools_end` : dans le header, avant et après les outils (recherche, compte, panier), pour un téléphone ou une icône. Un bouton avec un texte passe plutôt par le menu secondaire (texte traduit avec les menus) : l'enfant n'a pas de fichier de traduction ;
  - `mrz_mobile_menu_end` : en bas du menu mobile (aussi emplacement de section globale) ;
  - les emplacements des sections globales (`mrz_before_header`… `mrz_after_footer`).
- Le parent ne renomme ni ne supprime ce que les enfants utilisent (fonctions `mrz_theme_*`, classes `.mrz-`, hooks, emplacements de menus, sections du constructeur). Un changement qui casse ces noms impose une version majeure et des notes de release.

## Parent ou enfant
- Parent : comportements, accessibilité, JS, intégrations (WooCommerce, Gravity Forms, Polylang, plugins MRZ), et les variantes qui reviennent d'un site à l'autre, sous forme de réglages (dispositions du header, styles du menu mobile, style du lien des cartes). Une variante entre dans le parent quand trois sites en ont besoin, pas avant.
- Enfant : l'apparence du site, en variables et en CSS sur les classes `.mrz-` du parent.

## Transitions de page
Customizer > Transitions de page : fondu, glissement, rideau ou zoom, en View Transitions entre documents (CSS seul, `inc/customizer.php`). En deux temps : l'ancienne page disparaît entièrement sur la « couleur entre les deux pages » (fond du site par défaut), puis la nouvelle arrive ; jamais de superposition. Le header (`view-transition-name: mrz-header`) et la barre d'admin restent immobiles. Coupées si le visiteur limite les animations. Un effet propre au site (transformation d'image, révélation circulaire) va dans l'enfant ; ne jamais réutiliser le nom `mrz-header`.

## Carrousel
Pour tout ce qui défile (cartes, témoignages, logos) : jamais de bibliothèque ni de carrousel maison dans l'enfant.
```php
mrz_theme_carousel_open( array( 'label' => $mrzchild_title, 'autoplay' => 0, 'dots' => true ) );
foreach ( $mrzchild_items as $mrzchild_item ) {
	echo '<li class="mrz-carousel__slide">…</li>';
}
mrz_theme_carousel_close();
```
- Défilement natif du navigateur (scroll-snap) : glissé au doigt, trackpad, flèches du clavier sur la piste. `assets/js/carousel.js` (commandes) ne se charge que sur les pages qui appellent `mrz_theme_carousel_open()`.
- `label` : le titre de la section (nom du carrousel pour les lecteurs d'écran). Sans libellé, le carrousel n'est pas une région nommée (pas de `role="region"`). `autoplay` : secondes entre deux défilements, 0 par défaut ; activé, un bouton pause est toujours affiché, et la lecture s'arrête au survol, au focus et si l'utilisateur limite les animations (RGAA 13.8).
- Précédent et Suivant avancent d'une page de cartes visibles ; en fin de liste, Suivant revient au début. Commandes masquées si tout tient à l'écran.
- Apparence dans l'enfant, par section : `--mrz-carousel-per-view` (décimales acceptées : `3.5` montre la moitié de la suivante), avec un point de rupture par écran, `--mrz-carousel-gap`, `--mrz-carousel-button-size`, `--mrz-carousel-button-bg` et `--mrz-carousel-button-color` (transparent et texte par défaut), `--mrz-carousel-dot-size`.

## Lightbox
- `assets/js/lightbox.js` (`<dialog>` natif) s'ouvre sur les images de la section Galerie, les liens vers une image, une vidéo YouTube ou Vimeo, ou un fichier MP4 ou WebM du site dans un texte (`.mrz-prose`), et les galeries natives `[gallery link="file"]`. Chargé seulement si la page en a besoin (`inc/lightbox.php` regarde le contenu).
- Image affichée en entier ; plus grande que l'écran, elle passe en taille réelle au clic (ou bouton loupe) et l'on s'y déplace en faisant défiler. Flèches, Échap, glissé au doigt, focus rendu au lien.
- Vidéos : lecteur chargé seulement à l'ouverture, `youtube-nocookie.com` et Vimeo `dnt=1`. Ça reste un service tiers : à mentionner dans la politique de confidentialité du site. Fichier MP4 ou WebM : lecteur natif avec ses commandes ; le texte du lien nomme la vidéo. Une vidéo parlée a besoin de sous-titres ou d'une transcription à côté (RGAA 4).
- Pour une section sur mesure : un lien `<a href="(image en taille réelle)" data-mrz-lightbox="groupe">` autour de l'image et `wp_enqueue_script( 'mrz-lightbox' )`. Jamais de lightbox maison ni de bibliothèque (Fancybox, GLightbox).

## Sous-menus du header
Menus principal et secondaire, jusqu'à trois niveaux. Ouverts au survol (fermeture retardée de 0.2s), au clavier (Tab entre dans le sous-menu, Échap le ferme et rend le focus au lien parent) et au toucher (premier appui ouvre, second suit le lien). Le premier niveau part du bas du header (coins du haut droits, même fond que le header par défaut) ; un pont invisible garde le survol entre le lien et le sous-menu. Un sous-menu qui sortirait de l'écran s'ouvre vers la gauche (classe `.mrz-submenu--flip`). Tout est dans le parent (`src/css/components.css`, `assets/js/main.js`) : l'enfant ne règle que l'apparence, par les variables `--mrz-submenu-*`.
- **Mega menu** : classe CSS `mrz-megamenu` sur un élément du premier niveau (Apparence > Menus, « Classes CSS »). Son sous-menu devient un panneau en colonnes : chaque élément du deuxième niveau est un titre de colonne, ses enfants sont listés dessous, toujours visibles. Échap ferme tout le panneau et rend le focus au lien du premier niveau. Une **section globale** placée au deuxième niveau (boîte « Sections globales » d'Apparence > Menus) forme une colonne : image, texte, bouton, Cartes… Emplacement de la section : aucun. Elle n'apparaît que dans le mega menu du header (retirée du menu mobile, du menu secondaire et du premier niveau). Dans le menu, ses titres deviennent des paragraphes (le menu passe avant le `h1` de la page), ses Cartes passent à une par ligne, son Texte et média s'empile et ses images ne prennent pas la priorité de chargement. Apparence : `--mrz-megamenu-columns` (`auto-fit` : une colonne par élément du deuxième niveau, ou un nombre), `--mrz-megamenu-column-min`, `--mrz-megamenu-width`, et les `--mrz-submenu-*` ; une mise en forme propre au site va dans `css/header.css` de l'enfant, sur `.mrz-megamenu`. Ne jamais recoder l'ouverture ni Échap dans l'enfant.
- **Dispositions du header** (Customizer > Header) : classique, menu à gauche (`parts/header/header-left.php`), centrée, logo centré (`parts/header/header-split.php` : menu principal à gauche, logo au centre, menu secondaire et outils à droite ; sur mobile, logo à gauche). Réglage « Bouton Menu sur ordinateur » (`mrz_menu_button_desktop` : `none`, `replace` à la place du menu principal, `add` en plus du menu principal) : le bouton ouvre le panneau du menu mobile, qui montre le menu Mobile s'il est affecté, sinon le menu principal. Option « Menu » écrit à côté de l'icône (`mrz_menu_button_label`), qui devient le nom du bouton.

## Menu mobile
Quatre styles au choix (Customizer > Header mobile, theme_mod `mrz_mobile_menu_style` : `right`, `left`, `fullscreen`, `dropdown`), sur le même balisage `parts/mobile-menu/offcanvas.php`, classe `.mrz-offcanvas--<style>`. Le parent ne pose que la position et l'ouverture. En plein écran, le bouton fermer prend la place exacte du burger. L'apparence (fond, flou, opacité, survol des liens) se fait dans `css/mobile-menu.css` de l'enfant, en ciblant `.mrz-offcanvas--<style> .mrz-offcanvas__panel`. Ne pas copier le template.
