# Changelog

Toutes les évolutions notables du thème. Format inspiré de [Keep a Changelog](https://keepachangelog.com/fr/1.1.0/), versions en [SemVer](https://semver.org/lang/fr/) : le premier chiffre change quand un nom utilisé par les thèmes enfants casse (« Cassant ») ; avant la 1.0, c'est le deuxième.

## [Non publié]

## [0.4.1] - 2026-11-17

Correctifs issus des recettes de la 0.4.0 sur deux vrais sites (Beta-Coltrane, Beta-Bird).

### Corrigé
- Un bouton (`.mrz-btn`) écrit dans un Texte libre gardait la couleur des liens : texte invisible sur le bouton.
- Liens du texte et contour de focus : si la couleur principale manque de contraste sur le fond du site (charte claire), ils prennent la couleur du texte (RGAA : 4,5:1 pour les liens, 3:1 pour le focus). Nouvelle variable `--mrz-link-color`.
- Gravity Forms : focus clavier des champs et des boutons en 2px opaque à la couleur `--mrz-focus-color`, au lieu du halo pâle d'Orbital.
- Gravity Forms : le CSS du thème pour Orbital est compilé dans `dist/main.css` (quelques Ko de variables) au lieu d'un fichier chargé sur toutes les pages.
- WooCommerce : le CSS du thème se charge dès que WooCommerce est actif, pour les grilles `[products]` hors boutique.
- Panier, Commande et Mon compte sans constructeur : le titre de la page s'affiche en `h1`.
- Carrousel sans titre : plus de région sans nom (`role="region"` seulement avec un libellé).
- Sélecteur de langue masqué sous deux langues ; il suit la police et la taille du menu.
- Logo du header sans priorité de chargement haute (l'image de la bannière est le LCP).
- Plus de recalcul de mise en page forcé au chargement (hauteur du header mesurée par le `ResizeObserver`).

### Ajouté
- Variables : `--mrz-title-text-transform`, `--mrz-button-font-family`, `--mrz-card-media-ratio`, `--mrz-footer-link-color`, `--mrz-image-overlay`, `--mrz-header-max-width`, `--mrz-lang-inactive-opacity`, `--mrz-product-title-font-family`, `-size`, `-text-transform`.
- Classes stables sur les blocs du header : `.mrz-header__start`, `.mrz-header__end`.
- AGENTS.md : SVG par Safe SVG, mise en forme des listes MRZ Display Post, préfixes `aa_`, point de rupture dans les media queries de l'enfant, mega menu, `--color-text` redéfinissable, pas de texte en dur dans un hook. README : Rank Math et Safe SVG dans les prérequis.

## [0.4.0] - 2026-11-17

### Ajouté
- Sections du parent **Cartes** (`mrz_cards`) et **Footer** (`mrz_footer`), venues du modèle d'enfant : présentes sur tous les sites et mises à jour avec le thème. Apparence entièrement en variables (`--mrz-cards-*`, `--mrz-card-*`, `--mrz-footer-*`). Liens du footer à pleine couleur (contraste RGAA).
- Le Footer n'est proposé que dans les sections globales (filtre `mrz_theme_global_only_layouts`, que l'enfant peut compléter).
- Sous-menus du header : chevron indicateur, fermeture retardée au survol, Échap qui ferme et rend le focus au lien parent, ouverture au toucher (premier appui ouvre, second suit le lien), ouverture vers la gauche près du bord de l'écran. Variables `--mrz-submenu-bg`, `-color`, `-width`, `-radius`, `-shadow`, `-indicator`, `-indicator-size`.
- Carrousel : `mrz_theme_carousel_open()` et `mrz_theme_carousel_close()`. Défilement natif (scroll-snap), flèches, points, lecture automatique facultative avec bouton pause, accessible (RGAA). Script chargé seulement sur les pages qui en ont un. Variables `--mrz-carousel-per-view` (décimales acceptées), `-gap`, `-button-size`, `-dot-size`.
- Règles pour l'IA : RGAA (AGENTS.md).
- Règles pour l'IA : objectif Lighthouse 100 sur les quatre scores (AGENTS.md).

### Modifié
- Sous-menus du header : le premier niveau part du bas du header, coins du haut droits, avec le fond du header par défaut (`--mrz-submenu-bg`).
- Menu mobile plein écran : le bouton fermer prend la place exacte du burger. Dans les autres styles, il est bien à droite du panneau.
- `--mrz-field-padding` et `--mrz-button-padding` sont composées de `--mrz-field-padding-y`/`-x` et `--mrz-button-padding-y`/`-x` : régler ces dernières dans l'enfant.

### Corrigé
- Menu secondaire : même taille de texte que le menu principal dans le header ; dans le menu mobile, plus de trait doublé entre les deux menus.
- Gravity Forms : le thème de formulaire est réactivé, en **Orbital** imposé et branché sur les variables du thème (`--gf-*` dans `assets/css/gravity-forms.css`). Sans lui, le champ « choix d'image » affichait ses textes cachés et les boutons radio n'étaient pas mis en forme. Les champs, libellés et le bouton suivent le fond de leur section.
- Contour de focus invisible sur une section de couleur principale : il prend maintenant la couleur du texte de la section (`--mrz-focus-color`), sur tous les fonds, les images et le header transparent (RGAA 10.7).

## [0.3.0] - 2026-11-17

### Ajouté
- Fond du site dans le Customizer (MRZ Theme > Couleurs, vide = blanc) : le thème marche sur un site sombre. Le texte prend Sombre ou Clair, celle qui contraste le plus avec le fond.
- Chaque fond de section redéfinit `--color-background`, `--color-text` et `--color-muted` pour son contenu ; les champs de formulaire suivent la section.
- `--font-accent` : troisième police facultative (la police du texte par défaut).
- Règles pour l'IA : plugins MRZ obligatoires pour les listes et les cartes, interdiction de modifier le parent installé sur un site.
- Couleurs dans le Customizer (MRZ Theme > Couleurs) : principale, secondaire, **tertiaire** et **quaternaire** (nouvelles), sombre, clair, bordures. Le texte posé sur une couleur de la marque (`--color-*-contrast`) est calculé : Sombre ou Clair selon la couleur.
- Fonds de section « Couleur secondaire », « Couleur tertiaire » et « Couleur quaternaire ».
- Filtres d'icônes : un filtre SVG par couleur de la palette (`url(#mrz-icon-primary)`…, plus `white`), qui colore exactement une icône noire et suit le Customizer. `--mrz-icon-filter` (principale) et `--mrz-icon-filter-light` (blanc).
- Menu mobile : quatre styles au choix (panneau à droite, à gauche, plein écran, déroulé sous le header).
- Emplacement de section globale « Menu mobile : sous les liens » (`mrz_mobile_menu_end`).
- « Texte sur … » pour chaque couleur de la marque (automatique, Clair, Sombre) : quand la charte contredit le contraste calculé.
- « Texte courant » (Customizer > Couleurs) : paragraphes d'une autre couleur que les titres sur le fond du site ; les titres prennent `--mrz-title-color`.
- Lien fléché : `mrz_theme_button( $link, 'link' )`, classe `.mrz-btn--link`, flèche en masque réglable (`--mrz-link-arrow`, `--mrz-link-arrow-size`, `--mrz-link-font-size`).
- Largeur de section « Étroite » (`--mrz-container-narrow`, 48rem) et niveau de titre H1 ou H2 dans le Texte libre.
- Header transparent : `--mrz-header-transparent-bg`, `-color`, `-border`, et `--mrz-header-offset` (place prise par le header, mesurée).
- Sélecteur de langue en codes, drapeaux ou les deux (Customizer > Header).
- Polylang : sections globales traduisibles d'office ; une section insérée à la main et la page 404 sont prises dans la langue courante (`mrz_theme_translated_id()`).

### Modifié
- Customizer > Couleurs : fond du site et texte courant en tête, puis les couleurs de la marque, puis Sombre, Clair et Bordures.
- Plus aucun blanc en dur : le texte sur une couleur, sur une image de fond et dans le header transparent prend « Clair » (une charte crème reste crème). Libellés des couleurs Sombre et Clair revus.
- Gravity Forms : son thème CSS (`theme.min.css`) est désactivé ; il se chargeait après l'enfant et passait devant lui. Les champs prennent le style du thème.
- `--mrz-field-bg`, `--mrz-field-color`, `--mrz-field-border` ne sont plus définies par défaut : les champs suivent la section. Les définir dans l'enfant pour les fixer sur tout le site.
- Les couleurs ne se règlent plus dans le `style.css` de l'enfant : une valeur `--color-*` laissée dans l'enfant passerait devant le Customizer.
- Une page n'affiche plus jamais son titre (son contenu porte le `<h1>`) ; un article affiche toujours le sien. Réglages « Masquer le titre » et « Masquer le footer » retirés de la meta box : le footer se retire d'une page par les conditions de sa section globale.

### Corrigé
- `mrz_theme_section_open()` : l'argument `class` accepte plusieurs classes (séparées par des espaces, ou un tableau) ; les espaces disparaissaient.
- Sélecteur de langue : plus de notices avec Polylang 3.9 (`display_names_as` obsolète, `show_wrapper` manquant).
- Menu mobile : connecté, il s'ouvre sous la barre d'admin au lieu de passer dessous.
- Section Texte libre : un formulaire Gravity Forms inséré par shortcode perdait ses champs (`wp_kses_post()` retirait les `<input>` et les `<select>`).
- La loupe du header est alignée sur les autres icônes, à la même taille.
- Un texte masqué en fin de page (zone d'annonce du panier de WooCommerce) n'ajoute plus une ligne blanche sous le footer.

## [0.2.0] - 2026-11-16

### Cassant
- Constructeur : le groupe de champs appartient au thème enfant (`acf-json/group_mrzchild_builder.json`, clé `field_mrz_builder`). Le parent n'injecte que trois sections : `mrz_text`, `mrz_html`, `mrz_global_section`. Les layouts `hero`, `text_image`, `cards`, `gallery`, `cta`, `faq`, `form`, `posts` et `map` sont retirés ; `text` et `global_section` deviennent `mrz_text` et `mrz_global_section`.
- Footer : plus de footer par défaut, ni zones de widgets, ni emplacement de menu « Footer », ni réglage de copyright. Le footer est une section globale à l'emplacement « Footer ». `mrz_theme_copyright()` devient `mrz_theme_text_tags()`.
- Couleurs : la palette se réduit à `--color-primary`, `--color-secondary`, `--color-dark`, `--color-light`, `--color-border`. `--color-accent` est retirée ; `--color-text`, `--color-muted`, `--color-background` et `--color-primary-contrast` sont dérivées.
- Variables `--mrz-footer-*` retirées.

### Ajouté
- Menu d'admin « MRZ Theme » qui ouvre le Customizer et contient les sections globales ; logos dans la section Header.
- Header : recherche, lien vers le compte (options), menu secondaire, icônes SVG remplaçables depuis l'enfant, variable `--mrz-header-padding`, hooks `mrz_header_tools_start`, `mrz_header_tools_end`, `mrz_mobile_menu_end`.
- Variables partagées : boutons (`--mrz-button-*`), champs (`--mrz-field-*`, `--mrz-label-*`), titres h1 à h6, tailles de texte, menus (`--mrz-menu-*`).
- Traduction française de l'admin du thème (`languages/fr_FR`).
- Gravity Forms branché sur les variables (`assets/css/gravity-forms.css`).
- Fichiers `css/*.css` de l'enfant chargés automatiquement après son `style.css`.
- Mises à jour par les releases GitHub (Plugin Update Checker v6.0).
- Conditions d'affichage des sections globales : sur tout le site, seulement sur certains types de contenus ou pages, ou partout sauf sur eux.
- Réglages par page (meta box MRZ Theme) : masquer le titre, le header ou le footer, forcer le header transparent ou non.

### Corrigé
- Logo trop grand sur les pages WooCommerce.
- Largeur du Texte libre qui ne suivait pas son réglage.
- Ancres cachées sous le header sticky.
- Boutons illisibles sur les fonds « couleur principale » et « sombre ».
- Gutenberg : écrans Compositions et Polices retirés d'Apparence.

## [0.1.0] - 2026-11-15
Première version : éditeur classique, constructeur ACF, sections globales, Customizer, WooCommerce, Polylang.
