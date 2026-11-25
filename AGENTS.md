# MRZ Theme Experience : règles pour l'IA

Thème WordPress classique de l'Agence Double A. Le guide d'utilisation est dans `README.md`.

## Principes
- Éditeur classique partout (Gutenberg désactivé par filtres dans `inc/setup.php`).
- Les pages et les sections globales se construisent avec le champ ACF Flexible Content `mrz_builder` (clé `field_mrz_builder`). Son groupe, `group_mrzchild_builder`, appartient au **thème enfant** (`acf-json/` de l'enfant) : chaque site a ses propres sections.
- Le texte (blog, pages simples) passe par `the_content()` et la classe `.mrz-prose`.
- ACF Pro est obligatoire. Sans lui, les pages affichent seulement leur contenu classique.

## Fiches de référence (`docs/ia/`)
Ce fichier contient les règles. Le détail est dans des fiches, à lire quand la tâche touche leur sujet :
- `docs/ia/sections.md` : sections du parent, réglages communs (fond, image et vidéo de fond, animations), recherche. **À lire avant de construire une page.**
- `docs/ia/sections.json` : champs de chaque section et des réglages communs (noms, clés, types, choix, valeurs par défaut), généré depuis `inc/builder-sections.php` par `wp mrz-theme sections --write`. Ne jamais l'écrire à la main. Sur un site, `wp mrz-theme sections` affiche la même référence.
- `docs/ia/variables.md` : toutes les variables CSS, par famille. **À lire avant d'écrire du CSS dans l'enfant.**
- `docs/ia/composants.md` : header transparent, options du Customizer, réglages par page, sections globales et footer, transitions, carrousel, lightbox, sous-menus et mega menu, menu mobile.
- `docs/ia/integrations.md` : plugins MRZ (Display Post, Maps), SVG, Polylang, Gravity Forms, WooCommerce.

Les interdits de ces fiches, à connaître sans les ouvrir :
- Listes de contenus : toujours MRZ Display Post (`[mrzdpe]`) ; cartes : MRZ Maps (`[mrzme]`). Jamais de `WP_Query` ni de filtre JS maison.
- Carrousel, lightbox, sous-menus, mega menu : ceux du parent. Jamais de bibliothèque (Swiper, Fancybox…) ni de version maison dans l'enfant.
- Gravity Forms et WooCommerce : seulement les variables du thème, jamais leurs sélecteurs ni `--gf-*` dans l'enfant. Panier et commande restent en blocs.
- SVG : jamais d'autorisation maison (`upload_mimes`), le plugin Safe SVG s'en charge.
- Couleurs : dans le Customizer, jamais dans le CSS de l'enfant.

## Lighthouse : 100 partout
Chaque site vise **100 sur les quatre scores Lighthouse** (Performance, Accessibilité, Bonnes pratiques, SEO), en mobile. À chaque choix, prendre la solution la plus simple et la plus rapide à afficher.
- **CSS avant JS.** Pas de bibliothèque, pas de jQuery, pas de CDN. Un module JS (carrousel, lightbox) ne se charge que sur les pages qui s'en servent.
- **Image principale (LCP)** : une vraie image `<img>` avec `'fetchpriority' => 'high'` et `'loading' => 'eager'`, jamais une image de fond CSS. Le réglage « Image de fond » des sections le fait déjà. Toutes les autres par `wp_get_attachment_image()` (dimensions, `srcset`, chargement différé).
- **Aucun décalage de mise en page (CLS)** : images avec leurs dimensions, polices en woff2 auto-hébergées avec `font-display: swap`, le moins de fichiers de police possible.
- **Accessibilité** : voir RGAA ci-dessous, qui couvre largement ce que mesure Lighthouse.
- **SEO** : Rank Math pose le titre et la meta description. Le contenu des pages est dans les champs ACF, donc l'extrait automatique est vide : **écrire la meta description de chaque page** dans Rank Math.
- Hors du thème : durée de cache, CSS critique et formats WebP/AVIF relèvent de WP Rocket et de l'hébergement. En local, ces alertes de Lighthouse sont normales.

## Accessibilité : RGAA
Le thème et chaque site suivent les critères du RGAA : https://accessibilite.numerique.gouv.fr/methode/criteres-et-tests/. Un 100 en accessibilité sur Lighthouse n'en couvre qu'une petite partie : le reste se vérifie à la main.
- **Images** (thème 1) : texte alternatif pertinent pour une image porteuse de sens, `alt=""` pour une image décorative, jamais de texte important dans une image.
- **Couleurs** (thème 3) : contraste d'au moins 4,5:1 pour le texte (3:1 pour un grand texte et les composants d'interface) ; une information n'est jamais donnée par la couleur seule.
- **Liens** (thème 6) : intitulé explicite, ou complété par un texte pour lecteur d'écran (« Lire la suite » ne suffit pas seul).
- **Scripts et clavier** (thèmes 7 et 12) : tout se fait au clavier, focus toujours visible, ordre de tabulation logique, composants JS avec les rôles et états ARIA attendus.
- **Structure** (thèmes 8 et 9) : `lang` de la page juste, titres hiérarchisés sans saut, listes en `<ul>`/`<ol>`, zones `<header>`, `<nav>`, `<main>`, `<footer>`.
- **Formulaires** (thème 11) : chaque champ a une étiquette visible et liée, erreurs explicites. Gravity Forms : jamais le balisage hérité (« legacy markup ») ; étiquettes visibles, pas de simple texte indicatif dans le champ.
- **Contenu en mouvement** (thème 13) : rien ne défile tout seul sans bouton pause ; animations coupées avec `prefers-reduced-motion`.
- Pas de mention « conforme RGAA » ni de déclaration d'accessibilité sur un site, sauf demande expresse (clients publics soumis à l'obligation légale).

