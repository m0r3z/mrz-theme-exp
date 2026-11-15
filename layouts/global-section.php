<?php
/**
 * Layout Section globale : insère une section globale à cet endroit.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

mrz_theme_render_global_section( (int) get_sub_field( 'section' ) );
