<?php
/**
 * Handles The Events Calendar integration with the "Hello Elementor" theme.
 *
 * @since   5.7.0
 *
 * @package Tribe\Events\Integrations\Hello_Elementor
 */

namespace Tribe\Events\Integrations\Hello_Elementor;

use TEC\Common\DI\Service_Provider;


/**
 * Class Service_Provider
 *
 * @since   5.7.0
 *
 * @package Tribe\Events\Integrations\Hello_Elementor
 */
class Service_Provider extends Service_Provider {

	public function register() {
		if ( 'hello-elementor' !== get_template() ) {
			return;
		}
		require_once __DIR__ . '/hello-elementor-functions.php';
	}
}
