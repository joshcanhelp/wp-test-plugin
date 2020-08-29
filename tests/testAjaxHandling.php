<?php

class TestAjaxHandling extends \PHPUnit\Framework\TestCase {

	public function setUp() {
		add_filter( 'wp_doing_ajax', '__return_true' );
	}

	public function tearDown() {
		remove_filter( 'wp_doing_ajax', '__return_true' );
		remove_filter( 'wp_die_ajax_handler', 'wp_ajax_halt_handler_filter' );
		remove_filter( 'wp_die_ajax_handler', 'wp_ajax_print_handler_filter' );
		unset( $_POST['user_id'] );
		unset( $_REQUEST['_ajax_nonce'] );
	}

	public function testThatInvalidNonceWillFail() {
		add_filter( 'wp_die_ajax_handler', 'wp_ajax_halt_handler_filter' );

		try {
			prefixed_ajax_admin_delete_custom_profile_data();
			$caught_exception = 'No exception caught';
		} catch ( \Exception $e ) {
			$caught_exception = $e->getMessage();
		}

		$this->assertEquals( 'bad_nonce', $caught_exception );
	}

	public function testThatAjaxRequestSuccessfullyDeletesMeta() {
		add_filter( 'wp_die_ajax_handler', 'wp_ajax_print_handler_filter' );
		update_user_meta( 1, 'custom_profile_data', uniqid() );
		wp_set_current_user( 1 );
		$_POST['user_id']        = 1;
		$_REQUEST['_ajax_nonce'] = wp_create_nonce( 'delete_custom_profile_data' );

		ob_start();
		prefixed_ajax_admin_delete_custom_profile_data();
		$this->assertEquals( '{"success":true}', ob_get_clean() );
		$this->assertEmpty( get_user_meta( 1, 'custom_profile_data' ), true );
	}
}
