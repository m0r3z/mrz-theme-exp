/**
 * MRZ Theme Experience : animations au défilement (vanilla).
 * Ajoute .is-inview à chaque élément à animer quand il entre à l'écran : la transition
 * CSS se joue alors en entier, sans dépendre de la vitesse de défilement.
 * Par défaut, l'animation se joue à l'envers quand on remonte et que l'élément repasse
 * sous la ligne de disparition (plus haute que celle d'apparition), puis de nouveau en
 * redescendant. Réglage « Une seule fois » (window.mrzMotion.once) : une fois apparu, il reste.
 * Chargé seulement si une section a une animation (inc/builder.php), ou sur tout le site
 * quand le surlignage animé est activé (inc/setup.php).
 */
( function () {
	'use strict';

	window.mrzMotionReady = true;

	var replay = ! ( window.mrzMotion && window.mrzMotion.once );
	// Cascade : jamais dans un accordéon (éléments et réponses), qui a sa propre animation d'ouverture.
	var selector = [
		'.mrz-motion--fade-up > .mrz-container',
		'.mrz-motion--fade > .mrz-container',
		'.mrz-motion--zoom > .mrz-container',
		'.mrz-motion--cascade :is(.mrz-cards__item, .mrz-gallery__item, .mrz-split__grid > *, .mrz-prose:not(:where(.mrz-accordion__answer)) > *)',
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
	 * Deux lignes. Apparition : l'élément qui passe la ligne basse (10 % au-dessus du bas
	 * de l'écran) en descendant s'anime en entier. Disparition, en remontant : dès que son
	 * haut repasse sous une ligne plus haute (30 % au-dessus du bas), l'animation se joue à
	 * l'envers pendant qu'il est encore bien visible. Sorti par le haut, il reste visible.
	 * Réglage « Une seule fois » : pas de disparition.
	 */
	var showObserver = new IntersectionObserver(
		function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( entry.isIntersecting ) {
					entry.target.classList.add( 'is-inview' );
					if ( ! replay ) {
						showObserver.unobserve( entry.target );
					}
				} else if ( entry.boundingClientRect.top > 0 ) {
					entry.target.classList.remove( 'is-inview' );
				}
			} );
		},
		{ rootMargin: '0px 0px -10% 0px', threshold: 0.05 }
	);

	// Ne retire la classe qu'au passage de la ligne : au chargement, un élément déjà
	// entre les deux lignes s'affiche et reste affiché.
	var aboveHideLine = new WeakMap();
	var hideObserver = replay ? new IntersectionObserver(
		function ( entries ) {
			entries.forEach( function ( entry ) {
				var wasAbove = aboveHideLine.get( entry.target );
				aboveHideLine.set( entry.target, entry.isIntersecting );
				if ( wasAbove && ! entry.isIntersecting && entry.boundingClientRect.top > 0 ) {
					entry.target.classList.remove( 'is-inview' );
				}
			} );
		},
		{ rootMargin: '0px 0px -30% 0px', threshold: 0 }
	) : null;

	targets.forEach( function ( target ) {
		showObserver.observe( target );
		if ( hideObserver ) {
			hideObserver.observe( target );
		}
	} );
}() );
