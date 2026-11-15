<?php
/**
 * Article seul et CPT : parts/content/single-{post_type}.php, sinon single.php.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) {
	the_post();
	get_template_part( 'parts/content/single', get_post_type() );
}

get_footer();
