# Changelog

Toutes les évolutions notables du thème. Format inspiré de [Keep a Changelog](https://keepachangelog.com/fr/1.1.0/), versions en [SemVer](https://semver.org/lang/fr/) : le premier chiffre change quand un nom utilisé par les thèmes enfants casse (« Cassant ») ; avant la 1.0, c'est le deuxième.

## [Non publié]

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

### Modifié
- Plus aucun blanc en dur : le texte sur une couleur, sur une image de fond et dans le header transparent prend « Clair » (une charte crème reste crème). Libellés des couleurs Sombre et Clair revus.
- Gravity Forms : son thème CSS (`theme.min.css`) est désactivé ; il se chargeait après l'enfant et passait devant lui. Les champs prennent le style du thème.
- `--mrz-field-bg`, `--mrz-field-color`, `--mrz-field-border` ne sont plus définies par défaut : les champs suivent la section. Les définir dans l'enfant pour les fixer sur tout le site.
- Les couleurs ne se règlent plus dans le `style.css` de l'enfant : une valeur `--color-*` laissée dans l'enfant passerait devant le Customizer.
- Une page n'affiche plus jamais son titre (son contenu porte le `<h1>`) ; un article affiche toujours le sien. Réglages « Masquer le titre » et « Masquer le footer » retirés de la meta box : le footer se retire d'une page par les conditions de sa section globale.

### Corrigé
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
