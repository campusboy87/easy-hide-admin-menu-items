/**
 * EHAMI — Easy Hide Admin Menu Items.
 *
 * Single consolidated script with isolated IIFE sections.
 * Each section is self-contained and exits early if its DOM anchors are missing.
 *
 * Sections:
 *   1. Helpers        — shared AJAX helper (ehami.post)
 *   2. User Interface — hide icons, toggle switch, admin bar dropdown
 *   3. Submenu Toggle — instant submenu icon injection on checkbox change
 *   4. Menu Sorting   — drag-and-drop reordering via SortableJS
 *   5. Sidebar Resize — drag handle on sidebar edge
 *   6. Separators     — live CSS preview for separator modes
 *   7. Settings Page  — form handling, notices, reset buttons
 *   8. Video Tooltips — auto-playing video hints on settings labels
 *
 * @since 1.5.0
 */


/* =========================================================================
   1. HELPERS — shared AJAX helper
   ========================================================================= */

(function () {
	'use strict';

	/**
	 * POST to wp-admin/admin-ajax.php.
	 *
	 * Replicates jQuery.post() serialization so that PHP receives
	 * nested arrays, e.g. $_POST['options']['key'] = 'value'.
	 *
	 * @param {string}   action  Full AJAX action name (e.g. 'ehami_save_settings').
	 * @param {Object}   data    Flat or nested object to send.
	 * @param {Function} onDone  Called on HTTP 2xx (optional).
	 * @param {Function} onFail  Called on HTTP error (optional).
	 */
	ehami.post = function ( action, data, onDone, onFail ) {
		var body = 'action=' + encodeURIComponent( action )
		         + '&_ajax_nonce=' + encodeURIComponent( ehami.nonce );

		if ( data ) {
			body += '&' + serialize( data );
		}

		var xhr = new XMLHttpRequest();
		xhr.open( 'POST', window.ajaxurl );
		xhr.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );

		xhr.onload = function () {
			if ( xhr.status >= 200 && xhr.status < 300 ) {
				if ( onDone ) onDone( xhr );
			} else {
				if ( onFail ) onFail( xhr );
			}
		};

		xhr.onerror = function () {
			if ( onFail ) onFail( xhr );
		};

		xhr.send( body );
	};

	/**
	 * Serialize a nested object to a URL-encoded query string.
	 *
	 * Example:
	 *   { options: { key: 'val' } }  →  "options[key]=val"
	 *   { order: { '#id': 0 } }      →  "order[%23id]=0"
	 *
	 * @param {Object} obj
	 * @param {string} prefix  Used internally for recursion.
	 * @return {string}
	 */
	function serialize( obj, prefix ) {
		var parts = [];

		for ( var key in obj ) {
			if ( ! obj.hasOwnProperty( key ) ) continue;

			var name = prefix ? prefix + '[' + key + ']' : key;

			if ( typeof obj[ key ] === 'object' && obj[ key ] !== null ) {
				parts.push( serialize( obj[ key ], name ) );
			} else {
				parts.push( encodeURIComponent( name ) + '=' + encodeURIComponent( obj[ key ] ) );
			}
		}

		return parts.join( '&' );
	}

})();


/* =========================================================================
   2. USER INTERFACE — hide icons, toggle switch, admin bar dropdown
   ========================================================================= */

