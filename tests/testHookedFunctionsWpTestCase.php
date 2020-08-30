<?php

use WpUnitTestHelpers\WpTestCase;
use WpUnitTestHelpers\Exceptions\WpDieHaltException;

class TestHookedFunctionsWpTestCase extends WpTestCase {

	public function testThatLoginInitIsHooked() {
		$this->assertHooked(
			'login_init',
			[
				'prefixed_login_init_code_check' => [
					'priority'      => 1,
					'accepted_args' => 0,
				],
			]
		);
	}

	public function testThatTemplateRedirectIsHooked() {
		$this->assertHooked(
			'template_redirect',
			[
				'prefixed_redirect_to_campaign_landing_page' => [
					'priority'      => 1,
					'accepted_args' => 0,
				],
			]
		);
	}

	public function testThatDoLoginIsHooked() {
		$this->assertHooked(
			'do_login',
			[
				'prefixed_get_user_profile_data_on_login' => [
					'priority'      => 10,
					'accepted_args' => 1,
				],
			]
		);
	}

	public function testThatWpAjaxIsHooked() {
		$this->assertHooked(
			'wp_ajax_delete_custom_profile_data',
			[
				'prefixed_ajax_admin_delete_custom_profile_data' => [
					'priority'      => 10,
					'accepted_args' => 1,
				],
			]
		);
	}
}
