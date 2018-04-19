<?php

require dirname( __FILE__ ) . '/includes/Assets_Seeker.php';

define( 'SERVICE_WORKER_ENDPOINT', 'service-worker' );
define( 'NOT_AVAILABLE_ENDPOINT', 'not-available' );
define( 'UPDATE_CACHE_QUERY_VAR', 'update-asset-caches' );

add_action( 'init', function () {
	add_rewrite_endpoint( 'service-worker', EP_ROOT );
	add_rewrite_endpoint( NOT_AVAILABLE_ENDPOINT, EP_ROOT );
} );

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_script( 'pwa-fetch-html', WPMU_PLUGIN_URL . '/pwa/fetch-html.js', [ 'jquery'], false, true );

} );

add_filter( 'query_vars', function ( $vars ) {
	$vars[] = SERVICE_WORKER_ENDPOINT;
	$vars[] = UPDATE_CACHE_QUERY_VAR;

	return $vars;
} );

add_action( 'after_switch_theme', function () {
	wp_remote_get( add_query_arg( UPDATE_CACHE_QUERY_VAR, '1', home_url() ), array( 'timeout' => 120 )  );
} );

add_action( 'template_redirect', function () {
	global $wp_query;
	if ( isset( $wp_query->query[ SERVICE_WORKER_ENDPOINT ] ) ) {
		header( 'Content-Type: text/javascript' );
		header( 'Service-Worker-Allowed: /' );
		include dirname( __FILE__ ) . '/js/service-worker.js.php';
		exit;
	}
	if ( isset( $wp_query->query[ NOT_AVAILABLE_ENDPOINT ] ) ) {
		include dirname( __FILE__ ) . '/not-available.php';
		exit;
	}
	wp_enqueue_script( 'pwa-service-worker', WPMU_PLUGIN_URL . '/pwa/register-service-worker.js' );
	//sleep( 5 );


	if ( isset( $wp_query->query[ UPDATE_CACHE_QUERY_VAR ] ) ) {
		new Assets_Seeker();
		update_option( 'pwd_last_updated', current_time( 'U' ) );
	}
} );

