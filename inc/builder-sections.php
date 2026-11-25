<?php
/**
 * Sections du parent, injectées dans le constructeur de chaque site.
 *
 * Le constructeur (champ field_mrz_builder) est déclaré par le thème enfant.
 * Le parent y ajoute ses sections au chargement : une section ajoutée ici
 * apparaît sur tous les sites à la mise à jour.
 *
 * Règles, car le contenu des pages est rattaché aux noms et aux clés :
 * - noms (mrz_*) et clés (layout_mrz_*, field_mrz_*) figés, jamais renommés ;
 * - une section n'est jamais supprimée : la masquer du menu, garder son template.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

/**
 * Fonds possibles (sections, cartes) : les classes .mrz-bg--* du thème.
 *
 * @param string $none_label Libellé du choix « aucun ».
 * @return array
 */
function mrz_theme_background_choices( $none_label = '' ) {
	return array(
		'none'       => $none_label ? $none_label : __( 'None', 'mrz-theme-exp' ),
		'light'      => __( 'Light', 'mrz-theme-exp' ),
		'dark'       => __( 'Dark', 'mrz-theme-exp' ),
		'primary'    => __( 'Primary color', 'mrz-theme-exp' ),
		'secondary'  => __( 'Secondary color', 'mrz-theme-exp' ),
		'tertiary'   => __( 'Tertiary color', 'mrz-theme-exp' ),
		'quaternary' => __( 'Quaternary color', 'mrz-theme-exp' ),
	);
}

/**
 * Réglages communs d'une section (fond, espacement, largeur, ancre, image),
 * lus par mrz_theme_section_open().
 *
 * @param string $prefix Préfixe des clés, propre à chaque section.
 * @return array Champ ACF de type groupe.
 */
function mrz_theme_section_settings_field( $prefix ) {
	return array(
		'key'        => $prefix . '_settings',
		'name'       => 'settings',
		'label'      => __( 'Section settings', 'mrz-theme-exp' ),
		'type'       => 'group',
		'layout'     => 'block',
		'sub_fields' => array(
			array(
				'key'           => $prefix . '_settings_background',
				'name'          => 'background',
				'label'         => __( 'Background', 'mrz-theme-exp' ),
				'type'          => 'select',
				'choices'       => mrz_theme_background_choices(),
				'default_value' => 'none',
				'wrapper'       => array( 'width' => '25' ),
			),
			array(
				'key'           => $prefix . '_settings_spacing',
				'name'          => 'spacing',
				'label'         => __( 'Spacing', 'mrz-theme-exp' ),
				'type'          => 'select',
				'choices'       => array(
					'normal'  => __( 'Normal', 'mrz-theme-exp' ),
					'compact' => __( 'Compact', 'mrz-theme-exp' ),
					'large'   => __( 'Large', 'mrz-theme-exp' ),
					'none'    => __( 'None', 'mrz-theme-exp' ),
				),
				'default_value' => 'normal',
				'wrapper'       => array( 'width' => '25' ),
			),
			array(
				'key'           => $prefix . '_settings_width',
				'name'          => 'width',
				'label'         => __( 'Width', 'mrz-theme-exp' ),
				'type'          => 'select',
				'choices'       => array(
					'container' => __( 'Container', 'mrz-theme-exp' ),
					'narrow'    => __( 'Narrow (reading width)', 'mrz-theme-exp' ),
					'wide'      => __( 'Wide', 'mrz-theme-exp' ),
					'full'      => __( 'Full width', 'mrz-theme-exp' ),
				),
				'default_value' => 'container',
				'wrapper'       => array( 'width' => '25' ),
			),
			array(
				'key'     => $prefix . '_settings_anchor',
				'name'    => 'anchor',
				'label'   => __( 'Anchor', 'mrz-theme-exp' ),
				'type'    => 'text',
				'wrapper' => array( 'width' => '25' ),
			),
			array(
				'key'           => $prefix . '_settings_spacing_top',
				'name'          => 'spacing_top',
				'label'         => __( 'Top spacing', 'mrz-theme-exp' ),
				'type'          => 'select',
				'choices'       => mrz_theme_spacing_side_choices(),
				'default_value' => 'inherit',
				'wrapper'       => array( 'width' => '25' ),
			),
			array(
				'key'           => $prefix . '_settings_spacing_bottom',
				'name'          => 'spacing_bottom',
				'label'         => __( 'Bottom spacing', 'mrz-theme-exp' ),
				'type'          => 'select',
				'choices'       => mrz_theme_spacing_side_choices(),
				'default_value' => 'inherit',
				'wrapper'       => array( 'width' => '25' ),
			),
			array(
				'key'           => $prefix . '_settings_height',
				'name'          => 'height',
				'label'         => __( 'Height', 'mrz-theme-exp' ),
				'type'          => 'select',
				'choices'       => array(
					'auto'   => __( 'Automatic (content)', 'mrz-theme-exp' ),
					'large'  => __( 'Large (70% of the screen)', 'mrz-theme-exp' ),
					'screen' => __( 'Full screen', 'mrz-theme-exp' ),
				),
				'default_value' => 'auto',
				'wrapper'       => array( 'width' => '25' ),
			),
			array(
				'key'           => $prefix . '_settings_image_overlay',
				'name'          => 'image_overlay',
				'label'         => __( 'Image overlay', 'mrz-theme-exp' ),
				'type'          => 'select',
				'choices'       => array(
					'dark'  => __( 'Dark overlay, light text', 'mrz-theme-exp' ),
					'light' => __( 'Light overlay, light text', 'mrz-theme-exp' ),
					'none'  => __( 'No overlay, section colors', 'mrz-theme-exp' ),
				),
				'default_value' => 'dark',
				'wrapper'       => array( 'width' => '25' ),
			),
			array(
				'key'           => $prefix . '_settings_background_image',
				'name'          => 'background_image',
				'label'         => __( 'Background image', 'mrz-theme-exp' ),
				'type'          => 'image',
				'return_format' => 'id',
				'preview_size'  => 'medium',
				'instructions'  => __( 'Shown behind the content. On the first section of the page, it loads first (main image of the page).', 'mrz-theme-exp' ),
			),
			array(
				'key'           => $prefix . '_settings_background_video',
				'name'          => 'background_video',
				'label'         => __( 'Background video', 'mrz-theme-exp' ),
				'instructions'  => __( 'MP4 or WebM, muted and looped, with a pause button. Always set a background image too: it is shown first, while the video loads, and stays alone on mobile.', 'mrz-theme-exp' ),
				'type'          => 'file',
				'return_format' => 'id',
				'mime_types'    => 'mp4,webm',
				'wrapper'       => array( 'width' => '67' ),
			),
			array(
				'key'     => $prefix . '_settings_video_mobile',
				'name'    => 'video_mobile',
				'label'   => __( 'Video on mobile', 'mrz-theme-exp' ),
				'type'    => 'true_false',
				'ui'      => 1,
				'message' => __( 'Also play the video on small screens (more data)', 'mrz-theme-exp' ),
				'wrapper' => array( 'width' => '33' ),
			),
			array(
				'key'     => $prefix . '_settings_gradient',
				'name'    => 'gradient',
				'label'   => __( 'Gradient', 'mrz-theme-exp' ),
				'type'    => 'true_false',
				'ui'      => 1,
				'message' => __( 'From the background color to a second color', 'mrz-theme-exp' ),
				// 34 et non 33 : trop large pour remonter à droite de la vidéo, plus haute, la ligne du dégradé repart à gauche.
				'wrapper' => array( 'width' => '34' ),
			),
			array(
				'key'               => $prefix . '_settings_gradient_to',
				'name'              => 'gradient_to',
				'label'             => __( 'Gradient to', 'mrz-theme-exp' ),
				'type'              => 'select',
				'choices'           => mrz_theme_background_choices( __( 'Site background', 'mrz-theme-exp' ) ),
				'default_value'     => 'dark',
				// Pas de logique conditionnelle : le clone ACF des sections de l'enfant ne réécrit pas ses clés.
				'instructions'      => __( 'Used only when Gradient is on.', 'mrz-theme-exp' ),
				'wrapper'           => array( 'width' => '33' ),
			),
			array(
				'key'               => $prefix . '_settings_gradient_direction',
				'name'              => 'gradient_direction',
				'label'             => __( 'Gradient direction', 'mrz-theme-exp' ),
				'type'              => 'select',
				'choices'           => array(
					'bottom'   => __( 'Downward', 'mrz-theme-exp' ),
					'right'    => __( 'To the right', 'mrz-theme-exp' ),
					'diagonal' => __( 'Diagonal', 'mrz-theme-exp' ),
				),
				'default_value'     => 'bottom',
				// Pas de logique conditionnelle : le clone ACF des sections de l'enfant ne réécrit pas ses clés.
				'instructions'      => __( 'Used only when Gradient is on.', 'mrz-theme-exp' ),
				'wrapper'           => array( 'width' => '33' ),
			),
			array(
				'key'           => $prefix . '_settings_motion',
				'name'          => 'motion',
				'label'         => __( 'Animation on scroll', 'mrz-theme-exp' ),
				'instructions'  => __( 'Never on the first section of the page. Off for visitors who limit animations.', 'mrz-theme-exp' ),
				'type'          => 'select',
				'choices'       => array(
					'none'    => __( 'None', 'mrz-theme-exp' ),
					'fade_up' => __( 'Appear upward', 'mrz-theme-exp' ),
					'fade'    => __( 'Fade in', 'mrz-theme-exp' ),
					'zoom'    => __( 'Zoom in', 'mrz-theme-exp' ),
					'cascade' => __( 'Cascade (items one after another)', 'mrz-theme-exp' ),
				),
				'default_value' => 'none',
				'wrapper'       => array( 'width' => '50' ),
			),
			array(
				'key'           => $prefix . '_settings_parallax',
				'name'          => 'parallax',
				'label'         => __( 'Parallax', 'mrz-theme-exp' ),
				'instructions'  => __( 'The background image scrolls slower than the page.', 'mrz-theme-exp' ),
				'type'          => 'select',
				'choices'       => array(
					'none'   => __( 'None', 'mrz-theme-exp' ),
					'light'  => __( 'Subtle', 'mrz-theme-exp' ),
					'medium' => __( 'Medium', 'mrz-theme-exp' ),
				),
				'default_value' => 'none',
				'wrapper'       => array( 'width' => '50' ),
			),
		),
	);
}

