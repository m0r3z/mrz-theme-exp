<?php
/**
 * Options du Customizer et CSS qu'elles génèrent.
 *
 * Les couleurs et polices ne sont pas ici : elles vivent dans les variables CSS
 * (src/css/tokens.css, surchargées dans le style.css du thème enfant).
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

/**
 * Nettoie une case à cocher.
 *
 * @param mixed $value Valeur reçue.
 * @return bool
 */
function mrz_theme_sanitize_checkbox( $value ) {
	return (bool) $value;
}

/**
 * Nettoie un choix de liste en le confrontant aux choix du contrôle.
 *
 * @param string               $value   Valeur reçue.
 * @param WP_Customize_Setting $setting Réglage concerné.
 * @return string
 */
function mrz_theme_sanitize_choice( $value, $setting ) {
	$control = $setting->manager->get_control( $setting->id );
	$choices = $control ? $control->choices : array();

	return array_key_exists( $value, $choices ) ? $value : $setting->default;
}

/**
 * Enregistre un réglage mrz_ avec sa valeur par défaut.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 * @param string               $name         Nom sans préfixe.
 * @param callable             $sanitize     Fonction de nettoyage.
 * @param string               $transport    refresh ou postMessage.
 */
function mrz_theme_add_setting( $wp_customize, $name, $sanitize, $transport = 'refresh' ) {
	$defaults = mrz_theme_option_defaults();

	$wp_customize->add_setting(
		'mrz_' . $name,
		array(
			'default'           => $defaults[ $name ],
			'sanitize_callback' => $sanitize,
			'transport'         => $transport,
		)
	);
}

