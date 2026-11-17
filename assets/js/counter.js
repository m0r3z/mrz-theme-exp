/**
 * MRZ Theme Experience : compteur des chiffres clés (vanilla).
 * La valeur finale est déjà dans la page ; ce script la fait monter de 0 quand la carte
 * arrive à l'écran. Il garde le reste du texte (« 13 M€ », « +50 », « 98 % ») et le
 * séparateur des milliers. Rien si le visiteur limite les animations.
 * Les lecteurs d'écran ne lisent que la valeur finale.
 * Chargé seulement par les Cartes en style « Chiffres clés » avec le compteur.
 */
( function () {
	'use strict';

	if ( window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches || ! ( 'IntersectionObserver' in window ) ) {
		return;
	}

	var duration = 1600;
	var numberPattern = /\d[\d\s  .,]*\d|\d/;

	/* Premier nœud de texte qui contient un nombre (le titre peut contenir un <em>). */
	function findTextNode( element ) {
		var walker = document.createTreeWalker( element, NodeFilter.SHOW_TEXT );
		while ( walker.nextNode() ) {
			if ( numberPattern.test( walker.currentNode.nodeValue ) ) {
				return walker.currentNode;
			}
		}
		return null;
	}

	function prepare( element ) {
		var node = findTextNode( element );
		if ( ! node ) {
			return null;
		}
		var text = node.nodeValue;
		var match = text.match( numberPattern );
		var raw = match[ 0 ];
		// Décimales : une virgule ou un point suivi de 1 ou 2 chiffres en fin de nombre.
		var decimalMatch = raw.match( /[.,](\d{1,2})$/ );
		var decimals = decimalMatch ? decimalMatch[ 1 ].length : 0;
		var decimalMark = decimalMatch ? raw.charAt( raw.length - decimals - 1 ) : ',';
		var thousands = ( raw.match( /[\s  ]|[.,](?=\d{3})/ ) || [ '' ] )[ 0 ];
		var digits = raw.replace( /[^\d]/g, '' );
		var value = parseInt( digits, 10 ) / Math.pow( 10, decimals );

		return {
			node: node,
			before: text.slice( 0, match.index ),
			after: text.slice( match.index + raw.length ),
			value: value,
			decimals: decimals,
			decimalMark: decimalMark,
			thousands: thousands,
		};
	}

	function format( number, item ) {
		var parts = number.toFixed( item.decimals ).split( '.' );
		var integer = parts[ 0 ].replace( /\B(?=(\d{3})+(?!\d))/g, item.thousands );
		return integer + ( item.decimals ? item.decimalMark + parts[ 1 ] : '' );
	}

	function run( item ) {
		var start = null;
		function frame( time ) {
			if ( null === start ) {
				start = time;
			}
			var progress = Math.min( 1, ( time - start ) / duration );
			var eased = 1 - Math.pow( 1 - progress, 3 );
			item.node.nodeValue = item.before + format( item.value * eased, item ) + item.after;
			if ( progress < 1 ) {
				window.requestAnimationFrame( frame );
			}
		}
		window.requestAnimationFrame( frame );
	}

	var observer = new IntersectionObserver( function ( entries ) {
		entries.forEach( function ( entry ) {
			if ( entry.isIntersecting ) {
				observer.unobserve( entry.target );
				run( entry.target.mrzCount );
			}
		} );
	}, { threshold: 0.6 } );

	/*
	 * Lecteurs d'écran : ils lisent la valeur finale, jamais les valeurs intermédiaires.
	 * Le contenu animé passe en aria-hidden, la valeur finale est copiée en texte masqué.
	 */
	function shield( element ) {
		var visible = document.createElement( 'span' );
		visible.setAttribute( 'aria-hidden', 'true' );
		while ( element.firstChild ) {
			visible.appendChild( element.firstChild );
		}
		var spoken = document.createElement( 'span' );
		spoken.className = 'screen-reader-text';
		spoken.textContent = visible.textContent;
		element.append( visible, spoken );
	}

	document.querySelectorAll( '[data-mrz-count]' ).forEach( function ( element ) {
		var item = prepare( element );
		if ( item ) {
			shield( element );
			element.mrzCount = item;
			observer.observe( element );
		}
	} );
}() );
