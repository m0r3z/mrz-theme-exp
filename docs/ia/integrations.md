# Plugins et intégrations

Fiche de référence pour l'IA, lue quand la tâche touche ce sujet. Les règles générales sont dans `../../AGENTS.md`.

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
- Thème par défaut de Gravity Forms : laisser Orbital (réglage des nouvelles installations). Le parent retire les couleurs qu'il imprime pour chaque formulaire (`gform_pre_render`) : aucun contournement dans l'enfant.
- Padding des champs et des boutons : `--mrz-field-padding-y`/`-x` et `--mrz-button-padding-y`/`-x` (Gravity Forms les lit séparément), pas le raccourci `--mrz-field-padding`.
- Poids : Orbital ajoute environ 37 Ko compressés, seulement sur les pages qui ont un formulaire.

## WooCommerce
Aucune surcharge de template. `woocommerce.php` + hooks dans `inc/woocommerce.php` + `assets/css/woocommerce.css` (chargé sur tout le site : une grille `[products]` peut être sur n'importe quelle page). Panier et commande : les blocs de WooCommerce (pages créées par WooCommerce), jamais remplacés par les shortcodes ; leurs boutons prennent ceux du thème. Les boutons + et − de quantité ne concernent que l'ancien panier en shortcode. Jamais de sélecteur de WooCommerce dans l'enfant : passer par les variables (`--mrz-product-title-*`, boutons, champs). Page Boutique : ses sections (constructeur ACF) s'affichent au-dessus des produits et portent le `h1` ; sans sections, WooCommerce affiche le titre « Boutique » en `h1`.
