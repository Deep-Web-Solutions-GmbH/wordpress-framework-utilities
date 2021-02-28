<?php

namespace DeepWebSolutions\Framework\Utilities\WordPress\Assets;

use DeepWebSolutions\Framework\Helpers\WordPress\Assets\AssetsHelpersTrait as HelpersModuleTrait;
use DeepWebSolutions\Framework\Utilities\Plugin\PluginAwareInterface;
use DeepWebSolutions\Framework\Utilities\Plugin\PluginComponentInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Enhances the helpers' module Assets trait.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\WordPress\Assets
 */
trait AssetsHelpersTrait {
	use HelpersModuleTrait {
		get_asset_handle as get_asset_handle_helpers;
	}

	/**
	 * Returns a meaningful, hopefully unique, handle for an asset.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     AssetsHelpers::get_asset_handle()
	 *
	 * @param   string  $name   The actual descriptor of the asset's purpose. Leave blank for default.
	 * @param   array   $extra  Further descriptor of the asset's purpose.
	 * @param   string  $root   Prepended to all asset handles inside the same class.
	 *
	 * @return  string
	 */
	public function get_asset_handle( string $name = '', array $extra = array(), string $root = 'dws-framework-utilities' ): string {
		if ( $this instanceof PluginComponentInterface ) {
			$root = ( 'dws-framework-utilities' === $root ) ? '' : $root;
			$root = join( '_', array( $this->get_plugin()->get_plugin_slug(), $root ?: $this->get_instance_name() ) ); // phpcs:ignore
		} elseif ( $this instanceof PluginAwareInterface ) {
			$root = $this->get_plugin()->get_plugin_slug();
		}

		return $this->get_asset_handle_helpers( $name, $extra, $root );
	}
}
