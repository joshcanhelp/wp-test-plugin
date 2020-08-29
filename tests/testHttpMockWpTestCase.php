<?php
use WpUnitTestHelpers\WpTestCase;

class TestHttpMockWpTestCase extends WpTestCase {

	public function setup() {
		parent::setUp();
		delete_user_meta( 1, 'custom_profile_data' );
	}

	public function tearDown() {
		parent::tearDown();
		delete_user_meta( 1, 'custom_profile_data' );
	}

	public function testThatItSetsTheUserMetaOnSuccess() {
		$this->startHttpMocking();
		$this->setHttpMockResponse( 200, '{"location": "Seattle, WA, USA"}' );

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
	}

	public function testThatItHandlesFailureConditions() {
		$this->startHttpMocking();
		$this->setHttpMockResponse( null );
		$this->setHttpMockResponse( 404 );
		$this->setHttpMockResponse( 200 );

		$test_user = (object) array(
			'ID'   => 1,
			'data' => (object) array( 'user_email' => '__test_email__' ),
		);

		for ( $i = 1; $i <= 3; $i++ ) {
			$result = prefixed_get_user_profile_data_on_login( uniqid(), $test_user );
			$this->assertFalse( $result );
			$this->assertEmpty( get_user_meta( 1, 'custom_profile_data', true ) );
		}
	}
}
