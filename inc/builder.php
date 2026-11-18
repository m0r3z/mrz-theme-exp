<?php
/**
 * Moteur du constructeur de pages : champ ACF Flexible Content « mrz_builder ».
 *
 * Le groupe de champs du constructeur appartient au thème enfant (sections sur
 * mesure, préfixe mrzchild_). Le parent y injecte ses propres sections
 * (inc/builder-sections.php) et affiche chaque section avec layouts/{nom}.php,
 * cherché d'abord dans l'enfant.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

/**
 * Alerte dans l'admin si ACF Pro manque : le thème se rabat alors sur the_content().
 */
function mrz_theme_acf_missing_notice() {
	if ( class_exists( 'ACF' ) || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	echo '<div class="notice notice-error"><p>' . esc_html__( 'MRZ Theme Experience requires Advanced Custom Fields Pro. Without it, pages only display their classic content.', 'mrz-theme-exp' ) . '</p></div>';
}
add_action( 'admin_notices', 'mrz_theme_acf_missing_notice' );

/**
 * Charge les groupes de champs du thème depuis acf-json/.
 *
 * @param array $paths Chemins existants.
 * @return array
 */
function mrz_theme_acf_load_paths( $paths ) {
	$paths[] = MRZ_THEME_DIR . '/acf-json';

	return $paths;
}
add_filter( 'acf/settings/load_json', 'mrz_theme_acf_load_paths' );

/**
 * En développement seulement (constante MRZ_THEME_DEV), les groupes du parent
 * (group_mrz_*) s'enregistrent dans son dossier acf-json/. Sur un site client,
 * ils ne s'écrivent jamais dans le thème parent. Les groupes de l'enfant
 * (group_mrzchild_*) suivent le chemin par défaut d'ACF : acf-json/ de l'enfant.
 *
 * @param array $paths Chemins d'enregistrement.
 * @param array $post  Groupe de champs.
 * @return array
 */
function mrz_theme_acf_save_paths( $paths, $post ) {
	if ( defined( 'MRZ_THEME_DEV' ) && MRZ_THEME_DEV && isset( $post['key'] ) && str_starts_with( $post['key'], 'group_mrz_' ) ) {
		return array( MRZ_THEME_DIR . '/acf-json' );
	}

	return $paths;
}
add_filter( 'acf/json/save_paths', 'mrz_theme_acf_save_paths', 10, 2 );

/**
 * Titre lisible des layouts repliés : nom du layout + titre ou début du texte.
 *
 * @param string $title  Titre par défaut.
 * @param array  $field  Champ flexible.
 * @param array  $layout Layout.
 * @return string
 */
function mrz_theme_layout_title( $title, $field, $layout ) {
	$label = get_sub_field( 'title' );

	if ( ! $label && 'mrz_global_section' === $layout['name'] ) {
		$section_id = (int) get_sub_field( 'section' );
		$label      = $section_id ? get_the_title( $section_id ) : '';
	}
	if ( ! $label ) {
		$content = get_sub_field( 'content' );
		$label   = $content ? wp_trim_words( wp_strip_all_tags( $content ), 8 ) : '';
	}

	return $label ? $title . ' : <em>' . esc_html( $label ) . '</em>' : $title;
}
add_filter( 'acf/fields/flexible_content/layout_title/name=mrz_builder', 'mrz_theme_layout_title', 10, 3 );

/**
 * La page a-t-elle des sections dans le constructeur ?
 *
 * @param int|null $post_id Post concerné.
 * @return bool
 */
function mrz_theme_has_builder( $post_id = null ) {
	if ( ! function_exists( 'have_rows' ) ) {
		return false;
	}

	$layouts = get_post_meta( $post_id ? $post_id : get_the_ID(), 'mrz_builder', true );

	return is_array( $layouts ) && ! empty( $layouts );
}

/**
 * Affiche toutes les sections du constructeur.
 *
 * @param int|null $post_id Post concerné.
 */
function mrz_theme_render_builder( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	while ( have_rows( 'mrz_builder', $post_id ) ) {
		the_row();
		$layout = str_replace( '_', '-', sanitize_key( get_row_layout() ) );
		get_template_part( 'layouts/' . $layout );
	}
}

/**
 * Ouvre une section : fond, espacement, largeur, ancre (réglages communs).
 *
 * @param string $layout Nom du layout.
 * @param array  $args   image (ID d'image de fond), class (classes en plus : chaîne
 *                       séparée par des espaces, ou tableau).
 */
function mrz_theme_section_open( $layout, $args = array() ) {
	$settings = (array) get_sub_field( 'settings' );

	// Rang de la section dans la page (sections globales du haut comprises) : la première porte l'image prioritaire.
	// Une section dans un mega menu ne compte pas.
	if ( empty( $GLOBALS['mrz_theme_in_menu'] ) ) {
		$GLOBALS['mrz_theme_section_index'] = ( $GLOBALS['mrz_theme_section_index'] ?? 0 ) + 1;
	}

	$background = $settings['background'] ?? 'none';
	$spacing    = $settings['spacing'] ?? 'normal';
	$width      = $settings['width'] ?? 'container';
	$anchor     = $settings['anchor'] ?? '';
	$image_id   = ! empty( $args['image'] ) ? (int) $args['image'] : (int) ( $settings['background_image'] ?? 0 );
	$video_id   = (int) ( $settings['background_video'] ?? 0 );
	$overlay    = $settings['image_overlay'] ?? 'dark';
	$height     = $settings['height'] ?? 'auto';

	$classes = array(
		'mrz-section',
		'mrz-section--' . $layout,
		'mrz-bg--' . $background,
		'mrz-space--' . $spacing,
	);
	foreach ( array( 'top', 'bottom' ) as $side ) {
		$value = $settings[ 'spacing_' . $side ] ?? 'inherit';
		if ( in_array( $value, array( 'none', 'compact', 'normal', 'large' ), true ) ) {
			$classes[] = 'mrz-space-' . $side . '--' . $value;
		}
	}
	if ( in_array( $height, array( 'large', 'screen' ), true ) ) {
		$classes[] = 'mrz-height--' . $height;
	}
	// Animation au défilement : jamais sur la première section (elle porterait l'image principale, LCP).
	$motion = $settings['motion'] ?? 'none';
	if ( in_array( $motion, array( 'fade_up', 'fade', 'zoom', 'cascade' ), true ) && 1 !== ( $GLOBALS['mrz_theme_section_index'] ?? 0 ) && empty( $GLOBALS['mrz_theme_in_menu'] ) ) {
		$classes[] = 'mrz-motion--' . str_replace( '_', '-', $motion );
		mrz_theme_motion_init();
	}

	if ( $image_id || $video_id ) {
		$classes[] = 'mrz-has-bg-image';
		$parallax  = $settings['parallax'] ?? 'none';
		if ( in_array( $parallax, array( 'light', 'medium' ), true ) ) {
			$classes[] = 'mrz-parallax--' . $parallax;
		}
		// Voile et texte en Clair, sauf « sans voile » : la section garde ses couleurs.
		if ( 'none' !== $overlay ) {
			$classes[] = 'mrz-has-image';
		}
		if ( 'light' === $overlay ) {
			$classes[] = 'mrz-overlay--light';
		}
	}

	// Dégradé : de la couleur de fond de la section vers une couleur de la palette.
	$style = '';
	if ( ! empty( $settings['gradient'] ) ) {
		$to         = sanitize_key( $settings['gradient_to'] ?? 'dark' );
		// Fond du site : sa valeur figée à la racine, la section redéfinit --color-background.
		$to_color   = in_array( $to, array( 'none', 'background' ), true ) ? 'var(--mrz-site-background)' : 'var(--color-' . $to . ')';
		$directions = array(
			'bottom'   => 'to bottom',
			'right'    => 'to right',
			'diagonal' => '135deg',
		);
		$classes[]  = 'mrz-gradient';
		$style      = '--mrz-gradient-to:' . $to_color . ';--mrz-gradient-direction:' . ( $directions[ $settings['gradient_direction'] ?? 'bottom' ] ?? 'to bottom' ) . ';';
	}
	if ( ! empty( $args['class'] ) ) {
		$extra   = is_array( $args['class'] ) ? $args['class'] : preg_split( '/\s+/', (string) $args['class'], -1, PREG_SPLIT_NO_EMPTY );
		$classes = array_merge( $classes, $extra );
	}

	$containers = array(
		'container' => 'mrz-container',
		'narrow'    => 'mrz-container mrz-container--narrow',
		'wide'      => 'mrz-container mrz-container--wide',
		'full'      => 'mrz-container--full',
	);

	printf(
		'<section%1$s class="%2$s"%3$s>',
		$anchor ? ' id="' . esc_attr( sanitize_title( $anchor ) ) . '"' : '',
		esc_attr( implode( ' ', array_map( 'sanitize_html_class', $classes ) ) ),
		$style ? ' style="' . esc_attr( $style ) . '"' : ''
	);

	if ( $image_id ) {
		mrz_theme_section_background_image( $image_id );
	}
	if ( $video_id ) {
		mrz_theme_section_background_video( $video_id, ! empty( $settings['video_mobile'] ) );
	}

	printf( '<div class="%s">', esc_attr( $containers[ $width ] ?? 'mrz-container' ) );
}

/**
 * Chargement d'une image de section : prioritaire dans la première section de la page
 * (image principale, LCP), différé ailleurs.
 *
 * @return array Attributs loading, fetchpriority et decoding pour wp_get_attachment_image().
 */
function mrz_theme_section_image_loading() {
	$is_first = empty( $GLOBALS['mrz_theme_in_menu'] ) && 1 === ( $GLOBALS['mrz_theme_section_index'] ?? 0 );

	return array(
		'loading'       => $is_first ? 'eager' : 'lazy',
		'fetchpriority' => $is_first ? 'high' : 'auto',
		'decoding'      => $is_first ? 'sync' : 'async',
	);
}

/**
 * Format réel d'une image en variable CSS (--mrz-img-ratio), pour une règle
 * « aspect-ratio: var(--mrz-xxx-ratio, var(--mrz-img-ratio)) » : la place de l'image
 * est réservée avant son chargement (pas de décalage, CLS), recadrage facultatif.
 *
 * @param int    $image_id ID de l'image.
 * @param string $size     Taille affichée.
 * @return string Déclaration CSS ('' si dimensions inconnues).
 */
function mrz_theme_image_ratio_style( $image_id, $size ) {
	$source = wp_get_attachment_image_src( $image_id, $size );

	return $source && $source[1] && $source[2] ? '--mrz-img-ratio:' . (int) $source[1] . ' / ' . (int) $source[2] . ';' : '';
}

/**
 * Image de fond d'une section : une vraie <img> décorative, recadrée sur toute la section.
 *
 * @param int $image_id ID de l'image.
 */
function mrz_theme_section_background_image( $image_id ) {
	echo wp_get_attachment_image(
		$image_id,
		'full',
		false,
		array(
			'class' => 'mrz-section__bg',
			'alt'   => '',
			'sizes' => '100vw',
		) + mrz_theme_section_image_loading()
	);
}

/**
 * Balise d'un titre de section : celle demandée, ou un paragraphe dans un mega menu
 * (le menu passe avant le h1 de la page : pas de titre là, RGAA 9.1).
 *
 * @param string $tag Balise voulue (h1 à h6).
 * @return string
 */
function mrz_theme_heading_tag( $tag ) {
	return empty( $GLOBALS['mrz_theme_in_menu'] ) ? $tag : 'p';
}

/**
 * Texte d'un titre : seuls <em> (mot en couleur, --mrz-title-highlight-color) et <br> passent.
 *
 * @param string $title Titre saisi.
 * @return string HTML sûr.
 */
function mrz_theme_title_html( $title ) {
	return wp_kses(
		(string) $title,
		array(
			'em' => array(),
			'br' => array(),
		)
	);
}

/**
 * Ferme une section.
 */
function mrz_theme_section_close() {
	echo '</div></section>';
}

/**
 * Titre et introduction d'une section.
 *
 * @param string $title Titre.
 * @param string $intro Introduction.
 */
function mrz_theme_section_heading( $title, $intro = '' ) {
	if ( ! $title && ! $intro ) {
		return;
	}

	echo '<header class="mrz-section__heading">';
	if ( $title ) {
		$tag = mrz_theme_heading_tag( 'h2' );
		echo '<' . $tag . ' class="mrz-section__title">' . mrz_theme_title_html( $title ) . '</' . $tag . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- balise d'une liste fermée, titre filtré par wp_kses.
	}
	if ( $intro ) {
		echo '<p class="mrz-section__intro">' . esc_html( $intro ) . '</p>';
	}
	echo '</header>';
}

/**
 * Animations au défilement : charge assets/js/motion.js et, juste avant la première
 * section animée, pose la classe mrz-motion-js sur <html>. Le CSS ne cache les éléments
 * à animer qu'avec cette classe : sans JavaScript, tout s'affiche normalement, et la
 * classe est posée avant que la section s'affiche (pas d'élément qui apparaît, disparaît
 * puis s'anime). Si motion.js ne démarre pas dans les 4 s, la classe est retirée.
 */
function mrz_theme_motion_init() {
	static $done = false;
	if ( $done ) {
		return;
	}
	$done = true;

	wp_enqueue_script( 'mrz-motion' );
	wp_print_inline_script_tag( 'document.documentElement.classList.add("mrz-motion-js");setTimeout(function(){if(!window.mrzMotionReady){document.documentElement.classList.remove("mrz-motion-js");}},4000);' );
}
