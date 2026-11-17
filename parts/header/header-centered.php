<?php
/**
 * Header centré : logo au-dessus, menu en dessous.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;
?>
<header class="mrz-header mrz-header--centered">
	<div class="mrz-container mrz-header__bar grid grid-cols-[1fr_auto_1fr] items-center gap-4">
		<div class="mrz-header__start"></div>
		<?php mrz_theme_logo(); ?>
		<div class="mrz-header__end flex justify-end"><?php mrz_theme_header_tools(); ?></div>
	</div>
	<?php mrz_theme_primary_nav( 'mrz-nav--centered' ); ?>
</header>
