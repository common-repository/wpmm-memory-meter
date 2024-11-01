jQuery( document ).ready( function( $ ) {

	// Translation

	const { __, _x, _n, _nx, sprintf } = wp.i18n;

	// Clear logs confirmation

	$( 'body' ).on( 'click', '#wpmm-memory-meter-clear-logs', function( e ) {

		if ( !confirm( __( 'Are you sure you want to clear all of the logs?', 'wpmm-memory-meter' ) ) ) {

			e.preventDefault();

		}

	});

});