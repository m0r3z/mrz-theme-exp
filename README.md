# MRZ Theme Experience

Thème WordPress classique et léger de l'Agence Double A. Il remplace Salient + WPBakery sur les nouveaux projets.

- **Éditeur classique** partout (Gutenberg désactivé, CSS de blocs retirés du front).
- **Pages construites avec ACF Pro** : un constructeur (Flexible Content `mrz_builder`) dont les sections sont faites sur mesure pour chaque site, dans le thème enfant.
- **Un seul CSS compilé** (Tailwind v4, ~22 ko), JS vanilla sans jQuery.
- **Retouches sans compiler** : le thème enfant redéfinit des variables CSS et ajoute du CSS classique.

Ce guide s'adresse à la personne qui installe ou retouche un site. Les règles détaillées pour l'IA sont dans [AGENTS.md](AGENTS.md).

## Sommaire

1. [Prérequis](#prérequis)
2. [Démarrer un site](#démarrer-un-site)
3. [Construire une page](#construire-une-page)
4. [Réglages par page](#réglages-par-page)
5. [Sections globales](#sections-globales)
6. [Menu MRZ Theme](#menu-mrz-theme)
7. [Retoucher un site à la main](#retoucher-un-site-à-la-main)
8. [Blog, CPT et plugins](#blog-cpt-et-plugins)
9. [Pour l'IA](#pour-lia)
10. [Faire évoluer le thème parent](#faire-évoluer-le-thème-parent)
11. [État et feuille de route](#état-et-feuille-de-route)

## Prérequis

| Élément | Version |
| --- | --- |
| WordPress | 6.6 ou plus (testé jusqu'à 7.1) |
| PHP | 8.1 ou plus |
| ACF Pro | Obligatoire, à jour (testé en 6.8) : les plugins MRZ déclarent `Requires Plugins: advanced-custom-fields`, que la 6.1 ne satisfait pas |
| Safe SVG | Requis pour les icônes des Cartes et des réseaux du Footer, et pour un logo en SVG : WordPress refuse les SVG sans lui, et il les nettoie |
| Rank Math | Titre et meta description de chaque page (le contenu des pages est dans ACF : écrire la description page par page) |

Sans ACF Pro, le thème affiche une alerte dans l'admin et les pages montrent seulement leur contenu classique. Rien ne plante.

Aucun Node n'est nécessaire sur le serveur : le CSS compilé (`dist/main.css`) est livré avec le thème.

## Démarrer un site

1. Installer le thème parent `mrz-theme-exp` (zip de la dernière release) et un thème enfant copié du modèle privé [`mrz-theme-exp-child`](https://github.com/m0r3z/mrz-theme-exp-child). Activer **l'enfant**, jamais le parent seul.
2. Activer ACF Pro. Les groupes de champs se chargent tout seuls depuis `acf-json/` (parent et enfant) : rien à importer. Le thème enfant doit contenir `acf-json/group_mrzchild_builder.json` (fourni dans le modèle).
3. **Apparence > Menus** : affecter les emplacements.

   | Emplacement | Rôle |
   | --- | --- |
   | Primary menu | Menu du header (3 niveaux) |
   | Secondary menu | Menu secondaire à droite du header, avant la recherche, le compte et le panier (2 niveaux) ; repris sous le menu principal dans le menu mobile. Vide = rien ne s'affiche |
   | Mobile menu | Menu mobile ; s'il est vide, le menu principal est repris |

4. **MRZ Theme** (menu de l'admin) : couleurs, logo, header, menu mobile, 404 (voir [Menu MRZ Theme](#menu-mrz-theme)).
5. **Le footer** : le thème n'en affiche aucun par défaut. Créer une section globale placée à l'emplacement « Footer » (voir [Sections globales](#sections-globales)). Sans elle, le site n'a pas de pied de page.
6. Dans le `style.css` de l'enfant : couleurs et polices du client (voir [Retoucher un site à la main](#retoucher-un-site-à-la-main)).

## Construire une page

Le constructeur apparaît sous le titre des **pages** et des **sections globales**. Les articles et les CPT gardent l'éditeur classique.

- Une page avec au moins une section affiche le constructeur, et lui seul : le contenu de l'éditeur classique est alors ignoré.
- Une page sans section affiche son titre et son contenu classique.
- Repliée, une section affiche son type et son titre (ou le début de son texte) : la page se lit d'un coup d'œil.

### Les sections

Le menu « Ajouter une section » réunit deux sources :

- **les sections du site**, faites sur mesure à partir de la maquette (une par rangée : bannière, services, témoignages…). Elles vivent dans le thème enfant et n'existent que sur ce site ;
- **les sections du parent**, présentes sur tous les sites et mises à jour avec le thème :

| Section | Contenu | À savoir |
| --- | --- | --- |
| Free text (Texte libre) | Titre, texte de l'éditeur classique | Pour un formulaire, une liste MRZ Display Post ou une carte MRZ Maps : y coller le shortcode |
| Raw HTML (HTML brut) | Code HTML | Affiché tel quel, scripts compris. Visible seulement pour les administrateurs |
| Cards (Cartes) | Titre, cartes (image ou icône, titre, texte, lien, fond), en grille ou en carrousel | Une icône SVG prend la couleur du texte de la carte |
| Text and media (Texte et média) | Texte (surtitre, titre, texte, deux boutons) d'un côté ; image, second texte ou encadré coloré de l'autre | Côté, largeur des colonnes (égales ou 2/3), alignement vertical, colonne collante. Empilées sur mobile, texte en premier |
| Accordion (Accordéon) | Titre, introduction, éléments à déplier (question, réponse) | Sans JavaScript (`<details>`). Premier élément ouvert, un seul ouvert à la fois en option |
| Footer | Logo, phrase, bouton, colonnes de liens, réseaux sociaux, ligne du bas | Proposée seulement dans les sections globales, à l'emplacement « Footer » |
| Global section | Une section globale existante | Pour réutiliser un bloc à un endroit précis |

Une section sur mesure dont le nom contient `hero`, placée en premier, active le header transparent si l'option est cochée.

### Réglages communs

Le Texte libre et les sections sur mesure qui les reprennent ont un groupe de réglages :

| Réglage | Choix |
| --- | --- |
| Fond | Aucun, clair, sombre, l'une des quatre couleurs de la marque |
| Espacement | Normal, compact, large, aucun |
| Espace en haut, en bas | Comme l'espacement, ou réglé à part (aucun, compact, normal, large) |
| Largeur | Conteneur, étroite (largeur de lecture : mentions légales, article), large, pleine largeur |
| Ancre | Identifiant pour un lien `#ancre` dans un menu |
| Hauteur | Automatique, grande (70 % de l'écran), plein écran ; contenu centré verticalement |
| Image de fond | Vraie image derrière le contenu. Celle de la première section de la page se charge en priorité (image principale, LCP) |
| Voile sur l'image | Sombre (texte clair), léger (texte clair, vérifier le contraste), sans voile (la section garde ses couleurs : image composée, motif) |
| Dégradé | De la couleur de fond vers une seconde couleur de la palette, vers le bas, la droite ou en diagonale |

**Bannière de page** : un Texte libre en tête de page, titre en H1, image de fond, hauteur grande ou plein écran. Pas besoin d'une section « hero » sur mesure.

Les couleurs des fonds sont celles de MRZ Theme > Couleurs. Le texte posé sur une couleur de la marque prend Sombre ou Clair selon la couleur, automatiquement ; quand la charte en décide autrement, le réglage « Texte sur … » de la couleur l'impose.

Le Texte libre a aussi :
- un niveau de titre : H2 par défaut, H1 quand il ouvre la page (bannière, mentions légales) ;
- un surtitre (petite ligne au-dessus du titre) et un alignement à gauche ou centré ;
- deux boutons facultatifs, plein et à contour, sous le texte.

Dans les titres des sections du parent, `<em>mot</em>` met un mot en couleur et `<br>` force un retour à la ligne.

Les Cartes ont un nombre de cartes par ligne propre à la section (automatique, 2, 3, 4) et un style « chiffres clés » : le titre de la carte est le chiffre, affiché en grand, le texte sert de libellé.

### Ajouter une section sur mesure

Une section du site = un layout dans `acf-json/group_mrzchild_builder.json` de l'enfant (préfixe `mrzchild_`), son template `layouts/mrzchild-<nom>.php` et son CSS dans `css/<partie>.css` de l'enfant. La recette détaillée est dans [AGENTS.md](AGENTS.md). Une mise à jour du parent ne touche jamais ces sections.

## Réglages par page

Chaque page, article ou CPT a, dans la colonne de droite de son édition, une boîte **MRZ Theme** :

| Réglage | Effet |
| --- | --- |
| Masquer le header | Masque le header et le menu mobile (landing page). Le body reçoit `mrz-no-header` |
| Header | Défaut (réglage du Customizer), transparent ou non transparent, pour cette page seulement |

- **Titre** : une page n'affiche jamais son titre, son contenu porte son propre `<h1>` (en général la première section). Un article affiche toujours le sien. Les pages faites d'un seul shortcode (panier, commande, compte, mentions légales) ont donc besoin d'un `<h1>` dans leur contenu.
- **Footer** : c'est une section globale. Pour le retirer d'une page, l'exclure dans les conditions de la section (« Partout sauf sur… »).

## Sections globales

**MRZ Theme > Global sections.** Une section globale se construit comme une page (même constructeur) et s'affiche :

- **automatiquement sur tout le site**, à l'emplacement choisi (c'est ainsi qu'on fait le footer), ou
- **à la main**, avec la section « Global section » dans une page (choisir alors « Manual insertion only »).

| Emplacement | Position |
| --- | --- |
| Before the header / After the header | Autour du header |
| Before the content / After the content | Au début ou à la fin du contenu principal |
| Footer (the site footer) | **Le footer du site**, dans la balise `<footer>`. Le thème n'a pas d'autre footer |
| Before the footer / After the footer | Avant ou après le footer |
| WooCommerce: before the product list | Au-dessus de la liste des produits |
| WooCommerce: after a product | Sous la fiche produit |

**Où l'afficher** (pour une section placée à un emplacement) :

| Choix | Effet |
| --- | --- |
| Sur tout le site | Partout (par défaut) |
| Seulement sur… | Uniquement sur les types de contenus et les pages choisis |
| Partout sauf sur… | Partout, sauf sur les types de contenus et les pages choisis |

Un type de contenu couvre ses contenus et ses pages de liste : « Articles » inclut le blog, les catégories et les étiquettes ; « Produits » inclut la boutique et les catégories de produits. « Pages précises » permet de viser une page, la page du blog ou la boutique.

La **priorité** ordonne plusieurs sections au même emplacement (10 par défaut, plus petit = plus haut). La colonne « Where it shows » de la liste résume où chaque section apparaît. Seules les sections publiées s'affichent.

## Menu MRZ Theme

Le menu **MRZ Theme** de l'admin rassemble le thème :

- **Theme options** ouvre le Customizer natif directement sur le panneau MRZ Theme, avec l'aperçu en direct ;
- **Global sections** liste les [sections globales](#sections-globales).

Restent à leur place native : les menus (**Apparence**), le titre, le slogan et l'icône du site (**Personnaliser > Identité du site**). Les couleurs de la charte sont dans le Customizer. Le reste de l'identité visuelle (polices, tailles, rayons) est dans le CSS de l'enfant.

Les écrans Compositions et Polices de Gutenberg sont retirés d'Apparence.

| Section | Réglage | Défaut |
| --- | --- | --- |
| MRZ Theme > Colors | Couleurs principale, secondaire, tertiaire et quaternaire de la marque ; sombre (texte sur fond clair, sections « Sombre ») ; clair (texte sur fond sombre, sections « Clair ») ; bordures et traits ; fond du site (vide = blanc). Le texte suit le fond : sur un fond sombre, il passe en Clair | `#2a5bd7`, `#1d1d1f`, `#e0a526`, `#8a5cc2`, `#20201f`, `#f4f4f2`, `#dededd`, vide |
| MRZ Theme > Header | Logo (le logo natif de WordPress, déplacé ici) | |
| | Logo du header transparent : affiché avant le défilement, remplacé par le logo principal ensuite | aucun |
| | Hauteur du logo : desktop, mobile (px) | 48, 40 |
| | Disposition : classique (logo à gauche, menu à droite), menu à gauche (logo, menu juste après, outils à droite) ou centrée (logo au-dessus, menu dessous) | classique |
| | Bouton Menu aussi sur ordinateur : le menu principal s'ouvre dans le panneau du menu mobile sur tous les écrans | non |
| | Header sticky | oui |
| | Header transparent quand applicable (pages qui commencent par une section hero) | non |
| | Recherche : icône qui déplie le formulaire de recherche (Échap ou clic ailleurs pour fermer) | non |
| | Lien vers le compte : icône vers la page « Mon compte » de WooCommerce, à côté du panier | non |
| MRZ Theme > Mobile header | Header sticky sur mobile | oui |
| | Menu mobile : panneau à droite, panneau à gauche, plein écran, ou déroulé sous le header (le burger ouvre et ferme). Le parent ne pose que la position et l'ouverture ; fond, flou, survol se règlent dans l'enfant | panneau à droite |
| | Point de rupture mobile (px) : à augmenter si le menu a beaucoup d'entrées | 1000 |
| MRZ Theme > Page transitions | Transitions animées entre les pages, sans JavaScript ; désactivées pour les visiteurs qui limitent les animations | non |
| | Effet : fondu, glissement, rideau (une couleur balaie l'écran) ou zoom ; durée (ms) | fondu, 300 |
| | Rideau : couleur (palette) et sens (vers le haut, le bas, la gauche, la droite) | principale, vers le haut |
| MRZ Theme > General | Largeur du contenu (px) | 1280 |
| | Page 404 : page affichée pour une URL inexistante, avec un vrai statut 404 | page par défaut du thème |

## Retoucher un site à la main

Toutes les retouches se font dans le **thème enfant**, en CSS classique : dans l'éditeur de fichiers, le gestionnaire de fichiers N0C ou en FTP. Le thème parent ne se modifie jamais sur un site : il serait écrasé à la mise à jour.

### Couleurs, polices, formes

Les **couleurs** se règlent dans **MRZ Theme > Couleurs**, jamais dans le CSS : une charte de une à quatre couleurs de marque, plus sombre, clair et bordures.

Le reste se règle en redéfinissant les variables dans le `:root` du `style.css` de l'enfant :

```css
:root {
	--font-title: "Urbanist", Arial, sans-serif;
	--radius-button: 6px;
}
```

| Variable | Rôle | Défaut |
| --- | --- | --- |
| `--font-title` / `--font-body` | Polices des titres / du texte | police système |
| `--font-accent` | Troisième police, facultative (mono, manuscrite…) | police du texte |
| `--radius-base` / `--radius-button` | Arrondi des cartes et images / des boutons | `10px` / `999px` |
| `--spacing-section`, `-sm`, `-lg` | Espacement vertical des sections : normal, compact, large | `6rem`, `3rem`, `9rem` |
| `--mrz-h1` à `--mrz-h6` | Tailles des titres | valeurs fluides |
| `--mrz-button-*`, `--mrz-field-*`, `--mrz-label-*` | Boutons, champs et libellés, les mêmes partout (thème, Gravity Forms, WooCommerce) | voir `tokens.css` |
| `--mrz-menu-font-family`, `-font-size`, `-font-weight`, `-text-transform`, `-letter-spacing` | Police, taille, graisse, casse et espacement des menus | police du texte, `1rem`, `500`, aucune, aucun |
| `--mrz-body-size`, `--mrz-body-line-height` | Taille et interligne du texte | `1.0625rem`, `1.6` |
| `--mrz-gutter` | Marges latérales du contenu, du header et des sections | de 20 à 40 px selon l'écran |
| `--mrz-header-padding` | Espace en haut et en bas du header | `1rem` |
| `--mrz-header-bg`, `--mrz-header-color`, `--mrz-header-border` | Header | fond, texte, bordure du site |
| `--mrz-icon-filter`, `--mrz-icon-filter-light` | Couleur des icônes noires en image (SVG, PNG) : `filter: var(--mrz-icon-filter)`. Valeurs possibles : `url(#mrz-icon-primary)`, `-secondary`, `-tertiary`, `-quaternary`, `-dark`, `-light`, `-border`, `-white` | principale, blanc |

Les couleurs du Customizer s'écrivent en variables, à utiliser dans le CSS de l'enfant : `--color-primary`, `--color-secondary`, `--color-tertiary`, `--color-quaternary`, `--color-dark`, `--color-light`, `--color-border`. En découlent, sans réglage : le texte atténué (`--color-muted`), le fond de page (blanc) et le texte posé sur chaque couleur de la marque (`--color-primary-contrast`, `-secondary-`, `-tertiary-`, `-quaternary-`, sombre ou blanc selon la couleur). La liste complète est dans `src/css/tokens.css` du parent. La largeur du site et la hauteur du logo du header se règlent dans MRZ Theme, pas en CSS.

### Polices

Les polices sont auto-hébergées (performance, RGPD) : déposer les fichiers `woff2` dans un dossier `fonts/` de l'enfant, déclarer le `@font-face` en haut de son `style.css` (exemple commenté dans le modèle), puis renseigner `--font-title` et `--font-body`.

### CSS sur mesure

Écrire sous les variables, avec les classes `.mrz-` du thème :

```css
.mrz-header .mrz-menu a {
	text-transform: uppercase;
}
```

Le CSS du parent est rangé dans des couches (`@layer`) : le CSS de l'enfant passe toujours devant, **sans `!important`**.

**Règle absolue** : pas de classe Tailwind nouvelle dans l'enfant (`mt-12`, `bg-red-500`…). Le CSS du parent ne contient que les classes qu'il utilise lui-même : une classe ajoutée dans l'enfant n'existe nulle part et n'aura aucun effet. Écrire une vraie règle CSS à la place.

### Surcharger un template

Copier le fichier du parent dans l'enfant, au même chemin, puis le modifier :

| Pour changer | Copier |
| --- | --- |
| Une section du parent | `layouts/mrz-<nom>.php` (ex. `mrz-text.php`) |
| Le header | `parts/header/header-classic.php` ou `header-centered.php` |
| Le menu mobile | `parts/mobile-menu/offcanvas.php` |
| La carte d'un article dans les listes | `parts/content/content.php`, ou `content-{post_type}.php` pour un seul type |
| L'affichage d'un article seul | `parts/content/single.php`, ou `single-{post_type}.php` pour un seul type |

Une surcharge ne reçoit plus les corrections du parent : n'en faire qu'en dernier recours.

Pour ajouter un élément sans copier de template, l'enfant se branche sur un hook dans son `functions.php` :

| Hook | Où |
| --- | --- |
| `mrz_header_tools_start` | Header, avant le menu secondaire, la recherche, le compte et le panier |
| `mrz_header_tools_end` | Header, après le panier, avant le bouton du menu mobile |
| `mrz_mobile_menu_end` | En bas du menu mobile, aussi disponible comme emplacement de section globale (« Menu mobile : sous les liens ») |

Un hook ne doit pas écrire de texte visible : l'enfant n'a pas de fichier de traduction, et le texte ne suivrait pas les langues du site. Un bouton comme « Prendre rendez-vous » se met dans le **menu secondaire**, avec une classe CSS sur l'élément du menu (champ « Classes CSS » d'Apparence > Menus), mise en forme dans `css/header.css` de l'enfant : le texte se traduit avec le menu. Les hooks servent aux éléments sans texte ou dont le texte vient d'un champ (section globale, réglage) :

```php
add_action( 'mrz_header_tools_end', function () {
	// Une section globale « Bouton du header », traduite avec Polylang comme les autres.
	mrz_theme_render_global_section( 123 );
} );
```

### Changer une icône

Les icônes du header sont dans `assets/icons/` du parent : `cart.svg` (panier), `account.svg` (compte), `search.svg` (recherche). Pour en changer une, déposer un SVG du même nom dans `assets/icons/` de l'enfant : il est pris à la place, sans toucher au code. Le SVG prend la couleur du texte s'il utilise `fill="currentColor"` ; sa taille se règle en CSS sur `.mrz-icon`.

## Blog, CPT et plugins

- **Blog et archives** : grille de cartes avec image, date et catégories, pagination, titres d'archive sans préfixe (« Actualités » plutôt que « Catégorie : Actualités »).
- **Article seul** : image à la une, contenu, navigation entre articles, commentaires.
- **CPT** : affichés comme les articles. Pour un rendu propre à un CPT, créer `parts/content/single-{post_type}.php` (et `content-{post_type}.php` pour sa carte) dans l'enfant.
- **WooCommerce** : aucun template surchargé, donc rien à mettre à jour quand WooCommerce évolue. Les styles suivent les variables du thème et ne se chargent que sur les pages WooCommerce. Icône du panier dans le header, avec le nombre d'articles, et icône du compte en option (MRZ Theme > Header). Les pages Panier et Commande utilisent les shortcodes `[woocommerce_cart]` et `[woocommerce_checkout]`, pas les blocs.
- **Polylang** : sélecteur de langue dans le header et le menu mobile, dès que Polylang est actif.
- **Gravity Forms**, **MRZ Display Post**, **MRZ Maps** : leurs shortcodes (`[gravityform]`, `[mrzdpe]`, `[mrzme]`), dans un Texte libre, un champ de section ou un widget.

## Pour l'IA

L'IA construit chaque site à partir de sa maquette : une section sur mesure par rangée (champs ACF, template, CSS) dans le thème enfant, puis les pages en remplissant le constructeur, jamais en HTML :

```php
update_field(
	'field_mrz_builder',
	array(
		array(
			'acf_fc_layout' => 'mrzchild_hero',
			'title'         => 'Titre de la page',
			'settings'      => array( 'background' => 'dark' ),
		),
		array(
			'acf_fc_layout' => 'mrz_text',
			'content'       => '<p>Texte libre.</p>',
		),
	),
	$post_id
);
```

La recette d'une section, les noms des champs et les règles du constructeur sont dans [AGENTS.md](AGENTS.md). Tout ce que l'IA construit reste visible et modifiable dans l'admin.

## Faire évoluer le thème parent

Seul le thème parent se compile, dans son dépôt Git.

```bash
npm install
npm run build
```

`npm run watch` recompile à chaque modification pendant le développement.

- **Recompiler avant chaque commit** qui touche un template PHP ou `src/css/` : Tailwind ne garde que les classes trouvées dans les fichiers. `dist/main.css` est commité.
- Une classe Tailwind s'écrit en entier dans le code (pas `'md:grid-cols-' . $n`), sinon Tailwind ne la voit pas.
- Le JS (`assets/js/`) et `assets/css/woocommerce.css` ne passent par aucun build.
- **Traductions** : chaînes source en anglais, traduction française dans `languages/fr_FR.po` et `fr_FR.mo`. Après un ajout ou une modification de chaîne : régénérer le `.pot` (`wp i18n make-pot . languages/mrz-theme-exp.pot --exclude=node_modules,lib,dist,src --skip-js`), compléter `fr_FR.po`, puis `wp i18n make-mo languages/fr_FR.po languages/`.
- **Champs ACF** : sur le site de développement, définir `define( 'MRZ_THEME_DEV', true );` dans `wp-config.php`. Les modifications des groupes du parent (`group_mrz_*`) s'enregistrent alors dans `acf-json/` du thème. Sans cette constante (sites clients), elles ne s'écrivent jamais dans le parent.
- **Version** : `Version:` dans `style.css` et `MRZ_THEME_VERSION` dans `functions.php`, toujours identiques.
### Publier une mise à jour

Les sites se mettent à jour depuis les releases GitHub du dépôt, grâce à [Plugin Update Checker](https://github.com/YahnisElsts/plugin-update-checker) (`lib/`, branché dans `inc/updater.php`).

1. **Version** : monter `Version:` dans `style.css` et `MRZ_THEME_VERSION` dans `functions.php`. Premier chiffre si un nom utilisé par les enfants casse (fonction `mrz_theme_*`, classe `.mrz-`, variable, hook, section `mrz_`), deuxième pour un ajout, troisième pour une correction.
2. **CHANGELOG.md** : déplacer « Non publié » sous le numéro de version, avec la date. Les notes de la release reprennent ce bloc.
3. **Build** : `npm run build`, commiter, pousser `main`.
4. **Recette sur MRZ-Dev** (bientôt `helloworld.local`, voir plus bas), connecté en administrateur, en desktop et en mobile :
   - la **page d'accueil** de MRZ-Dev, qui sert de recette : titres, texte, listes, boutons et lien fléché sur chaque fond, cartes en grille et en carrousel (flèches, points, pause, glissé au doigt), ancre sous le header, formulaire, liste MRZ Display Post dans un Texte libre (la liste s'affiche, aucun JSON en clair), MRZ Maps, HTML brut ;
   - accueil, une page en Texte libre, un article, une catégorie, une recherche, une 404 (vrai statut 404) ;
   - boutique, fiche produit, ajout au panier (la pastille se met à jour), panier, commande, compte ;
   - menu mobile (ouverture, Échap, sous-menus), sous-menus desktop (survol, Tab puis Échap, appui sur tablette), recherche du header, footer ;
   - **site sombre** : dans le Customizer, régler « Fond du site » sur une couleur sombre et repasser la page Recette en aperçu, sans publier (texte de chaque fond de section, formulaire dans une section claire, header transparent, image de fond) ;
   - **bilingue** (HelloWorld, Polylang) : sélecteur de langue, footer et section globale insérée à la main dans chaque langue, 404 dans chaque langue ;
   - aucune nouvelle ligne dans `wp-content/debug.log`.
5. **Release** : un tag égal à la version précédée de `v`, les notes tirées du CHANGELOG :

   ```bash
   GH_TOKEN=$(gh auth token --user m0r3z) gh release create v0.4.0 --title "0.4.0" --notes-file notes.md
   ```

6. **Premier site** : lancer la mise à jour sur un site de test qui n'est pas une copie Git (`dev.local`), refaire la recette rapide, puis seulement les autres sites, depuis ManageWP. Chaque site affiche « Mise à jour disponible » dans les 12 heures, ou tout de suite depuis **Tableau de bord > Mises à jour**.

- Le zip téléchargé est celui que GitHub génère pour la release : les fichiers listés dans `.gitattributes` (`package.json`…) en sont exclus.
- Une release marquée « pre-release » est ignorée par les sites.
- Sans aucune release, les sites lisent la version de `style.css` sur `main` : ne monter la version qu'au moment de publier.
- Une copie de travail Git (site de développement) ne cherche jamais de mise à jour : elle écraserait le dépôt.
- La mise à jour n'est détectée que sur un site où le thème est actif (directement ou par son enfant) : le contrôle est chargé par son `functions.php`. Pour tester sur un site de démo, activer le thème le temps du test.
- Première mise à jour testée le 16/11/2026 sur `dev.local` : 0.1.0 → 0.2.0 par le système de mise à jour de WordPress, nom du dossier conservé, fichiers de développement absents.
- Le thème enfant n'est jamais mis à jour à distance.

### Organisation des fichiers

```
mrz-theme-exp/
├── functions.php        chargeur, aucune logique
├── inc/                 options, setup, assets, customizer, template-tags,
│                        builder (moteur), builder-sections (sections du parent),
│                        global-sections, woocommerce, updater, admin
├── acf-json/            groupe de champs des sections globales (emplacement)
├── layouts/             templates des sections du parent (mrz-text, mrz-html, mrz-global-section)
├── parts/               header/, mobile-menu/, content/
├── src/css/             tokens.css (variables), base.css, components.css, main.css (entrée)
├── dist/main.css        CSS compilé, commité
├── assets/              js/main.js, css/woocommerce.css, icons/ (SVG du header)
├── lib/                 Plugin Update Checker (mises à jour par GitHub)
└── AGENTS.md            règles pour l'IA
```

## État et feuille de route

**Version publiée : 0.5.0 (17/11/2026).** Détail des changements dans [CHANGELOG.md](CHANGELOG.md).

**Aucun site en production** : le thème tourne sur MRZ-Dev (où vit le dépôt) et sur trois sites de test, HelloWorld, Beta-Bird (Energies Réunion, bilingue) et Beta-Sonny (Morez, site sombre). Tant que c'est le cas, un changement cassant ne demande aucune migration.

**Site de test :** `https://helloworld.local/`, instance de développement dédiée aux recettes (Polylang compris).

En place : éditeur classique, constructeur (sections du parent injectées, sections sur mesure dans l'enfant), sections globales avec conditions (dont le footer), réglages par page, menu MRZ Theme sur le Customizer, header classique ou centré avec menu secondaire, recherche, compte et panier, menu mobile en quatre styles, couleurs de la charte dans le Customizer (quatre couleurs de marque, sombre, clair, bordures) et filtres d'icônes qui les suivent, variables partagées (texte, boutons, champs, menus), Gravity Forms et WooCommerce branchés sur ces variables, traduction française, mises à jour par les releases GitHub (testées sur `dev.local`).

À faire, chacun comme un chantier à part, dans l'ordre conseillé :

1. **Polylang** : passer une recette sur `helloworld.local`, en bilingue. Déjà en place depuis le site test Energies Réunion : sections globales traduisibles d'office, section insérée à la main et page 404 prises dans la langue courante, sélecteur compatible Polylang 3.9 (codes, drapeaux ou les deux). Reste à vérifier les conditions des sections globales sur des pages d'une seule langue.
2. ~~**Menus du header**~~ : fait après la 0.3.0 (sous-menus au survol, au clavier, au toucher, retournés au bord de l'écran, variables `--mrz-submenu-*`).
3. **WooCommerce** : avec les variables :
   - la liste des produits, la fiche produit et sa galerie, y compris en responsive ;
   - les boutons « Ajouter au panier », « Valider la commande » et « Commander » ;
   - le panier, la commande, et le compte client avec son menu de navigation.
   
   Ajouter une option « Description et avis à la suite, sans onglets », par les hooks WooCommerce et sans copie de template : c'est une demande fréquente. Dans le parent.
4. **Blog** : réglage de l'article seul, avec deux choix (demandé par le site test Energies Réunion) :
   - image à la une au-dessus du titre (l'actuel) ;
   - bannière avec le titre posé sur l'image, header transparent automatique dans ce cas.
   
   Mise en forme de la page des articles et des catégories. Dans le parent.
5. **JS, en modules chargés seulement sur les pages qui s'en servent** :
   - **lightbox** : sur les liens vers une image et sur les galeries natives de WordPress (`[gallery]`), qui sont aussi à mettre en forme ;
   - ~~**carrousel**~~ : fait après la 0.3.0 (`mrz_theme_carousel_open()`, défilement natif, RGAA). Reste à faire si besoin : un bandeau de logos en défilement continu, avec bouton pause ;
   - **vidéo de fond de section** : un réglage « vidéo » (ID de média) à côté de l'image de fond, `<video autoplay muted loop playsinline>` avec l'image en `poster`.
   
   Le parent fournit le comportement, l'enfant le balisage.
6. **V2** :
   - animations au défilement et parallax ;
   - **méga menu** : un élément du menu qui ouvre une section globale. Il réutilise ce qui existe déjà au lieu d'ajouter un constructeur de menus.
7. **Premier site client** : vérifier que ManageWP détecte et lance la mise à jour du thème.
8. **Theme Check** : passer le plugin avant la 1.0.

Petits restes, à traiter quand l'occasion se présente :
- **Bordure des champs sur les fonds de couleur** (RGAA, contraste des composants) : sur une section sombre ou colorée, `--color-border` peut se confondre avec le fond. Prendre une bordure tirée du texte de la section (`color-mix()` de `--color-text`) quand `--mrz-field-border` n'est pas réglée.
- **Mobile, connecté** : une bande de page apparaît au-dessus du header sticky au défilement.

Pas prévu dans le parent : d'autres variantes de header et les effets de survol. Ils se font dans le thème enfant, en CSS, site par site. Le parent porte les comportements (sticky, menus, accessibilité, JS, intégrations) ; l'enfant porte l'apparence. Un ajout qui revient sur trois sites remonte dans le parent.
