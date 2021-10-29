<?php

namespace DeepWebSolutions\Framework\Utilities\Validation;

use DeepWebSolutions\Framework\Foundations\Exceptions\NotSupportedException;
use DeepWebSolutions\Framework\Foundations\Services\AbstractMultiHandlerService;

\defined( 'ABSPATH' ) || exit;

/**
 * Performs various data validation actions against values defined in various given handlers.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Validation
 */
class ValidationService extends AbstractMultiHandlerService implements ValidationServiceInterface {
	// region INHERITED METHODS

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function get_handler( string $handler_id ): ?ValidationHandlerInterface { // phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod.Found
		/* @noinspection PhpIncompatibleReturnTypeInspection */
		return parent::get_handler( $handler_id );
	}

	// endregion

	// region METHODS

	/**
	 * Validates a value based on passed parameters.
	 *
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   mixed   $value              The value to validate.
	 * @param   string  $default_key        The key of the default value in the within the handler.
	 * @param   string  $validation_type    The type of validation to perform. Valid values are listed in the ValidationTypesEnum class.
	 * @param   string  $handler_id         The ID of the handler to use for validation.
	 *
	 * @throws  NotSupportedException   Thrown if the validation type requested is not supported.
	 *
	 * @return  array|bool|callable|float|int|string
	 */
	public function validate_value( $value, string $default_key, string $validation_type, string $handler_id = 'settings' ) {
		switch ( $validation_type ) {
			case ValidationTypesEnum::STRING:
				return $this->validate_string( $value, $default_key, $handler_id );
			case ValidationTypesEnum::ARRAY:
				return $this->validate_array( $value, $default_key, $handler_id );
			case ValidationTypesEnum::BOOLEAN:
				return $this->validate_boolean( $value, $default_key, $handler_id );
			case ValidationTypesEnum::INTEGER:
				return $this->validate_integer( $value, $default_key, $handler_id );
			case ValidationTypesEnum::FLOAT:
				return $this->validate_float( $value, $default_key, $handler_id );
			case ValidationTypesEnum::CALLABLE:
				return $this->validate_callable( $value, $default_key, $handler_id );
		}

		throw new NotSupportedException( 'Validation type not supported' );
	}

	/**
	 * Validates a value based on passed parameters.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   mixed   $value              The value to validate.
	 * @param   string  $default_key        The key of the default value in the within the handler.
	 * @param   string  $options_key        The key of the supported options within the handler.
	 * @param   string  $validation_type    The type of validation to perform. Valid values are listed in the ValidationTypesEnum class.
	 * @param   string  $handler_id         The ID of the handler to use for validation.
	 *
	 * @throws  NotSupportedException   Thrown if the validation type requested is not supported.
	 *
	 * @return  string|array
	 */
	public function validate_allowed_value( $value, string $default_key, string $options_key, string $validation_type, string $handler_id = 'settings' ) {
		switch ( $validation_type ) {
			case ValidationTypesEnum::STRING:
				return $this->validate_allowed_string( $value, $default_key, $options_key, $handler_id );
			case ValidationTypesEnum::ARRAY:
				return $this->validate_allowed_array( $value, $default_key, $options_key, $handler_id );
		}

		throw new NotSupportedException( 'Validation type not supported' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function validate_string( $value, string $key, string $handler_id = 'settings' ): string {
		return $this->get_handler( $handler_id )->validate_string( $value, $key );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function validate_allowed_string( $value, string $default_key, string $options_key, string $handler_id = 'settings' ): string {
		return $this->get_handler( $handler_id )->validate_allowed_string( $value, $default_key, $options_key );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function validate_array( $value, string $key, string $handler_id = 'settings' ): array {
		return $this->get_handler( $handler_id )->validate_array( $value, $key );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function validate_allowed_array( $value, string $default_key, string $options_key, string $handler_id = 'settings' ): array {
		return $this->get_handler( $handler_id )->validate_allowed_array( $value, $default_key, $options_key );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function validate_boolean( $value, string $key, string $handler_id = 'settings' ): bool {
		return $this->get_handler( $handler_id )->validate_boolean( $value, $key );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function validate_integer( $value, string $key, string $handler_id = 'settings' ): int {
		return $this->get_handler( $handler_id )->validate_integer( $value, $key );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function validate_float( $value, string $key, string $handler_id = 'settings' ): float {
		return $this->get_handler( $handler_id )->validate_float( $value, $key );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function validate_callable( $value, string $key, string $handler_id = 'settings' ): callable {
		return $this->get_handler( $handler_id )->validate_callable( $value, $key );
	}

	// endregion

	// region HELPERS

	/**
	 * {@inheritDoc}
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	protected function get_handler_class(): string {
		return ValidationHandlerInterface::class;
	}

	// endregion
}
