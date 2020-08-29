<?php

class TestHttpMock extends \PHPUnit\Framework\TestCase {

	public function setup() {
		delete_user_meta( 1, 'custom_profile_data' );
	}

	public function tearDown() {
		delete_user_meta( 1, 'custom_profile_data' );
	}

	public function testThatItSetsTheUserMetaOnSuccess() {
		add_filter( 'pre_http_request', 'pre_http_request_mock_success', 1 );

		$test_user = (object) array(
			'ID'   => 1,
			'data' => (object) array( 'user_email' => '__test_email__' ),
		);
		$result    = prefixed_get_user_profile_data_on_login( uniqid(), $test_user );

		$this->assertTrue( $result );
		$this->assertEquals(
			array( 'location' => 'Seattle, WA, USA' ),
			get_user_meta( 1, 'custom_profile_data', true )
		);

		remove_filter( 'pre_http_request', 'pre_http_request_mock_success', 1 );
	}

	// Existing test methods ...

	public function testThatItHandlesFailureConditions() {
		$test_user = (object) array(
			'ID'   => 1,
			'data' => (object) array( 'user_email' => '__test_email__' ),
		);

		foreach ( array( 'wp_error', 'not_found', 'empty_response' ) as $condition ) {
			add_filter( 'pre_http_request', 'pre_http_request_mock_' . $condition, 1 );
			$result = prefixed_get_user_profile_data_on_login( uniqid(), $test_user );
			$this->assertFalse( $result );
			$this->assertEmpty( get_user_meta( 1, 'custom_profile_data', true ) );
			remove_filter( 'pre_http_request', 'pre_http_request_mock_' . $condition, 1 );
		}
	}
}
