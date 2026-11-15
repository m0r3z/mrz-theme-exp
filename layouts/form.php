<?php
/**
 * Layout Formulaire Gravity Forms.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

$mrz_form_id = (int) get_sub_field( 'form_id' );

if ( ! $mrz_form_id || ! function_exists( 'gravity_form' ) ) {
	return;
}

mrz_theme_section_open( 'form' );
mrz_theme_section_heading( get_sub_field( 'title' ), get_sub_field( 'intro' ) );
?>
<div class="mx-auto max-w-3xl">
	<?php gravity_form( $mrz_form_id, false, false, false, null, true ); ?>
</div>
<?php
mrz_theme_section_close();
