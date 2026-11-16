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
| ACF Pro | Obligatoire |

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

4. **MRZ Theme** (menu de l'admin) : logo, header, menu mobile, 404 (voir [Menu MRZ Theme](#menu-mrz-theme)).
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
| Global section | Une section globale existante | Pour réutiliser un bloc à un endroit précis |

Une section sur mesure dont le nom contient `hero`, placée en premier, active le header transparent si l'option est cochée.

### Réglages communs

Le Texte libre et les sections sur mesure qui les reprennent ont un groupe de réglages :

| Réglage | Choix |
| --- | --- |
| Fond | Aucun, clair, sombre, couleur principale, ou image de fond |
| Espacement | Normal, compact, large, aucun |
| Largeur | Conteneur, large, pleine largeur |
| Ancre | Identifiant pour un lien `#ancre` dans un menu |

Les couleurs des fonds suivent les variables du thème enfant (`--color-light`, `--color-dark`, `--color-primary`).

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

Restent à leur place native : les menus (**Apparence**), le titre, le slogan et l'icône du site (**Personnaliser > Identité du site**). Ce qui touche à l'identité visuelle (couleurs, polices, rayons) n'est pas dans le Customizer : c'est dans le CSS de l'enfant.

Les écrans Compositions et Polices de Gutenberg sont retirés d'Apparence.

| Section | Réglage | Défaut |
| --- | --- | --- |
| MRZ Theme > Header | Logo (le logo natif de WordPress, déplacé ici) | |
| | Logo du header transparent : affiché avant le défilement, remplacé par le logo principal ensuite | aucun |
| | Hauteur du logo : desktop, mobile (px) | 48, 40 |
| | Disposition : classique (logo à gauche, menu à droite) ou centrée (logo au-dessus, menu dessous) | classique |
| | Header sticky | oui |
| | Header transparent quand applicable (pages qui commencent par une section hero) | non |
| | Recherche : icône qui déplie le formulaire de recherche (Échap ou clic ailleurs pour fermer) | non |
| | Lien vers le compte : icône vers la page « Mon compte » de WooCommerce, à côté du panier | non |
| MRZ Theme > Mobile header | Header sticky sur mobile | oui |
| | Point de rupture mobile (px) : à augmenter si le menu a beaucoup d'entrées | 1000 |
| MRZ Theme > Page transitions | Transitions animées entre les pages, sans JavaScript ; désactivées pour les visiteurs qui limitent les animations | non |
| | Effet : fondu ou glissement ; durée (ms) | fondu, 300 |
| MRZ Theme > General | Largeur du contenu (px) | 1280 |
| | Page 404 : page affichée pour une URL inexistante, avec un vrai statut 404 | page par défaut du thème |

## Retoucher un site à la main

Toutes les retouches se font dans le **thème enfant**, en CSS classique : dans l'éditeur de fichiers, le gestionnaire de fichiers N0C ou en FTP. Le thème parent ne se modifie jamais sur un site : il serait écrasé à la mise à jour.

### Couleurs, polices, formes

Redéfinir les variables dans le `:root` du `style.css` de l'enfant :

```css
:root {
	--color-primary: #0b6e4f;
	--color-secondary: #c08a2e;
	--font-title: "Urbanist", Arial, sans-serif;
	--radius-button: 6px;
}
```

| Variable | Rôle | Défaut |
| --- | --- | --- |
| `--color-primary` | Couleur principale : boutons, liens, fonds « couleur principale » | `#2a5bd7` |
| `--color-secondary` | Couleur secondaire de la marque | `#1d1d1f` |
| `--color-dark` | Texte courant et fonds « sombre » | `#20201f` |
| `--color-light` | Fonds « clair » | `#f4f4f2` |
| `--color-border` | Traits et bordures | `#dededd` |
| `--font-title` / `--font-body` | Polices des titres / du texte | police système |
| `--radius-base` / `--radius-button` | Arrondi des cartes et images / des boutons | `10px` / `999px` |
| `--spacing-section`, `-sm`, `-lg` | Espacement vertical des sections : normal, compact, large | `6rem`, `3rem`, `9rem` |
| `--mrz-h1` à `--mrz-h6` | Tailles des titres | valeurs fluides |
| `--mrz-button-*`, `--mrz-field-*`, `--mrz-label-*` | Boutons, champs et libellés, les mêmes partout (thème, Gravity Forms, WooCommerce) | voir `tokens.css` |
| `--mrz-menu-font-family`, `-font-size`, `-font-weight`, `-text-transform`, `-letter-spacing` | Police, taille, graisse, casse et espacement des menus | police du texte, `1rem`, `500`, aucune, aucun |
| `--mrz-body-size`, `--mrz-body-line-height` | Taille et interligne du texte | `1.0625rem`, `1.6` |
| `--mrz-gutter` | Marges latérales du contenu, du header et des sections | de 20 à 40 px selon l'écran |
| `--mrz-header-padding` | Espace en haut et en bas du header | `1rem` |
| `--mrz-header-bg`, `--mrz-header-color`, `--mrz-header-border` | Header | fond, texte, bordure du site |

La palette s'arrête à ces cinq couleurs. Le texte atténué (`--color-muted`, dérivé de `--color-dark`), le fond de page et le texte posé sur la couleur principale (blancs) en découlent : ne pas les régler dans l'enfant. La liste complète est dans `src/css/tokens.css` du parent. La largeur du site et la hauteur du logo du header se règlent dans MRZ Theme, pas en CSS.

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
| `mrz_mobile_menu_end` | En bas du menu mobile |

```php
add_action( 'mrz_header_tools_end', function () {
	echo '<a class="mrz-btn mrz-btn--primary mrz-desktop-only" href="/contact/">' . esc_html__( 'Prendre rendez-vous', 'mrz-theme-exp-child' ) . '</a>';
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
4. **Recette sur MRZ-Dev**, connecté en administrateur, en desktop et en mobile :
   - la page privée **Recette du thème** (`/recette-theme/`) : titres, texte, listes, boutons sur chaque fond, ancre sous le header, formulaire, MRZ Display Post, MRZ Maps, HTML brut ;
   - accueil, une page en Texte libre, un article, une catégorie, une recherche, une 404 (vrai statut 404) ;
   - boutique, fiche produit, ajout au panier (la pastille se met à jour), panier, commande, compte ;
   - menu mobile (ouverture, Échap, sous-menus), recherche du header, footer ;
   - aucune nouvelle ligne dans `wp-content/debug.log`.
5. **Release** : un tag égal à la version précédée de `v`, les notes tirées du CHANGELOG :

   ```bash
   GH_TOKEN=$(gh auth token --user m0r3z) gh release create v0.2.0 --title "0.2.0" --notes-file notes.md
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

**Version publiée : 0.2.0 (16/11/2026).** Détail des changements dans [CHANGELOG.md](CHANGELOG.md).

En place : éditeur classique, constructeur (sections du parent injectées, sections sur mesure dans l'enfant), sections globales avec conditions (dont le footer), réglages par page, menu MRZ Theme sur le Customizer, header classique ou centré avec menu secondaire, recherche, compte et panier, menu mobile, variables partagées (palette de cinq couleurs, texte, boutons, champs, menus), Gravity Forms et WooCommerce branchés sur ces variables, traduction française, mises à jour par les releases GitHub (testées sur `dev.local`).

À faire, chacun comme un chantier à part :

- **WooCommerce** : mise en forme du panier, de la commande et du compte avec les variables ;
- **Menus** : style des sous-menus déroulants du header ; variantes du menu mobile, dans l'enfant ;
- **V2** : animations au défilement, parallax, lightbox, en modules chargés seulement quand ils servent ;
- **Premier site client** : vérifier que ManageWP détecte et lance la mise à jour du thème ;
- **Theme Check** : passer le plugin avant la 1.0.

Pas prévu dans le parent : d'autres variantes de header ou de menu mobile, des effets de survol. Ils se font dans le thème enfant, en CSS, site par site. Le parent porte les comportements (sticky, accessibilité, JS, intégrations) ; l'enfant porte l'apparence. Un ajout qui revient sur trois sites remonte dans le parent.
