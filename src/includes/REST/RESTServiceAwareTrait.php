<?php

namespace DeepWebSolutions\Framework\Utilities\REST;

\defined( 'ABSPATH' ) || exit;

/**
 * Basic implementation of the REST-service-aware interface.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\REST
 */
trait RESTServiceAwareTrait {
	// region FIELDS AND CONSTANTS

	/**
	 * Instance of the REST service.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     RESTService
	 */
	protected RESTService $rest_service;

	// endregion

	// region GETTERS

	/**
	 * Gets the current REST service instance set on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  RESTService
	 */
	public function get_rest_service(): RESTService {
		return $this->rest_service;
	}

	// endregion

	// region SETTERS

	/**
	 * Sets a REST service instance on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   RESTService     $rest_service   REST service instance to use from now on.
	 */
	public function set_rest_service( RESTService $rest_service ) {
		$this->rest_service = $rest_service;
	}

	// endregion
}
