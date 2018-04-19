<?php

require dirname( __FILE__ ) . '/includes/Assets_Seeker.php';

add_action( 'init', function () {
	wp_enqueue_script( 'pwa-fetch-html', WPMU_PLUGIN_URL . '/pwa/fetch-html.js' );

	add_rewrite_endpoint( 'service-worker', EP_ROOT );
	add_rewrite_endpoint( 'not-available', EP_ROOT );
} );

add_filter( 'query_vars', function ( $vars ) {
	$vars[] = 'service-worker';
	$vars[] = 'update-asset-caches';

	return $vars;
} );

add_action( 'template_redirect', function () {
	global $wp_query;
	if ( isset( $wp_query->query['service-worker'] ) ) {
		header( 'Content-Type: text/javascript' );
		header( 'Service-Worker-Allowed: /' );
		include dirname( __FILE__ ) . '/js/service-worker.js.php';
		exit;
	}
	if ( isset( $wp_query->query['not-available'] ) ) {
		include dirname( __FILE__ ) . '/not-available.php';
		exit;
	}
	wp_enqueue_script( 'pwa-service-worker', WPMU_PLUGIN_URL . '/pwa/register-service-worker.js' );
	//sleep( 5 );


	if ( isset( $wp_query->query['update-asset-caches'] ) ) {
		new Assets_Seeker();
	}
} );

