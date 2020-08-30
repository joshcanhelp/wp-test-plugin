<?php

use WpUnitTestHelpers\WpTestCase;
use WpUnitTestHelpers\Exceptions\WpDieHaltException;

class TestEnqueuedStyleWpTestCase extends WpTestCase {

	public function testThatAdminScriptIsRegistered() {
		prefixed_admin_enqueue_scripts();

		$scripts = wp_scripts();
		$script  = $scripts->registered['prefixed_admin'];
		$this->assertEquals( PREFIXED_PLUGIN_URL . 'assets/admin.js', $script->src );
		$this->assertContains( 'jquery', $script->deps );
		$this->assertEquals( '0.0.1', $script->ver );

		$localization_json = trim( str_replace( 'var adminScriptVars = ', '', $script->extra['data'] ), ';' );
		$localization      = json_decode( $localization_json, true );

		$this->assertEquals( 'A message ...', $localization['a_message'] );
	}
}