(function () {
	'use strict';

	var sidebar    = document.getElementById( 'adminmenu' );
	var barSwitch  = document.getElementById( 'wp-admin-bar-ehami-switch' );

	if ( ! sidebar || ! barSwitch ) {
		return;
	}

	var btnSwitch    = document.getElementById( 'switch-flat' );
	var dropdownBox  = barSwitch.querySelector( '.switch__content' );

	/* =====================================================================
	   Hide Icons — top-level and submenu items
	   ===================================================================== */

	/**
	 * Create a Dashicons "hidden" icon element.
	 *
	 * @param {string} className  'ehami-remove-li' or 'ehami-remove-sub-li'
	 * @return {HTMLSpanElement}
	 */
	function createHideIcon( className ) {
		var span       = document.createElement( 'span' );
		span.title     = ehami.hid;
		span.className = 'dashicons dashicons-hidden ' + className;
		return span;
	}

	// Inject hide icon into every top-level menu item.
	sidebar.querySelectorAll( 'li.menu-top' ).forEach( function ( li ) {
		li.insertBefore( createHideIcon( 'ehami-remove-li' ), li.firstChild );
	});

	// Inject submenu hide icons when the option is enabled.
	if ( ehami.hide_submenu ) {
		sidebar.querySelectorAll( '.wp-submenu > li' ).forEach( function ( li ) {
			li.insertBefore( createHideIcon( 'ehami-remove-sub-li' ), li.firstChild );
		});
	}

	/* =====================================================================
	   Click Handler — hide icon → admin bar
	   ===================================================================== */

	// Delegated click on sidebar: catches both top-level and submenu icons.
	sidebar.addEventListener( 'click', function ( e ) {
		var icon = e.target.closest( '.ehami-remove-li, .ehami-remove-sub-li' );
		if ( ! icon ) return;

		e.preventDefault();
		e.stopPropagation();
		addItemToAdminBar( icon.closest( 'li' ) );
	});

	/**
	 * Hide a sidebar menu item and add it to the admin bar dropdown.
	 *
	 * @param {HTMLElement} li  The <li> that was clicked.
	 */
	function addItemToAdminBar( li ) {
		var isSubmenu = !! li.closest( '.wp-submenu' );
		var id, text;

		if ( isSubmenu ) {
			// Submenu: id = href, text = "Parent > Child".
			var link = li.querySelector( 'a' );
			id = link ? link.getAttribute( 'href' ) : '';

			var parentLi   = li.closest( '.wp-has-submenu' );
			var parentName = parentLi ? parentLi.querySelector( '.wp-menu-name' ) : null;
			var parentText = '';

			if ( parentName ) {
				var clone = parentName.cloneNode( true );
				clone.querySelectorAll( 'span' ).forEach( function ( s ) { s.remove(); });
				parentText = clone.textContent.trim();
			}

			text = parentText + ' > ' + li.textContent.trim();
		} else {
			// Top-level: id = #element-id, text = menu name.
			id = '#' + li.id;
			var menuName = li.querySelector( '.wp-menu-name' );
			text = menuName ? menuName.textContent.trim() : li.textContent.trim();
		}

		// Build admin bar entry.
		var entry = document.createElement( 'p' );
		entry.setAttribute( 'data-id', id );
		entry.innerHTML = '<span class="text">' + text + '</span>'
		                + ' <span class="dashicons dashicons-no ehami-restore-li"></span>';

		dropdownBox.insertBefore( entry, dropdownBox.firstChild );

		// Animate the menu item flying toward the admin bar toggle.
		flyToBar( li );
		updateCounter( 'plus' );

		ehami.post( 'ehami_add_item', {
			options: { item: { id: id, text: text } }
		});
	}

	/* =====================================================================
	   Fly Animation — menu item → admin bar
	   ===================================================================== */

	/**
	 * Animate a menu item flying from the sidebar toward the admin bar toggle.
	 * Replicates the old jQuery UI "transfer" effect: a visible outline box
	 * morphs from the source position/size to the target position/size.
	 *
	 * @param {HTMLElement} li  The sidebar <li> being hidden.
	 */
	function flyToBar( li ) {
		var liRect  = li.getBoundingClientRect();
		var barRect = barSwitch.getBoundingClientRect();

		// Create the transfer outline.
		var outline       = document.createElement( 'div' );
		outline.className = 'ehami-fly-clone';

		// Start at the menu item position.
		outline.style.left   = liRect.left + 'px';
		outline.style.top    = liRect.top + 'px';
		outline.style.width  = liRect.width + 'px';
		outline.style.height = liRect.height + 'px';

		document.body.appendChild( outline );

		// Force layout so the browser registers the start position.
		outline.offsetHeight;

		// Animate to the admin bar toggle position.
		outline.classList.add( '--animating' );
		outline.style.left   = barRect.left + 'px';
		outline.style.top    = barRect.top + 'px';
		outline.style.width  = barRect.width + 'px';
		outline.style.height = barRect.height + 'px';

		// Fade out the original menu item during the flight.
		li.style.transition = 'opacity 0.3s ease';
		li.style.opacity    = '0';

		// Cleanup after animation.
		outline.addEventListener( 'transitionend', function onEnd( e ) {
			if ( e.propertyName !== 'left' ) return;
			outline.removeEventListener( 'transitionend', onEnd );
			outline.remove();

			li.style.display    = 'none';
			li.style.transition = '';
			li.style.opacity    = '';
		});

		// Fallback cleanup if transitionend doesn't fire.
		setTimeout( function () {
			outline.remove();
			li.style.display    = 'none';
			li.style.transition = '';
			li.style.opacity    = '';
		}, 600 );

		// Flash the dropdown so user sees the new entry.
		flashDropdown();
	}

	/* =====================================================================
	   Restore Item — admin bar → sidebar
	   ===================================================================== */

	// Delegated click inside the dropdown: restore button or item navigation.
	dropdownBox.addEventListener( 'click', function ( e ) {

		// Restore button click.
		if ( e.target.closest( '.ehami-restore-li' ) ) {
			e.preventDefault();
			restoreItem( e.target.closest( 'p' ) );
			return;
		}

		// Item text click → navigate to that admin page.
		var entry = e.target.closest( 'p[data-id]' );
		if ( ! entry ) return;

		e.preventDefault();
		var dataId = entry.getAttribute( 'data-id' );

		if ( dataId.indexOf( '#' ) !== -1 ) {
			var target = document.querySelector( dataId );
			var link   = target ? target.querySelector( 'a' ) : null;
			if ( link ) link.click();
		} else {
			window.location.href = dataId;
		}
	});

	/**
	 * Restore a previously hidden menu item back to the sidebar.
	 *
	 * @param {HTMLElement} entry  The <p data-id="…"> element in the dropdown.
	 */
	function restoreItem( entry ) {
		var id = entry.getAttribute( 'data-id' );

		// Fade out and remove the dropdown entry.
		entry.style.transition = 'opacity 0.15s ease';
		entry.style.opacity    = '0';
		setTimeout( function () { entry.remove(); }, 150 );

		// Remove the PHP-injected CSS rule that hides this item.
		removeCssRule( id );

		// Show the corresponding sidebar element.
		if ( id.indexOf( '.php' ) !== -1 ) {
			sidebar.querySelectorAll( 'a[href^="' + id + '"]' ).forEach( function ( a ) {
				showElement( a.closest( 'li' ) );
			});
		} else {
			var el = sidebar.querySelector( id );
			if ( el ) {
				showElement( el );
				showElement( el.closest( 'li' ) );
			}
		}

		updateCounter( 'minus' );
		ehami.post( 'ehami_remove_item', { options: { id: id } } );
	}

	/**
	 * Remove the display:none CSS rule for a given item from the inline style tag.
	 * PHP injects rules like:
	 *   .ehami-enable #adminmenu .wp-submenu a[href="edit.php"] { display: none; }
	 *   .ehami-enable #adminmenu #menu-posts { display: none; }
	 *
	 * @param {string} id  The item identifier (href or CSS selector).
	 */
	function removeCssRule( id ) {
		var styleEl = document.getElementById( 'ehami-style-inline-css' );
		if ( ! styleEl ) return;

		// Escape special regex characters in the id.
		var escaped = id.replace( /[.*+?^${}()|[\]\\]/g, '\\$&' );

		// Build a pattern that matches the full CSS rule containing this id.
		var pattern = new RegExp(
			'\\.ehami-enable\\s+#adminmenu[^{]*' + escaped + '[^}]*\\{[^}]*\\}',
			'g'
		);

		styleEl.textContent = styleEl.textContent.replace( pattern, '' );
	}

	/**
	 * Make a hidden element visible again.
	 *
	 * @param {HTMLElement|null} el
	 */
	function showElement( el ) {
		if ( ! el ) return;
		el.style.display = '';
		el.style.opacity = '1';
	}

	/* =====================================================================
	   Toggle Switch — show/hide all hidden items
	   ===================================================================== */

	if ( btnSwitch ) {
		btnSwitch.addEventListener( 'click', function () {
			toggleHiddenItems( this.checked );
			ehami.post( 'ehami_save_status', { options: { status: this.checked ? 1 : 0 } } );
		});
	}

	/**
	 * Show or hide all items that are listed in the admin bar dropdown.
	 *
	 * @param {boolean} shouldHide  True = hide items, false = restore them.
	 */
	function toggleHiddenItems( shouldHide ) {
		dropdownBox.querySelectorAll( 'p[data-id]' ).forEach( function ( entry ) {
			var id = entry.getAttribute( 'data-id' );

			if ( id.indexOf( '.php' ) !== -1 ) {
				sidebar.querySelectorAll( 'a[href="' + id + '"]' ).forEach( function ( a ) {
					var li = a.closest( 'li' );
					if ( li && ! li.classList.contains( 'wp-has-submenu' ) ) {
						li.style.display = shouldHide ? 'none' : '';
					}
				});
			} else {
				var target = sidebar.querySelector( id );
				if ( target ) {
					target.style.display = shouldHide ? 'none' : '';
					var li = target.closest( 'li' );
					if ( li ) li.style.display = shouldHide ? 'none' : '';
				}
			}
		});

		document.body.classList.toggle( 'ehami-enable', shouldHide );
	}

	/* =====================================================================
	   Admin Bar Dropdown — hover open/close
	   ===================================================================== */

	barSwitch.addEventListener( 'mouseenter', function () {
		dropdownBox.classList.add( '--open' );
	});

	barSwitch.addEventListener( 'mouseleave', function () {
		dropdownBox.classList.remove( '--open' );
	});

	/**
	 * Briefly flash the dropdown open (feedback after hiding an item).
	 */
	function flashDropdown() {
		dropdownBox.classList.add( '--open' );

		setTimeout( function () {
			dropdownBox.classList.remove( '--open' );
		}, 800 );
	}

	/* =====================================================================
	   Counter — hidden items count
	   ===================================================================== */

	/**
	 * Update the counter and manage the "no items" placeholder.
	 *
	 * @param {string} direction  'plus' or 'minus'.
	 */
	function updateCounter( direction ) {
		var count = parseInt( ehami.count_items, 10 );
		ehami.count_items = ( direction === 'plus' ) ? ++count : --count;

		if ( ehami.count_items === 0 && btnSwitch ) {
			btnSwitch.checked = false;

			if ( ! dropdownBox.querySelector( '.no-items' ) ) {
				var p       = document.createElement( 'p' );
				p.className = 'no-items';
				p.textContent = ehami.no_items;
				dropdownBox.insertBefore( p, dropdownBox.firstChild );
			}
		} else {
			var noItems = dropdownBox.querySelector( '.no-items' );
			if ( noItems ) noItems.remove();
		}

		if ( direction === 'plus' && btnSwitch ) {
			btnSwitch.checked = true;
		}
	}

	/* =====================================================================
	   "Don't display hide icons" checkbox
	   ===================================================================== */

	(function () {
		var checkbox = document.getElementById( 'hide-icons-checkbox' );
		if ( ! checkbox ) return;

		function toggle() {
			var hidden   = checkbox.checked;
			var selector = '.ehami-remove-li, .ehami-remove-sub-li, span.ehami-sort-handle';

			document.querySelectorAll( selector ).forEach( function ( el ) {
				el.style.display = hidden ? 'none' : '';
			});

			document.body.classList.toggle( 'ehami-icons-hidden', hidden );
		}

		checkbox.addEventListener( 'change', function () {
			toggle();
			ehami.post( 'ehami_hide_icons_disable', {
				options: { hide_icons_disable: checkbox.checked ? 1 : 0 }
			});
		});

		// Apply initial state.
		toggle();
	})();

})();


