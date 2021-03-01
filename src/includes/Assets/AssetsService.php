<?php

namespace DeepWebSolutions\Framework\Utilities\Assets;

use DeepWebSolutions\Framework\Foundations\Actions\Runnable\RunFailureException;
use DeepWebSolutions\Framework\Foundations\Actions\Runnable\RunnableTrait;
use DeepWebSolutions\Framework\Foundations\Actions\RunnableInterface;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginAwareInterface;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginAwareTrait;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginInterface;
use DeepWebSolutions\Framework\Helpers\WordPress\Request;
use DeepWebSolutions\Framework\Helpers\WordPress\RequestTypesEnum;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksService;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksServiceRegisterTrait;

defined( 'ABSPATH' ) || exit;

/**
 * Compatibility layer between the framework and WordPress' API for assets.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Assets
 */
class AssetsService implements PluginAwareInterface, RunnableInterface {
	// region TRAITS

	use HooksServiceRegisterTrait;
	use PluginAwareTrait;
	use RunnableTrait;

	// endregion

	// region FIELDS AND CONSTANTS

	/**
	 * Assets handlers to run.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     AssetsHandlerInterface[]
	 */
	protected array $handlers;

	// endregion

	// region MAGIC METHODS

	/**
	 * AssetsService constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   PluginInterface             $plugin             Instance of the plugin.
	 * @param   HooksService                $hooks_service      Instance of the hooks service.
	 * @param   AssetsHandlerInterface[]    $handlers           Assets handlers to run.
	 */
	public function __construct( PluginInterface $plugin, HooksService $hooks_service, array $handlers = array() ) {
		$this->set_plugin( $plugin );
		$this->register_hooks( $hooks_service );
		$this->set_handlers( $handlers );
	}

	// endregion

	// region SETTERS

	/**
	 * Sets the list of handlers to check.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $handlers   Collection of handlers to check.
	 *
	 * @return  $this
	 */
	public function set_handlers( array $handlers ): AssetsService {
		$this->handlers = array();

		foreach ( $handlers as $handler ) {
			if ( $handler instanceof AssetsHandlerInterface ) {
				$this->handlers[] = $handler;
			}
		}

		return $this;
	}

	// endregion

	// region INHERITED METHODS

	/**
	 * Registers hooks with the hooks service.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   HooksService    $hooks_service      Instance of the hooks service.
	 */
	public function register_hooks( HooksService $hooks_service ): void {
		$hook = Request::is_type( RequestTypesEnum::FRONTEND_REQUEST ) ? 'wp_enqueue_scripts' : 'admin_enqueue_scripts';
		$hooks_service->add_action( $hook, $this, 'run', PHP_INT_MAX );
	}

	/**
	 * Registers and enqueues the assets with WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  RunFailureException|null
	 */
	public function run(): ?RunFailureException {
		if ( is_null( $this->is_ran ) ) {
			$this->is_ran     = true;
			$this->run_result = null;

			foreach ( $this->handlers as $handler ) {
				$result = $handler->run();
				if ( ! is_null( $result ) ) {
					$this->run_result = $result;
					break;
				}
			}
		}

		return $this->run_result;
	}

	// endregion

	// region GETTERS

	/**
	 * Returns the list of handlers registered to run.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  AssetsHandlerInterface[]
	 */
	public function get_handlers(): array {
		return $this->handlers;
	}

	// endregion

	// region METHODS

	/**
	 * Adds a handler to the list of handlers to run.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   AssetsHandlerInterface    $handler    Handler to add.
	 *
	 * @return  $this
	 */
	public function register_handler( AssetsHandlerInterface $handler ): AssetsService {
		$this->handlers[] = $handler;
		return $this;
	}

	// endregion
}
