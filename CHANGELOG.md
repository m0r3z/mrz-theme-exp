# Changelog

Toutes les évolutions notables du thème. Format inspiré de [Keep a Changelog](https://keepachangelog.com/fr/1.1.0/), versions en [SemVer](https://semver.org/lang/fr/) : le premier chiffre change quand un nom utilisé par les thèmes enfants casse (« Cassant ») ; avant la 1.0, c'est le deuxième.

## [Non publié]

### Ajouté
- Variables d'espacement du texte (`.mrz-prose`) : `--mrz-prose-space`, `--mrz-prose-h2-space`, `--mrz-prose-h3-space`, `--mrz-prose-h4-space`. Mêmes valeurs qu'avant, rendu inchangé.
- Classe `mrz-icon-image` : une icône en image (SVG noir) posée dans un contenu prend la couleur principale, avec `mx-auto` pour la centrer.
- Admin, constructeur : les réglages de chaque section (fond, espacements, largeur, hauteur, image, animation…) sont repliés derrière un interrupteur à côté de « Réglages de la section ». Affichage seulement : les réglages sont enregistrés tels quels, interrupteur ouvert ou non. Vaut aussi pour les sections de l'enfant qui clonent ces réglages.
- Accordéon : ouverture et fermeture en douceur, l'élément qui se referme (un seul ouvert à la fois) aussi. En CSS seul (`::details-content`, `interpolate-size`) ; navigateur sans ce support : ouverture immédiate, comme avant. Variable `--mrz-accordion-duration` (0,35 s). Rien si le visiteur limite les animations. Vaut aussi pour les accordéons de la fiche produit.
- Customizer > MRZ Theme > Blog : « Afficher les articles précédent et suivant » (coché par défaut, comme avant). Décoché, l'article seul n'a plus ces liens en bas.
- Blog : les catégories en cartouches entre le titre et les articles, sur la page des articles et les pages de catégorie. « Tous » puis chaque catégorie qui a des articles, celle de la page en plein (couleur principale, `aria-current`). De simples liens vers les catégories, sans filtre ni JavaScript ; rien sous deux catégories. Variables `--mrz-blog-category-radius` (999px) et `--mrz-blog-categories-space` (2,5 rem).
- Customizer > MRZ Theme > **Blog** : « Disposition de l'article seul », classique (titre, puis image à la une, comme avant) ou **bannière** (image à la une en fond du haut de l'article, sur 70 % de l'écran, date et titre posés dessus avec le voile des images de section, image chargée en priorité). En bannière, le header est toujours transparent par-dessus. Un article sans image à la une, et les CPT, restent en classique. « Articles par ligne » sur ordinateur (2 ou 3, par défaut 3) et « Afficher l'extrait dans les listes » (coché par défaut), pour le blog, les catégories, les archives et la recherche.
- Page des articles (Réglages > Lecture) : ses sections du constructeur s'affichent au-dessus de la liste et portent le `h1`, comme la page Boutique (première page de la liste seulement). Sous la dernière section, le même écart que sous un titre d'archive (3 rem) avant les catégories et les articles. Sans sections, rien ne change.

### Modifié
- Admin, constructeur : plus de description sous « Second bouton » (« Bouton à contour. »), « Hauteur » (« Contenu centré verticalement. ») et « Surtitre ». « Niveau du titre » dit seulement « Un seul H1 par page. ».
- Animations au défilement, en remontant : l'élément disparaît dès que son haut repasse sous une ligne à 30 % du bas de l'écran, pendant qu'il est encore bien visible, au lieu d'attendre qu'il soit presque sorti de l'écran. L'apparition, en descendant, ne change pas (ligne à 10 % du bas).

### Corrigé
- Accordéon en cascade : ses éléments et les paragraphes de ses réponses apparaissaient un par un au défilement, et certains restaient invisibles en laissant un grand vide. Plus aucune animation au défilement dans un accordéon.
- Header sticky : ouvrir la recherche faisait remonter la page de la hauteur du header (décalage des ancres appliqué au focus du champ). Même correction pour le focus clavier dans le header et dans le sous-menu d'ancres collant.

### Retiré
- Lightbox : plus de lecteur Vimeo. Un lien vers une vidéo Vimeo reste un lien ordinaire (il ouvre Vimeo). Restent les vidéos YouTube et les fichiers MP4 ou WebM du site.


## [0.8.6] - 2026-11-18

### Modifié
- Admin : le menu MRZ Theme a l'icône du thème (`assets/icons/menu.svg`, quatre cercles et étoile), repeinte aux couleurs de l'admin comme les icônes de WordPress, à la place du pinceau.

## [0.8.5] - 2026-11-18

### Modifié
- Connexion, inscription, mot de passe perdu : une colonne étroite et centrée, sans cadre, bouton pleine largeur. Connexion et inscription ne s'affichent plus côte à côte : « Vous n'avez pas encore de compte ? S'inscrire » mène au formulaire d'inscription (`?action=register`), qui renvoie à la connexion ; lien « Retour à la connexion » sous le mot de passe perdu. Inscription : le texte sur les données personnelles passe sous le bouton « S'inscrire ». L'inscription suit le réglage de WooCommerce (Réglages > Comptes et confidentialité). Variable `--mrz-account-form-width`.
- Page de remerciement et détail d'une commande : message de confirmation en avant avec une coche, récapitulatif en lignes (numéro, date, e-mail, total, paiement), tableau à traits simples, titres à taille raisonnable, adresses côte à côte dans des cadres légers, coordonnées bancaires du virement en cartes. Variable `--mrz-order-width`.
- Mon compte : menu et contenu côte à côte en grille (menu de 15 rem, 3 rem d'écart, empilés sur mobile ; `--mrz-account-nav-width`, `--mrz-account-gap`) au lieu des flottants de WooCommerce et de leur grand vide ; menu à la couleur du texte, page active en gras, sans pastille ; boutons des tableaux petits et à contour, « Changement de mot de passe » avec un vrai intertitre, adresses côte à côte avec titre et lien « Modifier » alignés.

## [0.8.4] - 2026-11-18

### Ajouté
- Customizer > Shop : « Produits similaires », leur nombre sur une ligne (4 par défaut, 0 pour aucun), indépendant des colonnes de la boutique.
- Fiche produit : **variations en pastilles ou en couleurs**, au choix sur chaque attribut (Produits > Attributs > Modifier, champ « Type » : Menu déroulant, Pastilles, Couleurs). En « Couleurs », une couleur par terme (Configurer les termes, sélecteur de couleur, colonne dans la liste) : ronds de couleur, nom de la couleur choisie à côté du libellé (« Couleur : Moutarde ») ; terme sans couleur affiché par son nom. Vrais boutons radio, au clavier ; le menu de WooCommerce reste masqué et calcule toujours prix, stock et image. Choix impossible grisé, combinaison en rupture barrée (et annoncée aux lecteurs d'écran). Thème changé : retour aux menus déroulants, couleurs gardées sur les termes. Variables `--mrz-swatch-gap`, `--mrz-swatch-padding`, `--mrz-swatch-radius`, `--mrz-swatch-size`.
- Customizer > General : « Jouer les animations au défilement une seule fois » (décoché par défaut). Coché, un élément apparu reste affiché, même si on remonte puis redescend ; décoché, l'animation se joue en descendant, et à l'envers en remontant, dès que l'élément repasse sous la ligne de déclenchement.

### Modifié
- Panneau du panier, articles : nouvelle disposition, image carrée à gauche ; titre et croix en face ; options de la variation dessous (« Moutarde – 45 × 45 cm ») ; prix de la ligne et **quantité modifiable** (− et +, ou au clavier ; 0 retire l'article) en bas, sans recharger la page. Titre long sur plusieurs lignes sans passer sous la croix, barre de défilement qui ne recouvre pas la quantité. Croix de suppression : icône SVG centrée (au lieu du caractère « × »), sans fond au survol, zone de clic de 28 px, remplaçable par `--mrz-minicart-remove-icon`, `--mrz-minicart-remove-icon-size`, `--mrz-minicart-remove-size`. Par défaut, titre et prix en 15 px, options en 13 px, quantité compacte (13 px, 32 px de haut). Variables `--mrz-minicart-thumb`, `--mrz-minicart-item-gap`, typo du titre, des options et du prix (`--mrz-minicart-name-*`, `--mrz-minicart-options-*`, `--mrz-minicart-price-*`), `--mrz-minicart-qty-size`, `--mrz-minicart-qty-height`.
- Fiche produit : de l'air entre le prix de la variation choisie et le formulaire d'ajout.
- Boutique et catégories : de l'air entre le titre (et la description) et les filtres et produits (`--mrz-shop-header-gap`, 2,5 rem) ; description resserrée sous le titre, en largeur de lecture.
- Animations au défilement : déclenchées quand l'élément entre à l'écran, puis jouées en entier (0,9 s, `--mrz-motion-duration`), au lieu d'avancer avec le défilement : plus d'éléments à moitié apparus en bas de l'écran. Petit module `assets/js/motion.js`, chargé seulement si une section a une animation ; sans JavaScript, tout s'affiche normalement. Elles marchent maintenant aussi sur Firefox. Le surlignage animé du mot en couleur des titres suit les mêmes règles : tracé en entier quand le titre entre à l'écran (`--mrz-highlight-duration` 0,8 s, `--mrz-highlight-delay` 0,2 s), rejoué ou non selon le réglage « une seule fois ». `--mrz-motion-range`, `-range-2` et `-range-3` remplacées par `--mrz-motion-duration` et `--mrz-motion-stagger` (cascade).
- Fiche produit : informations complémentaires séparées par des traits pleins aux couleurs du site (plus de pointillés ni de double trait avec l'accordéon) ; de l'air entre le texte court et le formulaire d'ajout, et entre le prix de la variation choisie et ce formulaire (`--mrz-product-summary-gap`, 1,5 rem).
- Panier vide (bloc Panier) : seulement le message et un bouton « Parcourir la boutique », centrés. Plus d'icône, de séparateur ni de « Nouveau dans la boutique » (grille de blocs WooCommerce qui ne suivait pas le style des produits du thème), retirés au rendu sans toucher au contenu de la page.

### Corrigé
- Boutique en 2, 3, 5 ou 6 colonnes : chaque produit ne prenait qu'une fraction de sa case (largeur en pourcentage imposée par WooCommerce).

## [0.8.3] - 2026-11-18

### Ajouté
- Sections globales : emplacement **Top bar**, un bandeau au-dessus du header (livraison offerte, annonce). Il défile avec la page et le header sticky reste seul en haut ; sous un header transparent, le header descend sous le bandeau puis remonte avec lui. Espacement réduit, texte plus petit, fond réglé par la section. Ses sections ne prennent ni la priorité d'image ni le `h1` de la page. Comme le header, il reste immobile pendant les transitions de page. Les mêmes sections ouvrent le panneau du panier. Variables `--mrz-topbar-*`.

### Modifié
- Panneau du panier : ouverture et fermeture adoucies, en CSS seul : le voile apparaît et disparaît en fondu (0,5 s), le panneau glisse doucement depuis la droite et y repart (0,6 s). Plus de trait sous le titre « Panier ». Fermeture d'un coup sur les navigateurs sans prise en charge (Firefox). Variables `--mrz-minicart-duration` et `--mrz-minicart-backdrop-duration`.

## [0.8.2] - 2026-11-18

### Ajouté
- Header transparent automatique dès que la première section a une image ou une vidéo de fond (plus besoin d'une section « hero » ; un nom en `hero` compte toujours).
- Cartes : réglage « Style du lien » (lien fléché, bouton à contour, bouton plein).
- Lightbox : un lien vers un fichier MP4 ou WebM du site s'ouvre dans un lecteur natif.
- Bouton Menu sur ordinateur à trois choix (non, à la place du menu principal, en plus du menu principal), et mot « Menu » visible en option. L'ancienne case cochée vaut « à la place ».
- Variables : `--mrz-eyebrow-font-family`, `--mrz-header-backdrop`, `--mrz-logo-height-scrolled`, `--mrz-link-weight`, `--mrz-link-underline-thickness`, `--mrz-carousel-button-bg` et `-color`, `--mrz-accordion-question-font-family` et `-text-transform`, `--mrz-button-icon` et `-icon-size`, `--mrz-title-highlight-color-on-image`.

### Modifié
- AGENTS.md : contradictions levées (section « hero », variantes d'apparence dans le parent, formes de bouton, panier et commande en blocs, bouton du header par un hook) et doublons retirés.

## [0.8.1] - 2026-11-18

### Corrigé
- Défilement horizontal de la page : un sous-menu du mega menu fermé (header en `overflow-x: clip`), les textes masqués des diapositives d'un carrousel (piste en `position: relative`), une légende de galerie sans espace (coupée).
- Lightbox : le glissé au doigt passe enfin à l'image suivante (`touch-action`), et la fermeture rend le focus au lien de l'image affichée, plus à celui qui l'a ouverte.
- Texte et média : la colonne collante s'arrête sous la barre d'admin et sous le sous-menu d'ancres collant. Dans une colonne de mega menu, texte et média s'empilent.
- Bandeau défilant : le texte d'un élément sert d'alternative à son logo (RGAA 1.1).
- Compteur des chiffres clés : les lecteurs d'écran lisent la valeur finale, pas les valeurs intermédiaires.
- Dégradé « vers le fond du site » dans une section colorée : il allait vers la couleur de la section (nouvelle variable `--mrz-site-background`).
- Couleur principale peu contrastée : le lien actif et le survol du menu, le lien actif du sous-menu d'ancres et la bordure des champs au focus suivent `--mrz-link-color` et `--mrz-focus-color` (RGAA 3.2).
- Header transparent : le focus était invisible dans les sous-menus (couleur claire du header sur leur fond blanc). Nouvelle variable `--mrz-site-focus-color`.
- Page Boutique sans `h1` depuis la 0.7.1 : ses sections s'affichent au-dessus des produits, sinon le titre de WooCommerce revient (RGAA 9.1).
- Texte libre centré : un formulaire garde ses étiquettes et ses champs alignés à gauche.
- Boutons : `--mrz-button-padding-x` et `-y` redéfinis sur un bouton sont pris en compte (le raccourci `--mrz-button-padding` reste possible).

## [0.8.0] - 2026-11-17

Boutique WooCommerce : réglages dans le Customizer, panneau du panier, ajout sans rechargement.

### Ajouté
- Customizer > MRZ Theme > **Boutique** : comportement de l'icône du panier, ajout sans rechargement sur la fiche, position des informations du produit, produits par ligne (et sur mobile), produits par page, tri, nombre de résultats, badge promo, boutons quantité.
- **Panneau du panier** (`<dialog>` à droite) ouvert par l'icône du panier et après un ajout : articles, suppression sans quitter la page, total, « Voir le panier » et « Commander », bouton « Continuer les achats » quand il est vide. Contenu chargé à l'ouverture (compatible avec le cache de page). Sans JavaScript, l'icône mène toujours au panier.
- Fiche produit : **ajout au panier sans rechargement** (tous types de produits, champs des plugins compris) ; **notification** discrète en bas d'écran pour l'ajout (mode page) et les erreurs.
- Fiche produit : description, informations complémentaires et avis en **accordéons** (ceux de la section Accordéon, variables `--mrz-accordion-*`), à côté des images sous le bouton d'ajout, ou sous les images.
- Boutons **+ et −** autour de la quantité (fiche produit ; panier en shortcode, le bloc Panier a les siens).
- Badge promo en pourcentage (« -20 % »), ou aucun badge.
- Module `assets/js/shop.js`, chargé seulement là où il sert. Variables `--mrz-shop-columns-mobile`, `--mrz-shop-gap`, `--mrz-minicart-*`, `--mrz-product-thumbs`.

### Modifié
- Listes de produits en grille : cartes de même hauteur, bouton en bas ; tri, pagination et badge promo aux couleurs du thème. Produits similaires sur une ligne de la boutique.
- Blocs Panier et Commande : leur mise en page de WooCommerce est gardée ; leurs boutons (« Valider la commande », « Commander », « Appliquer ») prennent le bouton du thème, en pleine largeur. Ils attendaient le style `.wp-element-button`, que le thème retire avec `classic-theme-styles` et `global-styles`.
- Notices de WooCommerce plus discrètes (fond léger, sans cadre).
- Galerie de la fiche produit affichée sans attendre son script (image principale plus rapide) ; miniatures espacées et arrondies.
- Mon compte : menu en liste de liens avec la page active marquée ; tableaux et adresses aux traits du site.

### Corrigé
- Champs de WooCommerce : une règle en dur écrasait les variables `--mrz-field-*`.
- Fiche produit : « Produits similaires » ne passait plus sous la fiche une fois les onglets retirés.

## [0.7.1] - 2026-11-17

### Retiré
- Titre `h1` automatique des pages Panier, Commande et Mon compte (ajouté en 0.4.1), et titre de la page Boutique de WooCommerce. Les catégories de produits gardent leur titre.

### Modifié
- Transitions de page : le header et la barre d'admin restent immobiles, seul le contenu change.
- Transitions de page en deux temps : l'ancienne page disparaît entièrement sur une couleur (« Couleur entre les deux pages », fond du site par défaut), puis la nouvelle arrive. Plus de superposition des deux pages, pour tous les effets.
- Customizer : le sens du rideau ne s'affiche que pour l'effet rideau.

### Corrigé
- Transition rideau : seul un aplat de couleur s'affichait (le balayage sautait d'un coup, `clip-path` ne s'animant pas depuis `none`).

## [0.7.0] - 2026-11-17

Sections d'appoint : sous-menu d'ancres, bandeau défilant, compteur des chiffres clés.

### Modifié
- Une ancre amène la section juste sous le header (et sous la barre d'ancres), sans marge en plus.

### Ajouté
- Section **Sous-menu d'ancres** (`mrz_anchors`) : liens vers les sections de la page, collés sous le header en option (les ancres s'arrêtent sous la barre), lien de la section affichée mis en évidence, défilement horizontal sur mobile. Variables `--mrz-anchors-*`.
- Section **Bandeau défilant** (`mrz_marquee`) : textes ou logos en défilement continu, en CSS ; trois vitesses, deux sens, séparateur. Bouton pause, arrêt au survol et au focus (RGAA 13.8) ; éléments à la suite si le visiteur limite les animations. Variables `--mrz-marquee-*`.
- Liens vers une ancre de la page : défilement doux jusqu'à la section (`scroll-behavior: smooth`, coupé si le visiteur limite les animations). Ce n'est pas un défilement adouci de la molette.
- Cartes « Chiffres clés » : **compteur** qui fait monter les chiffres depuis 0 à l'arrivée à l'écran ; valeur finale dans la page, formats « 13,5 M€ », « 1 200 », « +98 % » conservés.

## [0.6.2] - 2026-11-17

### Ajouté
- Galerie : affichage « Mosaïque » (masonry), en colonnes CSS sans JavaScript, chaque image à son format d'origine.

## [0.6.1] - 2026-11-17

### Modifié
- Animations au défilement plus lentes et plus douces : elles jouent sur une plus grande part du défilement (`--mrz-motion-range: entry 15% cover 55%`), avec une arrivée amortie (`--mrz-motion-easing`), sur une course un peu plus longue (`--mrz-motion-distance: 3rem`).
- Lightbox : ouverture en fondu, et chaque image arrive en fondu (`--mrz-lightbox-duration`, 0,45 s). Rien si le visiteur limite les animations.

### Corrigé
- Lightbox, image en taille réelle : on peut maintenant se déplacer jusqu'à chaque bord de l'image, y compris dans une petite fenêtre (la zone d'affichage ne s'élargit plus avec l'image).

## [0.6.0] - 2026-11-17

Mouvement et médias : animations au défilement en CSS, vidéo de fond, galerie et lightbox.

### Ajouté
- Animations au défilement, sans JavaScript : réglages de section « Animation » (apparition vers le haut, fondu, zoom, en cascade) et « Parallaxe » (légère, moyenne) sur l'image de fond. Jamais sur la première section, coupées si le visiteur limite les animations, contenu affiché normalement sur les navigateurs sans prise en charge (Firefox).
- Vidéo de fond de section (MP4 ou WebM) : l'image de fond s'affiche d'abord, la vidéo se télécharge une fois la page chargée, bouton pause (RGAA 13.8). Pas de vidéo sur mobile par défaut, ni si le visiteur limite les animations ou économise ses données. Script `assets/js/video.js` chargé seulement si une section en a une.
- Section **Galerie** (`mrz_gallery`) : grille ou carrousel, images par ligne, format (d'origine, carré, 4/3, 16/9), légendes. Variables `--mrz-gallery-*`.
- **Lightbox** (`<dialog>` natif) : images de la Galerie, liens vers une image ou une vidéo YouTube ou Vimeo dans un texte, galeries natives `[gallery]` (mises en forme par le thème). Image en entier, taille réelle au clic si elle dépasse l'écran ; flèches, Échap, glissé au doigt ; vidéos chargées seulement à l'ouverture (youtube-nocookie, Vimeo sans traceurs). Script chargé seulement si la page en a besoin.
- Customizer > Général : surlignage animé du mot en couleur des titres.
- Variables `--mrz-motion-*`, `--mrz-parallax-*`, `--mrz-highlight-*`.

## [0.5.2] - 2026-11-17

### Ajouté
- Header : disposition « logo centré » (menu principal à gauche, logo au centre, menu secondaire et outils à droite ; logo à gauche sur mobile).

## [0.5.1] - 2026-11-17

### Ajouté
- Mega menu : une section globale placée au deuxième niveau forme une colonne (image, texte, bouton, Cartes). Elle n'apparaît que dans le mega menu du header ; ses titres y deviennent des paragraphes et ses images ne prennent pas la priorité de chargement.
- `--mrz-megamenu-column-min`.
- Texte libre et Texte et média : niveaux de titre H1 à H6 (H2 par défaut).
- Admin : éditeurs de texte des sections moins hauts (150px au lieu de 300px).

### Modifié
- Mega menu : par défaut, autant de colonnes que d'éléments du deuxième niveau (`--mrz-megamenu-columns: auto-fit`, un nombre reste possible).
- Images des Cartes : même règle de chargement que les autres images de section (prioritaire dans la première section de la page, différé ailleurs), au lieu du choix automatique de WordPress.
- Les styles des liens du menu ne visent plus que les liens du menu (`.menu-item > a`).

### Corrigé
- Admin : l'aide des champs de titre affichait le résultat des balises `<em>` et `<br>` au lieu de les citer.

## [0.5.0] - 2026-11-17

Ce qui avait obligé à créer des sections sur mesure dans les enfants (bannière, texte et image, chiffres clés, accordéon, mega menu).

### Ajouté
- Réglages communs des sections :
  - espace en haut et en bas réglables à part ;
  - hauteur grande ou plein écran, contenu centré ;
  - voile sur l'image (sombre, léger, sans voile) ;
  - dégradé de la couleur de fond vers une seconde couleur de la palette.
- Variables `--mrz-image-overlay-light`, `--mrz-section-image-position`, `--mrz-section-height-large`, `--mrz-section-height-screen`.
- Texte libre : surtitre, alignement (gauche ou centré), deux boutons (plein et à contour).
- Titres des sections du parent : `<em>` met un mot en couleur (`--mrz-title-highlight-color`, contraste vérifié par le Customizer), `<br>` force un retour à la ligne. Fonction `mrz_theme_title_html()` pour les sections sur mesure.
- Cartes : nombre de cartes par ligne propre à la section, style « chiffres clés » (`--mrz-card-figure-size`).
- Variables du surtitre : `--mrz-eyebrow-color`, `-size`, `-weight`, `-text-transform`, `-letter-spacing`.
- Sur un fond de section ou une image, `--mrz-link-color` prend la couleur du texte de la section.
- Section **Texte et média** (`mrz_split`) : texte d'un côté, image, second texte ou encadré coloré de l'autre ; côté, largeur des colonnes, alignement vertical, colonne collante. Variables `--mrz-split-*`.
- Section **Accordéon** (`mrz_accordion`) : `<details>` natif sans JavaScript, premier élément ouvert et un seul ouvert à la fois en option. Variables `--mrz-accordion-*`.
- Header : disposition « menu à gauche » (logo, menu juste après, outils à droite) et réglage « Bouton Menu aussi sur ordinateur ».
- Mega menu : classe CSS `mrz-megamenu` sur un élément du premier niveau. Panneau en colonnes, Échap ferme tout le panneau. Variables `--mrz-megamenu-columns`, `--mrz-megamenu-width`.
- Footer : colonne libre après les colonnes de liens (texte, formulaire d'inscription). Variable `--mrz-footer-free-width`.
- Transitions de page : effets « rideau » (couleur et sens réglables) et « zoom », toujours sans JavaScript.

### Corrigé
- Images des Cartes : leur place est réservée avant chargement (format réel en `--mrz-img-ratio`). Depuis la 0.4.1, `--mrz-card-media-ratio` non réglé donnait une hauteur fausse à une image chargée en différé (décalage de mise en page).

### Modifié
- L'image de fond d'une section est une vraie `<img>` derrière le contenu, et non plus une image de fond CSS. Celle de la première section se charge en priorité (LCP) : un Texte libre avec titre en H1, image et hauteur plein écran fait une bannière.

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
