<?php
/**
 * Options du Customizer et CSS qu'elles génèrent.
 *
 * Les couleurs de la charte sont ici (section Couleurs) : elles s'écrivent en
 * variables CSS. Les polices et le reste du design vivent dans les variables
 * de src/css/tokens.css, surchargées dans le style.css du thème enfant.
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
 * Couleurs de la charte : nom (variable --color-<nom>) => libellé.
 *
 * @return array
 */
function mrz_theme_color_labels() {
	return array(
		'primary'   => __( 'Primary color', 'mrz-theme-exp' ),
		'secondary' => __( 'Secondary color', 'mrz-theme-exp' ),
		'tertiary'   => __( 'Tertiary color', 'mrz-theme-exp' ),
		'quaternary' => __( 'Quaternary color', 'mrz-theme-exp' ),
		'dark'      => __( 'Dark: text and dark backgrounds', 'mrz-theme-exp' ),
		'light'     => __( 'Light: light backgrounds', 'mrz-theme-exp' ),
		'border'    => __( 'Borders and lines', 'mrz-theme-exp' ),
	);
}

/**
 * Couleurs de la marque : celles qui reçoivent une variable de contraste et un fond de section.
 *
 * @return array
 */
function mrz_theme_brand_colors() {
	return array( 'primary', 'secondary', 'tertiary', 'quaternary' );
}

/**
 * Filtres SVG des icônes : un par couleur de la palette, plus le blanc.
 *
 * Une icône noire (SVG ou PNG en <img>) prend exactement la couleur du filtre :
 * filter: url(#mrz-icon-primary). Les filtres suivent le Customizer, sans
 * générateur de filtres CSS à relancer quand une couleur change.
 */
function mrz_theme_print_icon_filters() {
	$colors = array();
	foreach ( array_keys( mrz_theme_color_labels() ) as $name ) {
		$colors[ $name ] = sanitize_hex_color( mrz_theme_option( 'color_' . $name ) );
	}
	$colors['white'] = '#ffffff';

	echo '<svg class="mrz-icon-filters" width="0" height="0" aria-hidden="true" focusable="false" style="position:absolute;width:0;height:0;overflow:hidden">';
	foreach ( array_filter( $colors ) as $name => $color ) {
		printf(
			'<filter id="mrz-icon-%1$s" color-interpolation-filters="sRGB"><feFlood flood-color="%2$s"/><feComposite in2="SourceAlpha" operator="in"/></filter>',
			esc_attr( $name ),
			esc_attr( $color )
		);
	}
	echo '</svg>';
}
add_action( 'wp_body_open', 'mrz_theme_print_icon_filters' );

/**
 * Styles du menu mobile : valeur => libellé.
 *
 * @return array
 */
function mrz_theme_mobile_menu_styles() {
	return array(
		'right'      => __( 'Side panel from the right', 'mrz-theme-exp' ),
		'left'       => __( 'Side panel from the left', 'mrz-theme-exp' ),
		'fullscreen' => __( 'Full screen', 'mrz-theme-exp' ),
		'dropdown'   => __( 'Drops down below the header', 'mrz-theme-exp' ),
	);
}

/**
 * Couleur du texte posé sur une couleur : sombre sur une couleur claire, blanc sinon.
 *
 * @param string $hex Couleur #rrggbb.
 * @return string Valeur CSS.
 */
function mrz_theme_contrast_color( $hex ) {
	$hex = ltrim( (string) $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	if ( 6 !== strlen( $hex ) ) {
		return '#ffffff';
	}

	// Luminance relative (WCAG).
	$channels = array();
	foreach ( str_split( $hex, 2 ) as $channel ) {
		$value      = hexdec( $channel ) / 255;
		$channels[] = $value <= 0.03928 ? $value / 12.92 : pow( ( $value + 0.055 ) / 1.055, 2.4 );
	}
	$luminance = 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];

	// Seuil où le contraste avec le blanc et avec le noir s'équilibre.
	return $luminance > 0.179 ? 'var(--color-dark)' : '#ffffff';
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

	/* Couleurs de la charte : la seule source des couleurs du site. */
	$wp_customize->add_section(
		'mrz_colors',
		array(
			'title'       => __( 'Colors', 'mrz-theme-exp' ),
			'panel'       => 'mrz_theme',
			'priority'    => 5,
			'description' => __( 'The site palette. Every color of the theme, the forms and WooCommerce comes from these seven colors.', 'mrz-theme-exp' ),
		)
	);

	foreach ( mrz_theme_color_labels() as $name => $label ) {
		mrz_theme_add_setting( $wp_customize, 'color_' . $name, 'sanitize_hex_color' );
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'mrz_color_' . $name,
				array(
					'label'   => $label,
					'section' => 'mrz_colors',
				)
			)
		);
	}

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

	mrz_theme_add_setting( $wp_customize, 'mobile_menu_style', 'mrz_theme_sanitize_choice' );
	$wp_customize->add_control(
		'mrz_mobile_menu_style',
		array(
			'label'       => __( 'Mobile menu', 'mrz-theme-exp' ),
			'description' => __( 'How the menu opens. Its look (background, blur, hover) is styled in the child theme.', 'mrz-theme-exp' ),
			'section'     => 'mrz_mobile',
			'type'        => 'radio',
			'choices'     => mrz_theme_mobile_menu_styles(),
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
	foreach ( array_keys( mrz_theme_color_labels() ) as $name ) {
		$color = sanitize_hex_color( mrz_theme_option( 'color_' . $name ) );
		if ( $color ) {
			$css .= '--color-' . $name . ':' . $color . ';';
			if ( in_array( $name, mrz_theme_brand_colors(), true ) ) {
				$css .= '--color-' . $name . '-contrast:' . mrz_theme_contrast_color( $color ) . ';';
			}
		}
	}
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
