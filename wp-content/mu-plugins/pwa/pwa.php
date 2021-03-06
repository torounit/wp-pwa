<?php

require dirname( __FILE__ ) . '/includes/Assets_Seeker.php';

define( 'SERVICE_WORKER_ENDPOINT', 'service-worker' );
define( 'MANIFEST_ENDPOINT', 'manifest' );
define( 'NOT_AVAILABLE_ENDPOINT', 'not-available' );
define( 'UPDATE_CACHE_QUERY_VAR', 'update-asset-caches' );


/**
 * Add Rewrite rules.
 */
add_action( 'init', function () {
	add_rewrite_endpoint( SERVICE_WORKER_ENDPOINT, EP_ROOT );
	add_rewrite_endpoint( MANIFEST_ENDPOINT, EP_ROOT );
	add_rewrite_endpoint( NOT_AVAILABLE_ENDPOINT, EP_ROOT );
} );


add_filter( 'query_vars', function ( $vars ) {
	$vars[] = SERVICE_WORKER_ENDPOINT;
	$vars[] = MANIFEST_ENDPOINT;
	$vars[] = UPDATE_CACHE_QUERY_VAR;

	return $vars;
} );

/**
 * Controller
 */
add_action( 'template_redirect', function () {
	global $wp_query;

	if ( isset( $wp_query->query[ SERVICE_WORKER_ENDPOINT ] ) ) {
		header( 'Content-Type: text/javascript' );
		header( 'Cache-Control: max-age='. MINUTE_IN_SECONDS * 30 );
		header( 'Service-Worker-Allowed: /' );
		include dirname( __FILE__ ) . '/js/service-worker.js.php';
		exit;
	}
	if ( isset( $wp_query->query[ NOT_AVAILABLE_ENDPOINT ] ) ) {
		include dirname( __FILE__ ) . '/not-available.php';
		exit;
	}

	if ( isset( $wp_query->query[ MANIFEST_ENDPOINT ] ) ) {
		header( 'Content-Type: application/manifest+json' );
		include dirname( __FILE__ ) . '/manifest.php';
		exit;
	}


	if ( isset( $wp_query->query[ UPDATE_CACHE_QUERY_VAR ] ) ) {
		new Assets_Seeker();
		update_option( 'pwd_last_updated', current_time( 'U' ) );
	}
} );

/**
 * Rebuild Static Caches.
 */
add_action( 'after_switch_theme', function () {
	wp_remote_get( add_query_arg( UPDATE_CACHE_QUERY_VAR, '1', home_url() ), [ 'timeout' => 120 ] );
} );

/**
 * Gravatar always https.
 */
add_filter( 'get_avatar_url' , function ( $url ) {
	return preg_replace( '/http:\/\/[0-9]\.gravatar\.com/', 'https://secure.gravatar.com', $url );
});

/**
 * Add Head tags
 */
add_action( 'wp_head', function () {
	?>
	<meta name="theme-color" content="#<?php background_color();?>">
	<link rel="manifest" href="<?php echo home_url( MANIFEST_ENDPOINT ); ?>">
	<?php
} );

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_script( 'pwa-service-worker', WPMU_PLUGIN_URL . '/pwa/register-service-worker.js' );
	wp_enqueue_script( 'pwa-fetch-html', WPMU_PLUGIN_URL . '/pwa/fetch-html.js', [], false, true );
} );
