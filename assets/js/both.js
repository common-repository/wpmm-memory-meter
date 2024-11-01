jQuery( document ).ready( function( $ ) {

	// Translation

	const { __, _x, _n, _nx, sprintf } = wp.i18n;

	// Admin bar usage display

	$( '#wp-admin-bar-wpmm-memory-meter-admin-bar-menu > .ab-item' ).html( $( '#wpmm-memory-meter-memory-usage' ).attr( 'data-memory-usage' ) );
	$( '#wp-admin-bar-wpmm-memory-meter-admin-bar-menu > .ab-item' ).addClass( $( '#wpmm-memory-meter-memory-usage' ).attr( 'class' ) );
	$( '#wp-admin-bar-wpmm-memory-meter-admin-bar-menu > .ab-item' ).attr( 'title', $( '#wpmm-memory-meter-memory-usage' ).attr( 'title' ) );
	$( '#wpmm-memory-meter-memory-usage' ).remove();

});