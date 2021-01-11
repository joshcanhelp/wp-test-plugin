<?php

use \WpUnitTestHelpers\WpTestCaseAbstract;

class TestAjaxHandlingWpTestCase extends WpTestCaseAbstract {

	public function testThatInvalidNonceWillFail() {
		$this->startAjaxHalting();

		try {
			prefixed_ajax_admin_delete_custom_profile_data();
			$caught_exception = 'No exception caught';
		} catch ( \Exception $e ) {
			$caught_exception = $e->getMessage();
		}

		$this->assertEquals( 'bad_nonce', $caught_exception );
	}

	public function testThatAjaxRequestSuccessfullyDeletesMeta() {
		$this->startAjaxReturn();
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
