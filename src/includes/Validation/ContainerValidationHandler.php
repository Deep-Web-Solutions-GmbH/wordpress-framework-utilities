<?php

namespace DeepWebSolutions\Framework\Utilities\Validation;

use DeepWebSolutions\Framework\Foundations\Exceptions\InexistentPropertyException;
use DeepWebSolutions\Framework\Foundations\PluginUtilities\DependencyInjection\ContainerAwareInterface;
use DeepWebSolutions\Framework\Foundations\PluginUtilities\DependencyInjection\ContainerAwareTrait;
use DeepWebSolutions\Framework\Helpers\DataTypes\Arrays;
use DeepWebSolutions\Framework\Helpers\Security\Validation;
use Psr\Container\ContainerInterface;

\defined( 'ABSPATH' ) || exit;

/**
 * A validation handler that stores its data in a PSR-11 container.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Validation
 */
class ContainerValidationHandler extends AbstractValidationHandler implements ContainerAwareInterface {
	// region TRAITS

	use ContainerAwareTrait;

	// endregion

	// region MAGIC METHODS

	/**
	 * ContainerValidationHandler constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string                      $handler_id     The ID of the handler instance.
	 * @param   ContainerInterface|null     $container      PSR-11 container with the validation values.
	 */
	public function __construct( string $handler_id, ?ContainerInterface $container ) {
		parent::__construct( $handler_id );

		if ( ! \is_null( $container ) ) {
			$this->set_container( $container );
		}
	}

	// endregion

	// region METHODS

	/**
	 * Retrieves the default value for a given key.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string      $key    The key to retrieve the default value for.
	 *
	 * @noinspection PhpMissingReturnTypeInspection
	 * @return  InexistentPropertyException|mixed
	 */
	public function get_default_value( string $key ) {
		return $this->get_container_value( 'defaults/' . $key );
	}

	/**
	 * Retrieves a list of all default values.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	public function get_known_default_values(): array {
		return \array_keys( $this->get_container_value( 'defaults' ) );
	}

	/**
	 * Retrieves the supported options for a given key.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $key    The key to retrieve the supported options for.
	 *
	 * @return  InexistentPropertyException|array
	 */
	public function get_supported_options( string $key ) {
		return $this->get_container_value( 'options/' . $key );
	}

	/**
	 * Retrieves a list of all supported options configurations.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	public function get_known_supported_options(): array {
		return \array_keys( $this->get_container_value( 'options' ) );
	}

	/**
	 * Validates a given value as a boolean.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   mixed   $value  The value to validate.
	 * @param   string  $key    The composite key to retrieve the default value.
	 *
	 * @throws  InexistentPropertyException     Thrown when the default value was not found.
	 *
	 * @return  bool
	 */
	public function validate_boolean_value( $value, string $key ): bool {
		$default = $this->get_default_value_or_throw( $key );
		$default = \is_bool( $default ) ? $default : Validation::validate_boolean( $default, false );

		return Validation::validate_boolean( $value, $default );
	}

	/**
	 * Validates a given value as an int.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   mixed   $value  The value to validate.
	 * @param   string  $key    The composite key to retrieve the default value.
	 *
	 * @throws  InexistentPropertyException     Thrown when the default value was not found.
	 *
	 * @return  int
	 */
	public function validate_integer_value( $value, string $key ): int {
		$default = $this->get_default_value_or_throw( $key );
		$default = \is_int( $default ) ? $default : Validation::validate_integer( $default, 0 );

		return Validation::validate_integer( $value, $default );
	}

	/**
	 * Validates a given value as a float.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   mixed   $value  The value to validate.
	 * @param   string  $key    The composite key to retrieve the default value.
	 *
	 * @throws  InexistentPropertyException     Thrown when the default value was not found.
	 *
	 * @return  float
	 */
	public function validate_float_value( $value, string $key ): float {
		$default = $this->get_default_value_or_throw( $key );
		$default = \is_float( $default ) ? $default : Validation::validate_float( $default, 0.0 );

		return Validation::validate_float( $value, $default );
	}

	/**
	 * Validates a given value as a callable.
	 *
	 * @param   mixed   $value  The value to validate.
	 * @param   string  $key    The composite key to retrieve the default value.
	 *
	 * @throws  InexistentPropertyException     Thrown when the default value was not found.
	 *
	 * @return  callable
	 */
	public function validate_callback_value( $value, string $key ): callable {
		$default = $this->get_default_value_or_throw( $key );
		$default = \is_callable( $default ) ? $default : Validation::validate_callback(
			$default,
			function( $value ) {
				return $value;
			}
		);

		return Validation::validate_callback( $value, $default );
	}

	/**
	 * Validates a given value as a valid option.
	 *
	 * @param   mixed   $value          The value to validate.
	 * @param   string  $options_key    The composite key to retrieve the supported values.
	 * @param   string  $default_key    The composite key to retrieve the default value.
	 *
	 * @throws  InexistentPropertyException     Thrown when the default value or the supported values were not found.
	 *
	 * @return  mixed
	 */
	public function validate_supported_value( $value, string $options_key, string $default_key ) {
		$default          = $this->get_default_value_or_throw( $default_key );
		$supported_values = $this->get_supported_options_or_throw( $options_key );

		if ( Arrays::has_string_keys( $supported_values ) ) {
			$supported_values = \array_keys( $supported_values );
		}

		return Validation::validate_allowed_value( $value, $supported_values, $default );
	}

	// endregion

	// region HELPERS

	/**
	 * Retrieves a nested value from the container using a composite key.
	 *
	 * @param   string  $key    Composite key of the value to retrieve.
	 *
	 * @noinspection PhpMissingReturnTypeInspection
	 * @return  InexistentPropertyException|mixed
	 */
	protected function get_container_value( string $key ) {
		$boom = \explode( '/', $key );
		$key  = \array_shift( $boom );

		$value = $this->get_container_entry( $key );
		if ( \is_null( $value ) ) {
			return new InexistentPropertyException( \sprintf( 'Inexistent container entry: %s', $key ) );
		}

		foreach ( $boom as $key ) {
			if ( isset( $value[ $key ] ) || array_key_exists( $key, $value ) ) {
				$value = $value[ $key ];
			} else {
				return new InexistentPropertyException( \sprintf( 'Inexistent container entry: %s', $key ) );
			}
		}

		return $value;
	}

	/**
	 * Retrieves the default value for a given key or throws the exception if not found.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $key    The key inside the container.
	 *
	 * @throws  InexistentPropertyException     Thrown when the default value or the supported values were not found inside the containers.
	 *
	 * @noinspection PhpMissingReturnTypeInspection
	 * @return  mixed
	 */
	protected function get_default_value_or_throw( string $key ) {
		$default = $this->get_default_value( $key );

		if ( $default instanceof InexistentPropertyException ) {
			throw $default;
		}

		return $default;
	}

	/**
	 * Retrieves the supported options for a given key or throws the exception if not found.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $key    The key inside the container.
	 *
	 * @throws  InexistentPropertyException     Thrown when the default value or the supported values were not found inside the containers.
	 *
	 * @return  array
	 */
	protected function get_supported_options_or_throw( string $key ): array {
		$options = $this->get_supported_options( $key );

		if ( $options instanceof InexistentPropertyException ) {
			throw $options;
		}

		return $options;
	}

	// endregion
}
