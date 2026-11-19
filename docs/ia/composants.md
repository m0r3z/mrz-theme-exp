# Composants et réglages du thème

Fiche de référence pour l'IA, lue quand la tâche touche ce sujet. Les règles générales sont dans `../../AGENTS.md`.

## Header transparent
Header transparent : automatique quand l'option du Customizer est cochée et que la première section de la page a une image ou une vidéo de fond (réglages communs). Une première section dont le nom contient `hero` compte aussi, pour les sites qui en ont une. Pour le forcer ou le couper sur une page : meta box « MRZ Theme » de la page (`_mrz_transparent_header` à `on` ou `off`).
Sous un header transparent, la première section reçoit en haut `--spacing-section` + `--mrz-header-offset` (place du header). Une section sur mesure dont l'image est dans le flux (pas en image de fond) remet son `padding-top` à 0 et décale son texte de `var(--mrz-header-offset)`.

## Options
- Customizer natif, panneau « MRZ Theme » (le logo natif y est déplacé, section Header), ouvert par le menu d'admin « MRZ Theme » (`inc/admin.php`), qui porte aussi les sections globales. Pas de page d'options sur mesure : garder les écrans natifs de WordPress. Lecture : `mrz_theme_option( 'nom' )`, valeurs par défaut dans `inc/options.php`.
- Le point de rupture mobile est un réglage : les classes `.mrz-desktop-only` et `.mrz-mobile-only` sont pilotées par du CSS en ligne, pas par Tailwind. Une variable ne peut pas servir dans `@media` : l'enfant recopie la valeur du réglage (`@media (max-width: <valeur>px)`) et la note dans sa fiche de site.

## Réglages par page
Meta box native « MRZ Theme » (`inc/page-options.php`), sans ACF, sur tous les types publics. Metas `_mrz_hide_header` (`'1'` ou absente) et `_mrz_transparent_header` (`on`, `off` ou absente). Lecture : `mrz_theme_page_option( '_mrz_hide_header' )`. Classe du body : `mrz-no-header`. Ne jamais écrire de CSS sur `.page-id-*` : passer par ces réglages.
- **Titre** : une page n'affiche jamais son titre ; sa première section (ou son contenu) porte le `<h1>`. Un article affiche toujours son titre.
- **Footer** : section globale ; on le retire d'une page par ses conditions, pas par un réglage de page.

## Sections globales
CPT `mrz_section`. Meta `mrz_section_hook` (emplacement), `mrz_section_priority`, et conditions : `mrz_section_display` (`all`, `include`, `exclude`), `mrz_section_post_types` (types publics : un type couvre ses contenus et ses listes), `mrz_section_posts` (IDs). Test : `mrz_theme_global_section_matches()`. Polylang : voir plus bas. Hooks du thème : `mrz_topbar` (bandeau au-dessus du header et en haut du panneau du panier, `mrz_theme_topbar( 'header' | 'cart' )` ; ses sections ne comptent pas dans le rang des sections et leurs titres deviennent des paragraphes, comme dans un mega menu), `mrz_before_header`, `mrz_after_header`, `mrz_before_content`, `mrz_after_content`, `mrz_footer`, `mrz_before_footer`, `mrz_after_footer`. Une section sur toutes les pages (bannière avant le footer) : emplacement `mrz_before_footer`, conditions `all`. Sur quelques pages seulement : conditions `include`, ou section insérée à la main dans le constructeur de ces pages.

### Footer
- Le parent n'a pas de footer par défaut (ni widgets, ni menu de footer, ni copyright). Le footer d'un site est une section globale à l'emplacement `mrz_footer`, que `footer.php` enveloppe dans `<footer class="mrz-footer">`.
- Le parent fournit la section `mrz_footer` : logo (facultatif, centré), phrase et bouton, réseaux sociaux (icône SVG à la couleur du texte, lien, nom), colonnes (titre + liste de liens en WYSIWYG), ligne du bas (balises `{year}` et `{site}`). Elle n'est proposée que dans les sections globales (filtre `mrz_theme_global_only_layouts`, que l'enfant peut compléter). Apparence : variables `--mrz-footer-*`. Quand une maquette s'en écarte trop, l'enfant crée sa propre section `mrzchild_footer`.
- Les liens du footer sont écrits dans la section, pas dans des menus WordPress : une section globale se traduit en une fois avec Polylang, au lieu d'un menu par emplacement et par langue.

