<?php
/**
 * Header « menu à gauche » : logo, menu juste après, outils à droite.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;
?>
<header class="mrz-header mrz-header--left">
	<div class="mrz-container mrz-header__bar flex items-center gap-8">
		<?php mrz_theme_logo(); ?>
		<div class="mrz-header__start flex-1"><?php mrz_theme_primary_nav(); ?></div>
		<div class="mrz-header__end flex items-center gap-6"><?php mrz_theme_header_tools(); ?></div>
	</div>
</header>
