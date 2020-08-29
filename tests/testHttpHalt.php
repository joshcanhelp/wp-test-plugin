<?php

class TestHttpHalt extends \PHPUnit\Framework\TestCase {

	public function testThatItRequestsTheCorrectUrl() {
		add_filter( 'pre_http_request', 'pre_http_request_halt_request', 1, 3 );
		update_option( 'user_profile_api_access_token', '__test_api_token__' );

		$test_user = (object) array( 'data' => (object) array( 'user_email' => '__test_email__' ) );
		try {
			prefixed_get_user_profile_data_on_login( uniqid(), $test_user );
			$e_data = array();
		} catch ( Exception $e ) {
			$e_data = json_decode( $e->getMessage(), true );
		}

		$this->assertNotEmpty( $e_data );
		$this->assertEquals( 'https://api.joshcanhelp.com/user?email=__test_email__', $e_data['url'] );
		$this->assertEquals( 'GET', $e_data['method'] );
		$this->assertArrayHasKey( 'Authorization', $e_data['headers'] );
		$this->assertEquals( 'Bearer __test_api_token__', $e_data['headers']['Authorization'] );
	}

	public function tearDown() {
		delete_option( 'user_profile_api_access_token' );
		remove_filter( 'pre_http_request', 'pre_http_request_halt_request', 1 );
	}
}
