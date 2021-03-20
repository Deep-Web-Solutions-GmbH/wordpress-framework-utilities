<?php

namespace DeepWebSolutions\Framework\Utilities\REST\Helpers;

use DeepWebSolutions\Framework\Foundations\Plugin\PluginAwareInterface;
use DeepWebSolutions\Framework\Foundations\PluginComponent\PluginComponentInterface;

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
	 * @return  string
	 */
	public function get_rest_namespace( int $version = 1 ): string {
		if ( $this instanceof PluginComponentInterface ) {
			$namespace = \join( '/', array( $this->get_plugin()->get_plugin_slug(), $this->get_component_safe_name() ) );
		} elseif ( $this instanceof PluginAwareInterface ) {
			$namespace = $this->get_plugin()->get_plugin_slug();
		} else {
			$namespace = self::class;
		}

		return "{$namespace}/v{$version}";
	}
}
