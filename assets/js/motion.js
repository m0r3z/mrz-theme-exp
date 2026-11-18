/**
 * MRZ Theme Experience : animations au défilement (vanilla).
 * Ajoute .is-inview à chaque élément à animer quand il entre à l'écran : la transition
 * CSS se joue alors en entier, sans dépendre de la vitesse de défilement.
 * Par défaut, l'élément se cache de nouveau quand il ressort par le bas de l'écran
 * (on remonte), et se rejoue au prochain passage. Réglage « Une seule fois »
 * (window.mrzMotion.once) : une fois apparu, il reste.
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

	// Entrée : déclenchée quand un peu de l'élément est passé au-dessus du bas de l'écran.
	var show = new IntersectionObserver(
		function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( entry.isIntersecting ) {
					entry.target.classList.add( 'is-inview' );
					if ( ! replay ) {
						show.unobserve( entry.target );
					}
				}
			} );
		},
		{ rootMargin: '0px 0px -10% 0px', threshold: 0.05 }
	);

	// Sortie (sauf réglage « Une seule fois ») : l'élément entièrement ressorti par le bas, donc quand
	// on remonte, est prêt à se rejouer. Il ne se cache jamais sous les yeux du visiteur ;
	// sorti par le haut, il reste visible.
	var hide = replay ? new IntersectionObserver( function ( entries ) {
		entries.forEach( function ( entry ) {
			if ( ! entry.isIntersecting && entry.boundingClientRect.top > 0 ) {
				entry.target.classList.remove( 'is-inview' );
			}
		} );
	} ) : null;

	targets.forEach( function ( target ) {
		show.observe( target );
		if ( hide ) {
			hide.observe( target );
		}
	} );
}() );
