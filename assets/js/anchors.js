/**
 * MRZ Theme Experience : sous-menu d'ancres (vanilla).
 * Met en évidence le lien de la section affichée (aria-current) et donne la hauteur
 * de la barre collante au CSS (--mrz-anchors-height), pour que les ancres s'arrêtent
 * dessous. Chargé seulement par la section mrz_anchors.
 */
( function () {
	'use strict';

	document.querySelectorAll( '[data-mrz-anchors]' ).forEach( function ( nav ) {
		var links = Array.prototype.slice.call( nav.querySelectorAll( 'a[href^="#"]' ) );
		var targets = links.map( function ( link ) {
			return document.getElementById( decodeURIComponent( link.hash.slice( 1 ) ) );
		} );
		var bar = nav.closest( '.mrz-anchors--sticky' );

		function setHeight() {
			if ( bar ) {
				document.documentElement.style.setProperty( '--mrz-anchors-height', bar.offsetHeight + 'px' );
			}
		}

		function update() {
			// Section active : la dernière dont le haut a passé le bas de la barre (plus une marge).
			var limit = ( bar ? bar.getBoundingClientRect().bottom : 0 ) + window.innerHeight * 0.25;
			var active = -1;
			targets.forEach( function ( target, index ) {
				if ( target && target.getBoundingClientRect().top <= limit ) {
					active = index;
				}
			} );
			links.forEach( function ( link, index ) {
				if ( index === active ) {
					link.setAttribute( 'aria-current', 'location' );
				} else {
					link.removeAttribute( 'aria-current' );
				}
			} );
			// Sur mobile, la barre défile : le lien actif reste visible.
			if ( active > -1 && nav.scrollWidth > nav.clientWidth ) {
				var link = links[ active ];
				nav.scrollLeft = link.offsetLeft - ( nav.clientWidth - link.offsetWidth ) / 2;
			}
		}

		var pending = false;
		window.addEventListener( 'scroll', function () {
			if ( ! pending ) {
				pending = true;
				window.requestAnimationFrame( function () {
					pending = false;
					update();
				} );
			}
		}, { passive: true } );

		if ( bar && 'ResizeObserver' in window ) {
			new ResizeObserver( setHeight ).observe( bar );
		} else {
			setHeight();
		}
		update();
	} );
}() );
