<?php

namespace DeepWebSolutions\Framework\Utilities\Dependencies\Actions;

use DeepWebSolutions\Framework\Foundations\Actions\Initializable\Integrations\SetupableInactiveTrait;
use DeepWebSolutions\Framework\Foundations\Actions\Setupable\SetupableExtensionTrait;
use DeepWebSolutions\Framework\Foundations\Actions\Setupable\SetupFailureException;
use DeepWebSolutions\Framework\Foundations\Exceptions\NotImplementedException;
use DeepWebSolutions\Framework\Foundations\PluginComponent\PluginComponentInterface;
use DeepWebSolutions\Framework\Foundations\Utilities\DependencyInjection\ContainerAwareInterface;
use DeepWebSolutions\Framework\Utilities\AdminNotices\AdminNoticesService;
use DeepWebSolutions\Framework\Utilities\AdminNotices\AdminNoticesServiceAwareTrait;
use DeepWebSolutions\Framework\Utilities\AdminNotices\AdminNoticeTypesEnum;
use DeepWebSolutions\Framework\Utilities\AdminNotices\Helpers\AdminNoticesHelpersTrait;
use DeepWebSolutions\Framework\Utilities\AdminNotices\Notices\DismissibleNotice;
use DeepWebSolutions\Framework\Utilities\AdminNotices\Notices\Notice;
use DeepWebSolutions\Framework\Utilities\Dependencies\DependenciesService;
use DeepWebSolutions\Framework\Utilities\Dependencies\DependenciesServiceAwareInterface;
use DeepWebSolutions\Framework\Utilities\Dependencies\Handlers\MultiCheckerHandler;
use DeepWebSolutions\Framework\Utilities\Dependencies\Handlers\SingleCheckerHandler;
use DeepWebSolutions\Framework\Utilities\Dependencies\States\ActiveDependenciesTrait;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

\defined( 'ABSPATH' ) || exit;

/**
 * Trait for registering admin notices of missing dependencies of using instances.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Dependencies\Actions
 */
trait SetupDependenciesAdminNoticesTrait {
	// region TRAITS

	use ActiveDependenciesTrait;
	use AdminNoticesHelpersTrait;
	use AdminNoticesServiceAwareTrait;
	use SetupableExtensionTrait;
	use SetupableInactiveTrait;

	// endregion

	// region METHODS

	/**
	 * Try to automagically register admin notices if dependencies are not fulfilled.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @throws  NotFoundExceptionInterface      Thrown if the container can't find an entry.
	 * @throws  ContainerExceptionInterface     Thrown if the container encounters some other error.
	 *
	 * @return  SetupFailureException|null
	 */
	public function setup_dependencies_admin_notices(): ?SetupFailureException {
		$notices_service = $this->get_admin_notices_service();
		$handler_id      = ( $this instanceof PluginComponentInterface ? $this->get_id() : \get_class( $this ) ) . '_active';

		if ( $this instanceof DependenciesServiceAwareInterface ) {
			$handler = $this->get_dependencies_service()->get_handler( $handler_id );
		} elseif ( $this instanceof ContainerAwareInterface ) {
			$handler = $this->get_container()->get( DependenciesService::class )->get_handler( $handler_id );
		} else {
			throw new NotImplementedException( 'Dependencies admin notices scenario not supported' );
		}

		$missing_dependencies = $handler->get_missing_dependencies();
		if ( $handler instanceof MultiCheckerHandler ) {
			foreach ( $missing_dependencies as $type => $dependencies ) {
				if ( ! empty( $dependencies ) ) {
					$this->register_missing_dependencies_admin_notices( $notices_service, $dependencies, $type );
				}
			}
		} elseif ( $handler instanceof SingleCheckerHandler ) {
			$this->register_missing_dependencies_admin_notices( $notices_service, $missing_dependencies, $handler->get_checker()->get_type() );
		}

		return null;
	}

	// endregion

	// region HELPERS

	/**
	 * Register default admin notices for all missing dependencies.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   AdminNoticesService     $notices_service        Instance of the admin notices service.
	 * @param   array                   $missing_dependencies   Unfulfilled dependencies.
	 * @param   string                  $type                   The type of the unfulfilled dependencies.
	 */
	protected function register_missing_dependencies_admin_notices( AdminNoticesService $notices_service, array $missing_dependencies, string $type ): void {
		foreach ( $missing_dependencies as $checker_id => $dependencies ) {
			if ( ! empty( $dependencies ) ) {
				$is_optional_handler = ( \strpos( $checker_id, 'optional' ) !== false );
				$store               = $is_optional_handler ? 'options' : 'dynamic';

				$notice_handle  = $this->get_admin_notice_handle( "missing-{$type}", array( \md5( \wp_json_encode( $dependencies ) ) ) );
				$notice_message = $this->compose_message( $type, $dependencies, $is_optional_handler );
				$notice_params  = array( 'capability' => 'activate_plugins' );

				$notice = $is_optional_handler
					? new DismissibleNotice( $notice_handle, $notice_message, AdminNoticeTypesEnum::ERROR, $notice_params + array( 'persistent' => true ) )
					: new Notice( $notice_handle, $notice_message, AdminNoticeTypesEnum::ERROR, $notice_params );

				$notices_service->add_notice( $notice, $store );
			}
		}
	}