/**
 * Aide des champs de titre : mot en couleur et retour à la ligne (mrz_theme_title_html()).
 *
 * @return string
 */
function mrz_theme_title_instructions() {
	// Échappé : ACF affiche l'aide en HTML, les balises citées doivent rester visibles.
	return esc_html__( 'Put a word between <em> and </em> to color it; <br> for a line break.', 'mrz-theme-exp' );
}

/**
 * Niveaux de titre proposés (Texte libre, Texte et média).
 *
 * @return array
 */
function mrz_theme_title_tag_choices() {
	return array(
		'h2' => 'H2',
		'h1' => 'H1',
		'h3' => 'H3',
		'h4' => 'H4',
		'h5' => 'H5',
		'h6' => 'H6',
	);
}

/**
 * Espacement d'un seul côté d'une section (haut ou bas).
 *
 * @return array
 */
function mrz_theme_spacing_side_choices() {
	return array(
		'inherit' => __( 'Same as spacing', 'mrz-theme-exp' ),
		'none'    => __( 'None', 'mrz-theme-exp' ),
		'compact' => __( 'Compact', 'mrz-theme-exp' ),
		'normal'  => __( 'Normal', 'mrz-theme-exp' ),
		'large'   => __( 'Large', 'mrz-theme-exp' ),
	);
}

/**
 * Groupe « Réglages de section », inactif, à cloner dans les sections de l'enfant
 * (champ clone sur group_mrz_section_settings) pour leur donner les réglages communs.
 */
function mrz_theme_register_section_settings_group() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'      => 'group_mrz_section_settings',
			'title'    => __( 'Section settings (to clone)', 'mrz-theme-exp' ),
			'fields'   => array( mrz_theme_section_settings_field( 'field_mrz_section' ) ),
			'location' => array(),
			'active'   => false,
		)
	);
}
add_action( 'acf/init', 'mrz_theme_register_section_settings_group' );

/**
 * Sections fournies par le parent, au format des layouts ACF.
 *
 * @return array
 */