/* =========================================================================
   3. SUBMENU TOGGLE — instant submenu icon injection on checkbox change
   ========================================================================= */

(function () {
	'use strict';

	var ICON_CLASS = 'ehami-remove-sub-li';
	var ICON_SELECTOR = '.' + ICON_CLASS;
	var CHECKBOX_ID = 'ehami_hide_submenu';

	var checkbox = document.getElementById( CHECKBOX_ID );
	if ( ! checkbox ) {
		return;
	}

	var title = ( typeof ehami !== 'undefined' && ehami.hid ) ? ehami.hid : 'Hide menu';

	/**
	 * Build a hide-icon element.
	 *
	 * @return {HTMLSpanElement}
	 */
	function createIcon() {
		var span = document.createElement( 'span' );
		span.title = title;
		span.className = 'dashicons dashicons-hidden ' + ICON_CLASS;
		return span;
	}

	/**
	 * Insert hide-icons into all submenu items that don't have one yet.
	 */
	function insertIcons() {
		var items = document.querySelectorAll( '#adminmenu .wp-submenu > li' );

		for ( var i = 0; i < items.length; i++ ) {
			if ( ! items[ i ].querySelector( ICON_SELECTOR ) ) {
				items[ i ].insertBefore( createIcon(), items[ i ].firstChild );
			}
		}
	}

	/**
	 * Remove all hide-icons from submenu items.
	 */
	function removeIcons() {
		var icons = document.querySelectorAll( '#adminmenu ' + ICON_SELECTOR );

		for ( var i = 0; i < icons.length; i++ ) {
			icons[ i ].parentNode.removeChild( icons[ i ] );
		}
	}

	/**
	 * Sync DOM with checkbox state.
	 */
	function sync() {
		if ( checkbox.checked ) {
			insertIcons();
		} else {
			removeIcons();
		}
	}

	checkbox.addEventListener( 'change', sync );
})();


