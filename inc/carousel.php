<?php
/**
 * Carrousel : une liste qui défile horizontalement (scroll-snap natif du navigateur),
 * avec flèches, points et lecture automatique facultative.
 *
 * Le parent fournit le comportement, les sections de l'enfant le contenu :
 *
 *     mrz_theme_carousel_open( array( 'label' => $titre ) );
 *     foreach ( $elements as $element ) {
 *         echo '<li class="mrz-carousel__slide">…</li>';
 *     }
 *     mrz_theme_carousel_close();
 *
 * Le nombre de cartes visibles se règle en CSS (--mrz-carousel-per-view, décimales
 * acceptées). Le script ne se charge que sur les pages qui ont un carrousel.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

/**
 * Déclare le script du carrousel (chargé seulement par mrz_theme_carousel_open()).
 */
function mrz_theme_register_carousel_script() {
	wp_register_script(
		'mrz-carousel',
		MRZ_THEME_URI . '/assets/js/carousel.js',
		array(),
		mrz_theme_asset_version( 'assets/js/carousel.js' ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
	wp_add_inline_script(
		'mrz-carousel',
		'window.mrzCarousel = ' . wp_json_encode(
			array(
				/* translators: 1: number of the first visible item, 2: total number of items. */
				'position' => __( 'Item %1$s of %2$s', 'mrz-theme-exp' ),
				/* translators: 1: page number, 2: number of pages. */
				'page'     => __( 'Page %1$s of %2$s', 'mrz-theme-exp' ),
				'pause'    => __( 'Pause the carousel', 'mrz-theme-exp' ),
				'play'     => __( 'Play the carousel', 'mrz-theme-exp' ),
			)
		) . ';',
		'before'
	);
}
add_action( 'wp_enqueue_scripts', 'mrz_theme_register_carousel_script' );

/**
 * Ouvre un carrousel. Les éléments suivent, chacun dans un <li class="mrz-carousel__slide">.
 *
 * @param array $args {
 *     @type string $label    Nom du carrousel pour les lecteurs d'écran (le titre de la section).
 *     @type int    $autoplay Lecture automatique : secondes entre deux défilements, 0 pour aucune.
 *     @type bool   $dots     Points de navigation. Défaut true.
 *     @type string $class    Classes en plus sur le conteneur.
 * }
 */
function mrz_theme_carousel_open( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'label'    => '',
			'autoplay' => 0,
			'dots'     => true,
			'class'    => '',
		)
	);

	wp_enqueue_script( 'mrz-carousel' );

	$GLOBALS['mrz_theme_carousel_args'] = $args;

	printf(
		'<div class="mrz-carousel %1$s" role="region" aria-roledescription="%2$s"%3$s data-mrz-carousel data-autoplay="%4$d">',
		esc_attr( $args['class'] ),
		esc_attr__( 'carousel', 'mrz-theme-exp' ),
		$args['label'] ? ' aria-label="' . esc_attr( $args['label'] ) . '"' : '',
		absint( $args['autoplay'] )
	);
	// tabindex : la piste défile aux flèches du clavier, même sans lien dans les éléments.
	echo '<ul class="mrz-carousel__track" tabindex="0">';
}

/**
 * Ferme le carrousel et imprime ses commandes (cachées par main.css si tout tient à l'écran).
 */
function mrz_theme_carousel_close() {
	$args = $GLOBALS['mrz_theme_carousel_args'] ?? array();

	echo '</ul>';
	echo '<div class="mrz-carousel__controls">';

	if ( ! empty( $args['autoplay'] ) ) {
		printf(
			'<button type="button" class="mrz-carousel__button mrz-carousel__pause" data-mrz-carousel-pause aria-label="%1$s">%2$s</button>',
			esc_attr__( 'Pause the carousel', 'mrz-theme-exp' ),
			'<svg class="mrz-carousel__icon-pause" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M8 5v14M16 5v14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg><svg class="mrz-carousel__icon-play" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M8 5l11 7-11 7z" fill="currentColor"/></svg>'
		);
	}

	printf(
		'<button type="button" class="mrz-carousel__button mrz-carousel__prev" data-mrz-carousel-prev aria-label="%1$s"><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M15 6l-6 6 6 6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>',
		esc_attr__( 'Previous', 'mrz-theme-exp' )
	);

	if ( ! isset( $args['dots'] ) || $args['dots'] ) {
		echo '<div class="mrz-carousel__dots" data-mrz-carousel-dots></div>';
	}

	printf(
		'<button type="button" class="mrz-carousel__button mrz-carousel__next" data-mrz-carousel-next aria-label="%1$s"><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M9 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>',
		esc_attr__( 'Next', 'mrz-theme-exp' )
	);

	echo '</div>';
	// Annonce la position après un clic sur une commande (pas pendant la lecture automatique).
	echo '<p class="screen-reader-text" aria-live="polite" data-mrz-carousel-status></p>';
	echo '</div>';

	unset( $GLOBALS['mrz_theme_carousel_args'] );
}