function mrz_theme_parent_layouts() {
	$layouts = array(
		'layout_mrz_text'           => array(
			'key'        => 'layout_mrz_text',
			'name'       => 'mrz_text',
			'label'      => __( 'Free text', 'mrz-theme-exp' ),
			'display'    => 'block',
			'sub_fields' => array(
				array(
					'key'          => 'field_mrz_text_eyebrow',
					'name'         => 'eyebrow',
					'label'        => __( 'Eyebrow', 'mrz-theme-exp' ),
					'type'         => 'text',
					'wrapper'      => array( 'width' => '50' ),
				),
				array(
					'key'           => 'field_mrz_text_align',
					'name'          => 'align',
					'label'         => __( 'Alignment', 'mrz-theme-exp' ),
					'type'          => 'select',
					'choices'       => array(
						'left'   => __( 'Left', 'mrz-theme-exp' ),
						'center' => __( 'Centered', 'mrz-theme-exp' ),
					),
					'default_value' => 'left',
					'wrapper'       => array( 'width' => '50' ),
				),
				array(
					'key'          => 'field_mrz_text_title',
					'name'         => 'title',
					'label'        => __( 'Title', 'mrz-theme-exp' ),
					'instructions' => mrz_theme_title_instructions(),
					'type'         => 'text',
					'wrapper'      => array( 'width' => '75' ),
				),
				array(
					'key'           => 'field_mrz_text_title_tag',
					'name'          => 'title_tag',
					'label'         => __( 'Title level', 'mrz-theme-exp' ),
					'instructions'  => __( 'One H1 per page.', 'mrz-theme-exp' ),
					'type'          => 'select',
					'choices'       => mrz_theme_title_tag_choices(),
					'default_value' => 'h2',
					'wrapper'       => array( 'width' => '25' ),
				),
				array(
					'key'          => 'field_mrz_text_content',
					'name'         => 'content',
					'label'        => __( 'Text', 'mrz-theme-exp' ),
					'type'         => 'wysiwyg',
					'tabs'         => 'all',
					'toolbar'      => 'full',
					'media_upload' => 1,
				),
				array(
					'key'           => 'field_mrz_text_link',
					'name'          => 'link',
					'label'         => __( 'Button', 'mrz-theme-exp' ),
					'type'          => 'link',
					'return_format' => 'array',
					'wrapper'       => array( 'width' => '50' ),
				),
				array(
					'key'           => 'field_mrz_text_link_2',
					'name'          => 'link_2',
					'label'         => __( 'Second button', 'mrz-theme-exp' ),
					'type'          => 'link',
					'return_format' => 'array',
					'wrapper'       => array( 'width' => '50' ),
				),
				mrz_theme_section_settings_field( 'field_mrz_text' ),
			),
		),
		'layout_mrz_html'           => array(
			'key'        => 'layout_mrz_html',
			'name'       => 'mrz_html',
			'label'      => __( 'Raw HTML', 'mrz-theme-exp' ),
			'display'    => 'block',
			'sub_fields' => array(
				array(
					'key'          => 'field_mrz_html_code',
					'name'         => 'code',
					'label'        => __( 'HTML code', 'mrz-theme-exp' ),
					'type'         => 'textarea',
					'instructions' => __( 'Displayed as is, scripts included. Reserved for administrators.', 'mrz-theme-exp' ),
					'rows'         => 10,
					'new_lines'    => '',
				),
			),
		),
		'layout_mrz_cards'          => array(
			'key'        => 'layout_mrz_cards',
			'name'       => 'mrz_cards',
			'label'      => __( 'Cards', 'mrz-theme-exp' ),
			'display'    => 'block',
			'sub_fields' => array(
				array(
					'key'          => 'field_mrz_cards_title',
					'name'         => 'title',
					'label'        => __( 'Section title', 'mrz-theme-exp' ),
					'instructions' => __( 'Optional. Also names the carousel for screen readers.', 'mrz-theme-exp' ) . ' ' . mrz_theme_title_instructions(),
					'type'         => 'text',
					'wrapper'      => array( 'width' => '50' ),
				),
				array(
					'key'           => 'field_mrz_cards_display',
					'name'          => 'display',
					'label'         => __( 'Display', 'mrz-theme-exp' ),
					'type'          => 'select',
					'choices'       => array(
						'grid'     => __( 'Grid', 'mrz-theme-exp' ),
						'carousel' => __( 'Carousel', 'mrz-theme-exp' ),
					),
					'default_value' => 'grid',
					'wrapper'       => array( 'width' => '25' ),
				),
				array(
					'key'               => 'field_mrz_cards_autoplay',
					'name'              => 'autoplay',
					'label'             => __( 'Autoplay', 'mrz-theme-exp' ),
					'instructions'      => __( 'Every 5 seconds, with a pause button.', 'mrz-theme-exp' ),
					'type'              => 'true_false',
					'ui'                => 1,
					'wrapper'           => array( 'width' => '25' ),
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_mrz_cards_display',
								'operator' => '==',
								'value'    => 'carousel',
							),
						),
					),
				),
				array(
					'key'           => 'field_mrz_cards_per_row',
					'name'          => 'per_row',
					'label'         => __( 'Cards per row', 'mrz-theme-exp' ),
					'instructions'  => __( 'On a large screen. Automatic: the site setting.', 'mrz-theme-exp' ),
					'type'          => 'select',
					'choices'       => array(
						'auto' => __( 'Automatic', 'mrz-theme-exp' ),
						'2'    => '2',
						'3'    => '3',
						'4'    => '4',
					),
					'default_value' => 'auto',
					'wrapper'       => array( 'width' => '25' ),
				),
				array(
					'key'           => 'field_mrz_cards_card_style',
					'name'          => 'card_style',
					'label'         => __( 'Card style', 'mrz-theme-exp' ),
					'instructions'  => __( 'Key figures: the card title is the figure, shown large.', 'mrz-theme-exp' ),
					'type'          => 'select',
					'choices'       => array(
						'default' => __( 'Standard', 'mrz-theme-exp' ),
						'figures' => __( 'Key figures', 'mrz-theme-exp' ),
					),
					'default_value' => 'default',
					'wrapper'       => array( 'width' => '25' ),
				),
				array(
					'key'           => 'field_mrz_cards_link_style',
					'name'          => 'link_style',
					'label'         => __( 'Link style', 'mrz-theme-exp' ),
					'type'          => 'select',
					'choices'       => array(
						'link'    => __( 'Arrow link', 'mrz-theme-exp' ),
						'outline' => __( 'Outline button', 'mrz-theme-exp' ),
						'primary' => __( 'Filled button', 'mrz-theme-exp' ),
					),
					'default_value' => 'link',
					'wrapper'       => array( 'width' => '25' ),
				),
				array(
					'key'               => 'field_mrz_cards_count_up',
					'name'              => 'count_up',
					'label'             => __( 'Counter', 'mrz-theme-exp' ),
					'type'              => 'true_false',
					'ui'                => 1,
					'message'           => __( 'Figures count up from 0 when the cards come into view', 'mrz-theme-exp' ),
					'wrapper'           => array( 'width' => '25' ),
					'conditional_logic' => array( array( array( 'field' => 'field_mrz_cards_card_style', 'operator' => '==', 'value' => 'figures' ) ) ),
				),
				array(
					'key'          => 'field_mrz_cards_items',
					'name'         => 'items',
					'label'        => __( 'Cards', 'mrz-theme-exp' ),
					'type'         => 'repeater',
					'layout'       => 'block',
					'button_label' => __( 'Add a card', 'mrz-theme-exp' ),
					'collapsed'    => 'field_mrz_cards_item_title',
					'sub_fields'   => array(
						array(
							'key'           => 'field_mrz_cards_item_image',
							'name'          => 'image',
							'label'         => __( 'Image or icon', 'mrz-theme-exp' ),
							'instructions'  => __( 'An SVG icon takes the text color of the card.', 'mrz-theme-exp' ),
							'type'          => 'image',
							'return_format' => 'id',
							'preview_size'  => 'thumbnail',
							'wrapper'       => array( 'width' => '25' ),
						),
						array(
							'key'     => 'field_mrz_cards_item_title',
							'name'    => 'title',
							'label'   => __( 'Title', 'mrz-theme-exp' ),
							'type'    => 'text',
							'wrapper' => array( 'width' => '50' ),
						),
						array(
							'key'           => 'field_mrz_cards_item_background',
							'name'          => 'background',
							'label'         => __( 'Background', 'mrz-theme-exp' ),
							'type'          => 'select',
							'choices'       => mrz_theme_background_choices( __( 'None (border)', 'mrz-theme-exp' ) ),
							'default_value' => 'none',
							'wrapper'       => array( 'width' => '25' ),
						),
						array(
							'key'          => 'field_mrz_cards_item_text',
							'name'         => 'text',
							'label'        => __( 'Text', 'mrz-theme-exp' ),
							'type'         => 'wysiwyg',
							'tabs'         => 'all',
							'toolbar'      => 'basic',
							'media_upload' => 0,
						),
						array(
							'key'           => 'field_mrz_cards_item_link',
							'name'          => 'link',
							'label'         => __( 'Link', 'mrz-theme-exp' ),
							'instructions'  => __( 'Shown as an arrow link.', 'mrz-theme-exp' ),
							'type'          => 'link',
							'return_format' => 'array',
						),
					),
				),
				mrz_theme_section_settings_field( 'field_mrz_cards' ),
			),
		),
		'layout_mrz_split'          => array(
			'key'        => 'layout_mrz_split',
			'name'       => 'mrz_split',
			'label'      => __( 'Text and media', 'mrz-theme-exp' ),
			'display'    => 'block',
			'sub_fields' => array(
				array(
					'key'          => 'field_mrz_split_eyebrow',
					'name'         => 'eyebrow',
					'label'        => __( 'Eyebrow', 'mrz-theme-exp' ),
					'type'         => 'text',
				),
				array(
					'key'          => 'field_mrz_split_title',
					'name'         => 'title',
					'label'        => __( 'Title', 'mrz-theme-exp' ),
					'instructions' => mrz_theme_title_instructions(),
					'type'         => 'text',
					'wrapper'      => array( 'width' => '75' ),
				),
				array(
					'key'           => 'field_mrz_split_title_tag',
					'name'          => 'title_tag',
					'label'         => __( 'Title level', 'mrz-theme-exp' ),
					'instructions'  => __( 'One H1 per page.', 'mrz-theme-exp' ),
					'type'          => 'select',
					'choices'       => mrz_theme_title_tag_choices(),
					'default_value' => 'h2',
					'wrapper'       => array( 'width' => '25' ),
				),
				array(
					'key'          => 'field_mrz_split_content',
					'name'         => 'content',
					'label'        => __( 'Text', 'mrz-theme-exp' ),
					'type'         => 'wysiwyg',
					'tabs'         => 'all',
					'toolbar'      => 'full',
					'media_upload' => 1,
				),
				array(
					'key'           => 'field_mrz_split_link',
					'name'          => 'link',
					'label'         => __( 'Button', 'mrz-theme-exp' ),
					'type'          => 'link',
					'return_format' => 'array',
					'wrapper'       => array( 'width' => '50' ),
				),
				array(
					'key'           => 'field_mrz_split_link_2',
					'name'          => 'link_2',
					'label'         => __( 'Second button', 'mrz-theme-exp' ),
					'type'          => 'link',
					'return_format' => 'array',
					'wrapper'       => array( 'width' => '50' ),
				),
				array(
					'key'           => 'field_mrz_split_media_type',
					'name'          => 'media_type',
					'label'         => __( 'Other column', 'mrz-theme-exp' ),
					'type'          => 'select',
					'choices'       => array(
						'image'   => __( 'Image', 'mrz-theme-exp' ),
						'content' => __( 'Content', 'mrz-theme-exp' ),
					),
					'default_value' => 'image',
					'wrapper'       => array( 'width' => '25' ),
				),
				array(
					'key'               => 'field_mrz_split_image',
					'name'              => 'image',
					'label'             => __( 'Image', 'mrz-theme-exp' ),
					'type'              => 'image',
					'return_format'     => 'id',
					'preview_size'      => 'medium',
					'wrapper'           => array( 'width' => '75' ),
					'conditional_logic' => array( array( array( 'field' => 'field_mrz_split_media_type', 'operator' => '==', 'value' => 'image' ) ) ),
				),
				array(
					'key'               => 'field_mrz_split_aside_background',
					'name'              => 'aside_background',
					'label'             => __( 'Column background', 'mrz-theme-exp' ),
					'instructions'      => __( 'None: a second text. A color: a colored box.', 'mrz-theme-exp' ),
					'type'              => 'select',
					'choices'           => mrz_theme_background_choices(),
					'default_value'     => 'none',
					'wrapper'           => array( 'width' => '25' ),
					'conditional_logic' => array( array( array( 'field' => 'field_mrz_split_media_type', 'operator' => '==', 'value' => 'content' ) ) ),
				),
				array(
					'key'               => 'field_mrz_split_aside_content',
					'name'              => 'aside_content',
					'label'             => __( 'Column content', 'mrz-theme-exp' ),
					'type'              => 'wysiwyg',
					'tabs'              => 'all',
					'toolbar'           => 'full',
					'media_upload'      => 1,
					'conditional_logic' => array( array( array( 'field' => 'field_mrz_split_media_type', 'operator' => '==', 'value' => 'content' ) ) ),
				),
				array(
					'key'           => 'field_mrz_split_media_side',
					'name'          => 'media_side',
					'label'         => __( 'Image or content side', 'mrz-theme-exp' ),
					'type'          => 'select',
					'choices'       => array(
						'right' => __( 'Right', 'mrz-theme-exp' ),
						'left'  => __( 'Left', 'mrz-theme-exp' ),
					),
					'default_value' => 'right',
					'wrapper'       => array( 'width' => '25' ),
				),
				array(
					'key'           => 'field_mrz_split_ratio',
					'name'          => 'ratio',
					'label'         => __( 'Column widths', 'mrz-theme-exp' ),
					'type'          => 'select',
					'choices'       => array(
						'equal'      => __( 'Equal', 'mrz-theme-exp' ),
						'text_wide'  => __( 'Wide text (2/3)', 'mrz-theme-exp' ),
						'media_wide' => __( 'Wide image or content (2/3)', 'mrz-theme-exp' ),
					),
					'default_value' => 'equal',
					'wrapper'       => array( 'width' => '25' ),
				),
				array(
					'key'           => 'field_mrz_split_vertical_align',
					'name'          => 'vertical_align',
					'label'         => __( 'Vertical alignment', 'mrz-theme-exp' ),
					'type'          => 'select',
					'choices'       => array(
						'center' => __( 'Centered', 'mrz-theme-exp' ),
						'top'    => __( 'Top', 'mrz-theme-exp' ),
					),
					'default_value' => 'center',
					'wrapper'       => array( 'width' => '25' ),
				),
				array(
					'key'           => 'field_mrz_split_sticky',
					'name'          => 'sticky',
					'label'         => __( 'Sticky column', 'mrz-theme-exp' ),
					'instructions'  => __( 'Stays visible while scrolling, on a large screen.', 'mrz-theme-exp' ),
					'type'          => 'select',
					'choices'       => array(
						'none'  => __( 'None', 'mrz-theme-exp' ),
						'media' => __( 'Image or content', 'mrz-theme-exp' ),
						'text'  => __( 'Text', 'mrz-theme-exp' ),
					),
					'default_value' => 'none',
					'wrapper'       => array( 'width' => '25' ),
				),
				mrz_theme_section_settings_field( 'field_mrz_split' ),
			),
		),
		'layout_mrz_accordion'      => array(
			'key'        => 'layout_mrz_accordion',
			'name'       => 'mrz_accordion',
			'label'      => __( 'Accordion', 'mrz-theme-exp' ),
			'display'    => 'block',
			'sub_fields' => array(
				array(
					'key'          => 'field_mrz_accordion_title',
					'name'         => 'title',
					'label'        => __( 'Section title', 'mrz-theme-exp' ),
					'instructions' => __( 'Optional.', 'mrz-theme-exp' ) . ' ' . mrz_theme_title_instructions(),
					'type'         => 'text',
					'wrapper'      => array( 'width' => '50' ),
				),
				array(
					'key'     => 'field_mrz_accordion_intro',
					'name'    => 'intro',
					'label'   => __( 'Introduction', 'mrz-theme-exp' ),
					'type'    => 'text',
					'wrapper' => array( 'width' => '50' ),
				),
				array(
					'key'          => 'field_mrz_accordion_items',
					'name'         => 'items',
					'label'        => __( 'Items', 'mrz-theme-exp' ),
					'type'         => 'repeater',
					'layout'       => 'block',
					'button_label' => __( 'Add an item', 'mrz-theme-exp' ),
					'collapsed'    => 'field_mrz_accordion_item_question',
					'sub_fields'   => array(
						array(
							'key'   => 'field_mrz_accordion_item_question',
							'name'  => 'question',
							'label' => __( 'Question or heading', 'mrz-theme-exp' ),
							'type'  => 'text',
						),
						array(
							'key'          => 'field_mrz_accordion_item_answer',
							'name'         => 'answer',
							'label'        => __( 'Answer', 'mrz-theme-exp' ),
							'type'         => 'wysiwyg',
							'tabs'         => 'all',
							'toolbar'      => 'full',
							'media_upload' => 1,
						),
					),
				),
				array(
					'key'     => 'field_mrz_accordion_open_first',
					'name'    => 'open_first',
					'label'   => __( 'First item open', 'mrz-theme-exp' ),
					'type'    => 'true_false',
					'ui'      => 1,
					'wrapper' => array( 'width' => '50' ),
				),
				array(
					'key'          => 'field_mrz_accordion_single_open',
					'name'         => 'single_open',
					'label'        => __( 'One item open at a time', 'mrz-theme-exp' ),
					'instructions' => __( 'Opening an item closes the others.', 'mrz-theme-exp' ),
					'type'         => 'true_false',
					'ui'           => 1,
					'wrapper'      => array( 'width' => '50' ),
				),
				mrz_theme_section_settings_field( 'field_mrz_accordion' ),
			),
		),
		'layout_mrz_gallery'        => array(
			'key'        => 'layout_mrz_gallery',
			'name'       => 'mrz_gallery',
			'label'      => __( 'Gallery', 'mrz-theme-exp' ),
			'display'    => 'block',
			'sub_fields' => array(
				array(
					'key'          => 'field_mrz_gallery_title',
					'name'         => 'title',
					'label'        => __( 'Section title', 'mrz-theme-exp' ),
					'instructions' => __( 'Optional. Also names the carousel for screen readers.', 'mrz-theme-exp' ) . ' ' . mrz_theme_title_instructions(),
					'type'         => 'text',
					'wrapper'      => array( 'width' => '50' ),
				),
				array(
					'key'     => 'field_mrz_gallery_intro',
					'name'    => 'intro',
					'label'   => __( 'Introduction', 'mrz-theme-exp' ),
					'type'    => 'text',
					'wrapper' => array( 'width' => '50' ),
				),
				array(
					'key'           => 'field_mrz_gallery_images',
					'name'          => 'images',
					'label'         => __( 'Images', 'mrz-theme-exp' ),
					'instructions'  => __( 'The alternative text of each image describes it in the gallery; its caption is shown under it and in the viewer.', 'mrz-theme-exp' ),
					'type'          => 'gallery',
					'return_format' => 'id',
					'preview_size'  => 'thumbnail',
					'insert'        => 'append',
					'library'       => 'all',
				),
				array(
					'key'           => 'field_mrz_gallery_display',
					'name'          => 'display',
					'label'         => __( 'Display', 'mrz-theme-exp' ),
					'type'          => 'select',
					'choices'       => array(
						'grid'     => __( 'Grid', 'mrz-theme-exp' ),
						'masonry'  => __( 'Masonry (original formats, in columns)', 'mrz-theme-exp' ),
						'carousel' => __( 'Carousel', 'mrz-theme-exp' ),
					),
					'default_value' => 'grid',
					'wrapper'       => array( 'width' => '20' ),
				),
				array(
					'key'           => 'field_mrz_gallery_per_row',
					'name'          => 'per_row',
					'label'         => __( 'Images per row', 'mrz-theme-exp' ),
					'instructions'  => __( 'On a large screen.', 'mrz-theme-exp' ),
					'type'          => 'select',
					'choices'       => array(
						'auto' => __( 'Automatic', 'mrz-theme-exp' ),
						'2'    => '2',
						'3'    => '3',
						'4'    => '4',
						'5'    => '5',
					),
					'default_value' => 'auto',
					'wrapper'       => array( 'width' => '20' ),
				),
				array(
					'key'           => 'field_mrz_gallery_ratio',
					'name'          => 'ratio',
					'label'         => __( 'Image format', 'mrz-theme-exp' ),
					'instructions'  => __( 'Not used in masonry.', 'mrz-theme-exp' ),
					'type'          => 'select',
					'choices'       => array(
						'original' => __( 'Original', 'mrz-theme-exp' ),
						'square'   => __( 'Square', 'mrz-theme-exp' ),
						'4-3'      => '4/3',
						'16-9'     => '16/9',
					),
					'default_value' => '4-3',
					'wrapper'       => array( 'width' => '20' ),
				),
				array(
					'key'     => 'field_mrz_gallery_captions',
					'name'    => 'captions',
					'label'   => __( 'Captions', 'mrz-theme-exp' ),
					'type'    => 'true_false',
					'ui'      => 1,
					'message' => __( 'Show captions under the images', 'mrz-theme-exp' ),
					'wrapper' => array( 'width' => '20' ),
				),
				array(
					'key'           => 'field_mrz_gallery_lightbox',
					'name'          => 'lightbox',
					'label'         => __( 'Viewer', 'mrz-theme-exp' ),
					'type'          => 'true_false',
					'ui'            => 1,
					'default_value' => 1,
					'message'       => __( 'Open images full size on click', 'mrz-theme-exp' ),
					'wrapper'       => array( 'width' => '20' ),
				),
				mrz_theme_section_settings_field( 'field_mrz_gallery' ),
			),
		),
		'layout_mrz_anchors'        => array(
			'key'        => 'layout_mrz_anchors',
			'name'       => 'mrz_anchors',
			'label'      => __( 'Anchor menu', 'mrz-theme-exp' ),
			'display'    => 'block',
			'sub_fields' => array(
				array(
					'key'          => 'field_mrz_anchors_items',
					'name'         => 'items',
					'label'        => __( 'Links', 'mrz-theme-exp' ),
					'instructions' => __( 'Each link goes to a section of the page that has this anchor (section settings > Anchor).', 'mrz-theme-exp' ),
					'type'         => 'repeater',
					'layout'       => 'table',
					'button_label' => __( 'Add a link', 'mrz-theme-exp' ),
					'sub_fields'   => array(
						array(
							'key'   => 'field_mrz_anchors_item_label',
							'name'  => 'label',
							'label' => __( 'Text', 'mrz-theme-exp' ),
							'type'  => 'text',
						),
						array(
							'key'   => 'field_mrz_anchors_item_anchor',
							'name'  => 'anchor',
							'label' => __( 'Anchor', 'mrz-theme-exp' ),
							'type'  => 'text',
						),
					),
				),
				array(
					'key'           => 'field_mrz_anchors_sticky',
					'name'          => 'sticky',
					'label'         => __( 'Sticky', 'mrz-theme-exp' ),
					'type'          => 'true_false',
					'ui'            => 1,
					'default_value' => 1,
					'message'       => __( 'Stays visible under the header while scrolling', 'mrz-theme-exp' ),
					'wrapper'       => array( 'width' => '50' ),
				),
				array(
					'key'           => 'field_mrz_anchors_align',
					'name'          => 'align',
					'label'         => __( 'Alignment', 'mrz-theme-exp' ),
					'type'          => 'select',
					'choices'       => array(
						'left'   => __( 'Left', 'mrz-theme-exp' ),
						'center' => __( 'Centered', 'mrz-theme-exp' ),
					),
					'default_value' => 'center',
					'wrapper'       => array( 'width' => '50' ),
				),
				mrz_theme_section_settings_field( 'field_mrz_anchors' ),
			),
		),
		'layout_mrz_marquee'        => array(
			'key'        => 'layout_mrz_marquee',
			'name'       => 'mrz_marquee',
			'label'      => __( 'Scrolling band', 'mrz-theme-exp' ),
			'display'    => 'block',
			'sub_fields' => array(
				array(
					'key'          => 'field_mrz_marquee_items',
					'name'         => 'items',
					'label'        => __( 'Items', 'mrz-theme-exp' ),
					'instructions' => __( 'A text, or an image (logo). With a logo, the text becomes its alternative text (the logo name); left empty, the media library alternative text is used.', 'mrz-theme-exp' ),
					'type'         => 'repeater',
					'layout'       => 'table',
					'button_label' => __( 'Add an item', 'mrz-theme-exp' ),
					'sub_fields'   => array(
						array(
							'key'   => 'field_mrz_marquee_item_text',
							'name'  => 'text',
							'label' => __( 'Text', 'mrz-theme-exp' ),
							'type'  => 'text',
						),
						array(
							'key'           => 'field_mrz_marquee_item_image',
							'name'          => 'image',
							'label'         => __( 'Image', 'mrz-theme-exp' ),
							'type'          => 'image',
							'return_format' => 'id',
							'preview_size'  => 'thumbnail',
						),
						array(
							'key'           => 'field_mrz_marquee_item_link',
							'name'          => 'link',
							'label'         => __( 'Link', 'mrz-theme-exp' ),
							'type'          => 'link',
							'return_format' => 'array',
						),
					),
				),
				array(
					'key'           => 'field_mrz_marquee_speed',
					'name'          => 'speed',
					'label'         => __( 'Speed', 'mrz-theme-exp' ),
					'type'          => 'select',
					'choices'       => array(
						'slow'   => __( 'Slow', 'mrz-theme-exp' ),
						'normal' => __( 'Normal', 'mrz-theme-exp' ),
						'fast'   => __( 'Fast', 'mrz-theme-exp' ),
					),
					'default_value' => 'normal',
					'wrapper'       => array( 'width' => '33' ),
				),
				array(
					'key'           => 'field_mrz_marquee_direction',
					'name'          => 'direction',
					'label'         => __( 'Direction', 'mrz-theme-exp' ),
					'type'          => 'select',
					'choices'       => array(
						'left'  => __( 'To the left', 'mrz-theme-exp' ),
						'right' => __( 'To the right', 'mrz-theme-exp' ),
					),
					'default_value' => 'left',
					'wrapper'       => array( 'width' => '33' ),
				),
				array(
					'key'           => 'field_mrz_marquee_separator',
					'name'          => 'separator',
					'label'         => __( 'Separator', 'mrz-theme-exp' ),
					'instructions'  => __( 'Between two texts.', 'mrz-theme-exp' ),
					'type'          => 'text',
					'default_value' => '•',
					'wrapper'       => array( 'width' => '33' ),
				),
				mrz_theme_section_settings_field( 'field_mrz_marquee' ),
			),
		),
		'layout_mrz_footer'         => array(
			'key'        => 'layout_mrz_footer',
			'name'       => 'mrz_footer',
			'label'      => __( 'Footer', 'mrz-theme-exp' ),
			'display'    => 'block',
			'sub_fields' => array(
				array(
					'key'           => 'field_mrz_footer_logo',
					'name'          => 'logo',
					'label'         => __( 'Logo', 'mrz-theme-exp' ),
					'instructions'  => __( 'Optional: centered above the footer.', 'mrz-theme-exp' ),
					'type'          => 'image',
					'return_format' => 'id',
					'preview_size'  => 'medium',
					'wrapper'       => array( 'width' => '30' ),
				),
				array(
					'key'       => 'field_mrz_footer_title',
					'name'      => 'title',
					'label'     => __( 'Sentence', 'mrz-theme-exp' ),
					'type'      => 'textarea',
					'rows'      => 3,
					'new_lines' => 'br',
					'wrapper'   => array( 'width' => '40' ),
				),
				array(
					'key'           => 'field_mrz_footer_button',
					'name'          => 'button',
					'label'         => __( 'Button', 'mrz-theme-exp' ),
					'type'          => 'link',
					'return_format' => 'array',
					'wrapper'       => array( 'width' => '30' ),
				),
				array(
					'key'          => 'field_mrz_footer_columns',
					'name'         => 'columns',
					'label'        => __( 'Link columns', 'mrz-theme-exp' ),
					'type'         => 'repeater',
					'layout'       => 'block',
					'button_label' => __( 'Add a column', 'mrz-theme-exp' ),
					'sub_fields'   => array(
						array(
							'key'   => 'field_mrz_footer_column_title',
							'name'  => 'title',
							'label' => __( 'Title', 'mrz-theme-exp' ),
							'type'  => 'text',
						),
						array(
							'key'          => 'field_mrz_footer_column_links',
							'name'         => 'links',
							'label'        => __( 'Links', 'mrz-theme-exp' ),
							'instructions' => __( 'A bulleted list of links.', 'mrz-theme-exp' ),
							'type'         => 'wysiwyg',
							'tabs'         => 'all',
							'toolbar'      => 'basic',
							'media_upload' => 0,
						),
					),
				),
				array(
					'key'          => 'field_mrz_footer_socials',
					'name'         => 'socials',
					'label'        => __( 'Social networks', 'mrz-theme-exp' ),
					'instructions' => __( 'Black SVG icon (colored by the theme), link and network name (read by screen readers).', 'mrz-theme-exp' ),
					'type'         => 'repeater',
					'layout'       => 'table',
					'button_label' => __( 'Add a network', 'mrz-theme-exp' ),
					'sub_fields'   => array(
						array(
							'key'           => 'field_mrz_footer_social_icon',
							'name'          => 'icon',
							'label'         => __( 'Icon', 'mrz-theme-exp' ),
							'type'          => 'image',
							'return_format' => 'id',
							'preview_size'  => 'thumbnail',
						),
						array(
							'key'   => 'field_mrz_footer_social_url',
							'name'  => 'url',
							'label' => __( 'Link', 'mrz-theme-exp' ),
							'type'  => 'url',
						),
						array(
							'key'   => 'field_mrz_footer_social_name',
							'name'  => 'name',
							'label' => __( 'Name', 'mrz-theme-exp' ),
							'type'  => 'text',
						),
					),
				),
				array(
					'key'     => 'field_mrz_footer_free_title',
					'name'    => 'free_title',
					'label'   => __( 'Free column: title', 'mrz-theme-exp' ),
					'type'    => 'text',
					'wrapper' => array( 'width' => '33' ),
				),
				array(
					'key'          => 'field_mrz_footer_free_content',
					'name'         => 'free_content',
					'label'        => __( 'Free column: content', 'mrz-theme-exp' ),
					'instructions' => __( 'After the link columns: text, newsletter form (shortcode)…', 'mrz-theme-exp' ),
					'type'         => 'wysiwyg',
					'tabs'         => 'all',
					'toolbar'      => 'full',
					'media_upload' => 0,
					'wrapper'      => array( 'width' => '67' ),
				),
				array(
					'key'          => 'field_mrz_footer_bottom',
					'name'         => 'bottom',
					'label'        => __( 'Bottom line', 'mrz-theme-exp' ),
					'instructions' => __( 'Copyright, legal links. Tags: {year}, {site}.', 'mrz-theme-exp' ),
					'type'         => 'wysiwyg',
					'tabs'         => 'all',
					'toolbar'      => 'basic',
					'media_upload' => 0,
				),
				mrz_theme_section_settings_field( 'field_mrz_footer' ),
			),
		),
		'layout_mrz_global_section' => array(
			'key'        => 'layout_mrz_global_section',
			'name'       => 'mrz_global_section',
			'label'      => __( 'Global section', 'mrz-theme-exp' ),
			'display'    => 'block',
			'sub_fields' => array(
				array(
					'key'           => 'field_mrz_global_section_section',
					'name'          => 'section',
					'label'         => __( 'Global section', 'mrz-theme-exp' ),
					'type'          => 'post_object',
					'post_type'     => array( 'mrz_section' ),
					'return_format' => 'id',
					'ui'            => 1,
				),
			),
		),
	);

	// ACF rattache chaque sous-champ à sa section par parent_layout.
	foreach ( $layouts as $key => $layout ) {
		$layouts[ $key ] += array(
			'min' => '',
			'max' => '',
		);
		foreach ( $layouts[ $key ]['sub_fields'] as $index => $sub_field ) {
			$sub_field['parent_layout']                 = $key;
			$layouts[ $key ]['sub_fields'][ $index ] = mrz_theme_validate_field( $sub_field );
		}
	}

	return $layouts;
}

