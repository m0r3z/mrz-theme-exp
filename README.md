# MRZ Theme Experience

Thème WordPress classique et léger de l'Agence Double A. Il remplace Salient + WPBakery sur les nouveaux projets.

- **Éditeur classique** partout (Gutenberg désactivé, CSS de blocs retirés du front).
- **Pages construites avec ACF Pro** : un champ Flexible Content « Constructeur » (`mrz_builder`), une section par layout.
- **Un seul CSS compilé** (Tailwind v4, ~20 ko), JS vanilla sans jQuery.
- **Retouches sans compiler** : le thème enfant redéfinit des variables CSS et ajoute du CSS classique.

Ce guide s'adresse à la personne qui installe ou retouche un site. Les règles détaillées pour l'IA sont dans [AGENTS.md](AGENTS.md).

## Sommaire

1. [Prérequis](#prérequis)
2. [Démarrer un site](#démarrer-un-site)
3. [Construire une page](#construire-une-page)
4. [Sections globales](#sections-globales)
5. [Menu MRZ Theme](#menu-mrz-theme)
6. [Retoucher un site à la main](#retoucher-un-site-à-la-main)
7. [Blog, CPT et plugins](#blog-cpt-et-plugins)
8. [Pour l'IA](#pour-lia)
9. [Faire évoluer le thème parent](#faire-évoluer-le-thème-parent)
10. [Limites de la V1](#limites-de-la-v1)

## Prérequis

| Élément | Version |
| --- | --- |
| WordPress | 6.6 ou plus (testé jusqu'à 7.1) |
| PHP | 8.1 ou plus |
| ACF Pro | Obligatoire |

Sans ACF Pro, le thème affiche une alerte dans l'admin et les pages montrent seulement leur contenu classique. Rien ne plante.

Aucun Node n'est nécessaire sur le serveur : le CSS compilé (`dist/main.css`) est livré avec le thème.

## Démarrer un site

1. Installer le thème parent `mrz-theme-exp` et un thème enfant (modèle : `mrz-theme-exp-child`). Activer **l'enfant**, jamais le parent seul.
2. Activer ACF Pro. Les groupes de champs du thème se chargent tout seuls depuis `acf-json/` : rien à importer.
3. **Apparence > Menus** : affecter les emplacements.

   | Emplacement | Rôle |
   | --- | --- |
   | Primary menu | Menu du header (3 niveaux) |
   | Secondary menu | Menu secondaire à droite du header, avant la recherche, le compte et le panier (2 niveaux) ; repris sous le menu principal dans le menu mobile. Vide = rien ne s'affiche |
   | Mobile menu | Menu mobile ; s'il est vide, le menu principal est repris |
   | Footer menu | Liens du bas de page (1 niveau) |

4. **Apparence > Widgets** : jusqu'à trois colonnes dans le footer (Footer column 1 à 3), à côté du logo et du slogan.
5. **MRZ Theme** (menu de l'admin) : logo, header, menu mobile, 404, copyright (voir [Menu MRZ Theme](#menu-mrz-theme)).
6. Dans le `style.css` de l'enfant : couleurs et polices du client (voir [Retoucher un site à la main](#retoucher-un-site-à-la-main)).

## Construire une page

Le constructeur apparaît sous le titre des **pages** et des **sections globales**. Les articles et les CPT gardent l'éditeur classique.

- Une page avec au moins une section affiche le constructeur, et lui seul : le contenu de l'éditeur classique est alors ignoré.
- Une page sans section affiche son titre et son contenu classique.
- Repliée, une section affiche son type et son titre (ou le début de son texte) : la page se lit d'un coup d'œil.

### Les layouts

| Layout | Contenu | À savoir |
| --- | --- | --- |
| Hero | Titre, sous-titre, image de fond, hauteur (auto, large, plein écran), alignement, boutons | Placé en premier, il active le header transparent si l'option est cochée |
| Texte + image | Titre, texte, bouton, image à gauche ou à droite | |
| Grille de cartes | Titre, intro, 2 à 4 colonnes, cartes (image, titre, lien, texte) | Toute la carte est cliquable si elle a un lien |
| Galerie | Titre, colonnes, images | Les images ouvrent leur version pleine taille ; lightbox en V2 |
| CTA | Titre, texte, bouton | |
| FAQ | Titre, questions et réponses | Accordéon natif, sans JavaScript |
| Texte | Titre, texte libre | |
| Formulaire | Titre, intro, formulaire Gravity Forms | La liste des formulaires du site s'affiche toute seule |
| Liste de posts | Titre, intro, liste MRZ Display Post | |
| Carte | Titre, intro, carte MRZ Maps | |
| Section globale | Une section globale existante | Pour réutiliser un bloc à un endroit précis |

### Réglages communs

Chaque layout (sauf Section globale) a un onglet de réglages :

| Réglage | Choix |
| --- | --- |
| Fond | Aucun, clair, sombre, couleur principale, ou image de fond |
| Espacement | Normal, compact, large, aucun |
| Largeur | Conteneur, large, pleine largeur |
| Ancre | Identifiant pour un lien `#ancre` dans un menu |

Les couleurs des fonds suivent les variables du thème enfant (`--color-light`, `--color-dark`, `--color-primary`).

## Sections globales

**MRZ Theme > Global sections.** Une section globale se construit comme une page (même constructeur) et s'affiche :

- **automatiquement sur tout le site**, à l'emplacement choisi, ou
- **à la main**, avec le layout « Section globale » dans une page (choisir alors « Manual insertion only »).

| Emplacement | Position |
| --- | --- |
| Before the header / After the header | Autour du header |
| Before the content / After the content | Au début ou à la fin du contenu principal |
| Before the footer / After the footer | Autour du footer |
| WooCommerce: before the product list | Au-dessus de la liste des produits |
| WooCommerce: after a product | Sous la fiche produit |

La **priorité** ordonne plusieurs sections au même emplacement (10 par défaut, plus petit = plus haut). La colonne « Where it shows » de la liste résume où chaque section apparaît. Seules les sections publiées s'affichent.

## Menu MRZ Theme

Le menu **MRZ Theme** de l'admin rassemble le thème :

- **Theme options** ouvre le Customizer natif directement sur le panneau MRZ Theme, avec l'aperçu en direct ;
- **Global sections** liste les [sections globales](#sections-globales).

Restent à leur place native : les menus et les widgets (**Apparence**), le titre, le slogan et l'icône du site (**Personnaliser > Identité du site**). Ce qui touche à l'identité visuelle (couleurs, polices, rayons) n'est pas dans le Customizer : c'est dans le CSS de l'enfant.

Les écrans Compositions et Polices de Gutenberg sont retirés d'Apparence.

| Section | Réglage | Défaut |
| --- | --- | --- |
| MRZ Theme > Header | Logo (le logo natif de WordPress, déplacé ici) | |
| | Logo du header transparent : affiché avant le défilement, remplacé par le logo principal ensuite | aucun |
| | Hauteur du logo : desktop, mobile (px) | 48, 40 |
| | Disposition : classique (logo à gauche, menu à droite) ou centrée (logo au-dessus, menu dessous) | classique |
| | Header sticky | oui |
| | Header transparent quand applicable (pages qui commencent par un Hero) | non |
| | Recherche : icône qui déplie le formulaire de recherche (Échap ou clic ailleurs pour fermer) | non |
| | Lien vers le compte : icône vers la page « Mon compte » de WooCommerce, à côté du panier | non |
| MRZ Theme > Mobile header | Header sticky sur mobile | oui |
| | Point de rupture mobile (px) : à augmenter si le menu a beaucoup d'entrées | 1000 |
| MRZ Theme > Page transitions | Transitions animées entre les pages, sans JavaScript ; désactivées pour les visiteurs qui limitent les animations | non |
| | Effet : fondu ou glissement ; durée (ms) | fondu, 300 |
| MRZ Theme > General | Largeur du contenu (px) | 1280 |
| | Page 404 : page affichée pour une URL inexistante, avec un vrai statut 404 | page par défaut du thème |
| | Copyright du footer, balises `{year}` et `{site}` | `© {year} {site}` |

## Retoucher un site à la main

Toutes les retouches se font dans le **thème enfant**, en CSS classique : dans l'éditeur de fichiers, le gestionnaire de fichiers N0C ou en FTP. Le thème parent ne se modifie jamais sur un site : il serait écrasé à la mise à jour.

### Couleurs, polices, formes

Redéfinir les variables dans le `:root` du `style.css` de l'enfant :

```css
:root {
	--color-primary: #0b6e4f;
	--color-primary-contrast: #ffffff;
	--font-title: "Urbanist", Arial, sans-serif;
	--radius-button: 6px;
}
```

| Variable | Rôle | Défaut |
| --- | --- | --- |
| `--color-primary` / `--color-primary-contrast` | Couleur principale (boutons, fonds « couleur principale ») et texte posé dessus | `#2a5bd7` / `#ffffff` |
| `--color-secondary` | Couleur secondaire | `#1d1d1f` |
| `--color-accent` | Couleur d'accent | `#f2a541` |
| `--color-text` / `--color-muted` | Texte courant / texte atténué | `#20201f` / `#6b6b6a` |
| `--color-background` | Fond du site | `#ffffff` |
| `--color-light` / `--color-dark` | Fonds « clair » et « sombre » des sections | `#f4f4f2` / `#161616` |
| `--color-border` | Bordures | `#dededd` |
| `--font-title` / `--font-body` | Polices des titres / du texte | police système |
| `--radius-base` / `--radius-button` | Arrondi des cartes et images / des boutons | `10px` / `999px` |
| `--spacing-section`, `-sm`, `-lg` | Espacement vertical des sections : normal, compact, large | `6rem`, `3rem`, `9rem` |
| `--mrz-h1`, `--mrz-h2`, `--mrz-h3` | Tailles des titres | valeurs fluides |
| `--mrz-body-size`, `--mrz-body-line-height` | Taille et interligne du texte | `1.0625rem`, `1.6` |
| `--mrz-header-bg`, `--mrz-header-color`, `--mrz-header-border` | Header | fond, texte, bordure du site |
| `--mrz-footer-bg`, `--mrz-footer-color` | Footer | `--color-dark`, blanc |
| `--mrz-footer-logo-height` | Hauteur du logo du footer | `48px` |

La liste complète est dans `src/css/tokens.css` du parent. La largeur du site et la hauteur du logo du header se règlent dans MRZ Theme, pas en CSS.

### Polices

Les polices sont auto-hébergées (performance, RGPD) : déposer les fichiers `woff2` dans un dossier `fonts/` de l'enfant, déclarer le `@font-face` en haut de son `style.css` (exemple commenté dans le modèle), puis renseigner `--font-title` et `--font-body`.

### CSS sur mesure

Écrire sous les variables, avec les classes `.mrz-` du thème :

```css
.mrz-footer .mrz-logo__img {
	filter: brightness(0) invert(1);
}
```

Le CSS du parent est rangé dans des couches (`@layer`) : le CSS de l'enfant passe toujours devant, **sans `!important`**.

**Règle absolue** : pas de classe Tailwind nouvelle dans l'enfant (`mt-12`, `bg-red-500`…). Le CSS du parent ne contient que les classes qu'il utilise lui-même : une classe ajoutée dans l'enfant n'existe nulle part et n'aura aucun effet. Écrire une vraie règle CSS à la place.

### Surcharger un template

Copier le fichier du parent dans l'enfant, au même chemin, puis le modifier :

| Pour changer | Copier |
| --- | --- |
| Un layout du constructeur | `layouts/{layout}.php` (tirets : `text-image.php`) |
| Le header | `parts/header/header-classic.php` ou `header-centered.php` |
| Le menu mobile | `parts/mobile-menu/offcanvas.php` |
| La carte d'un article dans les listes | `parts/content/content.php`, ou `content-{post_type}.php` pour un seul type |
| L'affichage d'un article seul | `parts/content/single.php`, ou `single-{post_type}.php` pour un seul type |

Une surcharge ne reçoit plus les corrections du parent : n'en faire qu'en dernier recours.

### Changer une icône

Les icônes du header sont dans `assets/icons/` du parent : `cart.svg` (panier), `account.svg` (compte), `search.svg` (recherche). Pour en changer une, déposer un SVG du même nom dans `assets/icons/` de l'enfant : il est pris à la place, sans toucher au code. Le SVG prend la couleur du texte s'il utilise `fill="currentColor"` ; sa taille se règle en CSS sur `.mrz-icon`.

## Blog, CPT et plugins

- **Blog et archives** : grille de cartes avec image, date et catégories, pagination, titres d'archive sans préfixe (« Actualités » plutôt que « Catégorie : Actualités »).
- **Article seul** : image à la une, contenu, navigation entre articles, commentaires.
- **CPT** : affichés comme les articles. Pour un rendu propre à un CPT, créer `parts/content/single-{post_type}.php` (et `content-{post_type}.php` pour sa carte) dans l'enfant.
- **WooCommerce** : aucun template surchargé, donc rien à mettre à jour quand WooCommerce évolue. Les styles suivent les variables du thème et ne se chargent que sur les pages WooCommerce. Icône du panier dans le header, avec le nombre d'articles, et icône du compte en option (MRZ Theme > Header). Les pages Panier et Commande utilisent les shortcodes `[woocommerce_cart]` et `[woocommerce_checkout]`, pas les blocs.
- **Polylang** : sélecteur de langue dans le header et le menu mobile, dès que Polylang est actif.
- **Gravity Forms** : layout Formulaire.
- **MRZ Display Post** et **MRZ Maps** : layouts Liste de posts et Carte.

## Pour l'IA

L'IA construit les pages en écrivant des données dans le champ `mrz_builder`, jamais du HTML :

```php
update_field(
	'mrz_builder',
	array(
		array(
			'acf_fc_layout' => 'hero',
			'title'         => 'Titre de la page',
			'height'        => 'large',
			'settings'      => array( 'background' => 'dark' ),
		),
	),
	$post_id
);
```

Les noms des layouts, de leurs champs et de leurs valeurs, ainsi que les conventions de code, sont dans [AGENTS.md](AGENTS.md). Tout ce que l'IA construit reste visible et modifiable dans l'admin.

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
- **Champs ACF** : sur le site de développement, définir `define( 'MRZ_THEME_DEV', true );` dans `wp-config.php`. Les modifications des groupes `group_mrz_*` s'enregistrent alors dans `acf-json/` du thème. Sans cette constante (sites clients), elles ne s'écrivent jamais dans le parent.
- **Version** : `Version:` dans `style.css` et `MRZ_THEME_VERSION` dans `functions.php`, toujours identiques.
### Publier une mise à jour

Les sites se mettent à jour depuis les releases GitHub du dépôt, grâce à [Plugin Update Checker](https://github.com/YahnisElsts/plugin-update-checker) (`lib/`, branché dans `inc/updater.php`).

1. Monter la version dans `style.css` (`Version:`) et dans `functions.php` (`MRZ_THEME_VERSION`).
2. `npm run build`, commiter, pousser `main`.
3. Créer la release, avec un tag égal à la version précédée de `v` :

   ```bash
   gh release create v0.2.0 --title "0.2.0" --notes "Ce qui change dans cette version."
   ```

4. Chaque site affiche « Mise à jour disponible » dans les 12 heures, ou tout de suite depuis **Tableau de bord > Mises à jour**. La mise à jour se lance site par site ou en masse depuis ManageWP.

- Le zip téléchargé est celui que GitHub génère pour la release : les fichiers listés dans `.gitattributes` (`package.json`…) en sont exclus.
- Une release marquée « pre-release » est ignorée par les sites.
- Sans aucune release, les sites lisent la version de `style.css` sur `main` : ne monter la version qu'au moment de publier.
- Une copie de travail Git (site de développement) ne cherche jamais de mise à jour : elle écraserait le dépôt.
- Le thème enfant n'est jamais mis à jour à distance.

### Organisation des fichiers

```
mrz-theme-exp/
├── functions.php        chargeur, aucune logique
├── inc/                 options, setup, assets, customizer, template-tags,
│                        builder, global-sections, woocommerce, updater, admin
├── acf-json/            groupes de champs du constructeur et des sections globales
├── layouts/             un template par layout
├── parts/               header/, mobile-menu/, content/
├── src/css/             tokens.css (variables), base.css, components.css, main.css (entrée)
├── dist/main.css        CSS compilé, commité
├── assets/              js/main.js, css/woocommerce.css, icons/ (SVG du header)
├── lib/                 Plugin Update Checker (mises à jour par GitHub)
└── AGENTS.md            règles pour l'IA
```

## Limites de la V1

Prévu en V2, pas encore disponible :

- conditions d'affichage des sections globales (types de contenus, pages, exclusions) : en V1, une section automatique s'affiche sur tout le site ;
- animations au défilement, parallax, lightbox des galeries ;
- réglages par page (header transparent, masquer le titre ou le footer) ;
- styles Gravity Forms branchés sur les variables ;
- traduction française de l'admin du thème (les libellés sont en anglais).

Pas prévu dans le parent : d'autres variantes de header ou de menu mobile, des effets de survol. Ils se font dans le thème enfant, en CSS, site par site. Le parent porte les comportements (sticky, accessibilité, JS, intégrations) ; l'enfant porte l'apparence. Un ajout qui revient sur trois sites remonte dans le parent.
