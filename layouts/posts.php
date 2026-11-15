<?php
/**
 * Layout Liste de posts : une liste du plugin MRZ Display Post.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

$mrz_list_id = (int) get_sub_field( 'list_id' );

if ( ! $mrz_list_id || ! shortcode_exists( 'mrzdpe' ) ) {
	return;
}

mrz_theme_section_open( 'posts' );
mrz_theme_section_heading( get_sub_field( 'title' ), get_sub_field( 'intro' ) );
echo do_shortcode( '[mrzdpe id="' . $mrz_list_id . '"]' );
mrz_theme_section_close();
