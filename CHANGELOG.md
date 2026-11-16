# Changelog

Toutes les évolutions notables du thème. Format inspiré de [Keep a Changelog](https://keepachangelog.com/fr/1.1.0/), versions en [SemVer](https://semver.org/lang/fr/) : le premier chiffre change quand un nom utilisé par les thèmes enfants casse (« Cassant »).

## [Non publié]

### Cassant
- Constructeur : le groupe de champs appartient au thème enfant (`acf-json/group_mrzchild_builder.json`, clé `field_mrz_builder`). Le parent n'injecte que trois sections : `mrz_text`, `mrz_html`, `mrz_global_section`. Les layouts `hero`, `text_image`, `cards`, `gallery`, `cta`, `faq`, `form`, `posts` et `map` sont retirés ; `text` et `global_section` deviennent `mrz_text` et `mrz_global_section`.
- Footer : plus de footer par défaut, ni zones de widgets, ni emplacement de menu « Footer », ni réglage de copyright. Le footer est une section globale à l'emplacement « Footer ». `mrz_theme_copyright()` devient `mrz_theme_text_tags()`.
- Couleurs : la palette se réduit à `--color-primary`, `--color-secondary`, `--color-dark`, `--color-light`, `--color-border`. `--color-accent` est retirée ; `--color-text`, `--color-muted`, `--color-background` et `--color-primary-contrast` sont dérivées.
- Variables `--mrz-footer-*` retirées.

### Ajouté
- Menu d'admin « MRZ Theme » qui ouvre le Customizer et contient les sections globales ; logos dans la section Header.
- Header : recherche, lien vers le compte (options), menu secondaire, icônes SVG remplaçables depuis l'enfant, variable `--mrz-header-padding`, hooks `mrz_header_tools_start`, `mrz_header_tools_end`, `mrz_mobile_menu_end`.
- Variables partagées : boutons (`--mrz-button-*`), champs (`--mrz-field-*`, `--mrz-label-*`), titres h1 à h6, tailles de texte, menus (`--mrz-menu-*`).
- Gravity Forms branché sur les variables (`assets/css/gravity-forms.css`).
- Fichiers `css/*.css` de l'enfant chargés automatiquement après son `style.css`.
- Mises à jour par les releases GitHub (Plugin Update Checker v6.0).

### Corrigé
- Logo trop grand sur les pages WooCommerce.
- Largeur du Texte libre qui ne suivait pas son réglage.
- Ancres cachées sous le header sticky.
- Boutons illisibles sur les fonds « couleur principale » et « sombre ».
- Gutenberg : écrans Compositions et Polices retirés d'Apparence.

## [0.1.0] - 2026-11-15
Première version : éditeur classique, constructeur ACF, sections globales, Customizer, WooCommerce, Polylang.
