<?php

namespace DeepWebSolutions\Framework\Utilities\WordPress\Runnables\Shortcodes;

use DeepWebSolutions\Framework\Utilities\Lifecycle\Runnable\Exceptions\ResetFailure;
use DeepWebSolutions\Framework\Utilities\Lifecycle\Runnable\Exceptions\RunFailure;
use DeepWebSolutions\Framework\Utilities\Lifecycle\Runnable\RunnableInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Compatibility layer between the framework and WordPress' API for shortcodes.
 *
 * Maintain a list of all shortcodes that are registered throughout the plugin, and handles their registration with
 * the WordPress API after calling the run function.
 *
 * @see     https://github.com/DevinVinson/WordPress-Plugin-Boilerplate/blob/master/plugin-name/includes/class-plugin-name-loader.php
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\WordPress\Runnables\Shortcodes
 */
class ShortcodesHandler implements RunnableInterface {
	// region FIELDS AND CONSTANTS

	/**
	 * The shortcodes registered with WordPress that can be used after the handler runs.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     array
	 */
	protected array $shortcodes = array();

	// endregion

	// region INHERITED FUNCTIONS

	/**
	 * Register the filters, actions, and shortcodes with WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  RunFailure|null
	 */
	public function run(): ?RunFailure {
		foreach ( $this->shortcodes as $hook ) {
			if ( empty( $hook['component'] ) ) {
				add_shortcode( $hook['tag'], $hook['callback'] );
			} else {
				add_shortcode( $hook['tag'], array( $hook['component'], $hook['callback'] ) );
			}
		}

		$this->reset();
		return null;
	}

	/**
	 * Resets the shortcodes buffers.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  ResetFailure|null
	 */
	public function reset(): ?ResetFailure {
		$this->shortcodes = array();
		return null;
	}

	// endregion

	// region METHODS

	/**
	 * Add a new shortcode to the collection to be registered with WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string          $tag            The name of the WordPress shortcode that is being registered.
	 * @param   object|null     $component      A reference to the instance of the object on which the shortcode is defined.
	 * @param   string          $callback       The name of the function definition on the $component.
	 */
	public function add_shortcode( string $tag, ?object $component, string $callback ): void {
		$this->shortcodes = $this->add( $this->shortcodes, $tag, $component, $callback );
	}

	/**
	 * Remove a shortcode from the collection to be registered with WordPress.
	 *
	 * @param   string          $tag            The name of the WordPress shortcode that is being deregistered.
	 * @param   object|null     $component      A reference to the instance of the object on which the shortcode is defined.
	 * @param   string          $callback       The name of the function definition on the $component.
	 */
	public function remove_shortcode( string $tag, ?object $component, string $callback ): void {
		$this->shortcodes = $this->remove( $this->shortcodes, $tag, $component, $callback );
	}

	// endregion

	// region HELPERS

	/**
	 * A utility function that is used to register the shortcodes into a single collection.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array           $shortcodes     The collection of shortcodes that is being registered.
	 * @param   string          $tag            The name of the WordPress shortcode that is being registered.
	 * @param   object|null     $component      A reference to the instance of the object on which the shortcode is defined.
	 * @param   string          $callback       The name of the function definition on the $component.
	 *
	 * @access  protected
	 * @return  array      The collection of shortcodes registered with WordPress.
	 */
	protected function add( array $shortcodes, string $tag, ?object $component, string $callback ): array {
		$shortcodes[] = array(
			'tag'       => $tag,
			'component' => $component,
			'callback'  => $callback,
		);

		return $shortcodes;
	}

	/**
	 * A utility function that is used to remove the shortcodes from the single collection.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array           $shortcodes     The collection of shortcodes that is being unregistered.
	 * @param   string          $tag            The name of the WordPress shortcode that is being unregistered.
	 * @param   object|null     $component      A reference to the instance of the object on which the shortcode is defined.
	 * @param   string          $callback       The name of the function definition on the $component.
	 *
	 * @access  protected
	 * @return  array      The collection of shortcodes registered with WordPress.
	 */
	protected function remove( array $shortcodes, string $tag, ?object $component, string $callback ): array {
		foreach ( $shortcodes as $index => $hook_info ) {
			if ( $hook_info['tag'] === $tag && $hook_info['component'] === $component && $hook_info['callback'] === $callback ) {
				unset( $shortcodes[ $index ] );
				break;
			}
		}

		return $shortcodes;
	}

	// endregion
}
