'use strict';

const PRE_CACHE_NAME = 'pre-cache-v1';
const RUNTIME_CACHE_NAME = 'runtime-cache-v1';

const urlsToPreCache = [
	'/',
	'/not-available/',
	<?php foreach ( get_option( 'pwa_style_paths' ) as $css ): ?>
	'<?php echo esc_url( $css );?>',
	<?php endforeach; ?>
	<?php foreach ( get_option( 'pwa_script_paths' ) as $js ): ?>
	'<?php echo esc_url( $js );?>',
	<?php endforeach; ?>
];


self.addEventListener( 'install', ( event ) => {
	console.log( '[ServiceWorker] Install' );
	event.waitUntil(
		caches.open( PRE_CACHE_NAME )
			.then( ( cache ) => {
				console.log( '[ServiceWorker] Caching app' );
				return cache.addAll( urlsToPreCache );
			} )
	);
} );

self.addEventListener( 'activate', ( event ) => {
	console.log( '[ServiceWorker] Activate' );
	const cacheWhitelist = [ PRE_CACHE_NAME ];
	event.waitUntil(
		caches.keys().then( ( cacheNames ) => {
			return Promise.all(
				cacheNames.map( ( cacheName ) => {
					console.log( '[ServiceWorker] Removing old cache', cacheName );
					if (cacheWhitelist.indexOf( cacheName ) === - 1) {
						return caches.delete( cacheName );
					}
				} )
			);
		} )
	);
} );


self.addEventListener( 'fetch', ( event ) => {
	console.log( event.request );
	if (
		event.request.url.indexOf( 'wp-admin' ) === - 1 &&
		event.request.url.indexOf( 'wp-login' ) === - 1 &&
		event.request.method === 'GET'
	) {
		console.log( '[ServiceWorker] Fetch', event.request.url );
		// for cache.
		event.respondWith(
			caches.match( event.request ).then( ( response ) => {
				if (response) {
					if ([ 'style', 'script', 'image' ].indexOf( event.request.destination ) > - 1) {
						console.log( '[ServiceWorker] Cache Matched!', event.request.url, response );
						return response;
					}
				}

				let promise = fetch( event.request ).then( ( response ) => {
					console.log( event.request );
					if (! response || response.status !== 200 || response.type !== 'basic') {
						return response;
					}
					let responseToCache = response.clone();
					caches.open( RUNTIME_CACHE_NAME ).then( ( cache ) => {
						cache.put( event.request, responseToCache );
						console.log( '[ServiceWorker] Fetched&Cached Data', event.request.url );
					} );

					return response;
				} );

				if (response) {
					console.log( '[ServiceWorker] Cache Matched!', event.request.url, response );
					return response;
				}
				else {
					if (event.request.mode === 'navigate') {
						// Follback.
						return caches.match( '/not-available/' ).then( ( response ) => {
							return response;
						} );
					}
				}

				return promise;

			} )
		);

	}


} );