/* =========================================================================
   4. MENU SORTING — drag-and-drop reordering via SortableJS
   ========================================================================= */

(function () {
	'use strict';

	var sidebar = document.getElementById( 'adminmenu' );
	if ( ! sidebar ) return;

	/* =====================================================================
	   Original DOM Order — snapshot before any reordering
	   ===================================================================== */

	var originalOrder = [];
	var children = sidebar.querySelectorAll( ':scope > li' );

	children.forEach( function ( li ) {
		originalOrder.push( li );
	});

	/**
	 * Restore the original WordPress menu order in the DOM.
	 * Called by the "Reset menu order" button in settings-page.js.
	 */
	window.ehamiResetMenuDom = function () {
		originalOrder.forEach( function ( li ) {
			sidebar.appendChild( li );
		});
	};

	/* =====================================================================
	   Restore Saved Order
	   ===================================================================== */

	var savedOrder = ehami.menu_order;

	if ( savedOrder && typeof savedOrder === 'object' ) {
		var hasEntries = false;

		for ( var k in savedOrder ) {
			if ( savedOrder.hasOwnProperty( k ) ) {
				hasEntries = true;
				break;
			}
		}

		if ( hasEntries ) {
			var items = Array.from( sidebar.querySelectorAll( ':scope > li' ) );

			// Assign each item a sort position: saved position or current DOM index.
			items.forEach( function ( li, index ) {
				var key = li.id ? '#' + li.id : null;
				var pos = ( key && savedOrder[ key ] !== undefined ) ? savedOrder[ key ] : index;
				li.setAttribute( 'data-ehami-pos', pos );
			});

			items.sort( function ( a, b ) {
				return parseInt( a.getAttribute( 'data-ehami-pos' ), 10 )
				     - parseInt( b.getAttribute( 'data-ehami-pos' ), 10 );
			});

			items.forEach( function ( li ) {
				sidebar.appendChild( li );
				li.removeAttribute( 'data-ehami-pos' );
			});
		}
	}

	/* =====================================================================
	   SortableJS — drag handles and instance management
	   ===================================================================== */

	var sortableInstance = null;

	/**
	 * Initialize drag-and-drop sorting.
	 * Adds handle icons and creates the SortableJS instance.
	 */
	function initSorting() {
		if ( ! ehami.can_customize || sortableInstance ) return;

		// Add drag handle to each top-level menu item.
		sidebar.querySelectorAll( 'li.menu-top' ).forEach( function ( li ) {
			if ( li.querySelector( '.ehami-sort-handle' ) ) return;

			var handle       = document.createElement( 'span' );
			handle.className = 'dashicons dashicons-menu ehami-sort-handle';
			handle.title     = 'Drag to reorder';
			li.appendChild( handle );
		});

		// Make separators draggable by themselves.
		sidebar.querySelectorAll( 'li.wp-menu-separator' ).forEach( function ( li ) {
			li.classList.add( 'ehami-sort-handle' );
		});

		sortableInstance = Sortable.create( sidebar, {
			handle:          '.ehami-sort-handle',
			animation:       150,
			forceFallback:   true,
			fallbackOnBody:  false,
			fallbackTolerance: 3,
			ghostClass:      'ehami-sortable-ghost',
			chosenClass:     'ehami-sortable-chosen',
			dragClass:       'ehami-sortable-drag',
			direction:       'vertical',

			onStart: function () {
				var bg = window.getComputedStyle( sidebar ).backgroundColor;
				document.body.style.setProperty( '--ehami-sidebar-bg', bg );
				document.body.classList.add( 'ehami-sorting-active' );
			},

			onEnd: function () {
				document.body.classList.remove( 'ehami-sorting-active' );
				document.body.style.removeProperty( '--ehami-sidebar-bg' );

				// Build order map and save.
				var orderMap = {};
				sidebar.querySelectorAll( ':scope > li' ).forEach( function ( li, i ) {
					if ( li.id ) {
						orderMap[ '#' + li.id ] = i;
					}
				});

				ehami.post( 'ehami_save_menu_order', { order: orderMap } );
			}
		});
	}

	/**
	 * Destroy drag-and-drop sorting.
	 * Removes handle icons and destroys the SortableJS instance.
	 */
	function destroySorting() {
		if ( sortableInstance ) {
			sortableInstance.destroy();
			sortableInstance = null;
		}

		// Remove drag handles from menu items.
		sidebar.querySelectorAll( 'li.menu-top .ehami-sort-handle' ).forEach( function ( el ) {
			el.remove();
		});

		// Remove sort-handle class from separators.
		sidebar.querySelectorAll( 'li.wp-menu-separator.ehami-sort-handle' ).forEach( function ( li ) {
			li.classList.remove( 'ehami-sort-handle' );
		});
	}

	// Initialize on page load if the setting is enabled.
	if ( ehami.enable_menu_sorting ) {
		initSorting();
	}

	/* =====================================================================
	   Settings Page Toggle — instant enable/disable
	   ===================================================================== */

	var checkbox = document.getElementById( 'ehami_enable_menu_sorting' );

	if ( checkbox ) {
		checkbox.addEventListener( 'change', function () {
			if ( checkbox.checked ) {
				initSorting();
			} else {
				destroySorting();
			}
		});
	}

})();


