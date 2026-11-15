<?php
/**
 * Layout Texte + image.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

$mrz_image    = (int) get_sub_field( 'image' );
$mrz_position = get_sub_field( 'image_position' );

mrz_theme_section_open( 'text-image' );
?>
<div class="grid items-center gap-12 md:grid-cols-2">
	<div class="mrz-prose">
		<?php if ( get_sub_field( 'title' ) ) : ?>
			<h2><?php echo esc_html( get_sub_field( 'title' ) ); ?></h2>
		<?php endif; ?>
		<?php echo wp_kses_post( get_sub_field( 'content' ) ); ?>
		<?php mrz_theme_button( get_sub_field( 'button' ) ); ?>
	</div>
	<?php if ( $mrz_image ) : ?>
		<figure class="overflow-hidden rounded-base <?php echo 'left' === $mrz_position ? 'md:order-first' : ''; ?>">
			<?php echo wp_get_attachment_image( $mrz_image, 'large', false, array( 'class' => 'h-auto w-full' ) ); ?>
		</figure>
	<?php endif; ?>
</div>
<?php
mrz_theme_section_close();