/**
 * Complète un champ déclaré en PHP comme ACF le fait au chargement (_name, prefix…),
 * sous-champs compris : sans cela, les valeurs d'un groupe ne s'enregistrent pas.
 *
 * @param array $field Champ.
 * @return array
 */
function mrz_theme_validate_field( $field ) {
	if ( ! function_exists( 'acf_validate_field' ) ) {
		return $field;
	}

	$field = acf_validate_field( $field );

	if ( ! empty( $field['sub_fields'] ) ) {
		foreach ( $field['sub_fields'] as $index => $sub_field ) {
			$sub_field['parent']                = $field['key'];
			$field['sub_fields'][ $index ] = mrz_theme_validate_field( $sub_field );
		}
	}

	return $field;
}

/**
 * Faut-il injecter les sections du parent ? Jamais pendant l'édition ou
 * l'enregistrement d'un groupe de champs : ACF les recopierait dans le groupe
 * (base et JSON) de l'enfant.
 *
 * @return bool
 */
function mrz_theme_should_inject_layouts() {
	// ACF coupe ses filtres quand il enregistre un groupe de champs.
	if ( function_exists( 'acf_is_filter_enabled' ) && ! acf_is_filter_enabled( 'local' ) ) {
		return false;
	}

	if ( ! is_admin() ) {
		return true;
	}

	// Écrans des groupes de champs (édition, liste, synchronisation JSON).
	global $typenow;
	if ( 'acf-field-group' === $typenow ) {
		return false;
	}
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( $screen && 'acf-field-group' === $screen->post_type ) {
		return false;
	}

	// Requêtes AJAX de l'éditeur de groupes (ajout de champ, déplacement…).
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lecture seule du nom de l'action.
	$action = isset( $_REQUEST['action'] ) ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : '';
	if ( wp_doing_ajax() && ( str_starts_with( $action, 'acf/field_group' ) || str_starts_with( $action, 'acf/field' ) ) ) {
		return false;
	}

	return true;
}

