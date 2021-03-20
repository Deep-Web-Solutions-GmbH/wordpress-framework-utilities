<?php

namespace DeepWebSolutions\Framework\Utilities\AdminNotices\Helpers;

use DeepWebSolutions\Framework\Foundations\Exceptions\NotImplementedException;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginAwareInterface;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginInterface;
use DeepWebSolutions\Framework\Foundations\PluginComponent\PluginComponentInterface;
use DeepWebSolutions\Framework\Helpers\DataTypes\Strings;

\defined( 'ABSPATH' ) || exit;

/**
 * Basic implementation of the admin-notices-helpers-aware interface.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\AdminNotices\Helpers
 */
trait AdminNoticesHelpersTrait {
	/**
	 * Returns a meaningful, hopefully unique, handle for an admin notice.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $name   The actual descriptor of the notice's purpose. Leave blank for default.
	 * @param   array   $extra  Further descriptor of the notice's purpose.
	 * @param   string  $root   Prepended to all notice handles inside the same class.
	 *
	 * @return  string
	 */
	public function get_admin_notice_handle( string $name = '', array $extra = array(), string $root = 'dws-framework-utilities' ): string {
		if ( $this instanceof PluginComponentInterface ) {
			$root = ( 'dws-framework-utilities' === $root ) ? '' : $root;
			$root = \join( '_', array( $this->get_plugin()->get_plugin_slug(), $root ?: $this->get_name() ) ); // phpcs:ignore
		} elseif ( $this instanceof PluginAwareInterface ) {
			$root = $this->get_plugin()->get_plugin_slug();
		}

		return Strings::to_safe_string(
			\join(
				'_',
				\array_filter(
					\array_merge(
						array( $root, $name ),
						$extra
					)
				)
			),
			array(
				' '  => '-',
				'/'  => '',
				'\\' => '_',
			)
		);
	}

	/**
	 * Returns a formatted user-friendly name for the using class.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @throws  NotImplementedException     Thrown when called in an unsupported scenario.
	 *
	 * @return  string
	 */
	protected function get_registrant_name(): string {
		if ( $this instanceof PluginComponentInterface ) {
			$name = \sprintf( '%s: %s', $this->get_plugin()->get_plugin_name(), $this->get_name() );
		} elseif ( $this instanceof PluginInterface ) {
			$name = $this->get_plugin_name();
		} elseif ( $this instanceof PluginAwareInterface ) {
			$name = $this->get_plugin()->get_plugin_name();
		} else {
			throw new NotImplementedException( 'Registrant name scenario not implemented.' );
		}

		return $name;
	}
}
