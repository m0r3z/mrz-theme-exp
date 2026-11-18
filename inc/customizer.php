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
		// Ordre d'affichage dans le Customizer : le site, puis la marque, puis les teintes de base.
		'background' => __( 'Site background', 'mrz-theme-exp' ),
		'body'       => __( 'Body text', 'mrz-theme-exp' ),
		'primary'    => __( 'Primary color', 'mrz-theme-exp' ),
		'secondary'  => __( 'Secondary color', 'mrz-theme-exp' ),
		'tertiary'   => __( 'Tertiary color', 'mrz-theme-exp' ),
		'quaternary' => __( 'Quaternary color', 'mrz-theme-exp' ),
		'dark'       => __( 'Dark', 'mrz-theme-exp' ),
		'light'      => __( 'Light', 'mrz-theme-exp' ),
		'border'     => __( 'Borders and lines', 'mrz-theme-exp' ),
	);
}

/**
 * Aide de chaque couleur dans le Customizer.
 *
 * @return array
 */
function mrz_theme_color_descriptions() {
	return array(
		'dark'       => __( 'Text on light backgrounds, "Dark" sections.', 'mrz-theme-exp' ),
		'light'      => __( 'Text on dark backgrounds, "Light" sections.', 'mrz-theme-exp' ),
		'background' => __( 'Empty: white. The text takes Dark or Light, whichever reads best on it.', 'mrz-theme-exp' ),
		'body'       => __( 'Paragraphs on the site background, when they differ from the titles. Empty: same as the titles. Colored sections are not affected.', 'mrz-theme-exp' ),
	);
}

/**
 * Couleurs de fond possibles : celles qui reçoivent une variable de contraste (--color-<nom>-contrast).
 *
 * @return array
 */
function mrz_theme_brand_colors() {
	return array( 'primary', 'secondary', 'tertiary', 'quaternary', 'dark', 'light' );
}

/**
 * Couleurs de marque dont le texte posé dessus peut être imposé (Clair ou Sombre).
 *
 * @return array
 */