/**
 * Déclare le panneau MRZ Theme et ses sections.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function mrz_theme_customize_register( $wp_customize ) {

	$wp_customize->add_panel(
		'mrz_theme',
		array(
			'title'    => __( 'MRZ Theme', 'mrz-theme-exp' ),
			'priority' => 30,
		)
	);

	/* Header. */
	$wp_customize->add_section(
		'mrz_header',
		array(
			'title' => __( 'Header', 'mrz-theme-exp' ),
			'panel' => 'mrz_theme',
		)
	);

	/* Logos : en tête de la section Header (le logo natif y est déplacé plus bas). */
	mrz_theme_add_setting( $wp_customize, 'logo_transparent', 'absint' );
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'mrz_logo_transparent',
			array(
				'label'       => __( 'Logo for the transparent header', 'mrz-theme-exp' ),
				'description' => __( 'Shown before scrolling when the header is transparent. Replaced by the main logo on scroll.', 'mrz-theme-exp' ),
				'section'     => 'mrz_header',
				'mime_type'   => 'image',
				'priority'    => 2,
			)
		)
	);

	$logo_sizes = array(
		'logo_height'        => __( 'Logo height on desktop (px)', 'mrz-theme-exp' ),
		'logo_height_mobile' => __( 'Logo height on mobile (px)', 'mrz-theme-exp' ),
	);
	foreach ( $logo_sizes as $name => $label ) {
		mrz_theme_add_setting( $wp_customize, $name, 'absint' );
		$wp_customize->add_control(
			'mrz_' . $name,
			array(
				'label'       => $label,
				'section'     => 'mrz_header',
				'type'        => 'number',
				'priority'    => 3,
				'input_attrs' => array(
					'min'  => 16,
					'max'  => 300,
					'step' => 1,
				),
			)
		);
	}

	mrz_theme_add_setting( $wp_customize, 'header_layout', 'mrz_theme_sanitize_choice' );
	$wp_customize->add_control(
		'mrz_header_layout',
		array(
			'label'   => __( 'Header layout', 'mrz-theme-exp' ),
			'section' => 'mrz_header',
			'type'    => 'radio',
			'choices' => array(
				'classic'  => __( 'Classic: logo left, menu right', 'mrz-theme-exp' ),
				'centered' => __( 'Centered: logo above, menu below', 'mrz-theme-exp' ),
			),
		)
	);

	mrz_theme_add_setting( $wp_customize, 'header_sticky', 'mrz_theme_sanitize_checkbox' );
	$wp_customize->add_control(
		'mrz_header_sticky',
		array(
			'label'   => __( 'Sticky header', 'mrz-theme-exp' ),
			'section' => 'mrz_header',
			'type'    => 'checkbox',
		)
	);

	mrz_theme_add_setting( $wp_customize, 'header_transparent', 'mrz_theme_sanitize_checkbox' );
	$wp_customize->add_control(
		'mrz_header_transparent',
		array(
			'label'       => __( 'Transparent header when applicable', 'mrz-theme-exp' ),
			'description' => __( 'The header is transparent over the page until the user scrolls, on pages that start with a hero section (a section whose name contains "hero").', 'mrz-theme-exp' ),
			'section'     => 'mrz_header',
			'type'        => 'checkbox',
		)
	);

	mrz_theme_add_setting( $wp_customize, 'header_search', 'mrz_theme_sanitize_checkbox' );
	$wp_customize->add_control(
		'mrz_header_search',
		array(
			'label'       => __( 'Search', 'mrz-theme-exp' ),
			'description' => __( 'Search icon in the header, opening the search form.', 'mrz-theme-exp' ),
			'section'     => 'mrz_header',
			'type'        => 'checkbox',
		)
	);

	mrz_theme_add_setting( $wp_customize, 'header_account', 'mrz_theme_sanitize_checkbox' );
	$wp_customize->add_control(
		'mrz_header_account',
		array(
			'label'       => __( 'Account link', 'mrz-theme-exp' ),
			'description' => __( 'Icon linking to the WooCommerce "My account" page, next to the cart. Requires WooCommerce.', 'mrz-theme-exp' ),
			'section'     => 'mrz_header',
			'type'        => 'checkbox',
		)
	);

	/* Menu mobile. */
	$wp_customize->add_section(
		'mrz_mobile',
		array(
			'title' => __( 'Mobile header', 'mrz-theme-exp' ),
			'panel' => 'mrz_theme',
		)
	);

	mrz_theme_add_setting( $wp_customize, 'mobile_sticky', 'mrz_theme_sanitize_checkbox' );
	$wp_customize->add_control(
		'mrz_mobile_sticky',
		array(
			'label'   => __( 'Sticky header on mobile', 'mrz-theme-exp' ),
			'section' => 'mrz_mobile',
			'type'    => 'checkbox',
		)
	);

	mrz_theme_add_setting( $wp_customize, 'mobile_breakpoint', 'absint' );
	$wp_customize->add_control(
		'mrz_mobile_breakpoint',
		array(
			'label'       => __( 'Mobile breakpoint (px)', 'mrz-theme-exp' ),
			'description' => __( 'Below this width, the menu collapses into the mobile menu. Raise it when the menu has many items.', 'mrz-theme-exp' ),
			'section'     => 'mrz_mobile',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 600,
				'max'  => 1600,
				'step' => 10,
			),
		)
	);

	/* Transitions de page. */
	$wp_customize->add_section(
		'mrz_transitions',
		array(
			'title'       => __( 'Page transitions', 'mrz-theme-exp' ),
			'panel'       => 'mrz_theme',
			'description' => __( 'Uses the browser View Transitions API: no JavaScript. Browsers without support simply load the page normally.', 'mrz-theme-exp' ),
		)
	);

	mrz_theme_add_setting( $wp_customize, 'transition_enabled', 'mrz_theme_sanitize_checkbox' );
	$wp_customize->add_control(
		'mrz_transition_enabled',
		array(
			'label'   => __( 'Animated page transitions', 'mrz-theme-exp' ),
			'section' => 'mrz_transitions',
			'type'    => 'checkbox',
		)
	);

	mrz_theme_add_setting( $wp_customize, 'transition_effect', 'mrz_theme_sanitize_choice' );
	$wp_customize->add_control(
		'mrz_transition_effect',
		array(
			'label'   => __( 'Transition effect', 'mrz-theme-exp' ),
			'section' => 'mrz_transitions',
			'type'    => 'select',
			'choices' => array(
				'fade'  => __( 'Fade', 'mrz-theme-exp' ),
				'slide' => __( 'Slide up', 'mrz-theme-exp' ),
			),
		)
	);

	mrz_theme_add_setting( $wp_customize, 'transition_duration', 'absint' );
	$wp_customize->add_control(
		'mrz_transition_duration',
		array(
			'label'       => __( 'Duration (ms)', 'mrz-theme-exp' ),
			'section'     => 'mrz_transitions',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 100,
				'max'  => 1500,
				'step' => 50,
			),
		)
	);

	/* Général. */
	$wp_customize->add_section(
		'mrz_general',
		array(
			'title' => __( 'General', 'mrz-theme-exp' ),
			'panel' => 'mrz_theme',
		)
	);

	mrz_theme_add_setting( $wp_customize, 'container_width', 'absint' );
	$wp_customize->add_control(
		'mrz_container_width',
		array(
			'label'       => __( 'Content width (px)', 'mrz-theme-exp' ),
			'section'     => 'mrz_general',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 960,
				'max'  => 1920,
				'step' => 10,
			),
		)
	);

	mrz_theme_add_setting( $wp_customize, 'page_404', 'absint' );
	$wp_customize->add_control(
		'mrz_page_404',
		array(
			'label'       => __( '404 page', 'mrz-theme-exp' ),
			'description' => __( 'Page displayed when a URL does not exist. The real 404 status is kept for search engines.', 'mrz-theme-exp' ),
			'section'     => 'mrz_general',
			'type'        => 'dropdown-pages',
		)
	);
}
add_action( 'customize_register', 'mrz_theme_customize_register' );

