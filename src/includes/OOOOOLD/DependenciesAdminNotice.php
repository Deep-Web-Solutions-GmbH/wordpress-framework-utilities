<?php

namespace DeepWebSolutions\Framework\Utilities\Services\Traits\Dependencies;

use DeepWebSolutions\Framework\Utilities\Handlers\AdminNoticesHandler;
use DeepWebSolutions\Framework\Utilities\Handlers\Traits\AdminNotices;
use DeepWebSolutions\Framework\Utilities\Traits\IsActive\Dependencies\DependenciesRequired;

defined( 'ABSPATH' ) || exit;

/**
 * Trait for working with the dependencies service and outputting potential error notices on the admin side.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Services\Traits\Dependencies
 */
trait DependenciesAdminNotice {
	use DependenciesAwareTrait;
	use AdminNotices;
	use DependenciesRequired;

	// region METHODS

	/**
	 * Register any potential admin notices.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   AdminNoticesHandler     $admin_notices_handler      Instance of the admin notices handler.
	 */
	public function register_admin_notices( AdminNoticesHandler $admin_notices_handler ): void {
		$this->set_admin_notices_handler( $admin_notices_handler );

		$this->maybe_register_required_missing_php_extensions_notice( $admin_notices_handler );
		$this->maybe_register_optional_missing_php_extensions_notice( $admin_notices_handler );

		$this->maybe_register_required_missing_php_functions_notice( $admin_notices_handler );
		$this->maybe_register_optional_missing_php_functions_notice( $admin_notices_handler );

		$this->maybe_register_required_incompatible_php_settings_notice( $admin_notices_handler );
		$this->maybe_register_optional_incompatible_php_settings_notice( $admin_notices_handler );

		$this->maybe_register_required_missing_plugins_notice( $admin_notices_handler );
		$this->maybe_register_optional_missing_plugins_notice( $admin_notices_handler );
	}

	/**
	 * Maybe register an admin notice about missing required PHP extensions.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   AdminNoticesHandler     $admin_notices_handler      Instance of the admin notices handler.
	 */
	protected function maybe_register_required_missing_php_extensions_notice( AdminNoticesHandler $admin_notices_handler ) {
		$missing_php_extensions = $this->get_dependencies_service()->get_missing_php_extensions( 'required' );
		if ( ! empty( $missing_php_extensions ) ) {
			$admin_notices_handler->add_admin_notice(
				sprintf(
					/* translators: 1. Plugin or identifiable name, 2. Comma-separated list of missing PHP extensions. */
					_n(
						'<strong>%1$s</strong> requires the %2$s PHP extension to function. Contact your host or server administrator to install and configure the missing extension.',
						'<strong>%1$s</strong> requires the following PHP extensions to function: %2$s. Contact your host or server administrator to install and configure the missing extensions.',
						count( $missing_php_extensions ),
						'dws-wp-framework-utilities'
					),
					esc_html( $this->get_registrant_name() ),
					'<strong>' . implode( ', ', $missing_php_extensions ) . '</strong>'
				),
				$this->get_notice_id( 'missing-php-extensions' ),
				array(
					'capability'  => 'activate_plugins',
					'dismissible' => false,
				)
			);
		}
	}

	/**
	 * Maybe register an admin notice about missing optional PHP extensions.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   AdminNoticesHandler     $admin_notices_handler      Instance of the admin notices handler.
	 */
	protected function maybe_register_optional_missing_php_extensions_notice( AdminNoticesHandler $admin_notices_handler ) {
		$missing_php_extensions = $this->get_dependencies_service()->get_missing_php_extensions( 'optional' );
		if ( ! empty( $missing_php_extensions ) ) {
			// The extra information is useful because the notice is dismissible.
			$notice_id = $this->get_notice_id( 'missing-php-extensions', array( md5( wp_json_encode( $missing_php_extensions ) ) ) );

			$admin_notices_handler->add_global_admin_notice(
				sprintf(
					/* translators: 1. Plugin or identifiable name, 2. Comma-separated list of missing PHP extensions. */
					_n(
						'<strong>%1$s</strong> may behave unexpectedly because the %2$s PHP extension is missing. Contact your host or server administrator to install and configure the missing extension.',
						'<strong>%1$s</strong> may behave unexpectedly because the following PHP extensions are missing: %2$s. Contact your host or server administrator to install and configure the missing extensions.',
						count( $missing_php_extensions ),
						'dws-wp-framework-utilities'
					),
					esc_html( $this->get_registrant_name() ),
					'<strong>' . implode( ', ', $missing_php_extensions ) . '</strong>'
				),
				$notice_id,
				array(
					'capability' => 'activate_plugins',
				)
			);
		}
	}

