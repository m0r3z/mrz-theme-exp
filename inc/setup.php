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
	// Tailles des images produit, doublées pour les écrans haute densité : vignette de la liste
	// (4 colonnes d'environ 300 px), grande image de la fiche (environ 600 px), miniatures de la
	// galerie (environ 100 px). Déclarées ici, elles disparaissent du Customizer ; le format des
	// vignettes (carré, d'origine, personnalisé) y reste.
	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width'         => 600,
			'single_image_width'            => 1200,
			'gallery_thumbnail_image_width' => 200,
		)
	);
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	remove_theme_support( 'core-block-patterns' );

	register_nav_menus(
		array(
			'primary'   => __( 'Primary menu', 'mrz-theme-exp' ),
			'secondary' => __( 'Secondary menu (header, right)', 'mrz-theme-exp' ),
			'mobile'    => __( 'Mobile menu (falls back to primary)', 'mrz-theme-exp' ),
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
	if ( mrz_theme_option( 'highlight_animated' ) ) {
		$classes[] = 'mrz-highlight-animated';
	}

	return $classes;
}
add_filter( 'body_class', 'mrz_theme_body_classes' );

/**
 * Header transparent : seulement si l'option est active et que la page commence par
 * une section avec une image ou une vidéo de fond (comme le « when applicable » de Salient).
 * Une première section dont le nom contient « hero » compte aussi (compatibilité).
 * Un article en bannière (Customizer > Blog) l'est toujours.
 *
 * @return bool
 */
function mrz_theme_is_transparent_header() {
	if ( ! is_singular() || mrz_theme_page_option( '_mrz_hide_header' ) ) {
		return false;
	}

	// Réglage de la page (meta box MRZ Theme) : il passe avant l'option du Customizer.
	$page_setting = mrz_theme_page_option( '_mrz_transparent_header' );
	if ( 'on' === $page_setting || 'off' === $page_setting ) {
		return 'on' === $page_setting;
	}

	// Article en bannière (Customizer > Blog) : le header passe toujours sur l'image.
	if ( mrz_theme_is_post_banner() ) {
		return true;
	}

	if ( ! mrz_theme_option( 'header_transparent' ) ) {
		return false;
	}

	// ACF stocke la liste des layouts dans la meta du champ flexible, et les réglages de chaque rangée à part.
	$post_id = get_queried_object_id();
	$layouts = get_post_meta( $post_id, 'mrz_builder', true );
	if ( ! is_array( $layouts ) || ! isset( $layouts[0] ) ) {
		return false;
	}

	return str_contains( (string) $layouts[0], 'hero' )
		|| get_post_meta( $post_id, 'mrz_builder_0_settings_background_image', true )
		|| get_post_meta( $post_id, 'mrz_builder_0_settings_background_video', true );
}

/**
 * Titre d'archive sans préfixe « Catégorie : ».
 */
add_filter( 'get_the_archive_title_prefix', '__return_empty_string' );

/**
 * Surlignage animé du mot en couleur (Customizer) : sur tout le site, donc motion.js
 * et la classe mrz-motion-js sont posés dès <head>, avant le premier titre.
 */
function mrz_theme_highlight_motion() {
	if ( mrz_theme_option( 'highlight_animated' ) ) {
		mrz_theme_motion_init();
	}
}
add_action( 'wp_head', 'mrz_theme_highlight_motion', 1 );
