<?php

use WpUnitTestHelpers\WpTestCase;
use WpUnitTestHelpers\Exceptions\WpDieHaltException;

class TestHookedFunctionsWpTestCase extends WpTestCase {

	public function testThatLoginInitIsHooked() {
		$this->assertHooked(
			'login_init',
			array(
				'prefixed_login_init_code_check' => array(
					'priority'      => 1,
					'accepted_args' => 0,
				),
			)
		);
	}

	public function testThatTemplateRedirectIsHooked() {
		$this->assertHooked(
			'template_redirect',
			array(
				'prefixed_redirect_to_campaign_landing_page' => array(
					'priority'      => 1,
					'accepted_args' => 0,
				),
			)
		);
	}

	public function testThatDoLoginIsHooked() {
		$this->assertHooked(
			'do_login',
			array(
				'prefixed_get_user_profile_data_on_login' => array(
					'priority'      => 10,
					'accepted_args' => 1,
				),
			)
		);
	}

	public function testThatWpAjaxIsHooked() {
		$this->assertHooked(
			'wp_ajax_delete_custom_profile_data',
			array(
				'prefixed_ajax_admin_delete_custom_profile_data' => array(
					'priority'      => 10,
					'accepted_args' => 1,
				),
			)
		);
	}
}
