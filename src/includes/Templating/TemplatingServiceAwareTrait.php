<?php

namespace DeepWebSolutions\Framework\Utilities\Templating;

\defined( 'ABSPATH' ) || exit;

/**
 * Trait for working with the templating service.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Templating
 */
trait TemplatingServiceAwareTrait {
	// region FIELDS AND CONSTANTS

	/**
	 * Templating service instance..
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     TemplatingService
	 */
	protected TemplatingService $templating_service;

	// endregion

	// region GETTERS

	/**
	 * Gets the current templating service instance set on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  TemplatingService
	 */
	public function get_templating_service(): TemplatingService {
		return $this->templating_service;
	}

	// endregion

	// region SETTERS

	/**
	 * Sets a logging templating instance on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   TemplatingService       $templating_service     Templating service instance to use from now on.
	 */
	public function set_templating_service( TemplatingService $templating_service ) {
		$this->templating_service = $templating_service;
	}

	// endregion

	// region METHODS

	/**
	 * Wrapper around the service's own method.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $slug           Template slug.
	 * @param   string  $name           Template name. Pass an empty string to ignore.
	 * @param   string  $template_path  The relative path of the template from the root of the active theme.
	 * @param   string  $default_path   The absolute path to the template's folder within the plugin.
	 * @param   array   $args           Arguments to pass on to the template.
	 * @param   string  $constant_name  The name of the constant that should evaluate to true for debugging to be considered active.
	 *
	 * @return  void
	 */
	public function load_template_part( string $slug, string $name, string $template_path, string $default_path, array $args = array(), string $constant_name = 'TEMPLATE_DEBUG' ): void {
		$this->get_templating_service()->load_template_part( $slug, $name, $template_path, $default_path, $args, $constant_name );
	}

	/**
	 * Wrapper around the service's own method.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $slug           Template slug.
	 * @param   string  $name           Template name. Pass an empty string to ignore.
	 * @param   string  $template_path  The relative path of the template from the root of the active theme.
	 * @param   string  $default_path   The absolute path to the template's folder within the plugin.
	 * @param   array   $args           Arguments to pass on to the template.
	 * @param   string  $constant_name  The name of the constant that should evaluate to true for debugging to be considered active.
	 *
	 * @return  string
	 */
	public function get_template_part_html( string $slug, string $name, string $template_path, string $default_path, array $args = array(), string $constant_name = 'TEMPLATE_DEBUG' ): string {
		return $this->get_templating_service()->get_template_part_html( $slug, $name, $template_path, $default_path, $args, $constant_name );
	}

	/**
	 * Wrapper around the service's own method.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $template_name  The name of the template file searched for.
	 * @param   string  $template_path  The relative path of the template from the root of the active theme.
	 * @param   string  $default_path   The absolute path to the template's folder within the plugin.
	 * @param   array   $args           Arguments to pass on to the template.
	 * @param   string  $constant_name  The name of the constant that should evaluate to true for debugging to be considered active.
	 *
	 * @return  void
	 */
	public function load_template( string $template_name, string $template_path, string $default_path, array $args = array(), string $constant_name = 'TEMPLATE_DEBUG' ): void {
		$this->get_templating_service()->load_template( $template_name, $template_path, $default_path, $args, $constant_name );
	}

	/**
	 * Wrapper around the service's own method.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $template_name  The name of the template file searched for.
	 * @param   string  $template_path  The relative path of the template from the root of the active theme.
	 * @param   string  $default_path   The absolute path to the template's folder within the plugin.
	 * @param   array   $args           Arguments to pass on to the template.
	 * @param   string  $constant_name  The name of the constant that should evaluate to true for debugging to be considered active.
	 *
	 * @return  string
	 */
	public function get_template_html( string $template_name, string $template_path, string $default_path, array $args = array(), string $constant_name = 'TEMPLATE_DEBUG' ): string {
		return $this->get_templating_service()->get_template_html( $template_name, $template_path, $default_path, $args, $constant_name );
	}

	/**
	 * Wrapper around the service's own method.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $template_name  The name of the template file searched for.
	 * @param   string  $template_path  The relative path of the template from the root of the active theme.
	 * @param   string  $default_path   The absolute path to the template's folder within the plugin.
	 * @param   string  $constant_name  The name of the constant that should evaluate to true for debugging to be considered active.
	 *
	 * @return  string
	 */
	public function locate_template( string $template_name, string $template_path, string $default_path, string $constant_name = 'TEMPLATE_DEBUG' ): string {
		return $this->get_templating_service()->locate_template( $template_name, $template_path, $default_path, $constant_name );
	}

	// endregion
}
