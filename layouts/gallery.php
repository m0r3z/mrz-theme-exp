<?php
/**
 * Layout Galerie. Les liens portent data-mrz-lightbox : la lightbox (V2) s'y branchera.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

$mrz_images  = (array) get_sub_field( 'images' );
$mrz_columns = array(
	'2' => 'grid-cols-2',
	'3' => 'grid-cols-2 md:grid-cols-3',
	'4' => 'grid-cols-2 md:grid-cols-4',
);

mrz_theme_section_open( 'gallery' );
mrz_theme_section_heading( get_sub_field( 'title' ) );
?>
<div class="grid gap-4 <?php echo esc_attr( $mrz_columns[ get_sub_field( 'columns' ) ] ?? $mrz_columns['3'] ); ?>">
	<?php foreach ( $mrz_images as $mrz_image_id ) : ?>
		<a class="mrz-gallery__item aspect-square" href="<?php echo esc_url( wp_get_attachment_image_url( (int) $mrz_image_id, 'full' ) ); ?>" data-mrz-lightbox>
			<?php echo wp_get_attachment_image( (int) $mrz_image_id, 'medium_large', false, array( 'class' => 'h-full w-full object-cover' ) ); ?>
		</a>
	<?php endforeach; ?>
</div>
<?php
mrz_theme_section_close();
