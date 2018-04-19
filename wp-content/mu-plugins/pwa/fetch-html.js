(function() {

	let parseHTML = function( element ) {
		//Element is already parsed.
		if (element.querySelector) {
			return element;
		}

		//Element is a string and should be parsed
		if (typeof element === 'string') {
			let el = document.createElement( 'div' );
			el.innerHTML = element;

			var elements = el.children;

			//Only one root element, return it
			if (elements.length === 1) {
				return elements[ 0 ];
			}

			//Otherwise create a fragment, return it
			let frag = document.createDocumentFragment();

			while (elements.length > 0) {
				frag.appendChild( elements[ 0 ] )
			}

			return frag;
		}

		return null;
	};


	const selector = '#page';
	const selectorForSEO = `
					meta[property^="og"],
					meta[property^="twitter"],
					link[rel="canonical"],
					link[rel="prev"],
					link[rel="next"]
					`;

	document.addEventListener( "DOMContentLoaded", () => {
		if ('caches' in window) {
			console.log( '[fetch-html]', location.href );

			fetch( location.href );
			navigator.serviceWorker.addEventListener( 'message', ( message ) => {
				console.log( 'getMessage!', message );
				caches.match( location.href ).then( ( response ) => {
					return response.text()
				} ).then( ( responseText ) => {
					let html = parseHTML( responseText );
					document.querySelector( selector ).innerHTML = html.querySelector( selector ).innerHTML;
					document.querySelector( 'title' ).innerText = html.querySelector( 'title' ).innerText;
					//replace meta and link tags.
					let fragment = document.createDocumentFragment();
					html.querySelectorAll( selectorForSEO ).forEach( el => fragment.appendChild( el ) );
					document.querySelectorAll( selectorForSEO ).forEach( el => el.remove() );
					document.querySelector( 'head' ).appendChild( fragment );

				} ).finally( () => {
				} )

			} );

		}
	} );
})();

