document.addEventListener("DOMContentLoaded", function(event) {
	if ('caches' in window) {
		console.log('[fetch-html]',location.href);
		caches.match( location.href ).then( function( response ) {
			if (response) {
				console.log(response);
			}
		} );
		jQuery( "body" ).load( location.href + " #page" );
	}
});