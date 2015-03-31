
/**
 * Theme Roulette
 * http://wordpress.org/plugins
 *
 * Copyright (c) 2015 Adam Silverstein
 * Licensed under the GPLv2+ license.
 */

( function( window, undefined ) {
	'use strict';

	setTimeout( function() {
		var data = {
			'action':   'thmr_maybe_change_theme',
			'_wpnonce': thmr_data.nonce
		}
		jQuery.ajax(
			thmr_data.ajaxurl,
			{
				'data': data,
				'type': 'get'
			}
		)
		.done( function( data ) {
			console.log( data.data );
		} )

	}, 500 );

} )( this );