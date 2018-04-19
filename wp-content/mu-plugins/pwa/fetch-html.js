(function( $ ) {
	const selector = '#page';
	document.addEventListener( "DOMContentLoaded", function() {
		$( '.error-not-available' ).hide();
		if ('caches' in window) {
			console.log( '[fetch-html]', location.href );
			caches.match( location.href ).then( function( response ) {
				if (response) {
					console.log( response );
				}
			} );

			fetch( location.href ).then( ( response ) => {
				return response.text()
			} ).then( ( responseText ) => {
				let $html = jQuery( "<div>" ).append( jQuery.parseHTML( responseText ) );
				let content = $html.find( selector ).html();
				$( selector ).html( content );
				$( 'title' ).text( $html.find( 'title' ).text() )
			} ).finally( () => {
				$( '.error-not-available' ).show()
			} )
		}
	} );
})( jQuery );

