# Sections du parent et réglages communs

Fiche de référence pour l'IA, lue quand la tâche touche ce sujet. Les règles générales sont dans `../../AGENTS.md`.

## Champs : `sections.json`
Les champs de chaque section (nom, clé, type, choix et leurs libellés, valeur par défaut, sous-champs des répéteurs, conditions d'affichage `shown_if`) et ceux des réglages communs (`settings`) sont dans **`sections.json`**, à côté de cette fiche. Il est généré depuis `inc/builder-sections.php` : ne jamais l'écrire à la main.

- Sur un site : `wp mrz-theme sections` affiche la même référence, lue dans le thème installé.
- Dans le dépôt, après une modification de `inc/builder-sections.php` : `wp mrz-theme sections --write`, puis commiter le JSON avec le code. `--check` échoue si la fiche n'est plus à jour (étape de la release).

Cette fiche ne garde que ce que le JSON ne dit pas : à quoi sert chaque section et comment s'en servir.

## Sections du parent (injectées sur tous les sites)
Déclarées en PHP dans `inc/builder-sections.php`, ajoutées au constructeur de l'enfant au chargement.

| Section | Note |
| --- | --- |
| `mrz_text` | Texte libre. `title_tag` : `h1` si la section ouvre la page, sinon `h2`, puis `h3` à `h6` pour suivre la hiérarchie. `content` accepte le HTML et les shortcodes. `link` est le bouton plein, `link_2` le bouton à contour. Jamais de bouton, de surtitre ni de classe d'alignement écrits à la main dans le contenu : les champs le font |
| `mrz_html` | Affiché tel quel, scripts compris, sans section ni conteneur ; champ visible seulement pour un administrateur (`manage_options` et `unfiltered_html`) |
| `mrz_cards` | Cartes libres. `per_row` : cartes par ligne sur grand écran. `card_style` `figures` (chiffres clés) : le titre de la carte est le chiffre ; `count_up` le fait monter depuis 0 (`assets/js/counter.js`), la valeur finale étant déjà dans la page. Image SVG = icône à la couleur du texte. Titre des cartes en `h3` sous un titre de section, en `h2` sans titre de section. Variables `--mrz-cards-*`, `--mrz-card-*` |
| `mrz_split` | Texte et média. `aside_background` `none` = second texte, une couleur = encadré. Empilés sur mobile, texte en premier ; l'ordre du HTML ne change pas avec le côté (lecture et tabulation). Image prioritaire si la section ouvre la page. Variables `--mrz-split-*` |
| `mrz_accordion` | Accordéon en `<details>` natif, sans JS. Pas de titre dans `<summary>` (son rôle de bouton masque le titre aux lecteurs d'écran). Variables `--mrz-accordion-*` |
| `mrz_gallery` | Galerie (`images` : IDs). `display` `masonry` : colonnes CSS, formats d'origine, `ratio` ignoré. Le texte alternatif décrit l'image, la légende de la médiathèque s'affiche dessous et dans la lightbox. Variables `--mrz-gallery-*` |
| `mrz_anchors` | Sous-menu d'ancres. Collé sous le header en option ; lien de la section affichée en `aria-current` (`assets/js/anchors.js`). Son espacement vient de `--mrz-anchors-padding`. Lien actif et survol : seulement la couleur (`--mrz-anchors-active-color`), comme le menu. Les ancres arrivent juste sous le header et la barre, en défilement doux (`scroll-behavior: smooth` sur `html`, coupé par `prefers-reduced-motion`). Variables `--mrz-anchors-*` |
| `mrz_marquee` | Bandeau défilant en CSS. Logo : le texte de l'élément devient son alternative (sinon celle de la médiathèque), à remplir quand le logo est un lien. Bouton pause toujours affiché, arrêt au survol et au focus ; rien ne bouge si le visiteur limite les animations. Copies masquées aux lecteurs d'écran. Variables `--mrz-marquee-*` |
| `mrz_footer` | Footer. Proposée seulement dans les sections globales (`global_only` dans le JSON). `free_content` : colonne libre après les colonnes de liens (texte, formulaire d'inscription en shortcode). Variables `--mrz-footer-*` |
| `mrz_global_section` | Insère une section globale (`section` : ID d'un contenu `mrz_section`) |

Titres des sections du parent (Texte libre, titre et cartes de Cartes) : `<em>mot</em>` colore un mot (`--mrz-title-highlight-color`), `<strong>` le met en gras, `<br>` force un retour à la ligne ; rien d'autre ne passe (`mrz_theme_title_html()`, à utiliser aussi dans les sections sur mesure).

## Réglages communs (`settings`)
Champs et valeurs : `settings` dans `sections.json`. `width` `narrow` donne une largeur de lecture ; `gradient_to` `none` mène au fond du site. Lu par `mrz_theme_section_open()`, qui accepte aussi `'class'` : plusieurs classes séparées par des espaces, ou un tableau, et `'image'` (ID) pour une image de fond fournie par la section.

- **Image de fond** : rendue en vraie `<img class="mrz-section__bg">` derrière le contenu (`object-fit: cover`). La première de la page est en `fetchpriority="high"`, les autres en chargement différé. C'est le bon moyen d'avoir l'image principale d'une bannière (LCP).
- **Voile** : `dark` et `light` posent un voile (`--mrz-image-overlay`, `--mrz-image-overlay-light`) et passent le texte en Clair (classe `.mrz-has-image`) ; `none` laisse les couleurs de la section (image composée avec un côté blanc, motif). Vérifier le contraste du texte avec le voile léger et sans voile.
- **Vidéo de fond** (`background_video`) : toujours avec une image de fond, affichée d'abord (image d'attente et LCP). `assets/js/video.js` (chargé seulement si une section a une vidéo) pose la source après le chargement complet de la page ; rien sur mobile sauf `video_mobile`, rien si le visiteur limite les animations ou économise ses données. Bouton pause affiché pendant la lecture (RGAA 13.8). Jamais de vidéo de fond YouTube : un fichier du site.
- **Animations au défilement** (`motion`) : déclenchées à l'entrée à l'écran par `assets/js/motion.js` (classe `.is-inview`, `IntersectionObserver`), puis jouées en entier en CSS (`--mrz-motion-duration`), sans dépendre de la vitesse de défilement. En remontant, jouées à l'envers dès que l'élément repasse sous la ligne de déclenchement (10 % au-dessus du bas de l'écran), sauf réglage « une seule fois » du Customizer (`motion_once`). Le module n'est chargé que si une section en a une ; sans JavaScript, contenu affiché normalement (rien n'est caché sans la classe `mrz-motion-js` sur `<html>`). Le surlignage animé du mot en couleur (Customizer) passe par le même module et le même réglage (`--mrz-highlight-duration`, `--mrz-highlight-delay`). La parallaxe (`parallax`) reste en CSS seul, liée au défilement (`animation-timeline: view()`). Coupées par `prefers-reduced-motion`. `motion` ne s'applique jamais à la première section de la page (LCP). La parallaxe ne joue que sur l'image de fond. Ne pas ajouter de bibliothèque d'animation (AOS, GSAP) dans l'enfant : régler `--mrz-motion-*` et `--mrz-parallax-*`.
- **Bannière de page** : Texte libre, `title_tag` à `h1`, image (ou vidéo) de fond, hauteur `large` ou `screen`. Le header transparent suit tout seul (voir plus bas). Une section sur mesure seulement si la bannière fait ce qu'un Texte libre ne sait pas faire.
- **Images des sections** : `mrz_theme_section_image_loading()` donne `loading`, `fetchpriority` et `decoding` (prioritaire dans la première section de la page, différé ailleurs) ; `mrz_theme_image_ratio_style()` pose le format réel de l'image (`--mrz-img-ratio`) pour réserver sa place avant chargement. Les reprendre dans une section sur mesure. Ne jamais écrire `aspect-ratio: auto` sur une image : il annule la place réservée par ses attributs `width` et `height` (décalage de mise en page).

## Recherche et extrait
Le texte des sections (titres, textes, intitulés des boutons, éléments des répéteurs ; pas les réglages, les ancres ni les médias) est recopié sans balises dans la meta cachée `_mrz_search_text` à chaque enregistrement du constructeur, `update_field()` compris (`inc/search.php`). La recherche de WordPress le lit, et l'extrait automatique d'une page construite en part. `post_content` et `post_excerpt` ne sont jamais modifiés. Rank Math ne le lit pas : la meta description de chaque page reste à écrire.