	/**
	 * Maybe register an admin notice about missing required PHP functions.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   AdminNoticesHandler     $admin_notices_handler      Instance of the admin notices handler.
	 */
	protected function maybe_register_required_missing_php_functions_notice( AdminNoticesHandler $admin_notices_handler ) {
		$missing_php_functions = $this->get_dependencies_service()->get_missing_php_functions( 'required' );
		if ( ! empty( $missing_php_functions ) ) {
			$admin_notices_handler->add_admin_notice(
				sprintf(
					/* translators: 1. Plugin name or identifiable name, 2. Comma-separated list of missing PHP functions. */
					_n(
						'<strong>%1$s</strong> requires the %2$s PHP function to exist. Contact your host or server administrator to install and configure the missing function.',
						'<strong>%1$s</strong> requires the following PHP functions to exist: %2$s. Contact your host or server administrator to install and configure the missing functions.',
						count( $missing_php_functions ),
						'dws-wp-framework-utilities'
					),
					esc_html( $this->get_registrant_name() ),
					'<strong>' . implode( ', ', $missing_php_functions ) . '</strong>'
				),
				$this->get_notice_id( 'missing-functions' ),
				array(
					'capability'  => 'activate_plugins',
					'dismissible' => false,
				)
			);
		}
	}

	/**
	 * Maybe register an admin notice about missing optional PHP functions.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   AdminNoticesHandler     $admin_notices_handler      Instance of the admin notices handler.
	 */
	protected function maybe_register_optional_missing_php_functions_notice( AdminNoticesHandler $admin_notices_handler ) {
		$missing_php_functions = $this->get_dependencies_service()->get_missing_php_functions( 'optional' );
		if ( ! empty( $missing_php_functions ) ) {
			// The extra information is useful because the notice is dismissible.
			$notice_id = $this->get_notice_id( 'missing-php-functions', array( md5( wp_json_encode( $missing_php_functions ) ) ) );

			$admin_notices_handler->add_global_admin_notice(
				sprintf(
					/* translators: 1. Plugin name or identifiable name, 2. Comma-separated list of missing PHP functions. */
					_n(
						'<strong>%1$s</strong> may behave unexpectedly because the %2$s PHP function is missing. Contact your host or server administrator to install and configure the missing function.',
						'<strong>%1$s</strong> may behave unexpectedly because the following PHP functions are missing: %2$s. Contact your host or server administrator to install and configure the missing functions.',
						count( $missing_php_functions ),
						'dws-wp-framework-utilities'
					),
					esc_html( $this->get_registrant_name() ),
					'<strong>' . implode( ', ', $missing_php_functions ) . '</strong>'
				),
				$notice_id,
				array(
					'capability' => 'activate_plugins',
				)
			);
		}
	}

	/**
	 * Maybe register an admin notice about incompatible required PHP settings.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   AdminNoticesHandler     $admin_notices_handler      Instance of the admin notices handler.
	 */
	protected function maybe_register_required_incompatible_php_settings_notice( AdminNoticesHandler $admin_notices_handler ) {
		$incompatible_php_settings = $this->get_dependencies_service()->get_incompatible_php_settings( 'required' );
		if ( ! empty( $incompatible_php_settings ) ) {
			$message = sprintf(
				/* translators: Plugin name or identifiable name. */
				__( '<strong>%s</strong> cannot run because the following PHP configuration settings are expected:', 'dws-wp-framework-utilities' ),
				esc_html( $this->get_registrant_name() )
			) . '<ul>';

			foreach ( $incompatible_php_settings as $setting => $values ) {
				$setting_message = "<code>{$setting} = {$values['expected']}</code>";
				if ( ! empty( $values['type'] ) ) {
					switch ( $values['type'] ) {
						case 'min':
							$setting_message = sprintf(
								/* translators: PHP settings value. */
								__( '%s or higher', 'dws-wp-framework-utilities' ),
								$setting_message
							);
					}
				}

				$message .= "<li>{$setting_message}</li>";
			}

			$message .= '</ul>' . __( 'Please contact your hosting provider or server administrator to configure these settings.', 'dws-wp-framework-utilities' );

			$admin_notices_handler->add_admin_notice(
				$message,
				$this->get_notice_id( 'incompatible-php-settings' ),
				array(
					'capability'  => 'activate_plugins',
					'dismissible' => false,
				)
			);
		}
	}