/**
 * Déplace le logo natif dans la section Header, en premier : tous les réglages
 * du logo au même endroit. Il reste le logo natif (custom_logo) que lisent les plugins.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function mrz_theme_customize_move_logo( $wp_customize ) {
	$logo_control = $wp_customize->get_control( 'custom_logo' );

	if ( $logo_control ) {
		$logo_control->section  = 'mrz_header';
		$logo_control->priority = 1;
	}
}
add_action( 'customize_register', 'mrz_theme_customize_move_logo', 20 );

/**
 * CSS généré par les options : variables, point de rupture mobile, transitions.
 *
 * Le point de rupture est un réglage, donc ses media queries ne peuvent pas être
 * compilées par Tailwind : elles sont écrites ici.
 *
 * @return string
 */
function mrz_theme_customizer_css() {
	$breakpoint = max( 600, absint( mrz_theme_option( 'mobile_breakpoint' ) ) );

	$css  = ':root{';
	$css .= '--mrz-logo-height:' . absint( mrz_theme_option( 'logo_height' ) ) . 'px;';
	$css .= '--mrz-logo-height-mobile:' . absint( mrz_theme_option( 'logo_height_mobile' ) ) . 'px;';
	$css .= '--mrz-container:' . absint( mrz_theme_option( 'container_width' ) ) . 'px;';
	$css .= '}';

	/*
	 * Header sticky ou transparent : un lien vers une ancre s'arrête sous le header
	 * (hauteur mesurée par main.js) et sous la barre d'admin. Écrit ici, hors couche,
	 * pour passer devant la règle de la barre d'admin de WordPress.
	 */
	$css .= 'html:has(> body.mrz-header-sticky),html:has(> body.mrz-header-transparent){scroll-padding-top:calc(var(--mrz-header-height,0px) + var(--wp-admin--admin-bar--height,0px) + 1rem)}';

	$css .= '@media (min-width:' . ( $breakpoint + 1 ) . 'px){.mrz-mobile-only{display:none}}';
	$css .= '@media (max-width:' . $breakpoint . 'px){';
	$css .= '.mrz-desktop-only{display:none}';
	$css .= '.mrz-header .mrz-logo img{height:var(--mrz-logo-height-mobile)}';
	$css .= '.mrz-mobile-static:not(.mrz-header-transparent) .mrz-header{position:relative}';
	$css .= 'html:has(> body.mrz-mobile-static:not(.mrz-header-transparent)){scroll-padding-top:var(--wp-admin--admin-bar--height,0px)}';
	$css .= '}';

	if ( mrz_theme_option( 'transition_enabled' ) ) {
		$duration = absint( mrz_theme_option( 'transition_duration' ) );
		$css     .= '@view-transition{navigation:auto}';
		$css     .= '::view-transition-old(root),::view-transition-new(root){animation-duration:' . $duration . 'ms}';

		if ( 'slide' === mrz_theme_option( 'transition_effect' ) ) {
			$css .= '::view-transition-old(root){animation-name:mrz-vt-out}';
			$css .= '::view-transition-new(root){animation-name:mrz-vt-in}';
			$css .= '@keyframes mrz-vt-out{to{opacity:0;transform:translateY(-24px)}}';
			$css .= '@keyframes mrz-vt-in{from{opacity:0;transform:translateY(24px)}}';
		}

		$css .= '@media (prefers-reduced-motion:reduce){@view-transition{navigation:none}}';
	}

	return $css;
}