/* =========================================================================
   5. SIDEBAR RESIZE — drag handle on sidebar edge
   ========================================================================= */

(function () {
	'use strict';

	var wrap    = document.getElementById( 'adminmenuwrap' );
	var back    = document.getElementById( 'adminmenuback' );
	var menu    = document.getElementById( 'adminmenu' );
	var content = document.getElementById( 'wpcontent' );
	var footer  = document.getElementById( 'wpfooter' );

	if ( ! wrap || ! menu ) return;

	var MIN_WIDTH = 160;
	var MAX_WIDTH = 480;
	var STEP      = 10;

	var handle       = null;   // Resize handle element.
	var currentWidth = 0;      // Last applied width (0 = default).
	var dragging     = false;
	var startX       = 0;
	var startW       = 0;

	/**
	 * Whether the sidebar is currently folded ("Collapse menu").
	 * Custom width must not apply when folded.
	 */
	var isFolded = document.body.classList.contains( 'folded' )
	            || ( document.body.classList.contains( 'auto-fold' ) && window.innerWidth <= 960 );

	/* =====================================================================
	   Width Helpers
	   ===================================================================== */

	function clamp( val ) {
		return Math.max( MIN_WIDTH, Math.min( MAX_WIDTH, val ) );
	}

	function roundToStep( val ) {
		return Math.round( val / STEP ) * STEP;
	}

	/**
	 * Apply a custom sidebar width to all affected elements.
	 * Skips application when the menu is folded.
	 *
	 * @param {number} width
	 */
	function applyWidth( width ) {
		currentWidth = width;
		if ( isFolded ) return;

		var px = width + 'px';

		wrap.style.width       = px;
		back.style.width       = px;
		menu.style.width       = px;
		content.style.marginLeft = px;
		if ( footer ) footer.style.marginLeft = px;

		// Flyout submenus: non-active need left offset, active needs 100% width.
		menu.querySelectorAll( '.wp-not-current-submenu .wp-submenu' ).forEach( function ( el ) {
			el.style.left = px;
		});
		menu.querySelectorAll( '.wp-has-current-submenu .wp-submenu' ).forEach( function ( el ) {
			el.style.width = '100%';
		});

		if ( handle ) {
			handle.style.left = ( width - 3 ) + 'px';
		}
	}

	/**
	 * Remove all inline width/margin styles so WordPress manages the sidebar.
	 */
	function clearWidth() {
		[ wrap, back, menu ].forEach( function ( el ) { el.style.width = ''; });
		content.style.marginLeft = '';
		if ( footer ) footer.style.marginLeft = '';

		menu.querySelectorAll( '.wp-not-current-submenu .wp-submenu' ).forEach( function ( el ) {
			el.style.left = '';
		});
		menu.querySelectorAll( '.wp-has-current-submenu .wp-submenu' ).forEach( function ( el ) {
			el.style.width = '';
		});

		if ( handle ) handle.style.left = '';
	}

	/**
	 * Reset sidebar width to default.
	 * Called by the "Reset sidebar width" button in settings-page.js.
	 */
	window.ehamiResetSidebarWidth = function () {
		currentWidth = 0;
		clearWidth();

		// Remove PHP-injected sidebar-width CSS from the inline style tag.
		// Only `body:not(.folded)` rules belong to sidebar width — safe to strip.
		var styleEl = document.getElementById( 'ehami-style-inline-css' );
		if ( styleEl ) {
			styleEl.textContent = styleEl.textContent.replace(
				/body:not\(\.folded\)[^{]*\{[^}]*\}/g, ''
			);
		}
	};

	/* =====================================================================
	   Fold / Unfold Handling
	   ===================================================================== */

	// WordPress fires these custom events on fold/unfold.
	// jQuery is the dispatcher, so we listen via jQuery if available.
	if ( window.jQuery ) {
		jQuery( document ).on( 'wp-collapse-menu wp-menu-state-set', function ( e, data ) {
			if ( data.state === 'folded' ) {
				isFolded = true;
				clearWidth();
			} else {
				isFolded = false;
				if ( currentWidth > 0 ) applyWidth( currentWidth );
			}
		});
	}

	/* =====================================================================
	   Drag Handle — create, bind events, tear down
	   ===================================================================== */

	/**
	 * Create the resize handle, bind drag events, apply saved width.
	 */
	function initResize() {
		if ( ! ehami.can_customize || handle ) return;

		handle           = document.createElement( 'div' );
		handle.className = 'ehami-resize-handle';
		wrap.appendChild( handle );

		// Read accent color from active menu item for the handle highlight.
		var accentEl = document.querySelector( '#adminmenu a.wp-has-current-submenu' );
		if ( accentEl ) {
			handle.style.setProperty(
				'--ehami-resize-color',
				window.getComputedStyle( accentEl ).backgroundColor
			);
		}

		// Position at current sidebar edge.
		var initW = wrap.offsetWidth || 160;
		handle.style.left = ( initW - 3 ) + 'px';

		// Bind drag events.
		handle.addEventListener( 'mousedown', onMouseDown );
		document.addEventListener( 'mousemove', onMouseMove );
		document.addEventListener( 'mouseup', onMouseUp );

		// Apply saved width.
		if ( ehami.sidebar_width && ehami.sidebar_width > 0 ) {
			currentWidth = ehami.sidebar_width;
			if ( ! isFolded ) applyWidth( ehami.sidebar_width );
		}
	}

	/**
	 * Remove the resize handle and clean up.
	 */
	function destroyResize() {
		if ( ! handle ) return;

		document.removeEventListener( 'mousemove', onMouseMove );
		document.removeEventListener( 'mouseup', onMouseUp );

		handle.remove();
		handle = null;

		currentWidth = 0;
		clearWidth();
	}

	/* =====================================================================
	   Drag Events
	   ===================================================================== */

	function onMouseDown( e ) {
		if ( e.button !== 0 || isFolded ) return;

		e.preventDefault();
		dragging = true;
		startX   = e.clientX;
		startW   = wrap.offsetWidth;

		handle.classList.add( 'ehami-resize-active' );
		document.body.classList.add( 'ehami-resizing' );
	}

	function onMouseMove( e ) {
		if ( ! dragging ) return;
		applyWidth( clamp( startW + ( e.clientX - startX ) ) );
	}

	function onMouseUp() {
		if ( ! dragging ) return;

		dragging = false;
		handle.classList.remove( 'ehami-resize-active' );
		document.body.classList.remove( 'ehami-resizing' );

		var finalW = roundToStep( clamp( wrap.offsetWidth ) );
		applyWidth( finalW );
		ehami.post( 'ehami_save_sidebar_width', { width: finalW } );
	}

	/* =====================================================================
	   Init on Page Load
	   ===================================================================== */

	if ( ehami.enable_sidebar_resize ) {
		initResize();
	}

	/* =====================================================================
	   Settings Page Toggle — instant enable/disable
	   ===================================================================== */

	var checkbox = document.getElementById( 'ehami_enable_sidebar_resize' );

	if ( checkbox ) {
		checkbox.addEventListener( 'change', function () {
			if ( checkbox.checked ) {
				initResize();
			} else {
				destroyResize();
			}
		});
	}

})();


