<?php
/**
 * Section du parent « Galerie » : des images en grille ou en carrousel, ouvertes en
 * grand dans la lightbox du thème (assets/js/lightbox.js, chargé seulement si besoin).
 * Apparence : variables --mrz-gallery-* (src/css/tokens.css).
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

$mrz_images = get_sub_field( 'images' ) ?: array(); // phpcs:ignore Universal.Operators.DisallowShortTernary
if ( ! $mrz_images ) {
	return;
}

$mrz_title    = (string) get_sub_field( 'title' );
$mrz_carousel = 'carousel' === get_sub_field( 'display' );
$mrz_masonry  = 'masonry' === get_sub_field( 'display' );
$mrz_captions = (bool) get_sub_field( 'captions' );
$mrz_lightbox = (bool) get_sub_field( 'lightbox' );
$mrz_ratio    = (string) get_sub_field( 'ratio' );
$mrz_per_row  = (string) get_sub_field( 'per_row' );

$mrz_classes = array( 'mrz-gallery--ratio-' . ( in_array( $mrz_ratio, array( 'original', 'square', '4-3', '16-9' ), true ) ? $mrz_ratio : '4-3' ) );
if ( in_array( $mrz_per_row, array( '2', '3', '4', '5' ), true ) ) {
	$mrz_classes[] = 'mrz-gallery--per-row-' . $mrz_per_row;
}

// Un groupe par galerie : précédent et suivant restent dans la même galerie.
$GLOBALS['mrz_theme_gallery_count'] = ( $GLOBALS['mrz_theme_gallery_count'] ?? 0 ) + 1;
$mrz_group = 'gallery-' . (int) $GLOBALS['mrz_theme_gallery_count'];

if ( $mrz_lightbox ) {
	wp_enqueue_script( 'mrz-lightbox' );
}

mrz_theme_section_open( 'mrz-gallery', array( 'class' => $mrz_classes ) );
mrz_theme_section_heading( $mrz_title, (string) get_sub_field( 'intro' ) );

if ( $mrz_carousel ) {
	mrz_theme_carousel_open( array( 'label' => wp_strip_all_tags( $mrz_title ) ) );
} else {
	// Mosaïque : colonnes CSS, chaque image à son format d'origine (lecture colonne par colonne).
	echo '<ul class="mrz-gallery__grid' . ( $mrz_masonry ? ' mrz-gallery__grid--masonry' : '' ) . '">';
}

$mrz_count = count( $mrz_images );
foreach ( $mrz_images as $mrz_index => $mrz_image ) :
	$mrz_image   = (int) $mrz_image;
	$mrz_caption = (string) wp_get_attachment_caption( $mrz_image );
	$mrz_alt     = (string) get_post_meta( $mrz_image, '_wp_attachment_image_alt', true );
	$mrz_img     = wp_get_attachment_image(
		$mrz_image,
		'medium_large',
		false,
		array(
			'class' => 'mrz-gallery__image',
			'style' => mrz_theme_image_ratio_style( $mrz_image, 'medium_large' ),
		) + mrz_theme_section_image_loading()
	);
	?>
	<li class="mrz-gallery__item<?php echo $mrz_carousel ? ' mrz-carousel__slide' : ''; ?>">
		<figure class="mrz-gallery__figure">
			<?php if ( $mrz_lightbox ) : ?>
				<a class="mrz-gallery__link" href="<?php echo esc_url( (string) wp_get_attachment_image_url( $mrz_image, 'full' ) ); ?>" data-mrz-lightbox="<?php echo esc_attr( $mrz_group ); ?>"<?php echo $mrz_caption ? ' data-caption="' . esc_attr( $mrz_caption ) . '"' : ''; ?>>
					<?php echo $mrz_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image(). ?>
					<?php if ( ! $mrz_alt ) : ?>
						<?php /* translators: 1: image number, 2: number of images. */ ?>
						<span class="screen-reader-text"><?php echo esc_html( sprintf( __( 'Enlarge image %1$s of %2$s', 'mrz-theme-exp' ), $mrz_index + 1, $mrz_count ) ); ?></span>
					<?php endif; ?>
				</a>
			<?php else : ?>
				<?php echo $mrz_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image(). ?>
			<?php endif; ?>
			<?php if ( $mrz_captions && $mrz_caption ) : ?>
				<figcaption class="mrz-gallery__caption"><?php echo esc_html( $mrz_caption ); ?></figcaption>
			<?php endif; ?>
		</figure>
	</li>
	<?php
endforeach;

if ( $mrz_carousel ) {
	mrz_theme_carousel_close();
} else {
	echo '</ul>';
}

mrz_theme_section_close();
