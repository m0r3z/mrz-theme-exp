/**
 * MRZ Theme Experience : carrousel (vanilla, sans dépendance).
 *
 * Le navigateur fait défiler la piste (scroll-snap, glissé au doigt, flèches du
 * clavier). Ce script ajoute les commandes : précédent et suivant (par page de
 * cartes visibles, retour au début en fin de liste), points, lecture automatique
 * avec bouton pause (arrêtée au survol, au focus et si l'utilisateur limite les animations).
 * Chargé seulement par mrz_theme_carousel_open().
 */
( function () {
	'use strict';

	var i18n = window.mrzCarousel || {};
	var reduceMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' );

	function format( text, first, second ) {
		return String( text || '' ).replace( '%1$s', first ).replace( '%2$s', second );
	}

	function init( root ) {
		var track = root.querySelector( '.mrz-carousel__track' );
		var slides = track ? track.children : [];
		if ( ! track || ! slides.length ) {
			return;
		}

		var dotsBox = root.querySelector( '[data-mrz-carousel-dots]' );
		var status = root.querySelector( '[data-mrz-carousel-status]' );
		var pauseButton = root.querySelector( '[data-mrz-carousel-pause]' );
		var delay = ( parseInt( root.getAttribute( 'data-autoplay' ), 10 ) || 0 ) * 1000;
		var announce = false;
		var timer = null;
		var userPaused = reduceMotion.matches;
		var hovered = false;
		var focused = false;

		/* Distance entre deux cartes (largeur + écart). */
		function step() {
			return slides.length > 1 ? slides[ 1 ].offsetLeft - slides[ 0 ].offsetLeft : track.clientWidth;
		}

		function maxScroll() {
			return track.scrollWidth - track.clientWidth;
		}

		/* Une page = les cartes entièrement visibles (au moins une). */
		function pageWidth() {
			var gap = step() - slides[ 0 ].offsetWidth;
			return Math.max( 1, Math.floor( ( track.clientWidth + gap + 1 ) / step() ) ) * step();
		}

		function pageCount() {
			return maxScroll() <= 1 ? 1 : Math.ceil( ( maxScroll() - 1 ) / pageWidth() ) + 1;
		}

		function atEnd() {
			return track.scrollLeft >= maxScroll() - 2;
		}

		function currentPage() {
			return atEnd() ? pageCount() - 1 : Math.round( track.scrollLeft / pageWidth() );
		}

		function goTo( left, withAnnounce ) {
			announce = withAnnounce;
			track.scrollTo( {
				left: Math.max( 0, Math.min( left, maxScroll() ) ),
				behavior: reduceMotion.matches ? 'auto' : 'smooth',
			} );
		}

		function next( withAnnounce ) {
			goTo( atEnd() ? 0 : track.scrollLeft + pageWidth(), withAnnounce );
		}

		function prev( withAnnounce ) {
			goTo( track.scrollLeft <= 2 ? maxScroll() : track.scrollLeft - pageWidth(), withAnnounce );
		}

		function buildDots() {
			if ( ! dotsBox ) {
				return;
			}
			var count = pageCount();
			dotsBox.textContent = '';
			if ( count < 2 ) {
				return;
			}
			for ( var index = 0; index < count; index++ ) {
				var dot = document.createElement( 'button' );
				dot.type = 'button';
				dot.className = 'mrz-carousel__dot';
				dot.setAttribute( 'aria-label', format( i18n.page, index + 1, count ) );
				dot.addEventListener( 'click', goToPage.bind( null, index, count ) );
				dotsBox.appendChild( dot );
			}
		}

		function goToPage( index, count ) {
			goTo( index === count - 1 ? maxScroll() : index * pageWidth(), true );
		}

		/* Après un défilement : point actif, et annonce de la position si une commande l'a demandé. */
		function update() {
			if ( dotsBox ) {
				var page = currentPage();
				Array.prototype.forEach.call( dotsBox.children, function ( dot, index ) {
					if ( index === page ) {
						dot.setAttribute( 'aria-current', 'true' );
					} else {
						dot.removeAttribute( 'aria-current' );
					}
				} );
			}
			if ( announce && status ) {
				var first = Math.min( slides.length, Math.round( track.scrollLeft / step() ) + 1 );
				status.textContent = format( i18n.position, first, slides.length );
			}
			announce = false;
		}

		function refresh() {
			var scrollable = maxScroll() > 1;
			root.classList.toggle( 'is-scrollable', scrollable );
			root.classList.toggle( 'is-static', ! scrollable );
			buildDots();
			update();
			schedule();
		}

		/* Lecture automatique : jamais au survol, au focus, page cachée ou animations limitées. */
		function schedule() {
			clearInterval( timer );
			timer = null;
			if ( delay && ! userPaused && ! hovered && ! focused && ! document.hidden && maxScroll() > 1 ) {
				timer = setInterval( function () {
					next( false );
				}, delay );
			}
		}

		function setPaused( value ) {
			userPaused = value;
			root.classList.toggle( 'is-paused', value );
			if ( pauseButton ) {
				pauseButton.setAttribute( 'aria-label', value ? i18n.play : i18n.pause );
			}
			schedule();
		}

		var buttonNext = root.querySelector( '[data-mrz-carousel-next]' );
		var buttonPrev = root.querySelector( '[data-mrz-carousel-prev]' );
		if ( buttonNext ) {
			buttonNext.addEventListener( 'click', function () {
				next( true );
			} );
		}
		if ( buttonPrev ) {
			buttonPrev.addEventListener( 'click', function () {
				prev( true );
			} );
		}

		if ( pauseButton ) {
			setPaused( userPaused );
			pauseButton.addEventListener( 'click', function () {
				// Relancer depuis le bouton : la lecture reprend malgré le focus sur le bouton.
				focused = false;
				setPaused( ! userPaused );
			} );
		}

		root.addEventListener( 'mouseenter', function () {
			hovered = true;
			schedule();
		} );
		root.addEventListener( 'mouseleave', function () {
			hovered = false;
			schedule();
		} );
		root.addEventListener( 'focusin', function ( event ) {
			if ( event.target !== pauseButton ) {
				focused = true;
				schedule();
			}
		} );
		root.addEventListener( 'focusout', function ( event ) {
			if ( ! root.contains( event.relatedTarget ) ) {
				focused = false;
				schedule();
			}
		} );
		document.addEventListener( 'visibilitychange', schedule );

		var scrollTimer = null;
		track.addEventListener( 'scroll', function () {
			clearTimeout( scrollTimer );
			scrollTimer = setTimeout( update, 120 );
		}, { passive: true } );

		if ( 'ResizeObserver' in window ) {
			new ResizeObserver( refresh ).observe( track );
		} else {
			window.addEventListener( 'resize', refresh );
		}
		refresh();
	}

	document.querySelectorAll( '[data-mrz-carousel]' ).forEach( init );
}() );
