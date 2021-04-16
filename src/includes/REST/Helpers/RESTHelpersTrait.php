<?php

namespace DeepWebSolutions\Framework\Utilities\REST\Helpers;

use DeepWebSolutions\Framework\Foundations\Exceptions\NotImplementedException;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginAwareInterface;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginInterface;

\defined( 'ABSPATH' ) || exit;

/**
 * Basic implementation of the REST-helpers-aware interface.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\REST\Helpers
 */
trait RESTHelpersTrait {
	/**
	 * Returns a meaningful namespace for REST routes registered by the using class.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   int     $version    The version of the route.
	 *
	 * @throws  NotImplementedException     Thrown when the namespace generation context is unsupported.
	 *
	 * @return  string
	 */
	public function get_rest_namespace( int $version = 1 ): string {
		if ( $this instanceof PluginAwareInterface ) {
			$namespace = $this->get_plugin()->get_plugin_slug();
		} elseif ( $this instanceof PluginInterface ) {
			$namespace = $this->get_plugin_slug();
		} else {
			throw new NotImplementedException( 'Namespace generation context is not implemented.' );
		}

		return "{$namespace}/v{$version}";
	}
}
