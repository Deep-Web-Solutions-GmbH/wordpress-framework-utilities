<?php

namespace DeepWebSolutions\Framework\Utilities\Shortcodes;

use DeepWebSolutions\Framework\Foundations\Actions\Resettable\ResetFailureException;
use DeepWebSolutions\Framework\Foundations\Actions\Resettable\ResettableTrait;
use DeepWebSolutions\Framework\Foundations\Actions\ResettableInterface;
use DeepWebSolutions\Framework\Foundations\Actions\Runnable\RunFailureException;
use DeepWebSolutions\Framework\Foundations\Actions\Runnable\RunnableTrait;
use DeepWebSolutions\Framework\Foundations\Actions\RunnableInterface;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginAwareInterface;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginAwareTrait;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginInterface;
use DeepWebSolutions\Framework\Utilities\Logging\LoggingService;
use DeepWebSolutions\Framework\Utilities\Logging\LoggingServiceAwareInterface;
use DeepWebSolutions\Framework\Utilities\Logging\LoggingServiceAwareTrait;
use Psr\Log\LogLevel;

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
 * @package DeepWebSolutions\WP-Framework\Utilities\Shortcodes
 */
class ShortcodesService implements LoggingServiceAwareInterface, PluginAwareInterface, RunnableInterface, ResettableInterface {
	// region TRAITS

	use LoggingServiceAwareTrait;
	use PluginAwareTrait;
	use RunnableTrait;
	use ResettableTrait;

	// endregion

	// region FIELDS AND CONSTANTS

	/**
	 * The shortcodes registered with WordPress that can be used after the service runs.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     array
	 */
	protected array $shortcodes = array();

	// endregion

	// region MAGIC METHODS

	/**
	 * ShortcodesService constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   PluginInterface     $plugin             Instance of the plugin.
	 * @param   LoggingService      $logging_service    Instance of the logging service.
	 */
	public function __construct( PluginInterface $plugin, LoggingService $logging_service ) {
		$this->set_plugin( $plugin );
		$this->set_logging_service( $logging_service );
	}

	// endregion

	// region GETTERS

	/**
	 * Returns the list of shortcodes registered with WP by this service instance on run.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	public function get_shortcodes(): array {
		return $this->shortcodes;
	}

	// endregion

	// region INHERITED FUNCTIONS

	/**
	 * Register the shortcodes with WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  RunFailureException|null
	 */
	public function run(): ?RunFailureException {
		if ( is_null( $this->is_ran ) ) {
			foreach ( $this->shortcodes as $hook ) {
				if ( empty( $hook['component'] ) ) {
					add_shortcode( $hook['tag'], $hook['callback'] );
				} else {
					add_shortcode( $hook['tag'], array( $hook['component'], $hook['callback'] ) );
				}
			}

			$this->is_ran     = true;
			$this->run_result = $this->reset_result = $this->is_reset = null; // phpcs:ignore
		} else {
			/* @noinspection PhpIncompatibleReturnTypeInspection */
			return $this->log_event_and_doing_it_wrong_and_return_exception(
				__FUNCTION__,
				'The shortcodes service has been ran already. Please reset it before running it again.',
				'1.0.0',
				RunFailureException::class,
				null,
				LogLevel::NOTICE,
				'framework'
			);
		}

		return $this->run_result;
	}

	/**
	 * Un-registers the shortcodes with WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  ResetFailureException|null
	 */
	public function reset(): ?ResetFailureException {
		if ( is_null( $this->is_reset ) ) {
			foreach ( $this->shortcodes as $shortcode ) {
				remove_shortcode( $shortcode );
			}

			$this->is_reset     = true;
			$this->reset_result = $this->is_ran = $this->run_result = null; // phpcs:ignore
		} else {
			/* @noinspection PhpIncompatibleReturnTypeInspection */
			return $this->log_event_and_doing_it_wrong_and_return_exception(
				__FUNCTION__,
				'The shortcodes service has been reset already. Please run it before resetting it again.',
				'1.0.0',
				ResetFailureException::class,
				null,
				LogLevel::NOTICE,
				'framework'
			);
		}

		return $this->reset_result;
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

	/**
	 * Removes all shortcodes from the collection to be registered with WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function remove_all_shortcodes(): void {
		$this->shortcodes = array();
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