## Transitions de page
Customizer > Transitions de page : fondu, glissement, rideau ou zoom, en View Transitions entre documents (CSS seul, `inc/customizer.php`). En deux temps : l'ancienne page disparaît entièrement sur la « couleur entre les deux pages » (fond du site par défaut), puis la nouvelle arrive ; jamais de superposition. Le header (`view-transition-name: mrz-header`) et la barre d'admin restent immobiles. Coupées si le visiteur limite les animations. Un effet propre au site (transformation d'image, révélation circulaire) va dans l'enfant ; ne jamais réutiliser le nom `mrz-header`.

## Carrousel
Pour tout ce qui défile (cartes, témoignages, logos) : jamais de bibliothèque ni de carrousel maison dans l'enfant.
```php
mrz_theme_carousel_open( array( 'label' => $mrzchild_title, 'autoplay' => 0, 'dots' => true ) );
foreach ( $mrzchild_items as $mrzchild_item ) {
	echo '<li class="mrz-carousel__slide">…</li>';
}
mrz_theme_carousel_close();
```
- Défilement natif du navigateur (scroll-snap) : glissé au doigt, trackpad, flèches du clavier sur la piste. `assets/js/carousel.js` (commandes) ne se charge que sur les pages qui appellent `mrz_theme_carousel_open()`.
- `label` : le titre de la section (nom du carrousel pour les lecteurs d'écran). Sans libellé, le carrousel n'est pas une région nommée (pas de `role="region"`). `autoplay` : secondes entre deux défilements, 0 par défaut ; activé, un bouton pause est toujours affiché, et la lecture s'arrête au survol, au focus et si l'utilisateur limite les animations (RGAA 13.8).
- Précédent et Suivant avancent d'une page de cartes visibles ; en fin de liste, Suivant revient au début. Commandes masquées si tout tient à l'écran.
- Apparence dans l'enfant, par section : `--mrz-carousel-per-view` (décimales acceptées : `3.5` montre la moitié de la suivante), avec un point de rupture par écran, `--mrz-carousel-gap`, `--mrz-carousel-button-size`, `--mrz-carousel-button-bg` et `--mrz-carousel-button-color` (transparent et texte par défaut), `--mrz-carousel-dot-size`.

## Lightbox
- `assets/js/lightbox.js` (`<dialog>` natif) s'ouvre sur les images de la section Galerie, les liens vers une image, une vidéo YouTube, ou un fichier MP4 ou WebM du site dans un texte (`.mrz-prose`), et les galeries natives `[gallery link="file"]`. Chargé seulement si la page en a besoin (`inc/lightbox.php` regarde le contenu).
- Image affichée en entier ; plus grande que l'écran, elle passe en taille réelle au clic (ou bouton loupe) et l'on s'y déplace en faisant défiler. Flèches, Échap, glissé au doigt, focus rendu au lien.
- Vidéos : lecteur chargé seulement à l'ouverture, `youtube-nocookie.com`. Ça reste un service tiers : à mentionner dans la politique de confidentialité du site. Vimeo n'est pas pris en charge : une vidéo Vimeo s'héberge sur le site en MP4, ou passe par YouTube. Fichier MP4 ou WebM : lecteur natif avec ses commandes ; le texte du lien nomme la vidéo. Une vidéo parlée a besoin de sous-titres ou d'une transcription à côté (RGAA 4).
- Pour une section sur mesure : un lien `<a href="(image en taille réelle)" data-mrz-lightbox="groupe">` autour de l'image et `wp_enqueue_script( 'mrz-lightbox' )`. Jamais de lightbox maison ni de bibliothèque (Fancybox, GLightbox).

## Sous-menus du header
Menus principal et secondaire, jusqu'à trois niveaux. Ouverts au survol (fermeture retardée de 0.2s), au clavier (Tab entre dans le sous-menu, Échap le ferme et rend le focus au lien parent) et au toucher (premier appui ouvre, second suit le lien). Le premier niveau part du bas du header (coins du haut droits, même fond que le header par défaut) ; un pont invisible garde le survol entre le lien et le sous-menu. Un sous-menu qui sortirait de l'écran s'ouvre vers la gauche (classe `.mrz-submenu--flip`). Tout est dans le parent (`src/css/components.css`, `assets/js/main.js`) : l'enfant ne règle que l'apparence, par les variables `--mrz-submenu-*`.
- **Mega menu** : classe CSS `mrz-megamenu` sur un élément du premier niveau (Apparence > Menus, « Classes CSS »). Son sous-menu devient un panneau en colonnes : chaque élément du deuxième niveau est un titre de colonne, ses enfants sont listés dessous, toujours visibles. Échap ferme tout le panneau et rend le focus au lien du premier niveau. Une **section globale** placée au deuxième niveau (boîte « Sections globales » d'Apparence > Menus) forme une colonne : image, texte, bouton, Cartes… Emplacement de la section : aucun. Elle n'apparaît que dans le mega menu du header (retirée du menu mobile, du menu secondaire et du premier niveau). Dans le menu, ses titres deviennent des paragraphes (le menu passe avant le `h1` de la page), ses Cartes passent à une par ligne, son Texte et média s'empile et ses images ne prennent pas la priorité de chargement. Apparence : `--mrz-megamenu-columns` (`auto-fit` : une colonne par élément du deuxième niveau, ou un nombre), `--mrz-megamenu-column-min`, `--mrz-megamenu-width`, et les `--mrz-submenu-*` ; une mise en forme propre au site va dans `css/header.css` de l'enfant, sur `.mrz-megamenu`. Ne jamais recoder l'ouverture ni Échap dans l'enfant.
- **Dispositions du header** (Customizer > Header) : classique, menu à gauche (`parts/header/header-left.php`), centrée, logo centré (`parts/header/header-split.php` : menu principal à gauche, logo au centre, menu secondaire et outils à droite ; sur mobile, logo à gauche). Réglage « Bouton Menu sur ordinateur » (`mrz_menu_button_desktop` : `none`, `replace` à la place du menu principal, `add` en plus du menu principal) : le bouton ouvre le panneau du menu mobile, qui montre le menu Mobile s'il est affecté, sinon le menu principal. Option « Menu » écrit à côté de l'icône (`mrz_menu_button_label`), qui devient le nom du bouton.

## Menu mobile
Quatre styles au choix (Customizer > Header mobile, theme_mod `mrz_mobile_menu_style` : `right`, `left`, `fullscreen`, `dropdown`), sur le même balisage `parts/mobile-menu/offcanvas.php`, classe `.mrz-offcanvas--<style>`. Le parent ne pose que la position et l'ouverture. En plein écran, le bouton fermer prend la place exacte du burger. L'apparence (fond, flou, opacité, survol des liens) se fait dans `css/mobile-menu.css` de l'enfant, en ciblant `.mrz-offcanvas--<style> .mrz-offcanvas__panel`. Ne pas copier le template.
