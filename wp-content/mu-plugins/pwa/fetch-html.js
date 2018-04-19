(function( $ ) {
	document.addEventListener( "DOMContentLoaded", function( event ) {
		$( '.error-not-available' ).hide();
		if ('caches' in window) {
			console.log( '[fetch-html]', location.href );
			caches.match( location.href ).then( function( response ) {
				if (response) {
					console.log( response );
				}
			} );
			$( "body" ).load( location.href + " #page" ).finally( (() => {
				$( '.error-not-available' ).show()
			}) );
		}
	} );
})( jQuery );

