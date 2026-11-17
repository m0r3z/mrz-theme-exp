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
				'instructions'  => __( 'Content centered vertically.', 'mrz-theme-exp' ),
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
				'key'     => $prefix . '_settings_gradient',
				'name'    => 'gradient',
				'label'   => __( 'Gradient', 'mrz-theme-exp' ),
				'type'    => 'true_false',
				'ui'      => 1,
				'message' => __( 'From the background color to a second color', 'mrz-theme-exp' ),
				'wrapper' => array( 'width' => '33' ),
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
		),
	);
}

/**
 * Aide des champs de titre : mot en couleur et retour à la ligne (mrz_theme_title_html()).
 *
 * @return string
 */
function mrz_theme_title_instructions() {
	return __( 'Put a word between <em> and </em> to color it; <br> for a line break.', 'mrz-theme-exp' );
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
					'instructions' => __( 'Optional. Short line above the title.', 'mrz-theme-exp' ),
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
					'instructions'  => __( 'H1 only when this section opens the page (one H1 per page).', 'mrz-theme-exp' ),
					'type'          => 'select',
					'choices'       => array(
						'h2' => 'H2',
						'h1' => 'H1',
					),
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
					'instructions'  => __( 'Outlined.', 'mrz-theme-exp' ),
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
					'instructions' => __( 'Optional. Short line above the title.', 'mrz-theme-exp' ),
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
					'instructions'  => __( 'H1 only when this section opens the page (one H1 per page).', 'mrz-theme-exp' ),
					'type'          => 'select',
					'choices'       => array(
						'h2' => 'H2',
						'h1' => 'H1',
					),
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
					'instructions'  => __( 'Outlined.', 'mrz-theme-exp' ),
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
 * HTML brut : champ réservé aux comptes qui peuvent publier du HTML non filtré.
 *
 * @param array $field Champ.
 * @return array|false
 */
function mrz_theme_restrict_html_field( $field ) {
	return current_user_can( 'unfiltered_html' ) ? $field : false;
}
add_filter( 'acf/prepare_field/key=field_mrz_html_code', 'mrz_theme_restrict_html_field' );