/* =========================================================================
   6. SEPARATORS — live CSS preview for separator modes
   ========================================================================= */

(function () {
	'use strict';

	var STYLE_ID = 'ehami-separators-preview';

	var select = document.getElementById( 'ehami_separators_mode' );
	if ( ! select ) return;

	select.addEventListener( 'change', function () {
		applyPreview( select.value );
	});

	/**
	 * Inject inline CSS that previews the selected separators mode.
	 *
	 * @param {string} mode  'default', 'hidden', or 'highlight'.
	 */
	function applyPreview( mode ) {
		var css = '';

		if ( mode === 'hidden' ) {
			css = '#adminmenu li.wp-menu-separator { display: none !important; }';

		} else if ( mode === 'highlight' ) {
			css = '#adminmenu li.wp-menu-separator {'
			    + ' display: block !important; height: auto !important; margin: 0 !important; }';
			css += '#adminmenu li.wp-menu-separator > .separator {'
			     + ' height: 2px !important; margin: 8px 12px !important;'
			     + ' background: rgba(255,255,255,0.08) !important; border: none !important; }';

		} else {
			// "default" — restore WP core styles, overriding any saved PHP inline styles.
			css = '#adminmenu li.wp-menu-separator {'
			    + ' display: block !important; height: 5px !important;'
			    + ' padding: 0 !important; margin: 0 0 6px !important; }';
			css += '#adminmenu li.wp-menu-separator > .separator,'
			     + '#adminmenu div.separator {'
			     + ' height: 2px !important; padding: 0 !important; margin: 0 !important;'
			     + ' background: none !important; border: none !important; }';
		}

		// Reuse existing <style> or create a new one.
		var styleEl = document.getElementById( STYLE_ID );

		if ( styleEl ) {
			styleEl.textContent = css;
		} else {
			styleEl    = document.createElement( 'style' );
			styleEl.id = STYLE_ID;
			styleEl.textContent = css;
			document.head.appendChild( styleEl );
		}
	}

})();


