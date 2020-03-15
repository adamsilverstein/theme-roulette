/*! Theme Roulette - v1.0.0
 * http://wordpress.org/plugins
 * Copyright (c) 2020; * Licensed GPLv2+ */
/**
 * Theme Roulette
 * http://wordpress.org/plugins
 *
 * Copyright (c) 2015 Adam Silverstein
 * Licensed under the GPLv2+ license.
 */

( function( window, undefined ) {
	'use strict';

	window.setTimeout( function() {
		var data = {
			'action':   'thmr_maybe_change_theme',
			'_wpnonce': thmr_data.nonce
		};
		window.jQuery.ajax(
			thmr_data.ajaxurl,
			{
				'data': data,
				'type': 'get'
			}
		)
		.done( function( data ) {
		} );

	}, 500 );

} )( this );