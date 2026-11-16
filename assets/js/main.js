/**
 * MRZ Theme Experience : JavaScript du front (vanilla, sans dépendance).
 * Header au défilement, menu mobile, sous-menus.
 */
( function () {
	'use strict';

	var body = document.body;

	/* Header : classe is-scrolled dès que la page défile. */
	var header = document.querySelector( '.mrz-header' );
	if ( header ) {
		var onScroll = function () {
			header.classList.toggle( 'is-scrolled', window.scrollY > 10 );
		};
		onScroll();
		window.addEventListener( 'scroll', onScroll, { passive: true } );
	}

	/* Menu mobile en panneau latéral. */
	var panel = document.getElementById( 'mrz-mobile-menu' );
	if ( ! panel ) {
		return;
	}

	var toggles = document.querySelectorAll( '[data-mrz-menu-toggle]' );
	var lastFocus = null;
	var closeTimer = null;

	function setExpanded( value ) {
		toggles.forEach( function ( toggle ) {
			toggle.setAttribute( 'aria-expanded', value ? 'true' : 'false' );
		} );
	}

	function openMenu() {
		clearTimeout( closeTimer );
		lastFocus = document.activeElement;
		panel.hidden = false;
		requestAnimationFrame( function () {
			panel.classList.add( 'is-open' );
		} );
		body.classList.add( 'mrz-menu-open' );
		setExpanded( true );
		var closeButton = panel.querySelector( '.mrz-offcanvas__close' );
		if ( closeButton ) {
			closeButton.focus();
		}
	}

	function closeMenu() {
		if ( panel.hidden ) {
			return;
		}
		panel.classList.remove( 'is-open' );
		body.classList.remove( 'mrz-menu-open' );
		setExpanded( false );
		closeTimer = setTimeout( function () {
			panel.hidden = true;
		}, 300 );
		if ( lastFocus ) {
			lastFocus.focus();
		}
	}

	toggles.forEach( function ( toggle ) {
		toggle.addEventListener( 'click', openMenu );
	} );

	panel.querySelectorAll( '[data-mrz-close]' ).forEach( function ( element ) {
		element.addEventListener( 'click', closeMenu );
	} );

	document.addEventListener( 'keydown', function ( event ) {
		if ( 'Escape' === event.key ) {
			closeMenu();
		}
	} );

	/* Un lien vers une ancre de la page courante ferme le menu. */
	panel.querySelectorAll( 'a' ).forEach( function ( link ) {
		link.addEventListener( 'click', function () {
			if ( link.hash && link.pathname === window.location.pathname ) {
				closeMenu();
			}
		} );
	} );

	/* Sous-menus : un bouton ouvre ou ferme chaque niveau. */
	var label = ( window.mrzTheme && window.mrzTheme.i18n && window.mrzTheme.i18n.submenu ) || 'Open submenu';
	panel.querySelectorAll( '.menu-item-has-children > a' ).forEach( function ( link, index ) {
		var submenu = link.parentElement.querySelector( ':scope > .sub-menu' );
		if ( ! submenu ) {
			return;
		}

		submenu.id = 'mrz-submenu-' + index;
		submenu.hidden = true;

		var button = document.createElement( 'button' );
		button.type = 'button';
		button.className = 'mrz-submenu-toggle';
		button.setAttribute( 'aria-expanded', 'false' );
		button.setAttribute( 'aria-controls', submenu.id );
		button.setAttribute( 'aria-label', label );
		button.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
		link.after( button );

		button.addEventListener( 'click', function () {
			var isOpen = 'true' === button.getAttribute( 'aria-expanded' );
			button.setAttribute( 'aria-expanded', isOpen ? 'false' : 'true' );
			submenu.hidden = isOpen;
		} );
	} );
}() );

/* Recherche du header : focus sur le champ à l'ouverture, fermeture par Échap ou clic ailleurs. */
( function () {
	var search = document.querySelector( '.mrz-header-search' );
	if ( ! search ) {
		return;
	}

	search.addEventListener( 'toggle', function () {
		var input = search.querySelector( 'input[type="search"]' );
		if ( search.open && input ) {
			input.focus();
		}
	} );

	document.addEventListener( 'keydown', function ( event ) {
		if ( 'Escape' === event.key && search.open ) {
			search.open = false;
			search.querySelector( 'summary' ).focus();
		}
	} );

	document.addEventListener( 'click', function ( event ) {
		if ( search.open && ! search.contains( event.target ) ) {
			search.open = false;
		}
	} );
}() );
