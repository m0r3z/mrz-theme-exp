<?php
/**
 * Section du parent « Cartes » : des blocs libres (image ou icône, titre, texte,
 * lien fléché), chacun sur le fond de son choix, en grille ou en carrousel.
 * Apparence : variables --mrz-cards-* et --mrz-card-* (src/css/tokens.css).
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

$mrz_title    = (string) get_sub_field( 'title' );
$mrz_items    = get_sub_field( 'items' ) ?: array(); // phpcs:ignore Universal.Operators.DisallowShortTernary
$mrz_carousel = 'carousel' === get_sub_field( 'display' );
// Sans titre de section, les cartes portent les titres de niveau 2 (pas de saut de niveau).
$mrz_tag = $mrz_title ? 'h3' : 'h2';

if ( ! $mrz_items ) {
	return;
}

// Cartes par ligne propres à la section, style « chiffres clés ».
$mrz_classes  = array();
$mrz_per_row  = (string) get_sub_field( 'per_row' );
if ( in_array( $mrz_per_row, array( '2', '3', '4' ), true ) ) {
	$mrz_classes[] = 'mrz-cards--per-row-' . $mrz_per_row;
}
if ( 'figures' === get_sub_field( 'card_style' ) ) {
	$mrz_classes[] = 'mrz-cards--figures';
}

mrz_theme_section_open( 'mrz-cards', array( 'class' => $mrz_classes ) );
mrz_theme_section_heading( $mrz_title );

if ( $mrz_carousel ) {
	mrz_theme_carousel_open(
		array(
			'label'    => wp_strip_all_tags( $mrz_title ),
			'autoplay' => get_sub_field( 'autoplay' ) ? 5 : 0,
		)
	);
} else {
	echo '<ul class="mrz-cards__grid">';
}

foreach ( $mrz_items as $mrz_item ) :
	$mrz_background = sanitize_html_class( $mrz_item['background'] ?? 'none' );
	$mrz_image      = (int) ( $mrz_item['image'] ?? 0 );
	?>
	<li class="mrz-cards__item<?php echo $mrz_carousel ? ' mrz-carousel__slide' : ''; ?>">
		<div class="mrz-cards__card mrz-bg--<?php echo esc_attr( $mrz_background ); ?>">
			<?php
			if ( $mrz_image && 'image/svg+xml' === get_post_mime_type( $mrz_image ) ) :
				// Icône SVG : affichée en masque, de la couleur du texte de la carte.
				$mrz_alt = (string) get_post_meta( $mrz_image, '_wp_attachment_image_alt', true );
				?>
				<span class="mrz-cards__icon" style="--mrz-card-icon:url('<?php echo esc_url( wp_get_attachment_url( $mrz_image ) ); ?>')"<?php echo $mrz_alt ? ' role="img" aria-label="' . esc_attr( $mrz_alt ) . '"' : ' aria-hidden="true"'; ?>></span>
			<?php elseif ( $mrz_image ) : ?>
				<div class="mrz-cards__media"><?php echo wp_get_attachment_image( $mrz_image, 'medium_large', false, array( 'style' => mrz_theme_image_ratio_style( $mrz_image, 'medium_large' ) ) ); ?></div>
			<?php endif; ?>
			<?php if ( ! empty( $mrz_item['title'] ) ) : ?>
				<<?php echo esc_html( $mrz_tag ); ?> class="mrz-cards__title"><?php echo mrz_theme_title_html( $mrz_item['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- filtré par wp_kses. ?></<?php echo esc_html( $mrz_tag ); ?>>
			<?php endif; ?>
			<?php if ( ! empty( $mrz_item['text'] ) ) : ?>
				<div class="mrz-cards__text mrz-prose"><?php echo wp_kses_post( $mrz_item['text'] ); ?></div>
			<?php endif; ?>
			<?php if ( ! empty( $mrz_item['link']['url'] ) ) : ?>
				<div class="mrz-cards__link"><?php mrz_theme_button( $mrz_item['link'], 'link' ); ?></div>
			<?php endif; ?>
		</div>
	</li>
	<?php
endforeach;

if ( $mrz_carousel ) {
	mrz_theme_carousel_close();
} else {
	echo '</ul>';
}

mrz_theme_section_close();
