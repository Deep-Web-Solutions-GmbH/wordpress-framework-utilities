<?php

namespace DeepWebSolutions\Framework\Utilities\Validation\Handlers;

use DeepWebSolutions\Framework\Foundations\DependencyInjection\ContainerAwareInterface;
use DeepWebSolutions\Framework\Foundations\DependencyInjection\ContainerAwareTrait;
use DeepWebSolutions\Framework\Foundations\Exceptions\InexistentPropertyException;
use DeepWebSolutions\Framework\Helpers\DataTypes\Arrays;
use DeepWebSolutions\Framework\Helpers\DataTypes\Booleans;
use DeepWebSolutions\Framework\Helpers\DataTypes\Callables;
use DeepWebSolutions\Framework\Helpers\DataTypes\Floats;
use DeepWebSolutions\Framework\Helpers\DataTypes\Integers;
use DeepWebSolutions\Framework\Helpers\DataTypes\Strings;
use DeepWebSolutions\Framework\Utilities\Validation\AbstractValidationHandler;
use Psr\Container\ContainerInterface;

\defined( 'ABSPATH' ) || exit;

/**
 * A validation handler that stores its data in a PSR-11 container.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Validation\Handlers
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
	public function __construct( string $handler_id, ?ContainerInterface $container = null ) {
		parent::__construct( $handler_id );

		if ( ! \is_null( $container ) ) {
			$this->set_container( $container );
		}
	}

	// endregion

	// region METHODS

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function get_default_value( string $key ) {
		return $this->get_container_value( 'defaults/' . $key );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function get_known_default_values(): array {
		return \array_keys( $this->get_container_value( 'defaults' ) );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function get_supported_options( string $key ) {
		return $this->get_container_value( 'options/' . $key );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function get_known_supported_options(): array {
		return \array_keys( $this->get_container_value( 'options' ) );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function validate_string( $value, string $key ): string {
		$default = $this->get_default_value_or_throw( $key );
		$default = Strings::validate( $default, Strings::maybe_cast( $default, '' ) );

		return Strings::maybe_cast( $value, $default );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function validate_allowed_string( $value, string $default_key, string $options_key ): string {
		$value   = $this->validate_string( $value, $default_key );
		$options = $this->get_supported_options_or_throw( $options_key );

		$result = Strings::validate_allowed( $value, $options );
		if ( \is_null( $result ) && Arrays::has_string_keys( $options ) ) {
			$result = Strings::validate_allowed( $value, \array_keys( $options ) );
		}

		return $this->validate_string( $result, $default_key );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function validate_array( $value, string $key ): array {
		$default = $this->get_default_value_or_throw( $key );
		$default = Arrays::validate( $default, Arrays::maybe_cast( $default, array() ) );

		return Arrays::maybe_cast( $value, $default );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function validate_allowed_array( $value, string $default_key, string $options_key ): array {
		if ( \is_null( Arrays::validate( $value ) ) ) {
			return $this->validate_array( null, $default_key );
		}

		$options = $this->get_supported_options_or_throw( $options_key );
		$result  = \array_filter( Arrays::validate_allowed( $value, $options, false ) );
		if ( empty( $result ) && Arrays::has_string_keys( $options ) ) {
			$result = \array_filter( Arrays::validate_allowed( $value, \array_keys( $options ), false ) );
		}

		return $result;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function validate_boolean( $value, string $key ): bool {
		$default = $this->get_default_value_or_throw( $key );
		$default = Booleans::validate( $default, Booleans::maybe_cast( $default, false ) );

		return Booleans::maybe_cast( $value, $default );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function validate_integer( $value, string $key ): int {
		$default = $this->get_default_value_or_throw( $key );
		$default = Integers::validate( $default, Integers::maybe_cast( $default, 0 ) );

		return Integers::maybe_cast( $value, $default );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function validate_float( $value, string $key ): float {
		$default = $this->get_default_value_or_throw( $key );
		$default = Floats::validate( $default, Floats::maybe_cast( $default, 0.0 ) );

		return Floats::maybe_cast( $value, $default );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function validate_callable( $value, string $key ): callable {
		$default = $this->get_default_value_or_throw( $key );
		$default = Callables::validate( $default, fn( $value ) => $value );

		return Callables::validate( $value, $default );
	}

	// endregion

	// region HELPERS

	/**
	 * Retrieves a nested value from the container using a composite key.
	 *
	 * @param   string  $key    Composite key of the value to retrieve.
	 *
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
			if ( isset( $value[ $key ] ) || \array_key_exists( $key, $value ) ) { // This will support entries containing literal NULL.
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