function mrz_theme_contrast_override_colors() {
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
 * Luminance relative d'une couleur (WCAG).
 *
 * @param string $hex Couleur #rgb ou #rrggbb.
 * @return float|null Entre 0 (noir) et 1 (blanc), null si la couleur est invalide.
 */
function mrz_theme_luminance( $hex ) {
	$hex = ltrim( (string) $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	if ( 6 !== strlen( $hex ) || ! ctype_xdigit( $hex ) ) {
		return null;
	}

	$channels = array();
	foreach ( str_split( $hex, 2 ) as $channel ) {
		$value      = hexdec( $channel ) / 255;
		$channels[] = $value <= 0.03928 ? $value / 12.92 : pow( ( $value + 0.055 ) / 1.055, 2.4 );
	}

	return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
}

/**
 * Couleur du texte posé sur une couleur : « Sombre » ou « Clair » de la palette,
 * celle qui contraste le plus (jamais un blanc en dur : une charte crème reste crème).
 *
 * @param string $hex Couleur de fond.
 * @return string Valeur CSS.
 */
function mrz_theme_contrast_color( $hex ) {
	$background = mrz_theme_luminance( $hex );
	$dark       = mrz_theme_luminance( mrz_theme_option( 'color_dark' ) );
	$light      = mrz_theme_luminance( mrz_theme_option( 'color_light' ) );
	if ( null === $background || null === $dark || null === $light ) {
		return 'var(--color-dark)';
	}

	return mrz_theme_contrast_ratio( $background, $dark ) >= mrz_theme_contrast_ratio( $background, $light ) ? 'var(--color-dark)' : 'var(--color-light)';
}

/**
 * Rapport de contraste WCAG entre deux luminances (1 à 21).
 *
 * @param float $first  Luminance.
 * @param float $second Luminance.
 * @return float
 */
function mrz_theme_contrast_ratio( $first, $second ) {
	return ( max( $first, $second ) + 0.05 ) / ( min( $first, $second ) + 0.05 );
}

/**
 * Liens du texte et contour de focus : couleur principale, sauf si elle manque de contraste
 * sur le fond du site (charte claire, or sur crème). Ils prennent alors la couleur du texte.
 * Seuils RGAA : 4,5:1 pour un lien dans le texte, 3:1 pour le contour de focus.
 *
 * @return string Déclarations CSS pour :root ('' si la principale suffit).
 */
function mrz_theme_link_focus_css() {
	$primary    = mrz_theme_luminance( mrz_theme_option( 'color_primary' ) );
	$background = mrz_theme_luminance( mrz_theme_option( 'color_background' ) ? mrz_theme_option( 'color_background' ) : '#ffffff' );
	if ( null === $primary || null === $background ) {
		return '';
	}

	$ratio = mrz_theme_contrast_ratio( $primary, $background );
	$css   = '';
	if ( $ratio < 4.5 ) {
		$css .= '--mrz-link-color:var(--color-text);';
	}
	// Contour de focus et mot en couleur d'un titre (grand texte) : 3:1.
	if ( $ratio < 3 ) {
		$css .= '--mrz-focus-color:var(--color-text);--mrz-title-highlight-color:var(--color-text);';
	}

	return $css;
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
			'description' => __( 'The site palette. Every color of the theme, the forms and WooCommerce comes from it.', 'mrz-theme-exp' ),
		)
	);

	$descriptions = mrz_theme_color_descriptions();
	foreach ( mrz_theme_color_labels() as $name => $label ) {
		mrz_theme_add_setting( $wp_customize, 'color_' . $name, 'sanitize_hex_color' );
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'mrz_color_' . $name,
				array(
					'label'       => $label,
					'description' => $descriptions[ $name ] ?? '',
					'section'     => 'mrz_colors',
				)
			)
		);

		// Couleur de marque : texte posé dessus, calculé ou imposé (quand la charte contredit le calcul).
		if ( in_array( $name, mrz_theme_contrast_override_colors(), true ) ) {
			mrz_theme_add_setting( $wp_customize, 'color_' . $name . '_contrast', 'mrz_theme_sanitize_choice' );
			$wp_customize->add_control(
				'mrz_color_' . $name . '_contrast',
				array(
					/* translators: %s: color name, e.g. "Primary color". */
					'label'   => sprintf( __( 'Text on %s', 'mrz-theme-exp' ), $label ),
					'section' => 'mrz_colors',
					'type'    => 'select',
					'choices' => array(
						'auto'  => __( 'Automatic (best contrast)', 'mrz-theme-exp' ),
						'light' => __( 'Light', 'mrz-theme-exp' ),
						'dark'  => __( 'Dark', 'mrz-theme-exp' ),
					),
				)
			);
		}
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
				'left'     => __( 'Menu left: logo, menu next to it, tools on the right', 'mrz-theme-exp' ),
				'centered' => __( 'Centered: logo above, menu below', 'mrz-theme-exp' ),
				'split'    => __( 'Centered logo: main menu on the left, secondary menu and tools on the right', 'mrz-theme-exp' ),
			),
		)
	);

	mrz_theme_add_setting( $wp_customize, 'menu_button_desktop', 'mrz_theme_sanitize_choice' );
	$wp_customize->add_control(
		'mrz_menu_button_desktop',
		array(
			'label'       => __( 'Menu button on desktop', 'mrz-theme-exp' ),
			'description' => __( 'The Menu button opens the mobile menu panel (style: Mobile header). It shows the Mobile menu if one is assigned, otherwise the main menu.', 'mrz-theme-exp' ),
			'section'     => 'mrz_header',
			'type'        => 'radio',
			'choices'     => array(
				'none'    => __( 'No: mobile only', 'mrz-theme-exp' ),
				'replace' => __( 'Instead of the main menu', 'mrz-theme-exp' ),
				'add'     => __( 'In addition to the main menu', 'mrz-theme-exp' ),
			),
		)
	);

	mrz_theme_add_setting( $wp_customize, 'menu_button_label', 'mrz_theme_sanitize_checkbox' );
	$wp_customize->add_control(
		'mrz_menu_button_label',
		array(
			'label'   => __( 'Show the word "Menu" next to the Menu button icon', 'mrz-theme-exp' ),
			'section' => 'mrz_header',
			'type'    => 'checkbox',
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
			'description' => __( 'The header is transparent over the page until the user scrolls, on pages whose first section has a background image or video. A page can force it or turn it off (MRZ Theme box of the page).', 'mrz-theme-exp' ),
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

	mrz_theme_add_setting( $wp_customize, 'lang_switcher', 'mrz_theme_sanitize_choice' );
	$wp_customize->add_control(
		'mrz_lang_switcher',
		array(
			'label'       => __( 'Language switcher', 'mrz-theme-exp' ),
			'description' => __( 'Shown when Polylang is active, in the header and the mobile menu.', 'mrz-theme-exp' ),
			'section'     => 'mrz_header',
			'type'        => 'radio',
			'choices'     => array(
				'codes' => __( 'Codes (FR, EN)', 'mrz-theme-exp' ),
				'flags' => __( 'Flags', 'mrz-theme-exp' ),
				'both'  => __( 'Flags and codes', 'mrz-theme-exp' ),
			),
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
				'fade'    => __( 'Fade', 'mrz-theme-exp' ),
				'slide'   => __( 'Slide up', 'mrz-theme-exp' ),
				'curtain' => __( 'Curtain: a color sweeps the screen', 'mrz-theme-exp' ),
				'zoom'    => __( 'Zoom: the page steps back, the next one comes forward', 'mrz-theme-exp' ),
			),
		)
	);

	mrz_theme_add_setting( $wp_customize, 'transition_color', 'mrz_theme_sanitize_choice' );
	$wp_customize->add_control(
		'mrz_transition_color',
		array(
			'label'       => __( 'Color between the two pages', 'mrz-theme-exp' ),
			'description' => __( 'The page fades out to this color, then the next page comes in: the two pages never overlap.', 'mrz-theme-exp' ),
			'section'     => 'mrz_transitions',
			'type'        => 'select',
			'choices'     => array_intersect_key( mrz_theme_color_labels(), array_flip( array( 'background', 'primary', 'secondary', 'tertiary', 'quaternary', 'dark', 'light' ) ) ),
		)
	);

	mrz_theme_add_setting( $wp_customize, 'transition_direction', 'mrz_theme_sanitize_choice' );
	$wp_customize->add_control(
		'mrz_transition_direction',
		array(
			'label'           => __( 'Curtain direction', 'mrz-theme-exp' ),
			// Affiché seulement pour l'effet rideau (réévalué à chaque rafraîchissement de l'aperçu).
			'active_callback' => static function () {
				return 'curtain' === mrz_theme_option( 'transition_effect' );
			},
			'section' => 'mrz_transitions',
			'type'    => 'select',
			'choices' => array(
				'up'    => __( 'Upward', 'mrz-theme-exp' ),
				'down'  => __( 'Downward', 'mrz-theme-exp' ),
				'left'  => __( 'To the left', 'mrz-theme-exp' ),
				'right' => __( 'To the right', 'mrz-theme-exp' ),
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

	mrz_theme_add_setting( $wp_customize, 'highlight_animated', 'mrz_theme_sanitize_checkbox' );
	$wp_customize->add_control(
		'mrz_highlight_animated',
		array(
			'label'       => __( 'Animated highlight of the colored word', 'mrz-theme-exp' ),
			'description' => __( 'The word between <em> and </em> in a title gets highlighted when the title comes into view, like the scroll animations (once or on each pass).', 'mrz-theme-exp' ),
			'section'     => 'mrz_general',
			'type'        => 'checkbox',
		)
	);

	mrz_theme_add_setting( $wp_customize, 'motion_once', 'mrz_theme_sanitize_checkbox' );
	$wp_customize->add_control(
		'mrz_motion_once',
		array(
			'label'       => __( 'Play scroll animations only once', 'mrz-theme-exp' ),
			'description' => __( 'Once an element has appeared, it stays, even when scrolling back up and down again. Unchecked: the animation plays again on each pass.', 'mrz-theme-exp' ),
			'section'     => 'mrz_general',
			'type'        => 'checkbox',
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

	$css        = ':root{';
	$title_text = '';
	foreach ( array_keys( mrz_theme_color_labels() ) as $name ) {
		$color = sanitize_hex_color( mrz_theme_option( 'color_' . $name ) );
		if ( ! $color ) {
			continue;
		}

		if ( 'body' === $name ) {
			continue; // Écrit après la boucle : il dépend du fond du site.
		}

		$css .= '--color-' . $name . ':' . $color . ';';
		if ( in_array( $name, mrz_theme_brand_colors(), true ) ) {
			$forced = in_array( $name, mrz_theme_contrast_override_colors(), true ) ? mrz_theme_option( 'color_' . $name . '_contrast' ) : 'auto';
			$text   = in_array( $forced, array( 'light', 'dark' ), true ) ? 'var(--color-' . $forced . ')' : mrz_theme_contrast_color( $color );
			$css   .= '--color-' . $name . '-contrast:' . $text . ';';
		}
		// Fond du site réglé : le texte prend Sombre ou Clair, selon ce fond.
		if ( 'background' === $name ) {
			$title_text = mrz_theme_contrast_color( $color );
			$css       .= '--color-text:' . $title_text . ';';
		}
	}

	// Texte courant distinct des titres : les titres gardent la couleur calculée.
	$body = sanitize_hex_color( mrz_theme_option( 'color_body' ) );
	if ( $body ) {
		$css .= '--mrz-title-color:' . ( $title_text ? $title_text : 'var(--color-dark)' ) . ';';
		$css .= '--color-text:' . $body . ';';
	}
	$css .= mrz_theme_link_focus_css();
	$css .= '--mrz-logo-height:' . absint( mrz_theme_option( 'logo_height' ) ) . 'px;';
	$css .= '--mrz-logo-height-mobile:' . absint( mrz_theme_option( 'logo_height_mobile' ) ) . 'px;';
	$css .= '--mrz-container:' . absint( mrz_theme_option( 'container_width' ) ) . 'px;';
	$css .= '}';

	/*
	 * Header sticky ou transparent : un lien vers une ancre s'arrête juste sous le header
	 * (hauteur mesurée par main.js) et sous la barre d'admin. Écrit ici, hors couche,
	 * pour passer devant la règle de la barre d'admin de WordPress.
	 */
	$css .= 'html:has(> body.mrz-header-sticky),html:has(> body.mrz-header-transparent){scroll-padding-top:calc(var(--mrz-header-height,0px) + var(--mrz-anchors-height,0px) + var(--wp-admin--admin-bar--height,0px))}';

	$css .= '@media (min-width:' . ( $breakpoint + 1 ) . 'px){.mrz-mobile-only{display:none}}';
	$css .= '@media (max-width:' . $breakpoint . 'px){';
	$css .= '.mrz-desktop-only{display:none}';
	$css .= '.mrz-header .mrz-logo img{height:var(--mrz-logo-height-mobile)}';
	// Logo centré : sur mobile, le menu est dans le burger, le logo repasse à gauche (pas la place de le centrer).
	$css .= '.mrz-header--split .mrz-header__bar{grid-template-columns:auto 1fr}.mrz-header--split .mrz-header__start{display:none}';
	$css .= '.mrz-mobile-static:not(.mrz-header-transparent) .mrz-header{position:relative}';
	$css .= 'html:has(> body.mrz-mobile-static:not(.mrz-header-transparent)){scroll-padding-top:var(--wp-admin--admin-bar--height,0px)}';
	$css .= '}';

	if ( mrz_theme_option( 'transition_enabled' ) ) {
		$duration = absint( mrz_theme_option( 'transition_duration' ) );
		/*
		 * En deux temps, sur une couleur : l'ancienne page disparaît entièrement, puis la
		 * nouvelle arrive. Les deux pages ne se superposent jamais. Chaque moitié prend la
		 * moitié de la durée. Le header et la barre d'admin restent immobiles.
		 */
		$half   = max( 50, (int) round( $duration / 2 ) );
		$color  = sanitize_key( mrz_theme_option( 'transition_color' ) );
		$color  = $color ? $color : 'background';
		$effect = mrz_theme_option( 'transition_effect' );
		$frames = array(
			'fade'  => array( 'opacity:1', 'opacity:0', 'opacity:0', 'opacity:1' ),
			'slide' => array( 'opacity:1;transform:none', 'opacity:0;transform:translateY(-24px)', 'opacity:0;transform:translateY(24px)', 'opacity:1;transform:none' ),
			'zoom'  => array( 'opacity:1;transform:none', 'opacity:0;transform:scale(.96)', 'opacity:0;transform:scale(1.04)', 'opacity:1;transform:none' ),
		);
		if ( 'curtain' === $effect ) {
			// Rideau : la page s'efface en balayant, puis la nouvelle arrive par le même côté.
			$clips  = array(
				'up'    => array( 'inset(0 0 100% 0)', 'inset(100% 0 0 0)' ),
				'down'  => array( 'inset(100% 0 0 0)', 'inset(0 0 100% 0)' ),
				'left'  => array( 'inset(0 100% 0 0)', 'inset(0 0 0 100%)' ),
				'right' => array( 'inset(0 0 0 100%)', 'inset(0 100% 0 0)' ),
			);
			$clip   = $clips[ mrz_theme_option( 'transition_direction' ) ] ?? $clips['up'];
			// Les deux extrémités sont écrites : clip-path ne s'anime pas depuis « none ».
			$frames = array( 'clip-path:inset(0 0 0 0)', 'clip-path:' . $clip[0], 'clip-path:' . $clip[1], 'clip-path:inset(0 0 0 0)' );
			$easing = 'cubic-bezier(.7,0,.3,1)';
		} else {
			$frames = $frames[ $effect ] ?? $frames['fade'];
			$easing = 'ease-in-out';
		}

		$css .= '@view-transition{navigation:auto}';
		// Header, bandeau au-dessus du header et barre d'admin restent en place : seul le contenu change.
		$css .= '.mrz-header{view-transition-name:mrz-header}.mrz-topbar[data-mrz-topbar]{view-transition-name:mrz-topbar}#wpadminbar{view-transition-name:mrz-adminbar}';
		$css .= '::view-transition{background:var(--color-' . $color . ')}';
		$css .= '::view-transition-old(root),::view-transition-new(root){mix-blend-mode:normal}';
		$css .= '::view-transition-old(root){animation:mrz-vt-out ' . $half . 'ms ' . $easing . ' both}';
		$css .= '::view-transition-new(root){animation:mrz-vt-in ' . $half . 'ms ' . $easing . ' ' . $half . 'ms both}';
		$css .= '@keyframes mrz-vt-out{from{' . $frames[0] . '}to{' . $frames[1] . '}}';
		$css .= '@keyframes mrz-vt-in{from{' . $frames[2] . '}to{' . $frames[3] . '}}';

		$css .= '@media (prefers-reduced-motion:reduce){@view-transition{navigation:none}}';
	}

	return $css;
}
