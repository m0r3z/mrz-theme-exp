/**
 * MRZ Theme Experience : vidéo de fond de section (vanilla, sans dépendance).
 *
 * L'image de fond reste affichée tant que la vidéo n'a pas démarré. La vidéo ne se
 * télécharge qu'une fois la page chargée, jamais sur mobile (sauf réglage), ni si le
 * visiteur limite les animations ou économise ses données. Bouton pause toujours affiché
 * pendant la lecture (RGAA 13.8). Chargé seulement par mrz_theme_section_background_video().
 */
( function () {
	'use strict';

	var i18n = window.mrzVideo || {};
	var reduceMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	var saveData = navigator.connection && navigator.connection.saveData;
	var isSmall = window.matchMedia( '(max-width: 767px)' ).matches;

	function start( video ) {
		var section = video.parentElement;
		var button = section.querySelector( ':scope > [data-mrz-video-toggle]' );
		var source = document.createElement( 'source' );
		source.src = video.getAttribute( 'data-src' );
		source.type = video.getAttribute( 'data-type' ) || 'video/mp4';
		video.appendChild( source );

		video.addEventListener( 'playing', function () {
			section.classList.add( 'is-video-playing' );
			if ( button ) {
				button.hidden = false;
			}
		} );

		if ( button ) {
			button.addEventListener( 'click', function () {
				if ( video.paused ) {
					video.play();
					button.setAttribute( 'aria-label', i18n.pause || 'Pause' );
					section.classList.remove( 'is-video-paused' );
				} else {
					video.pause();
					button.setAttribute( 'aria-label', i18n.play || 'Play' );
					section.classList.add( 'is-video-paused' );
				}
			} );
		}

		video.load();
		var playing = video.play();
		if ( playing && playing.catch ) {
			// Lecture automatique refusée par le navigateur : l'image reste affichée.
			playing.catch( function () {} );
		}
	}

	function init() {
		if ( reduceMotion || saveData ) {
			return;
		}
		document.querySelectorAll( '[data-mrz-video]' ).forEach( function ( video ) {
			if ( isSmall && ! video.hasAttribute( 'data-mobile' ) ) {
				return;
			}
			start( video );
		} );
	}

	// Après l'affichage complet de la page : la vidéo ne retarde ni l'image principale ni le reste.
	if ( 'complete' === document.readyState ) {
		init();
	} else {
		window.addEventListener( 'load', init );
	}
}() );
