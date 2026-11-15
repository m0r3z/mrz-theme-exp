<?php
/**
 * Layout Carte : une carte du plugin MRZ Maps.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

$mrz_map_id = (int) get_sub_field( 'map_id' );

if ( ! $mrz_map_id || ! shortcode_exists( 'mrzme' ) ) {
	return;
}

mrz_theme_section_open( 'map' );
mrz_theme_section_heading( get_sub_field( 'title' ), get_sub_field( 'intro' ) );
echo do_shortcode( '[mrzme id="' . $mrz_map_id . '"]' );
mrz_theme_section_close();