/**
 * Ajoute les sections du parent au constructeur chargé.
 *
 * @param array $field Champ flexible.
 * @return array
 */
function mrz_theme_inject_parent_layouts( $field ) {
	if ( 'field_mrz_builder' !== $field['key'] || ! mrz_theme_should_inject_layouts() ) {
		return $field;
	}

	$layouts = (array) $field['layouts'];
	foreach ( mrz_theme_parent_layouts() as $key => $layout ) {
		if ( ! isset( $layouts[ $key ] ) ) {
			$layouts[ $key ] = $layout;
		}
	}
	// Sections réservées aux sections globales : absentes du constructeur des pages et articles.
	if ( mrz_theme_is_editing_content_outside_global_sections() ) {
		$global_only = (array) apply_filters( 'mrz_theme_global_only_layouts', array( 'mrz_footer' ) );
		foreach ( $layouts as $key => $layout ) {
			if ( in_array( $layout['name'] ?? '', $global_only, true ) ) {
				unset( $layouts[ $key ] );
			}
		}
	}

	$field['layouts'] = $layouts;

	return $field;
}
add_filter( 'acf/load_field/type=flexible_content', 'mrz_theme_inject_parent_layouts', 20 );

/**
 * Écran d'édition d'un contenu qui n'est pas une section globale (page, article…) ?
 * Hors admin (affichage) et en AJAX, toutes les sections restent déclarées.
 *
 * @return bool
 */
