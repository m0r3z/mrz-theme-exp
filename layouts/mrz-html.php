<?php
/**
 * Section du parent « HTML brut » : code affiché tel quel, sans section ni conteneur.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

echo get_sub_field( 'code', false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML brut saisi par un administrateur (mrz_theme_can_edit_raw_html()).