## Construire une page
Remplir `mrz_builder` avec `update_field( 'field_mrz_builder', $rows, $post_id )`. Chaque ligne porte `acf_fc_layout`. Sections du parent : `docs/ia/sections.md`, leurs champs : `docs/ia/sections.json`. Une modification de `inc/builder-sections.php` se commite avec le JSON régénéré. Une section sur mesure seulement pour ce qu'elles ne savent pas faire.

### Sections sur mesure (thème enfant)
Une section par rangée de la maquette. Pour en créer une :
1. Ajouter le layout dans `acf-json/group_mrzchild_builder.json` de l'enfant, sur `field_mrz_builder`. Nom `mrzchild_<nom>`, clés `layout_mrzchild_<nom>` et `field_mrzchild_<nom>_<champ>`, chaque sous-champ avec `parent_layout`. Mettre à jour `modified`.
   Un sous-champ d'un répéteur porte `parent_repeater` (voir l'exemple). **Instructions ACF : le HTML y est interprété.** Écrire `&lt;em&gt;`, jamais `<em>`.
   Champ image : `"preview_size": "thumbnail"`, pour une petite miniature dans l'admin.
2. Pour les réglages communs, ajouter un sous-champ `clone` de `group_mrz_section_settings` (`display: seamless`, `prefix_name: 0`).
3. Créer `layouts/mrzchild-<nom>.php` dans l'enfant (le nom, `_` remplacés par `-`) : `mrz_theme_section_open( 'mrzchild-<nom>' )`, le contenu échappé, `mrz_theme_section_close()`.
4. Le style de la section va dans le fichier de sa partie, `css/<partie>.css` de l'enfant, en CSS classique et uniquement avec les variables du site (voir CSS).

Exemple de layout avec un répéteur, à placer dans `layouts` de `field_mrz_builder` :