function mrz_theme_is_editing_content_outside_global_sections() {
	if ( ! is_admin() || wp_doing_ajax() ) {
		return false;
	}

	global $typenow;
	$screen    = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	$post_type = $screen && $screen->post_type ? $screen->post_type : (string) $typenow;

	return $post_type && 'mrz_section' !== $post_type;
}

/**
 * HTML brut : réservé aux administrateurs. unfiltered_html seul ne suffit pas : sur un
 * site simple, les éditeurs l'ont aussi. Les deux droits ensemble excluent aussi les
 * administrateurs d'un site en multisite.
 *
 * @return bool
 */
function mrz_theme_can_edit_raw_html() {
	return current_user_can( 'manage_options' ) && current_user_can( 'unfiltered_html' );
}

/**
 * HTML brut : champ masqué pour les autres comptes. Masqué, il n'est pas envoyé avec la
 * page : ACF garde le code déjà enregistré.
 *
 * @param array $field Champ.
 * @return array|false
 */
function mrz_theme_restrict_html_field( $field ) {
	return mrz_theme_can_edit_raw_html() ? $field : false;
}
add_filter( 'acf/prepare_field/key=field_mrz_html_code', 'mrz_theme_restrict_html_field' );

/**
 * HTML brut : un compte connecté sans le droit qui enverrait quand même le champ (requête
 * fabriquée) voit son code filtré, scripts retirés. Sans compte connecté (code du site,
 * WP-CLI), la valeur passe telle quelle.
 *
 * @param mixed $value Valeur envoyée.
 * @return mixed
 */
