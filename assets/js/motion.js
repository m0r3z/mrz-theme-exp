/**
 * MRZ Theme Experience : animations au défilement (vanilla).
 * Ajoute .is-inview à chaque élément à animer quand il entre à l'écran : la transition
 * CSS se joue alors en entier, sans dépendre de la vitesse de défilement.
 * Par défaut, l'animation se joue à l'envers quand on remonte et que l'élément repasse
 * sous la ligne de déclenchement, puis de nouveau en redescendant. Réglage « Une seule
 * fois » (window.mrzMotion.once) : une fois apparu, il reste.
 * Chargé seulement si une section a une animation (inc/builder.php), ou sur tout le site
 * quand le surlignage animé est activé (inc/setup.php).
 */
( function () {
	'use strict';

	window.mrzMotionReady = true;

	var replay = ! ( window.mrzMotion && window.mrzMotion.once );
	var selector = [
		'.mrz-motion--fade-up > .mrz-container',
		'.mrz-motion--fade > .mrz-container',
		'.mrz-motion--zoom > .mrz-container',
		'.mrz-motion--cascade :is(.mrz-cards__item, .mrz-gallery__item, .mrz-split__grid > *, .mrz-accordion__item, .mrz-prose > *)',
		// Surlignage animé du mot en couleur des titres (Customizer).
		'.mrz-highlight-animated :is(h1, h2, h3, h4, h5, h6, .mrz-section__title) em',
	].join( ',' );
	var targets = document.querySelectorAll( selector );

	if ( ! targets.length ) {
		return;
	}

	// Navigateur trop ancien : tout s'affiche.
	if ( ! ( 'IntersectionObserver' in window ) ) {
		document.documentElement.classList.remove( 'mrz-motion-js' );
		return;
	}

	/*
	 * Ligne de déclenchement : un peu au-dessus du bas de l'écran. L'élément qui la passe
	 * en descendant s'anime en entier. En remontant (il repasse dessous), l'animation se
	 * joue à l'envers, sauf réglage « Une seule fois ». Sorti par le haut, il reste visible.
	 */
	var observer = new IntersectionObserver(
		function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( entry.isIntersecting ) {
					entry.target.classList.add( 'is-inview' );
					if ( ! replay ) {
						observer.unobserve( entry.target );
					}
				} else if ( entry.boundingClientRect.top > 0 ) {
					entry.target.classList.remove( 'is-inview' );
				}
			} );
		},
		{ rootMargin: '0px 0px -10% 0px', threshold: 0.05 }
	);

	targets.forEach( function ( target ) {
		observer.observe( target );
	} );
}() );