```json
{
    "key": "layout_mrzchild_services",
    "name": "mrzchild_services",
    "label": "Services",
    "display": "block",
    "sub_fields": [
        {
            "key": "field_mrzchild_services_title",
            "label": "Titre",
            "name": "title",
            "type": "text",
            "instructions": "&lt;em&gt;mot&lt;/em&gt; pour le colorer ; &lt;br&gt; pour un retour à la ligne.",
            "parent_layout": "layout_mrzchild_services"
        },
        {
            "key": "field_mrzchild_services_items",
            "label": "Services",
            "name": "items",
            "type": "repeater",
            "layout": "block",
            "button_label": "Ajouter un service",
            "parent_layout": "layout_mrzchild_services",
            "sub_fields": [
                {
                    "key": "field_mrzchild_services_items_icon",
                    "label": "Icône",
                    "name": "icon",
                    "type": "image",
                    "return_format": "id",
                    "preview_size": "thumbnail",
                    "parent_repeater": "field_mrzchild_services_items"
                },
                {
                    "key": "field_mrzchild_services_items_text",
                    "label": "Texte",
                    "name": "text",
                    "type": "text",
                    "parent_repeater": "field_mrzchild_services_items"
                }
            ]
        },
        {
            "key": "field_mrzchild_services_settings",
            "label": "Réglages",
            "name": "settings",
            "type": "clone",
            "clone": ["group_mrz_section_settings"],
            "display": "seamless",
            "prefix_name": 0,
            "parent_layout": "layout_mrzchild_services"
        }
    ],
    "min": "",
    "max": ""
}
```

Un champ `link` vaut `array( 'url' => '', 'title' => '', 'target' => '' )`, affiché par `mrz_theme_button( $link, $style )` : `primary`, `outline` ou `link` (lien souligné suivi d'une flèche). Formulaires, listes MRZ Display Post et cartes MRZ Maps : shortcodes (`[gravityform]`, `[mrzdpe]`, `[mrzme]`) dans un Texte libre, qui les affiche sans filtrer leur balisage. Dans un champ de section sur mesure : `do_shortcode( wp_kses_post( $champ ) )`, jamais `wp_kses_post()` après le shortcode (il retire les `<script>` et les champs de formulaire).

### Code d'une section sur mesure (PHP)
- **Échapper toute sortie** : `esc_html()` pour un texte, `esc_url()` pour un lien, `esc_attr()` pour un attribut, `wp_kses_post()` pour un champ WYSIWYG. Jamais un `echo get_sub_field()` brut (sauf la section du parent `mrz_html`).
- **Images** : `wp_get_attachment_image( $id, $taille )`, jamais une balise `<img>` écrite à la main (WordPress ajoute `srcset`, dimensions et `loading`), avec `mrz_theme_section_image_loading()` pour la priorité. Un texte alternatif vide (`'alt' => ''`) pour une image décorative.
- **Image recadrée (`object-fit: cover`)** : en chargement différé, WordPress ajoute `sizes="auto"`, même devant un `sizes` explicite, et le navigateur choisit le fichier d'après la largeur affichée. Dans un cadre plus haut que l'image, ce fichier est trop petit, donc flou. Demander la taille `full`, pour que `srcset` propose aussi les grands fichiers, et vérifier la netteté à l'écran. Une image prioritaire n'a pas `sizes="auto"` : l'image principale de la page doit garder la priorité de `mrz_theme_section_image_loading()`.
- **Aucun texte visible écrit en dur dans un template de l'enfant** : tout texte (titre, bouton, « Voir plus », texte pour lecteur d'écran) vient d'un champ ACF, qui se traduit avec la page dans chaque langue. Un `aria-label` se construit à partir d'un champ (le titre de la section, par exemple). L'enfant n'a pas de fichier de traduction.
- **Titres** : un seul `<h1>` par page, porté par la première section ; les autres sections commencent à `<h2>`.
- **Liens** : `target="_blank"` toujours avec `rel="noopener"`. Un lien sans texte visible (icône) porte un texte pour les lecteurs d'écran (`<span class="screen-reader-text">`).
- **Ouvrir et fermer la section** avec `mrz_theme_section_open( 'mrzchild-<nom>' )` et `mrz_theme_section_close()` : réglages communs, ancre et conteneur sont gérés par le parent.
- **Variables PHP préfixées** `$mrzchild_` dans les templates (portée globale de `get_template_part`).

### Règles du constructeur
- Préfixes : `mrz_` pour le parent, `mrzchild_` pour l'enfant. Jamais de section `mrz_` dans l'enfant.
- La clé `field_mrz_builder` et le nom `mrz_builder` ne changent jamais.
- Une section du parent n'est jamais renommée (nom, clés de layout et de champs) : le contenu des pages y est rattaché.
- Une section du parent n'est jamais supprimée : la retirer du menu, garder son template pour les pages qui l'utilisent.
- Une section de l'enfant qui sert sur trois sites peut remonter dans le parent : renommer en `mrz_` et migrer le contenu des sites concernés.

