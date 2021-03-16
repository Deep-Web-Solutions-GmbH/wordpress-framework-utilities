<?php

namespace DeepWebSolutions\Framework\Utilities\REST;

\defined( 'ABSPATH' ) || exit;

/**
 * Describes a REST-service-aware instance.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\REST
 */
interface RESTServiceAwareInterface {
	/**
	 * Gets the current REST service instance set on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  RESTService
	 */
	public function get_rest_service(): RESTService;

	/**
	 * Sets a REST service instance on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   RESTService     $rest_service   REST service instance to use from now on.
	 */
	public function set_rest_service( RESTService $rest_service );
}
