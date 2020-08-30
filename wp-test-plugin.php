<?php
/**
 * Plugin Name: Test Plugin
 * Version: 0.0.1
 * Description: We're just here to write some tests ...
 * License: GPL v3
 *
 * @package joshcanhelp-wp-testing
 */

define( 'PREFIXED_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Get additional user profile data on do_login.
 *
 * @param string $user_login User login name.
 * @param object $user Complete user object.
 *
 * @return bool
 */
function prefixed_get_user_profile_data_on_login( $user_login, $user ) {
	$email_param = rawurlencode( $user->data->user_email );
	$api_token   = get_option( 'user_profile_api_access_token' );
	$response    = wp_remote_get(
		'https://api.joshcanhelp.com/user?email=' . $email_param,
		[ 'headers' => [ 'Authorization' => 'Bearer ' . $api_token ] ]
	);

	$response_code = (int) wp_remote_retrieve_response_code( $response );

	if ( ! is_wp_error( $response ) && 200 === $response_code && $response['body'] ) {
		$profile_data = json_decode( $response['body'], true );
		// Sanitize what we got back ...
		update_user_meta( $user->ID, 'custom_profile_data', $profile_data );

		return true;
	}

	// Log the error somehow ...
	return false;
}

add_action( 'do_login', 'prefixed_get_user_profile_data_on_login' );

/**
 * Redirect to a campaign page, if one exists.
 *
 * @return bool
 */
function prefixed_redirect_to_campaign_landing_page() {
	$campaign_id = get_query_var( 'cid' );

	if ( empty( $campaign_id ) ) {
		return false;
	}

	$active_campaigns = get_option( 'active_campaign_ids' );

	if ( ! is_array( $active_campaigns ) || ! in_array( $campaign_id, $active_campaigns, true ) ) {
		return false;
	}

	wp_safe_redirect( home_url( $campaign_id ) );
	exit;
}

add_action( 'template_redirect', 'prefixed_redirect_to_campaign_landing_page', 1, 0 );

function prefixed_login_init_code_check() {
	$login_code = get_option( 'login_code' );
	if ( isset( $_GET['lc'] ) && $login_code === $_GET['lc'] ) {
		return true;
	}
	wp_die( __( 'Not authorized', 'prefixed' ), __( 'Not authorized', 'prefixed' ), 403 );
}

add_action( 'login_init', 'prefixed_login_init_code_check', 1, 0 );

function prefixed_ajax_admin_delete_custom_profile_data() {
	check_ajax_referer( 'delete_custom_profile_data' );

	if ( ! current_user_can( 'edit_users' ) ) {
		wp_send_json_error( [ 'error' => __( 'Not authorized', 'prefixed' ) ] );
	}

	if ( empty( $_POST['user_id'] ) ) {
		wp_send_json_error( [ 'error' => __( 'No user ID', 'prefixed' ) ] );
	}

	delete_user_meta( $_POST['user_id'], 'custom_profile_data' );
	wp_send_json_success();
}

add_action( 'wp_ajax_delete_custom_profile_data', 'prefixed_ajax_admin_delete_custom_profile_data' );

function prefixed_admin_enqueue_scripts() {
	wp_register_script( 'prefixed_admin', PREFIXED_PLUGIN_URL . 'assets/admin.js', [ 'jquery' ], '0.0.1' );
	wp_localize_script(
		'prefixed_admin',
		'adminScriptVars',
		[ 'a_message' => __( 'A message ...', 'lang-prefix' ) ]
	);
}

add_action( 'admin_enqueue_scripts', 'prefixed_admin_enqueue_scripts', 1 );
