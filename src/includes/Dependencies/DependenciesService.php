<?php

namespace DeepWebSolutions\Framework\Utilities\Dependencies;

use DeepWebSolutions\Framework\Foundations\Utilities\Services\AbstractMultiHandlerService;

\defined( 'ABSPATH' ) || exit;

/**
 * Queries a given handler for dependencies fulfillment status.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @package DeepWebSolutions\WP-Framework\Utilities\Dependencies
 */
class DependenciesService extends AbstractMultiHandlerService {
	// region INHERITED METHODS

	/**
	 * Returns the instance of a given handler.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $handler_id     The ID of the handler to retrieve.
	 *
	 * @return  DependenciesHandlerInterface|null
	 */
	public function get_handler( string $handler_id ): ?DependenciesHandlerInterface { // phpcs:ignore
		/* @noinspection PhpIncompatibleReturnTypeInspection */
		return parent::get_handler( $handler_id );
	}

	// endregion

	// region METHODS

	/**
	 * Returns the dependencies being checked by a given handler.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string      $handler_id   The ID of the handler to retrieve the dependencies from.
	 *
	 * @return  array
	 */
	public function get_dependencies( string $handler_id ): array {
		return $this->get_handler( $handler_id )->get_dependencies();
	}

	/**
	 * Returns the missing dependencies of a given handler.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string      $handler_id   The ID of the handler to retrieve the missing dependencies from.
	 *
	 * @return  array
	 */
	public function get_missing_dependencies( string $handler_id ): array {
		return $this->get_handler( $handler_id )->get_missing_dependencies();
	}

	/**
	 * Returns the dependencies status of a given handler.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string      $handler_id   The ID of the handler to retrieve the dependencies status from.
	 *
	 * @return  mixed
	 */
	public function are_dependencies_fulfilled( string $handler_id ) {
		return $this->get_handler( $handler_id )->are_dependencies_fulfilled();
	}

	// endregion

	// region HELPERS

	/**
	 * Returns the class name of the used handler for better type-checking.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	protected function get_handler_class(): string {
		return DependenciesHandlerInterface::class;
	}

	// endregion
}
