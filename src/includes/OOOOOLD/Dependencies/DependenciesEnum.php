<?php

namespace DeepWebSolutions\Framework\Utilities\Dependencies;

/**
 * Valid values for dependency types.
 *
 * @see     DependenciesContainer
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Dependencies
 */
final class DependenciesEnum {
	/**
	 * Enum-type constant for referring to required dependencies.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  public
	 * @var     string
	 */
	public const OPTIONALITY_REQUIRED = 'required';

	/**
	 * Enum-type constant for referring to optional dependencies.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  public
	 * @var     string
	 */
	public const OPTIONALITY_OPTIONAL = 'optional';

	/**
	 * Enum-type constant for referring to php extensions dependencies.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  public
	 * @var     string
	 */
	public const TYPE_PHP_EXTENSIONS = 'php_extensions';

	/**
	 * Enum-type constant for referring to php functions dependencies.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  public
	 * @var     string
	 */
	public const TYPE_PHP_FUNCTIONS = 'php_functions';

	/**
	 * Enum-type constant for referring to php settings dependencies.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  public
	 * @var     string
	 */
	public const TYPE_PHP_SETTINGS = 'php_settings';

	/**
	 * Enum-type constant for referring to WP plugins dependencies.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  public
	 * @var     string
	 */
	public const TYPE_WP_PLUGINS = 'active_plugins';
}
