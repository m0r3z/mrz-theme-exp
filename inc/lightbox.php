<?php
/**
 * Lightbox : images en grand (taille réelle si l'écran est trop petit, au clic) et
 * vidéos YouTube ou Vimeo, dans un <dialog> natif. Ouverte par la section Galerie,
 * les liens vers une image ou une vidéo d'un texte, et les galeries natives [gallery].
 * Le script ne se charge que sur les pages qui en ont besoin.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

/**
 * Déclare le script de la lightbox.
 */
function mrz_theme_register_lightbox_script() {
	wp_register_script(
		'mrz-lightbox',
		MRZ_THEME_URI . '/assets/js/lightbox.js',
		array(),
		mrz_theme_asset_version( 'assets/js/lightbox.js' ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
	wp_add_inline_script(
		'mrz-lightbox',
		'window.mrzLightbox = ' . wp_json_encode(
			array(
				'label'    => __( 'Image viewer', 'mrz-theme-exp' ),
				'close'    => __( 'Close', 'mrz-theme-exp' ),
				'previous' => __( 'Previous', 'mrz-theme-exp' ),
				'next'     => __( 'Next', 'mrz-theme-exp' ),
				'zoomIn'   => __( 'Show actual size', 'mrz-theme-exp' ),
				'zoomOut'  => __( 'Fit to screen', 'mrz-theme-exp' ),
				'video'    => __( 'Video', 'mrz-theme-exp' ),
				/* translators: 1: position, 2: number of items. */
				'counter'  => __( '%1$s / %2$s', 'mrz-theme-exp' ),
			)
		) . ';',
		'before'
	);
}
add_action( 'wp_enqueue_scripts', 'mrz_theme_register_lightbox_script' );

/**
 * Charge la lightbox si un contenu a un lien vers une image, une vidéo YouTube ou Vimeo.
 *
 * @param string $content Contenu mis en forme (the_content, champs WYSIWYG d'ACF).
 * @return string Contenu inchangé.
 */
function mrz_theme_maybe_enqueue_lightbox( $content ) {
	if ( is_string( $content ) && preg_match( '#<a\s[^>]*href=["\'][^"\']+(\.(jpe?g|png|gif|webp|avif)|youtube\.com/watch|youtu\.be/|vimeo\.com/\d)#i', $content ) ) {
		wp_enqueue_script( 'mrz-lightbox' );
	}

	return $content;
}
add_filter( 'the_content', 'mrz_theme_maybe_enqueue_lightbox', 99 );
add_filter( 'acf_the_content', 'mrz_theme_maybe_enqueue_lightbox', 99 );

// Galeries natives [gallery] : mises en forme par le thème (src/css/components.css), sans le style en ligne de WordPress.
add_filter( 'use_default_gallery_style', '__return_false' );