## CSS

### Règle d'or : un CSS minimal, entièrement fait de variables
Un site doit avoir **un seul** style de bouton, de champ, de titre et de texte, partout : un bouton « Envoyer » de Gravity Forms, « Ajouter au panier » de WooCommerce et un bouton d'une section ont la même police, la même taille, la même graisse, le même rayon. Trois formes seulement, toutes par `mrz_theme_button( $link, $style )` : plein (`primary`), contour (`outline`) et lien fléché (`link`, classe `.mrz-btn--link`, pour « Voir tout », « Télécharger »). Les Cartes choisissent la leur (réglage « Style du lien »). Jamais de fonction de lien maison dans l'enfant.
- **Jamais de valeur en dur** (taille de police, graisse, couleur, rayon) dans un fichier de section ou de partie : toujours une variable. Une valeur qui manque devient une variable dans le `style.css` de l'enfant, puis sert partout.
- **Jamais de CSS propre à une page.** Pas de règle sur `.page-id-12`, pas de bouton retouché pour un seul formulaire. Si une page semble demander un style à part, c'est une section sur mesure qui le porte, avec les variables du site.
- **Le moins de CSS possible.** Avant d'écrire une règle, vérifier qu'une variable ne suffit pas. Ne pas redéfinir ce que le parent fait déjà (boutons, champs, titres, prose).
- Pas de `!important` : le CSS de l'enfant est hors couche et passe déjà devant celui du parent.

