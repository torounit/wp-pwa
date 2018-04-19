(function( $ ) {
	const selector = '#page';
	document.addEventListener( "DOMContentLoaded", () => {
		$( '.error-not-available' ).hide();
		if ('caches' in window) {
			console.log( '[fetch-html]', location.href );

			fetch( location.href );
			navigator.serviceWorker.addEventListener( 'message', ( message ) => {
				console.log( 'getMessage!', message );
				caches.match( location.href ).then( ( response ) => {
					return response.text()
				} ).then( ( responseText ) => {
					let $html = jQuery( "<div>" ).append( jQuery.parseHTML( responseText ) );
					let content = $html.find( selector ).html();
					$( selector ).html( content );

					$( 'title' ).text( $html.find( 'title' ).text() )
					$( 'meta[property^="og"]' ).remove()
					$( 'meta[property^="twitter"]' ).remove()
					$( 'link[rel="canonical"]' ).remove()
					$( 'link[rel="prev"]' ).remove()
					$( 'link[rel="next"]' ).remove()
					$( 'head' )
						.append( $html.find( 'meta[property^="og"]' ) )
						.append( $html.find( 'meta[property^="twitter"]' ) )
						.append( $html.find( 'link[rel="canonical"]' ) )
						.append( $html.find( 'link[rel="prev"]' ) )
						.append( $html.find( 'link[rel="next"]' ) )

				} ).finally( () => {
					$( '.error-not-available' ).show();
				} )

			} );

		}
	} );
})( jQuery );

