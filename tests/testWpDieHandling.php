<?php

class TestWpDieHandling extends \PHPUnit\Framework\TestCase {

	public function tearDown() {
		remove_filter( 'wp_die_handler', 'wp_die_handler_filter' );
		delete_option( 'login_code' );
		unset( $_GET['lc'] );
	}

	public function testThatNoCodeWillKillProcess() {
		add_filter( 'wp_die_handler', 'wp_die_handler_filter' );
		try {
			prefixed_login_init_code_check();
			$caught_json = '';
		} catch ( \Exception $e ) {
			$caught_json = json_decode( $e->getMessage(), true );
		}

		$this->assertNotEmpty( $caught_json, 'No exception caught' );
		$this->assertEquals( 'Not authorized', $caught_json['message'] );
		$this->assertEquals( 'Not authorized', $caught_json['title'] );
		$this->assertEquals( 403, $caught_json['args']['response'] );
	}

	public function testThatInvalidCodeWillKillProcess() {
		add_filter( 'wp_die_handler', 'wp_die_handler_filter' );
		update_option( 'login_code', 'valid_' . uniqid() );
		$_GET['lc'] = 'invalid_' . uniqid();

		try {
			prefixed_login_init_code_check();
			$caught_json = '';
		} catch ( \Exception $e ) {
			$caught_json = json_decode( $e->getMessage(), true );
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