### Site clair ou sombre : le texte suit le fond
- Le thème marche aussi bien sur un site clair que sombre. Le fond du site se règle dans le Customizer (« Fond du site ») ; `--color-text` y prend Sombre ou Clair, celle qui contraste le plus.
- Chaque fond de section (`.mrz-bg--light`, `--dark`, `--primary`…) redéfinit `--color-background`, `--color-text` et `--color-muted` pour tout ce qu'il contient. Les champs de formulaire suivent la section (`--mrz-field-bg`, `--mrz-field-color` et `--mrz-field-border` ne sont pas définies par défaut : ne les définir que pour fixer les champs sur tout le site).
- Dans le CSS de l'enfant : **toujours `var(--color-text)`, `var(--color-muted)`, `var(--color-background)`**, jamais `var(--color-dark)` ni un blanc pour colorer du texte. Ainsi, un bloc reste lisible quel que soit le fond où on le pose.
- Une carte qui change de fond dans une section : lui donner le même jeu de variables (`--color-background`, `--color-text`, `--color-muted`) plutôt que des couleurs.
- Redéfinir `--color-text` ou `--color-muted` sur une section ou un bloc est permis (liens gris d'un footer sur fond clair : `--color-text: var(--color-tertiary)`), à condition que le contraste reste suffisant.

### Fichiers du thème enfant
- `style.css` : en-tête du thème, `@font-face` des polices du site, et les variables du site dans `:root` (et dans des `@media` du `:root` pour les redéfinir sous un point de rupture). Rien d'autre.
- Une variable qui cite une autre variable (`--mrz-eyebrow-color: var(--color-text)`) est calculée dans `:root`, une fois pour toutes : elle ne suit pas une section qui redéfinit `--color-text`. Pour suivre le fond de la section, écrire `currentColor` ou redéfinir la variable sur la section.
- Une section globale est enveloppée dans `.mrz-global-section` : les sélecteurs `+` et `:last-child` visent cette enveloppe, pas la section qu'elle contient.
- `css/<partie>.css` : un fichier par partie du site (`header.css`, `footer.css`, `sections.css`, `formulaires.css` si besoin…). Chargés automatiquement par le parent, après `style.css`, par ordre alphabétique : aucun `wp_enqueue_style` à écrire.
- Une section sur mesure a ses règles dans le fichier de sa partie, ciblées par sa classe `.mrz-section--mrzchild-<nom>`.

### Thème parent
- Tailwind v4 compile seulement le thème parent : `npm run build`. Le CSS compilé `dist/main.css` est commité.
- Variables de design dans `src/css/tokens.css` (`@theme static` pour couleurs, polices, rayons, espacements ; `:root` pour le reste). Classes générées : `bg-primary`, `text-muted`, `font-title`, `rounded-base`, `py-section`…
- Les composants sont en CSS dans `src/css/components.css`, préfixe `.mrz-`, dans `@layer components`. Les éléments HTML (titres, champs, boutons d'envoi) dans `src/css/base.css`.
- Le CSS de Gravity Forms et de WooCommerce est hors couche : il passe devant le CSS du thème. Leurs réglages vont dans `src/css/gravity-forms.css` (importé hors couche dans `main.css`) et `assets/css/woocommerce.css` (chargé dès que WooCommerce est actif), toujours avec les variables.
- **Thème enfant : CSS classique uniquement.** Ne jamais écrire de nouvelle classe Tailwind dans un template de l'enfant : elle n'existe pas dans `dist/main.css`.
- Une classe Tailwind utilisée dans le parent doit apparaître en entier dans un fichier scanné (pas de nom construit par concaténation).

## Ce qui manque au parent
Ne jamais modifier le parent installé sur un site, même pour le dépanner : la prochaine mise à jour l'écrase. Contourner le moins possible dans l'enfant, et lister le manque dans le compte rendu (« Manques du parent ») : il sera corrigé dans le dépôt du parent.

## Code
- Préfixe `mrz_theme_` (fonctions), `mrz_` (theme_mods, CSS), textdomain `mrz-theme-exp`, chaînes source en anglais. Toute chaîne ajoutée ou modifiée : mettre à jour `languages/mrz-theme-exp.pot`, `fr_FR.po` et recompiler `fr_FR.mo` (commandes dans le README).
- Conventions WordPress, échappement de toutes les sorties, `wp_enqueue_*` pour tous les assets.
- JS vanilla, sans jQuery, dans `assets/js/` (pas de build JS).
- Icônes SVG dans `assets/icons/`, affichées avec `mrz_theme_icon( 'nom' )` (en ligne, `fill="currentColor"`). L'enfant remplace une icône par un fichier du même nom dans son `assets/icons/`.
- Tailles d'images : le thème n'en ajoute aucune. Il déclare seulement celles de WooCommerce (`inc/setup.php` : vignette 600, fiche 1200, galerie 200). Les templates appellent `medium`, `medium_large`, `large` et `full` ; une taille absente est remplacée par `full`. Le tri des tailles générées (plafond à 1920 px, seulement 768 px et WooCommerce) relève du mu-plugin d'agence `aa-images`, pas du thème.
- Surcharges depuis l'enfant : mêmes chemins (`layouts/`, `parts/`), en dernier recours (la copie ne reçoit plus les corrections du parent). Préférer les hooks :
  - `mrz_header_tools_start`, `mrz_header_tools_end` : dans le header, avant et après les outils (recherche, compte, panier), pour un téléphone ou une icône. Un bouton avec un texte passe plutôt par le menu secondaire (texte traduit avec les menus) : l'enfant n'a pas de fichier de traduction ;
  - `mrz_mobile_menu_end` : en bas du menu mobile (aussi emplacement de section globale) ;
  - les emplacements des sections globales (`mrz_before_header`… `mrz_after_footer`).
- Le parent ne renomme ni ne supprime ce que les enfants utilisent (fonctions `mrz_theme_*`, classes `.mrz-`, hooks, emplacements de menus, sections du constructeur). Un changement qui casse ces noms impose une version majeure et des notes de release.

## Parent ou enfant
- Parent : comportements, accessibilité, JS, intégrations (WooCommerce, Gravity Forms, Polylang, plugins MRZ), et les variantes qui reviennent d'un site à l'autre, sous forme de réglages (dispositions du header, styles du menu mobile, style du lien des cartes). Une variante entre dans le parent quand trois sites en ont besoin, pas avant.
- Enfant : l'apparence du site, en variables et en CSS sur les classes `.mrz-` du parent.
