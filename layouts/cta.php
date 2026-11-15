<?php
/**
 * Layout Appel à l'action.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

mrz_theme_section_open( 'cta' );
?>
<div class="mx-auto max-w-3xl text-center">
	<?php if ( get_sub_field( 'title' ) ) : ?>
		<h2 class="mrz-section__title"><?php echo esc_html( get_sub_field( 'title' ) ); ?></h2>
	<?php endif; ?>
	<?php if ( get_sub_field( 'text' ) ) : ?>
		<p class="mrz-section__intro"><?php echo esc_html( get_sub_field( 'text' ) ); ?></p>
	<?php endif; ?>
	<div class="mt-8"><?php mrz_theme_button( get_sub_field( 'button' ) ); ?></div>
</div>
<?php
mrz_theme_section_close();