function mrz_theme_filter_html_field_value( $value ) {
	if ( ! is_user_logged_in() || mrz_theme_can_edit_raw_html() ) {
		return $value;
	}

	return wp_kses_post( (string) $value );
}
add_filter( 'acf/update_value/key=field_mrz_html_code', 'mrz_theme_filter_html_field_value' );

/**
 * Éditeurs de texte des sections : moins hauts (150px au lieu de 300px), ils grandissent
 * avec le texte saisi. La page du constructeur reste lisible d'un coup d'œil.
 */
function mrz_theme_compact_wysiwyg() {
	?>
	<script>
	if ( window.acf ) {
		acf.add_filter( 'wysiwyg_tinymce_settings', function ( settings, id ) {
			// TinyMCE prend la hauteur du textarea (300px imposés par ACF) : on la réduit avant le démarrage.
			var textarea = document.getElementById( id );
			if ( textarea ) {
				textarea.style.height = '150px';
			}
			settings.height = 150;
			settings.min_height = 120;
			settings.autoresize_min_height = 150;
			return settings;
		} );
		// WordPress impose encore sa hauteur au démarrage : on la ramène une fois l'éditeur prêt.
		acf.addAction( 'wysiwyg_tinymce_init', function ( editor ) {
			editor.theme.resizeTo( null, 150 );
		} );
	}
	</script>
	<style>
	.acf-field-wysiwyg textarea.wp-editor-area { min-height: 150px; }
	.acf-field-wysiwyg .acf-editor-wrap iframe { min-height: 120px; }
	</style>
	<?php
}
add_action( 'acf/input/admin_footer', 'mrz_theme_compact_wysiwyg' );

