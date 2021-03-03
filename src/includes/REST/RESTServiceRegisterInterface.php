<?php

namespace DeepWebSolutions\Framework\Utilities\REST;

defined( 'ABSPATH' ) || exit;

/**
 * Describes a REST service utility instance.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\REST
 */
interface RESTServiceRegisterInterface {
	/**
	 * Using classes should define their REST configuration in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   RESTService     $rest_service       Instance of the REST service.
	 */
	public function register_rest_config( RESTService $rest_service ): void;
}
