<?php
/**
 * Supports du thème, menus, zones de widgets, éditeur classique.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

/**
 * Déclare les fonctionnalités du thème.
 */
function mrz_theme_setup() {
	load_theme_textdomain( 'mrz-theme-exp', MRZ_THEME_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 120,
			'width'       => 400,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	// WooCommerce : déclaré même si le plugin est inactif, sans effet dans ce cas.
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	remove_theme_support( 'core-block-patterns' );

	register_nav_menus(
		array(
			'primary'   => __( 'Primary menu', 'mrz-theme-exp' ),
			'secondary' => __( 'Secondary menu (header, right)', 'mrz-theme-exp' ),
			'mobile'    => __( 'Mobile menu (falls back to primary)', 'mrz-theme-exp' ),
			'footer'    => __( 'Footer menu', 'mrz-theme-exp' ),
		)
	);
}
add_action( 'after_setup_theme', 'mrz_theme_setup' );

/**
 * Largeur de contenu pour les embeds.
 */
function mrz_theme_content_width() {
	$GLOBALS['content_width'] = 768;
}
add_action( 'after_setup_theme', 'mrz_theme_content_width', 0 );

/**
 * Trois colonnes de widgets dans le footer.
 */
function mrz_theme_widgets_init() {
	for ( $column = 1; $column <= 3; $column++ ) {
		register_sidebar(
			array(
				/* translators: %d: footer column number. */
				'name'          => sprintf( __( 'Footer column %d', 'mrz-theme-exp' ), $column ),
				'id'            => 'footer-' . $column,
				'before_widget' => '<div id="%1$s" class="mrz-widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h2 class="mrz-widget__title">',
				'after_title'   => '</h2>',
			)
		);
	}
}
add_action( 'widgets_init', 'mrz_theme_widgets_init' );

/*
 * Éditeur classique partout : le contenu des pages passe par le constructeur ACF,
 * le texte par TinyMCE. Filtres natifs, aucun plugin nécessaire.
 */
add_filter( 'use_block_editor_for_post_type', '__return_false', 100 );
add_filter( 'use_widgets_block_editor', '__return_false' );

/**
 * Retire du front les CSS de Gutenberg, inutiles sans blocs.
 */
function mrz_theme_dequeue_block_styles() {
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'classic-theme-styles' );
	wp_dequeue_style( 'global-styles' );
	wp_dequeue_style( 'wc-blocks-style' );
}
add_action( 'wp_enqueue_scripts', 'mrz_theme_dequeue_block_styles', 100 );
remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );

/**
 * Classes du body selon les options.
 *
 * @param array $classes Classes existantes.
 * @return array
 */
function mrz_theme_body_classes( $classes ) {
	$classes[] = 'mrz-header-layout-' . sanitize_html_class( mrz_theme_option( 'header_layout' ) );

	if ( mrz_theme_option( 'header_sticky' ) ) {
		$classes[] = 'mrz-header-sticky';
	}
	if ( ! mrz_theme_option( 'mobile_sticky' ) ) {
		$classes[] = 'mrz-mobile-static';
	}
	if ( mrz_theme_is_transparent_header() ) {
		$classes[] = 'mrz-header-transparent';
	}

	return $classes;
}
add_filter( 'body_class', 'mrz_theme_body_classes' );

/**
 * Header transparent : seulement si l'option est active et que la page
 * commence par un hero (comme le « when applicable » de Salient).
 *
 * @return bool
 */
function mrz_theme_is_transparent_header() {
	if ( ! mrz_theme_option( 'header_transparent' ) || ! is_singular() ) {
		return false;
	}

	// ACF stocke la liste des layouts dans la meta du champ flexible.
	$layouts = get_post_meta( get_queried_object_id(), 'mrz_builder', true );

	return is_array( $layouts ) && isset( $layouts[0] ) && 'hero' === $layouts[0];
}

/**
 * Titre d'archive sans préfixe « Catégorie : ».
 */
add_filter( 'get_the_archive_title_prefix', '__return_empty_string' );
