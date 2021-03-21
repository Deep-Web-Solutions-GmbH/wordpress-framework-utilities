<?php

namespace DeepWebSolutions\Framework\Utilities\Assets;

use DeepWebSolutions\Framework\Foundations\Actions\RunnableInterface;
use DeepWebSolutions\Framework\Foundations\Logging\LoggingService;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginInterface;
use DeepWebSolutions\Framework\Foundations\Utilities\Services\AbstractMultiHandlerService;
use DeepWebSolutions\Framework\Foundations\Utilities\Services\Actions\RunnableHandlerServiceTrait;
use DeepWebSolutions\Framework\Helpers\WordPress\Request;
use DeepWebSolutions\Framework\Helpers\WordPress\RequestTypesEnum;
use DeepWebSolutions\Framework\Utilities\Assets\Handlers\ScriptsHandler;
use DeepWebSolutions\Framework\Utilities\Assets\Handlers\StylesHandler;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksService;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksServiceRegisterInterface;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksServiceRegisterTrait;

\defined( 'ABSPATH' ) || exit;

/**
 * Compatibility layer between the framework and WordPress' API for assets.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Assets
 */
class AssetsService extends AbstractMultiHandlerService implements HooksServiceRegisterInterface, RunnableInterface {
	// region TRAITS

	use HooksServiceRegisterTrait;
	use RunnableHandlerServiceTrait;

	// endregion

	// region MAGIC METHODS

	/**
	 * AssetsService constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   PluginInterface             $plugin             Instance of the plugin.
	 * @param   LoggingService              $logging_service    Instance of the logging service.
	 * @param   HooksService                $hooks_service      Instance of the hooks service.
	 * @param   AssetsHandlerInterface[]    $handlers           Assets handlers to run.
	 */
	public function __construct( PluginInterface $plugin, LoggingService $logging_service, HooksService $hooks_service, array $handlers = array() ) {
		parent::__construct( $plugin, $logging_service, $handlers );
		$this->register_hooks( $hooks_service );
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

	// endregion

	// region HELPERS

	/**
	 * Returns a list of what the default handlers actually are for the inheriting service.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	protected function get_default_handlers_classes(): array {
		return array( StylesHandler::class, ScriptsHandler::class );
	}

	/**
	 * Returns the class name of the used handler for better type-checking.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	protected function get_handler_class(): string {
		return AssetsHandlerInterface::class;
	}

	// endregion
}
