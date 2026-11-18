# MRZ Theme Experience

Thème WordPress classique et léger de l'Agence Double A. Il remplace Salient + WPBakery sur les nouveaux projets.

- **Éditeur classique** partout (Gutenberg désactivé, CSS de blocs retirés du front).
- **Pages construites avec ACF Pro** : un constructeur (Flexible Content `mrz_builder`) dont les sections sont faites sur mesure pour chaque site, dans le thème enfant.
- **Un seul CSS compilé** (Tailwind v4, 66 ko, 13 ko compressé), JS vanilla sans jQuery, en petits modules chargés seulement là où ils servent.
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
| Cards (Cartes) | Titre, cartes (image ou icône, titre, texte, lien, fond), en grille ou en carrousel | Une icône SVG prend la couleur du texte de la carte. Lien des cartes en lien fléché, bouton à contour ou bouton plein |
| Text and media (Texte et média) | Texte (surtitre, titre, texte, deux boutons) d'un côté ; image, second texte ou encadré coloré de l'autre | Côté, largeur des colonnes (égales ou 2/3), alignement vertical, colonne collante. Empilées sur mobile, texte en premier |
| Gallery (Galerie) | Titre, introduction, images ; grille, mosaïque (formats d'origine, en colonnes) ou carrousel, images par ligne, format, légendes | Ouvre les images dans la lightbox du thème |
| Anchor menu (Sous-menu d'ancres) | Liens vers les sections de la page | Collé sous le header en option, lien de la section affichée mis en évidence |
| Scrolling band (Bandeau défilant) | Textes ou logos qui défilent en continu | Bouton pause, arrêt au survol, rien ne bouge si le visiteur limite les animations |
| Accordion (Accordéon) | Titre, introduction, éléments à déplier (question, réponse) | Sans JavaScript (`<details>`). Premier élément ouvert, un seul ouvert à la fois en option. Ouverture et fermeture en douceur (`--mrz-accordion-duration`, 0,35 s) ; jamais d'animation au défilement sur ses éléments, même en cascade |
| Footer | Logo, phrase, bouton, colonnes de liens, réseaux sociaux, ligne du bas | Proposée seulement dans les sections globales, à l'emplacement « Footer » |
| Global section | Une section globale existante | Pour réutiliser un bloc à un endroit précis |

Si l'option est cochée, le header est transparent sur toute page dont la première section a une image ou une vidéo de fond (une section sur mesure dont le nom contient `hero` compte aussi).

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
| Animation au défilement | Aucune, apparition vers le haut, fondu, zoom, en cascade (éléments un par un, sauf dans un accordéon). Déclenchée quand l'élément entre à l'écran, puis jouée en entier ; jouée à l'envers en remontant, ou une seule fois (MRZ Theme > General) ; jamais sur la première section ; coupée si le visiteur limite les animations |
| Parallaxe | Aucune, légère, moyenne : l'image de fond défile moins vite que la page |
| Vidéo de fond | MP4 ou WebM, sans son, en boucle, avec bouton pause. L'image de fond s'affiche d'abord et reste seule sur mobile (sauf « Vidéo sur mobile »), si le visiteur limite les animations ou économise ses données |

**Bannière de page** : un Texte libre en tête de page, titre en H1, image de fond, hauteur grande ou plein écran. Le header transparent suit tout seul.

Les couleurs des fonds sont celles de MRZ Theme > Couleurs. Le texte posé sur une couleur de la marque prend Sombre ou Clair selon la couleur, automatiquement ; quand la charte en décide autrement, le réglage « Texte sur … » de la couleur l'impose.

Le Texte libre a aussi :
- un niveau de titre : H2 par défaut, H1 quand il ouvre la page (bannière, mentions légales) ;
- un surtitre (petite ligne au-dessus du titre) et un alignement à gauche ou centré ;
- deux boutons facultatifs, plein et à contour, sous le texte.

Dans les titres des sections du parent, `<em>mot</em>` met un mot en couleur et `<br>` force un retour à la ligne.

Les Cartes ont un nombre de cartes par ligne propre à la section (automatique, 2, 3, 4) et un style « chiffres clés » : le titre de la carte est le chiffre, affiché en grand, le texte sert de libellé. En option, les chiffres montent depuis 0 quand les cartes arrivent à l'écran (compteur).

### Ajouter une section sur mesure

Une section du site = un layout dans `acf-json/group_mrzchild_builder.json` de l'enfant (préfixe `mrzchild_`), son template `layouts/mrzchild-<nom>.php` et son CSS dans `css/<partie>.css` de l'enfant. La recette détaillée est dans [AGENTS.md](AGENTS.md). Une mise à jour du parent ne touche jamais ces sections.

## Réglages par page

Chaque page, article ou CPT a, dans la colonne de droite de son édition, une boîte **MRZ Theme** :

| Réglage | Effet |
| --- | --- |
| Masquer le header | Masque le header et le menu mobile (landing page). Le body reçoit `mrz-no-header` |
| Header | Défaut (réglage du Customizer), transparent ou non transparent, pour cette page seulement |

- **Titre** : une page n'affiche jamais son titre, son contenu porte son propre `<h1>` (en général la première section). Un article affiche toujours le sien. Les pages faites d'un seul bloc ou shortcode (panier, commande, compte, mentions légales) ont donc besoin d'un `<h1>` dans leur contenu.
- **Footer** : c'est une section globale. Pour le retirer d'une page, l'exclure dans les conditions de la section (« Partout sauf sur… »).

## Sections globales

**MRZ Theme > Global sections.** Une section globale se construit comme une page (même constructeur) et s'affiche :

- **automatiquement sur tout le site**, à l'emplacement choisi (c'est ainsi qu'on fait le footer), ou
- **à la main**, avec la section « Global section » dans une page (choisir alors « Manual insertion only »).

| Emplacement | Position |
| --- | --- |
| Top bar (above the header, hidden on scroll) | **Bandeau au-dessus du header** (« Livraison offerte dès… ») : il défile avec la page, le header sticky reste seul en haut ; sous un header transparent, le header descend sous le bandeau puis remonte. Immobile pendant les transitions de page, comme le header. Les mêmes sections s'affichent en haut du panneau du panier. Espacement réduit et texte plus petit d'office ; fond par le réglage Fond de la section (Texte libre ou Bandeau défilant). Rien ne s'affiche sans section |
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
| | Disposition : classique (logo à gauche, menu à droite), menu à gauche (logo, menu juste après, outils à droite), centrée (logo au-dessus, menu dessous) ou logo centré (menu principal à gauche, menu secondaire et outils à droite ; logo à gauche sur mobile) | classique |
| | Bouton Menu sur ordinateur : non (mobile seulement), à la place du menu principal, ou en plus du menu principal. Il ouvre le panneau du menu mobile (menu Mobile s'il est affecté, sinon le menu principal) | non |
| | Mot « Menu » écrit à côté de l'icône du bouton Menu | non |
| | Header sticky | oui |
| | Header transparent quand applicable (pages dont la première section a une image ou une vidéo de fond) | non |
| | Recherche : icône qui déplie le formulaire de recherche (Échap ou clic ailleurs pour fermer) | non |
| | Lien vers le compte : icône vers la page « Mon compte » de WooCommerce, à côté du panier | non |
| MRZ Theme > Mobile header | Header sticky sur mobile | oui |
| | Menu mobile : panneau à droite, panneau à gauche, plein écran, ou déroulé sous le header (le burger ouvre et ferme). Le parent ne pose que la position et l'ouverture ; fond, flou, survol se règlent dans l'enfant | panneau à droite |
| | Point de rupture mobile (px) : à augmenter si le menu a beaucoup d'entrées | 1000 |
| MRZ Theme > Page transitions | Transitions animées entre les pages, sans JavaScript ; désactivées pour les visiteurs qui limitent les animations | non |
| | Effet : fondu, glissement, rideau (la page s'efface en balayant) ou zoom ; durée (ms). En deux temps : la page disparaît sur une couleur, puis la suivante arrive, sans superposition | fondu, 300 |
| | Couleur entre les deux pages (palette ou fond du site) ; sens du rideau, affiché seulement pour le rideau | fond du site, vers le haut |
| MRZ Theme > General | Largeur du contenu (px) | 1280 |
| | Surlignage animé du mot en couleur des titres | non |
| | Jouer les animations au défilement une seule fois : un élément apparu reste affiché, même si on remonte puis redescend (décoché : l'animation se joue en descendant, et à l'envers en remontant) | non |
| | Page 404 : page affichée pour une URL inexistante, avec un vrai statut 404 | page par défaut du thème |
| MRZ Theme > Shop (WooCommerce actif) | Icône du panier : ouvre un panneau à droite, ou mène à la page Panier (toujours le cas sur les pages Panier et Commande) | panneau |
| | Ajout au panier sans recharger la fiche produit (les listes suivent le réglage de WooCommerce) | oui |
| | Description, informations complémentaires et avis, en accordéons : à côté des images sous le bouton d'ajout, ou sous les images | à côté des images |
| | Produits par ligne (boutique, catégories ; le shortcode `[products]` garde ses colonnes, MRZ Product Filter impose les siennes avec sa barre latérale) ; sur mobile (1 ou 2) | 4, 2 |
| | Produits similaires : nombre sous la fiche produit, sur une ligne (0 : aucun) | 4 |
| | Produits par page | 12 |
| | Menu de tri, nombre de résultats | oui, oui |
| | Badge promo : texte, réduction en pourcentage, aucun | texte |
| | Boutons + et − autour de la quantité | oui |

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

- **Blog et archives** : grille de cartes avec image, date et catégories, extrait, pagination, titres d'archive sans préfixe (« Actualités » plutôt que « Catégorie : Actualités »). Réglages dans MRZ Theme > Blog : articles par ligne sur ordinateur (2 ou 3, un seul sur mobile), extrait affiché ou non. Sur la page des articles et les catégories, les catégories s'affichent en cartouches entre le titre et les articles : « Tous » puis chaque catégorie qui a des articles, celle de la page en plein. Ce sont des liens vers les pages de catégorie, pas un filtre. Rien sous deux catégories. Variables `--mrz-blog-category-radius`, `--mrz-blog-categories-space`.
- **Page des articles** (Réglages > Lecture) : si elle a des sections dans le constructeur, elles s'affichent au-dessus de la liste et portent le `h1` (comme la page Boutique), sur la première page de la liste seulement. Sans sections, son titre s'affiche en `h1`.
- **Article seul** : date et catégories, titre, image à la une, contenu, étiquettes, navigation entre articles, commentaires. MRZ Theme > Blog > « Disposition de l'article seul » : classique (titre, puis image) ou bannière (l'image à la une en fond du haut de l'article, titre posé dessus avec le voile des images de section, header transparent par-dessus, même si le header transparent n'est pas activé). Un article sans image à la une reste en classique, les CPT aussi. Les liens vers les articles précédent et suivant se retirent dans MRZ Theme > Blog.
- **CPT** : affichés comme les articles. Pour un rendu propre à un CPT, créer `parts/content/single-{post_type}.php` (et `content-{post_type}.php` pour sa carte) dans l'enfant.
- **WooCommerce** : aucun template surchargé, donc rien à mettre à jour quand WooCommerce évolue. Les styles suivent les variables du thème. Icône du panier dans le header, avec le nombre d'articles, qui ouvre le panneau du panier ; icône du compte en option (MRZ Theme > Header). Réglages de la boutique dans MRZ Theme > Shop. Les pages Panier et Commande gardent les **blocs** de WooCommerce (pages créées ainsi à l'installation) : leur mise en page vient de WooCommerce, leurs boutons du thème (`.wp-element-button`, dans `assets/css/woocommerce.css`). Ne pas les remplacer par les anciens shortcodes. **Variations** : sur chaque attribut (Produits > Attributs > Modifier), champ « Type » : Menu déroulant (WooCommerce), Pastilles (texte, pour les tailles) ou Couleurs (ronds, pour les teintes ; une couleur par terme dans Configurer les termes). Les attributs propres à un seul produit restent en menu déroulant. Mon compte, visiteur : connexion seule, et inscription sur `?action=register` quand elle est ouverte (WooCommerce > Réglages > Comptes et confidentialité, « Autoriser les clients à créer un compte sur la page Mon compte »). Panier vide : le thème ne garde que le message et le lien vers la boutique (filtre `render_block_data`). Les boutons + et − du thème ne concernent que le panier en shortcode : le bloc Panier a les siens.
- **Polylang** : sélecteur de langue dans le header et le menu mobile, dès que Polylang est actif.
- **Gravity Forms**, **MRZ Display Post**, **MRZ Maps** : leurs shortcodes (`[gravityform]`, `[mrzdpe]`, `[mrzme]`), dans un Texte libre, un champ de section ou un widget.

## Pour l'IA

L'IA construit chaque site à partir de sa maquette : une section sur mesure par rangée (champs ACF, template, CSS) dans le thème enfant, puis les pages en remplissant le constructeur, jamais en HTML :

```php
update_field(
	'field_mrz_builder',
	array(
		array(
			'acf_fc_layout' => 'mrz_text',
			'title'         => 'Titre de la page',
			'title_tag'     => 'h1',
			'settings'      => array( 'background_image' => 123, 'height' => 'large' ),
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
   - boutique, fiche produit (galerie, accordéons, + et −), ajout au panier sans rechargement (la pastille se met à jour, le panneau s'ouvre), panneau du panier (clavier, Échap, suppression d'un article, panier vide), panier, commande, compte ;
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
├── inc/                 options, setup, assets, admin, customizer, template-tags,
│                        builder (moteur), builder-sections (sections du parent),
│                        global-sections, page-options, blog, modules, carousel, lightbox,
│                        video, woocommerce, woocommerce-swatches, updater
├── acf-json/            groupe de champs des sections globales (emplacement, conditions)
├── layouts/             sections du parent : mrz-text, mrz-html, mrz-global-section,
│                        mrz-cards, mrz-footer, mrz-gallery, mrz-split, mrz-accordion,
│                        mrz-anchors, mrz-marquee
├── parts/               header/ (classic, centered, left, split), mobile-menu/, content/
├── src/css/             tokens.css (variables), base.css, components.css,
│                        gravity-forms.css, main.css (entrée)
├── dist/main.css        CSS compilé, commité
├── assets/js/           main, motion, carousel, lightbox, video, counter, marquee,
│                        anchors, shop (chacun chargé seulement où il sert)
├── assets/css/          woocommerce.css (sans build)
├── assets/icons/        SVG du header et du menu d'admin
├── languages/           traduction française de l'admin
├── lib/                 Plugin Update Checker (mises à jour par GitHub)
└── AGENTS.md            règles pour l'IA
```

## État et feuille de route

**Version publiée : 0.8.6 (18/11/2026).** Détail des changements dans [CHANGELOG.md](CHANGELOG.md).

**Aucun site en production** : le thème tourne sur MRZ-Dev (où vit le dépôt) et sur trois sites de test, HelloWorld, Beta-Bird (Energies Réunion, bilingue) et Beta-Sonny (Morez, site sombre). Tant que c'est le cas, un changement cassant ne demande aucune migration.

**Site de test :** `https://helloworld.local/`, instance de développement dédiée aux recettes (Polylang compris).

En place : éditeur classique, constructeur (sections du parent injectées, sections sur mesure dans l'enfant), sections globales avec conditions (dont le footer), réglages par page, menu MRZ Theme sur le Customizer, header en quatre dispositions (classique, centré, menu à gauche, logo centré) avec mega menu, menu secondaire, recherche, compte et panier, menu mobile en quatre styles, dix sections du parent (dont Galerie, Texte et média, Accordéon, Sous-menu d'ancres, Bandeau défilant), animations au défilement, parallaxe, lightbox, carrousel, vidéo de fond, transitions de page, couleurs de la charte dans le Customizer (quatre couleurs de marque, sombre, clair, bordures) et filtres d'icônes qui les suivent, variables partagées (texte, boutons, champs, menus), Gravity Forms et WooCommerce (panneau du panier, variations en pastilles ou couleurs, compte) branchés sur ces variables, traduction française, mises à jour par les releases GitHub (testées sur `dev.local`).

**0.9**, dernière version avant la 1.0 : réglages du blog (point 4, fait) et les deux petits restes ci-dessous. **1.0** : Theme Check (point 9), puis premier site client (point 8). Polylang (point 1) attend la suite.

À faire, chacun comme un chantier à part :

1. **Polylang** : reporté après la 1.0. Passer une recette sur `helloworld.local`, en bilingue. Déjà en place depuis le site test Energies Réunion : sections globales traduisibles d'office, section insérée à la main et page 404 prises dans la langue courante, sélecteur compatible Polylang 3.9 (codes, drapeaux ou les deux). Reste à vérifier les conditions des sections globales sur des pages d'une seule langue.
2. ~~**Menus du header**~~ : fait après la 0.3.0 (sous-menus au survol, au clavier, au toucher, retournés au bord de l'écran, variables `--mrz-submenu-*`).
3. ~~**WooCommerce**~~ : fait en 0.8 (réglages Boutique, panneau du panier, ajout sans rechargement, accordéons, galerie, compte). À proposer ensuite : barre « livraison offerte » dans le panneau, bouton d'ajout collant sur mobile.
4. ~~**Blog (0.9)**~~ : fait, non publié. Customizer > MRZ Theme > Blog : article seul classique ou en bannière (titre sur l'image, header transparent), articles par ligne (2 ou 3), extrait affiché ou non ; sections de la page des articles au-dessus de la liste. Reste à voir à l'usage : sections propres aux catégories, choix de la disposition article par article.
5. ~~**JS, en modules chargés seulement sur les pages qui s'en servent**~~ : faits en 0.6 (lightbox et galerie, vidéo de fond) ; carrousel fait après la 0.3.0.
6. ~~**V2**~~ : animations au défilement et parallaxe faites en 0.6 (déclenchées par `motion.js` depuis la 0.8.4, parallaxe en CSS seul) ; mega menu fait en 0.5, avec les sections globales en colonne en 0.5.1.
7. ~~**0.7, sections d'appoint**~~ : faites (sous-menu d'ancres, bandeau défilant, compteur ; séparateurs de forme abandonnés). Abandonné pour le parent : la fenêtre pop-up (ce sera un plugin à part), la transition d'élément partagé et la révélation circulaire (dans l'enfant du site qui les veut).
8. **Premier site client (1.0)** : vérifier que ManageWP détecte et lance la mise à jour du thème.
9. **Theme Check (1.0)** : passer le plugin avant la 1.0.

Petits restes, à traiter dans la 0.9 :
- **Bordure des champs sur les fonds de couleur** (RGAA, contraste des composants) : sur une section sombre ou colorée, `--color-border` peut se confondre avec le fond. Prendre une bordure tirée du texte de la section (`color-mix()` de `--color-text`) quand `--mrz-field-border` n'est pas réglée.
- **Mobile, connecté** : une bande de page apparaît au-dessus du header sticky au défilement.

Pas prévu dans le parent : d'autres variantes de header et les effets de survol. Ils se font dans le thème enfant, en CSS, site par site. Le parent porte les comportements (sticky, menus, accessibilité, JS, intégrations) ; l'enfant porte l'apparence. Un ajout qui revient sur trois sites remonte dans le parent.