	/**
	 * Returns the admin notice message to display for the missing dependencies.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 *
	 * @param   string  $type               Message type to compose.
	 * @param   array   $dependencies       The list of missing dependencies.
	 * @param   bool    $is_optional        Whether the dependencies are optional or not.
	 *
	 * @throws  NotImplementedException     Thrown when the message type is not supported.
	 *
	 * @return  string
	 */
	protected function compose_message( string $type, array $dependencies, bool $is_optional = false ): string {
		switch ( $type ) {
			case 'php_extensions':
				$message = $this->compose_missing_php_extensions_message( $dependencies, $is_optional );
				break;
			case 'php_functions':
				$message = $this->compose_missing_php_functions_message( $dependencies, $is_optional );
				break;
			case 'php_settings':
				$message = $this->compose_incompatible_php_settings_message( $dependencies, $is_optional );
				break;
			case 'active_plugins':
				$message = $this->compose_missing_plugins_message( $dependencies, $is_optional );
				break;
			default:
				throw new NotImplementedException( 'Dependencies admin notices type not supported' );
		}

		return $message;
	}

	/**
	 * Returns the message of the admin notice for missing PHP extensions.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 *
	 * @param   array   $php_extensions     List of missing PHP extensions.
	 * @param   bool    $is_optional        Whether the extensions are optional or required.
	 *
	 * @return  string
	 */
	protected function compose_missing_php_extensions_message( array $php_extensions, bool $is_optional = false ): string {
		if ( $is_optional ) {
			return \sprintf(
				/* translators: 1. Plugin or identifiable name, 2. Comma-separated list of missing PHP extensions. */
				\_n(
					'<strong>%1$s</strong> may behave unexpectedly because the %2$s PHP extension is missing. Contact your host or server administrator to install and configure the missing extension.',
					'<strong>%1$s</strong> may behave unexpectedly because the following PHP extensions are missing: %2$s. Contact your host or server administrator to install and configure the missing extensions.',
					\count( $php_extensions ),
					'dws-wp-framework-utilities'
				),
				\esc_html( $this->get_registrant_name() ),
				'<strong>' . \implode( ', ', $php_extensions ) . '</strong>'
			);
		} else {
			return \sprintf(
				/* translators: 1. Plugin or identifiable name, 2. Comma-separated list of missing PHP extensions. */
				\_n(
					'<strong>%1$s</strong> requires the %2$s PHP extension to function. Contact your host or server administrator to install and configure the missing extension.',
					'<strong>%1$s</strong> requires the following PHP extensions to function: %2$s. Contact your host or server administrator to install and configure the missing extensions.',
					\count( $php_extensions ),
					'dws-wp-framework-utilities'
				),
				\esc_html( $this->get_registrant_name() ),
				'<strong>' . \implode( ', ', $php_extensions ) . '</strong>'
			);
		}
	}

	/**
	 * Returns the message of the admin notice for missing PHP functions.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 *
	 * @param   array   $php_functions      List of missing PHP functions.
	 * @param   bool    $is_optional        Whether the functions are optional or required.
	 *
	 * @return  string
	 */
	protected function compose_missing_php_functions_message( array $php_functions, bool $is_optional = false ): string {
		if ( $is_optional ) {
			return \sprintf(
				/* translators: 1. Plugin or identifiable name, 2. Comma-separated list of missing PHP functions. */
				\_n(
					'<strong>%1$s</strong> may behave unexpectedly because the %2$s PHP function is missing. Contact your host or server administrator to install and configure the missing function.',
					'<strong>%1$s</strong> may behave unexpectedly because the following PHP functions are missing: %2$s. Contact your host or server administrator to install and configure the missing functions.',
					\count( $php_functions ),
					'dws-wp-framework-utilities'
				),
				\esc_html( $this->get_registrant_name() ),
				'<strong>' . \implode( ', ', $php_functions ) . '</strong>'
			);
		} else {
			return \sprintf(
				/* translators: 1. Plugin or identifiable name, 2. Comma-separated list of missing PHP functions. */
				\_n(
					'<strong>%1$s</strong> requires the %2$s PHP function to exist. Contact your host or server administrator to install and configure the missing function.',
					'<strong>%1$s</strong> requires the following PHP functions to exist: %2$s. Contact your host or server administrator to install and configure the missing functions.',
					\count( $php_functions ),
					'dws-wp-framework-utilities'
				),
				\esc_html( $this->get_registrant_name() ),
				'<strong>' . \implode( ', ', $php_functions ) . '</strong>'
			);
		}
	}

