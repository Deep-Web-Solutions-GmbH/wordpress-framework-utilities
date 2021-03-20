<?php

namespace DeepWebSolutions\Framework\Utilities\REST;

use DeepWebSolutions\Framework\Utilities\REST\Helpers\RESTHelpersTrait;

\defined( 'ABSPATH' ) || exit;

/**
 * Basic implementation of the REST service register interface.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\REST
 */
trait RESTServiceRegisterTrait {
	// region TRAITS

	use RESTHelpersTrait;

	// endregion

	// region METHODS

	/**
	 * Using classes should define their REST configuration in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   RESTService     $rest_service       Instance of the REST service.
	 */
	abstract public function register_rest_config( RESTService $rest_service ): void;

	// endregion
}
