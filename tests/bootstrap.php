<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Wp_Test_Plugin
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find $_tests_dir/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
	require dirname( dirname( __FILE__ ) ) . '/wp-test-plugin.php';
}

tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';
//require dirname( dirname( __FILE__ ) ) . '/vendor/autoload.php';

function pre_http_request_halt_request( $preempt, $args, $url ) {
	throw new \Exception(
		json_encode(
			[
				'url'     => $url,
				'method'  => $args['method'],
				'headers' => $args['headers'],
				'body'    => json_decode( $args['body'], true ),
				'preempt' => $preempt,
			]
		)
	);
}

function pre_http_request_mock_success() {
	return [
		'response' => [ 'code' => 200 ],
		'body'     => '{"location": "Seattle, WA, USA"}',
	];
}

function pre_http_request_mock_wp_error() {
	return new WP_Error( '__test_wp_error_message__' );
}

function pre_http_request_mock_not_found() {
	return [
		'response' => [ 'code' => 404 ],
		'body'     => '__test_not_found_body__',
	];
}

function pre_http_request_mock_empty_response() {
	return [
		'response' => [ 'code' => 200 ],
		'body'     => '',
	];
}

function wp_redirect_halt_redirect( $location, $status ) {
	throw new \Exception(
		json_encode(
			[
				'location' => $location,
				'status'   => $status,
			]
		)
	);
}

function wp_die_handler_filter() {
	return 'wp_die_halt_handler';
}

function wp_die_halt_handler( $message, $title, $args ) {
	throw new \Exception(
		wp_json_encode(
			[
				'message' => $message,
				'title'   => $title,
				'args'    => $args,
			]
		)
	);
}

function wp_ajax_halt_handler_filter() {
	return 'wp_ajax_halt_handler';
}

function wp_ajax_halt_handler( $message, $title, $args ) {
	$is_bad_nonce = -1 === $message && ! empty( $args['response'] ) && 403 === $args['response'];
	throw new Exception( $is_bad_nonce ? 'bad_nonce' : 'die_ajax' );
}

function wp_ajax_print_handler_filter() {
	return 'wp_ajax_print_handler';
}

function wp_ajax_print_handler( $message ) {
	echo $message;
}
