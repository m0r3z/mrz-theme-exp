/**
 * MRZ Theme Experience : bandeau défilant (vanilla). Le défilement est en CSS ;
 * ce script ne gère que le bouton pause (RGAA 13.8). Chargé seulement par mrz_marquee.
 */
( function () {
	'use strict';

	var i18n = window.mrzMarquee || {};

	document.querySelectorAll( '[data-mrz-marquee]' ).forEach( function ( marquee ) {
		var button = marquee.querySelector( '[data-mrz-marquee-pause]' );
		if ( ! button ) {
			return;
		}
		button.addEventListener( 'click', function () {
			var paused = marquee.classList.toggle( 'is-paused' );
			button.setAttribute( 'aria-label', paused ? i18n.play || 'Play' : i18n.pause || 'Pause' );
		} );
	} );
}() );
