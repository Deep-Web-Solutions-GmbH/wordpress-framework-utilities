<?php

namespace DeepWebSolutions\Framework\Utilities\REST\Helpers;

defined( 'ABSPATH' ) || exit;

/**
 * Describes an object that has helpers for working with the REST API.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\REST\Helpers
 */
interface RESTHelpersAwareInterface {
	/**
	 * Returns a meaningful namespace for REST routes registered by the using class.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   int     $version    The version of the route.
	 *
	 * @return  string
	 */
	public function get_rest_namespace( int $version ): string;
}