/* =========================================================================
   7. SETTINGS PAGE — form handling, notices, reset buttons
   ========================================================================= */

(function () {
	'use strict';

	var container = document.getElementById( 'ehami-settings-page' );
	if ( ! container ) return;

	var form = container.querySelector( 'form' );

	/* =====================================================================
	   Form Submit — collect checkboxes + selects, save via AJAX
	   ===================================================================== */

	form.addEventListener( 'submit', function ( e ) {
		e.preventDefault();

		var data = {};

		// Checkboxes: checked = 1, unchecked = 0.
		form.querySelectorAll( '.ehami-settings__main input[type="checkbox"]' ).forEach( function ( cb ) {
			data[ cb.name ] = cb.checked ? 1 : 0;
		});

		// Select elements (separators_mode, etc.).
		form.querySelectorAll( '.ehami-settings__main select' ).forEach( function ( sel ) {
			data[ sel.name ] = sel.value;
		});

		saveSettings( data );
	});

	/**
	 * Send settings to the server.
	 *
	 * @param {Object} data  Key-value pairs from the form.
	 */
	function saveSettings( data ) {
		container.classList.add( '--saving' );

		ehami.post( 'ehami_save_settings', { options: data },
			function () {
				container.classList.remove( '--saving' );
				showNotice( 'success', ehami.saved || 'Settings saved.' );
			},
			function () {
				container.classList.remove( '--saving' );
				showNotice( 'error', ehami.save_error || 'Error saving settings.' );
			}
		);
	}

	/* =====================================================================
	   Reset Buttons
	   ===================================================================== */

	bindReset( 'ehami-reset-menu-order', 'ehami_reset_menu_order', function () {
		if ( typeof window.ehamiResetMenuDom === 'function' ) {
			window.ehamiResetMenuDom();
		}
	});

	bindReset( 'ehami-reset-sidebar-width', 'ehami_reset_sidebar_width', function () {
		if ( typeof window.ehamiResetSidebarWidth === 'function' ) {
			window.ehamiResetSidebarWidth();
		}
	});

	/**
	 * Bind a reset link: dim on click, send AJAX, restore on done/fail.
	 *
	 * @param {string}   elementId   The link's id attribute.
	 * @param {string}   action      AJAX action name.
	 * @param {Function} onSuccess   Extra logic to run on success.
	 */
	function bindReset( elementId, action, onSuccess ) {
		var link = document.getElementById( elementId );
		if ( ! link ) return;

		link.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			link.style.opacity = '0.5';

			ehami.post( action, null,
				function () {
					link.style.opacity = '1';
					if ( onSuccess ) onSuccess();
					showNotice( 'success', ehami.saved || 'Settings saved.' );
				},
				function () {
					link.style.opacity = '1';
					showNotice( 'error', ehami.save_error || 'Error.' );
				}
			);
		});
	}

	/* =====================================================================
	   Inline Notice — feedback near the Save button
	   ===================================================================== */

	var noticeEl    = container.querySelector( '.ehami-inline-notice' );
	var noticeTimer = null;

	/**
	 * Show a temporary status notice next to the Save button.
	 *
	 * @param {string} type     'success' or 'error'.
	 * @param {string} message  Text to display.
	 */
	function showNotice( type, message ) {
		if ( ! noticeEl ) return;

		var icon = ( type === 'success' ) ? 'dashicons-yes-alt' : 'dashicons-warning';

		noticeEl.innerHTML = '<span class="dashicons ' + icon + '"></span> ' + message;
		noticeEl.className = 'ehami-inline-notice --' + type;

		// Fade in.
		noticeEl.style.opacity = '0';
		noticeEl.offsetHeight; // force reflow
		noticeEl.style.transition = 'opacity 0.25s ease';
		noticeEl.style.opacity    = '1';

		// Auto-hide after 4 seconds.
		if ( noticeTimer ) clearTimeout( noticeTimer );

		noticeTimer = setTimeout( function () {
			noticeEl.style.transition = 'opacity 0.4s ease';
			noticeEl.style.opacity    = '0';

			setTimeout( function () {
				noticeEl.className = 'ehami-inline-notice';
				noticeEl.innerHTML = '';
				noticeEl.style.transition = '';
			}, 400 );
		}, 4000 );
	}

})();