	/**
	 * Returns the message of the admin notice for incompatible PHP settings.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 *
	 * @param   array   $php_settings       List of incompatible PHP settings.
	 * @param   bool    $is_optional        Whether the settings are optional or required.
	 *
	 * @return  string
	 */
	protected function compose_incompatible_php_settings_message( array $php_settings, bool $is_optional = false ): string {
		if ( $is_optional ) {
			$message = \sprintf(
				/* translators: Plugin name or identifiable name. */
				\__( '<strong>%s</strong> may behave unexpectedly because the following PHP configuration settings are expected:', 'dws-wp-framework-utilities' ),
				\esc_html( $this->get_registrant_name() )
			) . '<ul>';
			$message .= $this->format_incompatible_settings_list( $php_settings );
			$message .= '</ul>' . \__( 'Please contact your hosting provider or server administrator to configure these settings. The plugin will attempt to run despite this warning.', 'dws-wp-framework-utilities' );
		} else {
			$message = \sprintf(
				/* translators: Plugin name or identifiable name. */
				\__( '<strong>%s</strong> cannot run because the following PHP configuration settings are expected:', 'dws-wp-framework-utilities' ),
				\esc_html( $this->get_registrant_name() )
			) . '<ul>';
			$message .= $this->format_incompatible_settings_list( $php_settings );
			$message .= '</ul>' . \__( 'Please contact your hosting provider or server administrator to configure these settings.', 'dws-wp-framework-utilities' );
		}

		return $message;
	}

	/**
	 * Returns the message of the admin notice for missing WP plugins.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 *
	 * @param   array   $plugins        List of missing WP plugins.
	 * @param   bool    $is_optional    Whether the plugins are optional or required.
	 *
	 * @return  string
	 */
	protected function compose_missing_plugins_message( array $plugins, bool $is_optional = false ): string {
		if ( $is_optional ) {
			return \sprintf(
				/* translators: 1. Plugin or identifiable name, 2. Comma-separated list of missing PHP extensions. */
				\_n(
					'<strong>%1$s</strong> may behave unexpectedly because the %2$s plugin is either not installed or not active. Please install and activate the plugin first.',
					'<strong>%1$s</strong> may behave unexpectedly because the following plugins are either not installed or active: %2$s. Please install and activate these plugins first.',
					\count( $plugins ),
					'dws-wp-framework-utilities'
				),
				\esc_html( $this->get_registrant_name() ),
				'<strong>' . $this->format_missing_plugins_list( $plugins ) . '</strong>'
			);
		} else {
			return \sprintf(
				/* translators: 1. Plugin or identifiable name, 2. Comma-separated list of missing PHP extensions. */
				\_n(
					'<strong>%1$s</strong> requires the %2$s plugin to be installed and active. Please install and activate the plugin first.',
					'<strong>%1$s</strong> requires the following plugins to be installed and active: %2$s. Please install and activate these plugins first.',
					\count( $plugins ),
					'dws-wp-framework-utilities'
				),
				\esc_html( $this->get_registrant_name() ),
				'<strong>' . $this->format_missing_plugins_list( $plugins ) . '</strong>'
			);
		}
	}

	/**
	 * Formats the list of incompatible PHP settings in a human-friendly way.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $settings   List of incompatible settings dependencies.
	 *
	 * @return  string
	 */
	protected function format_incompatible_settings_list( array $settings ): string {
		$message = '';

		foreach ( $settings as $setting => $values ) {
			$setting_message = "<code>{$setting} = {$values['expected']}</code>";
			if ( ! empty( $values['type'] ) ) {
				switch ( $values['type'] ) {
					case 'min':
						$setting_message = \sprintf(
							/* translators: PHP settings value. */
							\__( '%s or higher', 'dws-wp-framework-utilities' ),
							$setting_message
						);
						break;
				}
			}

			$message .= "<li>{$setting_message}</li>";
		}

		return $message;
	}

	/**
	 * Formats the list of missing plugin dependencies in a human-friendly way.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $plugins    List of missing plugin dependencies.
	 *
	 * @return  string
	 */
	protected function format_missing_plugins_list( array $plugins ): string {
		$plugin_names = array();

		foreach ( $plugins as $missing_plugin ) {
			$plugin_name = $missing_plugin['name'];

			if ( isset( $missing_plugin['min_version'] ) ) {
				$plugin_name .= " {$missing_plugin['min_version']}+";
			}

			if ( isset( $missing_plugin['version'] ) ) {
				$formatted_version = \sprintf(
					/* translators: %s: Installed version of the dependant plugin */
					\__( 'You\'re running version %s', 'dws-wp-framework-utilities' ),
					$missing_plugin['version']
				);
				$plugin_name .= ' <em>(' . \esc_html( $formatted_version ) . ')</em>';
			}

			$plugin_names[] = $plugin_name;
		}

		return \join( ', ', $plugin_names );
	}

	// endregion
}
