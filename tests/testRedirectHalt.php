<?php

class TestRedirectHalt extends \PHPUnit\Framework\TestCase {

	public function setUp() {
		add_filter( 'wp_redirect', 'wp_redirect_halt_redirect', 1, 2 );
	}

	public function tearDown() {
		remove_filter( 'wp_redirect', 'wp_redirect_halt_redirect', 1 );
	}

	public function testThatAnActiveCidWillRedirectCorrectly() {
		set_query_var( 'cid', '__test_valid_cid__' );
		update_option( 'active_campaign_ids', array( '__test_valid_cid__' ) );

		try {
			prefixed_redirect_to_campaign_landing_page();
			$e_data = array();
		} catch ( Exception $e ) {
			$e_data = json_decode( $e->getMessage(), true );
		}

		$this->assertNotEmpty( $e_data );
		$this->assertEquals( 'http://example.org/__test_valid_cid__', $e_data['location'] );
		$this->assertEquals( 302, $e_data['status'] );
	}
}
