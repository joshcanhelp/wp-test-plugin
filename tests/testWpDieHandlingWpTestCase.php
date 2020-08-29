<?php

use WpUnitTestHelpers\WpTestCase;
use WpUnitTestHelpers\Exceptions\WpDieHaltException;

class TestWpDieHandlingWpTestCase extends WpTestCase {

	public function tearDown() {
		delete_option( 'login_code' );
		unset( $_GET['lc'] );
	}

	public function testThatNoCodeWillKillProcess() {
		$this->startWpDieHalting();

		try {
			prefixed_login_init_code_check();
			$caught_json = '';
		} catch ( WpDieHaltException $e ) {
			$caught_json = $e->getDecodedMessage();
		}

		$this->assertNotEmpty( $caught_json, 'No exception caught' );
		$this->assertEquals( 'Not authorized', $caught_json['message'] );
		$this->assertEquals( 'Not authorized', $caught_json['title'] );
		$this->assertEquals( 403, $caught_json['args']['response'] );
	}

	public function testThatInvalidCodeWillKillProcess() {
		$this->startWpDieHalting();
		update_option( 'login_code', 'valid_' . uniqid() );
		$_GET['lc'] = 'invalid_' . uniqid();

		try {
			prefixed_login_init_code_check();
			$caught_json = '';
		} catch ( WpDieHaltException $e ) {
			$caught_json = $e->getDecodedMessage();
		}

		$this->assertNotEmpty( $caught_json, 'No exception caught' );
		$this->assertEquals( 'Not authorized', $caught_json['message'] );
		$this->assertEquals( 'Not authorized', $caught_json['title'] );
		$this->assertEquals( 403, $caught_json['args']['response'] );
	}

	public function testThatValidCodeWillSucceed() {
		$valid_code = uniqid();
		update_option( 'login_code', $valid_code );
		$_GET['lc'] = $valid_code;

		$this->assertTrue( prefixed_login_init_code_check() );
	}
}