/**
 * Réglages communs d'une section (groupe « settings », du parent ou cloné dans l'enfant) :
 * repliés derrière un interrupteur, pour que le contenu de la section reste lisible.
 * Affichage seulement : les réglages restent dans le formulaire et sont enregistrés
 * tels quels, interrupteur ouvert ou non.
 */
function mrz_theme_section_settings_toggle() {
	?>
	<script>
	if ( window.acf ) {
		acf.addAction( 'new_field/type=group', function ( field ) {
			var el = field.$el[0];
			if ( 'settings' !== el.dataset.name || ! el.closest( '.acf-flexible-content' ) || el.querySelector( ':scope > .acf-label .mrz-settings-toggle' ) ) {
				return;
			}
			var label = el.querySelector( ':scope > .acf-label' );
			if ( ! label ) {
				return;
			}
			var button = document.createElement( 'button' );
			button.type = 'button';
			button.className = 'mrz-settings-toggle';
			button.setAttribute( 'role', 'switch' );
			button.setAttribute( 'aria-checked', 'false' );
			button.setAttribute( 'aria-label', <?php echo wp_json_encode( __( 'Show the section settings', 'mrz-theme-exp' ) ); ?> );
			button.addEventListener( 'click', function () {
				var open = ! el.classList.contains( 'mrz-settings-open' );
				el.classList.toggle( 'mrz-settings-open', open );
				button.setAttribute( 'aria-checked', open ? 'true' : 'false' );
			} );
			label.appendChild( button );
		} );
	}
	</script>
	<style>
	.acf-flexible-content .acf-field-group[data-name="settings"] > .acf-label { display: flex; align-items: center; gap: 10px; }
	.acf-flexible-content .acf-field-group[data-name="settings"] > .acf-label label { margin: 0; }
	.acf-flexible-content .acf-field-group[data-name="settings"]:not(.mrz-settings-open) > .acf-input { display: none; }
	.mrz-settings-toggle { position: relative; width: 36px; height: 20px; padding: 0; border: 1px solid #8c8f94; border-radius: 999px; background: #dcdcde; cursor: pointer; transition: background-color .15s; }
	.mrz-settings-toggle::after { content: ""; position: absolute; top: 2px; left: 2px; width: 14px; height: 14px; border-radius: 50%; background: #fff; box-shadow: 0 1px 2px rgb(0 0 0 / .3); transition: transform .15s; }
	.mrz-settings-toggle[aria-checked="true"] { border-color: #2271b1; background: #2271b1; }
	.mrz-settings-toggle[aria-checked="true"]::after { transform: translateX(16px); }
	.mrz-settings-toggle:focus-visible { outline: 2px solid #2271b1; outline-offset: 2px; }
	</style>
	<?php
}
add_action( 'acf/input/admin_footer', 'mrz_theme_section_settings_toggle' );

// Menu « Ajouter une section » : intitulés des sections du site et du thème.
function mrz_theme_layout_menu_groups() {
	$flags = JSON_UNESCAPED_UNICODE | JSON_HEX_TAG;
	?>
	<style>
	.acf-fc-popup {
		--mrz-site-sections: <?php echo wp_json_encode( __( 'Site sections', 'mrz-theme-exp' ), $flags ); ?>;
		--mrz-theme-sections: <?php echo wp_json_encode( __( 'Theme sections', 'mrz-theme-exp' ), $flags ); ?>;
	}
	.acf-fc-popup ul { max-height: 60vh; overflow: hidden auto; }
	.acf-fc-popup li { flex-shrink: 0; }
	.acf-fc-popup a { box-sizing: border-box; min-height: 40px; line-height: 30px; }
	.acf-fc-popup li:first-child:has(> a[data-layout^="mrzchild_"]),
	.acf-fc-popup li:has(> a[data-layout^="mrzchild_"]) + li:has(> a[data-layout^="mrz_"]) { flex-direction: column; align-items: stretch; height: auto; }
	.acf-fc-popup li:first-child:has(> a[data-layout^="mrzchild_"])::before,
	.acf-fc-popup li:has(> a[data-layout^="mrzchild_"]) + li:has(> a[data-layout^="mrz_"])::before { content: var(--mrz-theme-sections); padding: 10px 10px 4px; color: #50575e; font-weight: 600; }
	.acf-fc-popup li:first-child:has(> a[data-layout^="mrzchild_"])::before { content: var(--mrz-site-sections); }
	.acf-fc-popup li:has(> a[data-layout^="mrzchild_"]) + li:has(> a[data-layout^="mrz_"])::before { margin-top: 6px; border-top: 1px solid #dcdcde; }
	</style>
	<?php
}
add_action( 'acf/input/admin_footer', 'mrz_theme_layout_menu_groups' );

/**
 * Boutons « Expand All » et « Collapse All » du constructeur : absents de la traduction
 * française d'ACF (6.8). Traduits par le thème tant qu'ACF ne le fait pas.
 *
 * @param string $translation Texte traduit.
 * @param string $text        Texte d'origine.
 * @return string
 */
function mrz_theme_acf_missing_translations( $translation, $text ) {
	if ( $translation !== $text ) {
		return $translation;
	}

	if ( 'Expand All' === $text ) {
		return __( 'Expand all', 'mrz-theme-exp' );
	}

	if ( 'Collapse All' === $text ) {
		return __( 'Collapse all', 'mrz-theme-exp' );
	}

	return $translation;
}
add_filter( 'gettext_acf', 'mrz_theme_acf_missing_translations', 10, 2 );