	/**
	 * Maybe register an admin notice about incompatible optional PHP settings.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   AdminNoticesHandler     $admin_notices_handler      Instance of the admin notices handler.
	 */
	protected function maybe_register_optional_incompatible_php_settings_notice( AdminNoticesHandler $admin_notices_handler ) {
		$incompatible_php_settings = $this->get_dependencies_service()->get_incompatible_php_settings( 'optional' );
		if ( ! empty( $incompatible_php_settings ) ) {
			// The extra information is useful because the notice is dismissible.
			$notice_id = $this->get_notice_id( 'incompatible-php-settings', array( md5( wp_json_encode( $incompatible_php_settings ) ) ) );

			if ( false === $admin_notices_handler->is_notice_dismissed( $notice_id, true ) ) {
				$message = sprintf(
					/* translators: Plugin name or identifiable name. */
					__( '<strong>%s</strong> may behave unexpectedly because the following PHP configuration settings are expected:', 'dws-wp-framework-utilities' ),
					esc_html( $this->get_registrant_name() )
				) . '<ul>';

				foreach ( $incompatible_php_settings as $setting => $values ) {
					$setting_message = "<code>{$setting} = {$values['expected']}</code>";
					if ( ! empty( $values['type'] ) ) {
						switch ( $values['type'] ) {
							case 'min':
								$setting_message = sprintf(
									/* translators: PHP settings value. */
									__( '%s or higher', 'dws-wp-framework-utilities' ),
									$setting_message
								);
						}
					}

					$message .= "<li>{$setting_message}</li>";
				}

				$message .= '</ul>' . __( 'Please contact your hosting provider or server administrator to configure these settings. The plugin will attempt to run despite this warning.', 'dws-wp-framework-utilities' );

				$admin_notices_handler->add_global_admin_notice(
					$message,
					$notice_id,
					array(
						'capability' => 'activate_plugins',
					)
				);
			}
		}
	}

	/**
	 * Maybe register an admin notice about missing required WP plugins.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   AdminNoticesHandler     $admin_notices_handler      Instance of the admin notices handler.
	 */
	protected function maybe_register_required_missing_plugins_notice( AdminNoticesHandler $admin_notices_handler ) {
		$missing_active_plugins = $this->get_dependencies_service()->get_missing_active_plugins( 'required' );
		if ( ! empty( $missing_active_plugins ) ) {
			$admin_notices_handler->add_admin_notice(
				sprintf(
					/* translators: 1. Plugin name or identifiable name, 2. Comma-separated list of missing active plugins. */
					_n(
						'<strong>%1$s</strong> requires the %2$s plugin to be installed and active. Please install and activate the plugin first.',
						'<strong>%1$s</strong> requires the following plugins to be installed and active: %2$s. Please install and activate these plugins first.',
						count( $missing_active_plugins ),
						'dws-wp-framework-utilities'
					),
					esc_html( $this->get_registrant_name() ),
					'<strong>' . $this->format_plugins_list( $missing_active_plugins ) . '</strong>'
				),
				$this->get_notice_id( 'missing-active-plugins' ),
				array(
					'capability'  => 'activate_plugins',
					'dismissible' => false,
				)
			);
		}
	}

	/**
	 * Maybe register an admin notice about missing optional WP plugins.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   AdminNoticesHandler     $admin_notices_handler      Instance of the admin notices handler.
	 */
	protected function maybe_register_optional_missing_plugins_notice( AdminNoticesHandler $admin_notices_handler ) {
		$missing_active_plugins = $this->get_dependencies_service()->get_missing_active_plugins( 'optional' );
		if ( ! empty( $missing_active_plugins ) ) {
			// The extra information is useful because the notice is dismissible.
			$notice_id = $this->get_notice_id( 'missing-active-plugins', array( md5( wp_json_encode( $missing_active_plugins ) ) ) );

			$admin_notices_handler->add_global_admin_notice(
				sprintf(
					/* translators: 1. Plugin name or identifiable name, 2. Comma-separated list of missing active plugins. */
					_n(
						'<strong>%1$s</strong> may behave unexpectedly because the %2$s plugin is either not installed or not active. Please install and activate the plugin first.',
						'<strong>%1$s</strong> may behave unexpectedly because the following plugins are either not installed or active: %2$s. Please install and activate these plugins first.',
						count( $missing_active_plugins ),
						'dws-wp-framework-utilities'
					),
					esc_html( $this->get_registrant_name() ),
					'<strong>' . $this->format_plugins_list( $missing_active_plugins ) . '</strong>'
				),
				$notice_id,
				array(
					'capability' => 'activate_plugins',
				)
			);
		}
	}

	// endregion

	// region HELPERS

	/**
	 * Formats the list of missing plugin dependencies in a human-friendly way.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $missing_plugins    List of missing plugin dependencies.
	 *
	 * @return  string
	 */
	protected function format_plugins_list( array $missing_plugins ): string {
		$formatted_plugin_names = array();

		foreach ( $missing_plugins as $missing_plugin ) {
			$formatted_plugin_name = $missing_plugin['name'];

			if ( isset( $missing_plugin['min_version'] ) ) {
				$formatted_plugin_name .= " {$missing_plugin['min_version']}+";
			}

			if ( isset( $missing_plugin['version'] ) ) {
				$formatted_version = sprintf(
					/* translators: %s: Installed version of the dependant plugin */
					__( 'You\'re running version %s', 'dws-wp-framework-utilities' ),
					$missing_plugin['version']
				);
				$formatted_plugin_name .= ' <em>(' . esc_html( $formatted_version ) . ')</em>';
			}

			$formatted_plugin_names[] = $formatted_plugin_name;
		}

		return join( ', ', $formatted_plugin_names );
	}

	// endregion
}
