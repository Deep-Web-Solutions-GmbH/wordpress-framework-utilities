<?php

namespace DeepWebSolutions\Framework\Utilities\Services;

use DeepWebSolutions\Framework\Helpers\WordPress\Requests;
use DeepWebSolutions\Framework\Helpers\WordPress\Traits\FilesystemAwareTrait;
use DeepWebSolutions\Framework\Utilities\Services\Traits\LoggingServiceAwareTrait;
use Psr\Log\LogLevel;

defined( 'ABSPATH' ) || exit;

/**
 *
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Services
 */
final class TemplatingService {
	use LoggingServiceAwareTrait;
	use FilesystemAwareTrait;

	public function get_template_part( string $slug, string $name = '', array $args = array(), string $template_path = '', string $default_path = '', string $constant_name = 'TEMPLATE_DEBUG' ): void {
		if ( $name ) {
			$template = $this->locate_template( "{$slug}-{$name}.php", $template_path, $default_path, $constant_name );
		}

		if ( ! $template ) {
			$template = $this->locate_template( "{$slug}.php", $template_path, $default_path );
		}

		$filtered_template = apply_filters( 'get_template_part', $template, $slug, $name );
		if ( $filtered_template !== $template ) {
			if ( ! $this->get_wp_filesystem()->exists( $filtered_template ) ) {
				$this->log_event_and_doing_it_wrong( __FUNCTION__, sprintf( __( '%s does not exist.', 'woocommerce' ), '<code>' . $filtered_template . '</code>' ), '1.0.0', LogLevel::ERROR, 'framework' );
				return;
			}
			$template = $filtered_template;
		}

		load_template( $template, false, $args );
	}

	public function get_template( string $template_name, string $template_path, string $default_path, array $args = array(), string $constant_name = 'TEMPLATE_DEBUG' ): void {
		$template = self::locate_template( $template_name, $template_path, $default_path, $constant_name );

		$filter_template = apply_filters( 'test', $template, $template_name, $template_path, $default_path, $args, $constant_name );

		if ( $filter_template !== $template ) {
			if ( ! $this->get_wp_filesystem()->exists( $filter_template ) ) {
				$this->log_event_and_doing_it_wrong( __FUNCTION__, sprintf( __( '%s does not exist.', 'woocommerce' ), '<code>' . $filter_template . '</code>' ), '1.0.0', LogLevel::ERROR, 'framework' );
				return;
			}
			$template = $filter_template;
		}

		do_action( 'before_template_part', $template_name, $template_path, $template, $args );

		load_template( $template, false, $args );

		do_action( 'after_template_part', $template_name, $template_path, $template, $args );
	}

	public function get_template_html( string $template_name, string $template_path, string $default_path, array $args = array(), string $constant_name = 'TEMPLATE_DEBUG' ): string {
		ob_start();
		self::get_template( $template_name, $template_path, $default_path, $args, $constant_name );
		return ob_get_clean();
	}

	/**
	 * Returns the path to a template file. If the theme overwrites the file and debugging is disabled, returns the path
	 * to the theme's file, otherwise the path to the default file packaged with the plugin.
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
		$template = Requests::has_debug( $constant_name ) ? '' : locate_template(
			array(
				trailingslashit( $template_path ) . $template_name,
				$template_name,
			)
		);

		$result = empty( $template )
			? trailingslashit( $default_path ) . $template_name
			: $template;

		return apply_filters( 'test', $result, $template_name, $template_path, $default_path, $constant_name );
	}
}