/* =========================================================================
   8. VIDEO TOOLTIPS — auto-playing video hints on settings labels
   ========================================================================= */

(function () {
	'use strict';

	var hints = document.querySelectorAll( '.ehami-video-hint[data-video]' );
	if ( ! hints.length || ! ehami.video_url ) return;

	hints.forEach( function ( hint ) {
		var videoName = hint.getAttribute( 'data-video' );
		var tooltip   = null;
		var video     = null;
		var spinner   = null;
		var ready     = false;

		hint.addEventListener( 'mouseenter', function () {
			if ( ! tooltip ) {
				buildTooltip( videoName );
			}

			video.play().catch( function () {} );

			if ( ready ) {
				spinner.classList.add( '--hidden' );
			}
		});

		hint.addEventListener( 'mouseleave', function () {
			if ( video ) {
				video.pause();
				video.currentTime = 0;
			}
		});

		function buildTooltip( name ) {
			tooltip           = document.createElement( 'div' );
			tooltip.className = 'ehami-video-tooltip';

			spinner           = document.createElement( 'div' );
			spinner.className = 'ehami-video-spinner';
			tooltip.appendChild( spinner );

			video       = document.createElement( 'video' );
			video.src   = ehami.video_url + name + '.mp4';
			video.muted = true;
			video.loop  = true;
			video.setAttribute( 'playsinline', '' );
			video.setAttribute( 'preload', 'none' );

			video.addEventListener( 'canplaythrough', function () {
				ready = true;
				spinner.classList.add( '--hidden' );
			});

			tooltip.appendChild( video );
			hint.appendChild( tooltip );
		}
	});

})();
