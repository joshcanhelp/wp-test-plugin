<?php
use WpUnitTestHelpers\WpTestCase;
use WpUnitTestHelpers\Exceptions\HttpHaltException;

class TestHttpHaltWpTestCase extends WpTestCase {

	public function testThatItRequestsTheCorrectUrl() {
		$this->startHttpHalting();
		update_option( 'user_profile_api_access_token', '__test_api_token__' );

		$test_user = (object) array( 'data' => (object) array( 'user_email' => '__test_email__' ) );
		try {
			prefixed_get_user_profile_data_on_login( uniqid(), $test_user );
			$e_data = array();
		} catch ( HttpHaltException $e ) {
			$e_data = $e->getHttpRequest();
		}

		$this->assertNotEmpty( $e_data );
		$this->assertEquals( 'api.joshcanhelp.com', $e_data['url_parsed']['host'] );
		$this->assertEquals( '/user', $e_data['url_parsed']['path'] );
		$this->assertContains( 'email=__test_email__', $e_data['url_queries'] );
		$this->assertEquals( 'GET', $e_data['method'] );
		$this->assertArrayHasKey( 'Authorization', $e_data['headers'] );
		$this->assertEquals( 'Bearer __test_api_token__', $e_data['headers']['Authorization'] );
	}
}
