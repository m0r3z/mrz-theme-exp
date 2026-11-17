<?php
/**
 * Header « logo centré » : menu principal à gauche, logo au centre, menu secondaire
 * et outils à droite. Sur mobile, le menu passe dans le burger : logo à gauche, outils à droite.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;
?>
<header class="mrz-header mrz-header--split">
	<div class="mrz-container mrz-header__bar grid grid-cols-[1fr_auto_1fr] items-center gap-8">
		<div class="mrz-header__start"><?php mrz_theme_primary_nav(); ?></div>
		<?php mrz_theme_logo(); ?>
		<div class="mrz-header__end flex justify-end"><?php mrz_theme_header_tools(); ?></div>
	</div>
</header>
