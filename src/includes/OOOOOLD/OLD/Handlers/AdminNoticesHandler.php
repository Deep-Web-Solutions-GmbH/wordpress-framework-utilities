<?php

namespace DeepWebSolutions\Framework\Utilities\Handlers;

use DeepWebSolutions\Framework\Helpers\WordPress\Assets;
use DeepWebSolutions\Framework\Utilities\Handlers\Traits\Hooks;
use DeepWebSolutions\Framework\Utilities\Interfaces\Resources\PluginInterface;
use DeepWebSolutions\Framework\Utilities\Traits\Plugin;

defined( 'ABSPATH' ) || exit;

/**
 * Handles the registration and display of notices in the admin area.
 *
 * @see     https://github.com/skyverge/wc-plugin-framework/blob/de7f429af153a17a0fd84cf9a1c56c6ac5ffbc08/woocommerce/class-sv-wc-admin-notice-handler.php
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Handlers
 */
class AdminNoticesHandler {
	use Hooks;
	use Plugin;

	// region FIELDS AND CONSTANTS

	/**
	 * The slug of admin error notices.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  public
	 * @var     string  ERROR
	 */
	public const ERROR = 'error';

	/**
	 * The slug of admin warning notices.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  public
	 * @var     string  WARNING
	 */
	public const WARNING = 'warning';

	/**
	 * The slug of admin success notices.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  public
	 * @var     string  SUCCESS
	 */
	public const SUCCESS = 'success';

	/**
	 * The slug of admin info notices.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  public
	 * @var     string  INFO
	 */
	public const INFO = 'info';

	/**
	 * Collection of dynamically generated admin notices.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     array
	 */
	protected array $dynamic_admin_notices = array(
		self::ERROR   => array(),
		self::WARNING => array(),
		self::SUCCESS => array(),
		self::INFO    => array(),
	);

	/**
	 * Whether any user notices have been outputted during the current request.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     bool
	 */
	protected bool $has_outputted_admin_notices = false;

	// endregion

	// region MAGIC METHODS

	/**
	 * AdminNoticesHandler constructor.
	 *
	 * @param   PluginInterface $plugin        Instance of the current plugin.
	 * @param   HooksHandler    $hooks_handler Instance of the hooks handler.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function __construct( PluginInterface $plugin, HooksHandler $hooks_handler ) {
		$this->set_plugin( $plugin );
		$this->register_hooks( $hooks_handler );
	}

	// endregion

	// region INHERITED FUNCTIONS

	/**
	 * Register hooks with the hooks handler.
	 *
	 * @param   HooksHandler    $hooks_handler      Instance of the hooks handler.
	 *
	 * @version 1.0.0
	 *
	 * @see     HooksServiceRegisterTrait::register_hooks()
	 *
	 * @since   1.0.0
	 */
	protected function register_hooks( HooksHandler $hooks_handler ): void {
		// Output the admin notices.
		$hooks_handler->add_action( 'admin_notices', $this, 'output_user_admin_notices' );
		$hooks_handler->add_action( 'admin_notices', $this, 'output_dynamic_admin_notices' );
		$hooks_handler->add_action( 'admin_footer', $this, 'output_admin_notice_dismiss_js' );

		// AJAX handler for dismissing a notice.
		$hooks_handler->add_action( 'wp_ajax_dws_framework_utilities_' . $this->plugin->get_plugin_safe_slug() . '_dismiss_notice', $this, 'handle_ajax_dismiss' );
	}

	// endregion

	// region HOOKS

	/**
	 * Output user specific admin notices.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function output_user_admin_notices(): void {
		$user_id = get_current_user_id();
		$notices = get_user_meta( $user_id, $this->get_user_notices_meta_key(), true );

		foreach ( array( self::ERROR, self::WARNING, self::SUCCESS, self::INFO ) as $type ) {
			if ( ! isset( $notices[ $type ] ) ) {
				continue;
			}

			foreach ( $notices[ $type ] as $notice_id => $notice ) {
				if ( empty( $notice['message'] ) ) {
					unset( $notices[ $type ][ $notice_id ] );
					continue;
				} elseif ( ! $this->should_display_notice( $notice_id, $notice['params'] ) ) {
					unset( $notices[ $type ][ $notice_id ] );
					continue;
				} elseif ( ! boolval( $notice['params']['persistent'] ) ) {
					unset( $notices[ $type ][ $notice_id ] );
				}

				$this->has_outputted_admin_notices = true;
				$this->output_admin_notice(
					$notice['message'],
					$notice_id,
					$notice['params'],
				);
			}
		}

		update_user_meta( $user_id, $this->get_user_notices_meta_key(), $notices );
	}

	/**
	 * Output dynamic admin notices.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function output_dynamic_admin_notices(): void {
		foreach ( $this->dynamic_admin_notices as $type => $notices ) {
			foreach ( $notices as $notice_id => $notice ) {
				$this->has_outputted_admin_notices = true; // At least one message got outputted.
				$this->output_admin_notice( $notice['message'], $notice_id, $notice['params'] );
			}
		}
	}

	/**
	 * Outputs the JS that handles the notice dismiss action.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function output_admin_notice_dismiss_js(): void {
		if ( false === $this->has_outputted_admin_notices ) {
			return; // No notices were outputted.
		}

		ob_start();

		?>

		( function( $ ) {
			$( '.dws-framework-notice-<?php echo esc_js( $this->plugin->get_plugin_slug() ); ?>' ).on( 'click.wp-dismiss-notice', '.notice-dismiss', function( e ) {
				var notice = $( this ).closest( '.dws-framework-notice' );
				$.ajax( {
					url: ajaxurl,
					method: 'POST',
					data: {
						action: 'dws_framework_utilities_<?php echo esc_js( $this->plugin->get_plugin_safe_slug() ); ?>_dismiss_notice',
						notice_id: $( notice ).data( 'notice-id' ),
						is_global: $( notice ).data( 'notice-global' ),
						_wpnonce: '<?php echo esc_js( wp_create_nonce( 'dws-dismiss-notice' ) ); ?>'
					}
				} );
			} );
		} ) ( jQuery );

		<?php

		echo Assets::wrap_string_in_script_tags( ob_get_clean() ); // phpcs:ignore
	}

	/**
	 * Intercepts an AJAX request for dismissing a given notice.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function handle_ajax_dismiss(): void {
		if ( check_ajax_referer( 'dws-dismiss-notice' ) ) {
			$notice_id = filter_input( INPUT_POST, 'notice_id', FILTER_SANITIZE_STRING );
			$is_global = filter_input( INPUT_POST, 'is_global', FILTER_VALIDATE_BOOLEAN );
			$this->dismiss_notice( $notice_id, $is_global );
		}

		wp_die();
	}

	// endregion

	// region METHODS

	/**
	 * Registers a dynamic notice that will be displayed to all eligible users that have not dismissed it yet, if applicable.
	 *
	 * @param   string  $message    The message that should be displayed to the eligible users.
	 * @param   string  $notice_id  A unique message ID that helps us avoid displaying duplicates.
	 * @param   array   $params     {
	 *      Optional parameters.
	 *
	 *      @type string      type           The type of notice to display.
	 *      @type bool        dismissible    Whether the notice is dismissible or not.
	 *      @type bool        global         Whether the notice's dismissed status is handled globally or per-eligible-user.
	 *      @type string|null capability     The capability that a user must possess to be displayed the notice. Null if it doesn't apply.
	 *      @type bool        html           Whether the notice message contains HTML or just plain text.
	 * }
	 */
	public function add_admin_notice( string $message, string $notice_id, array $params = array() ) {
		$params = wp_parse_args(
			$params,
			array(
				'type'        => self::ERROR,
				'dismissible' => true,
				'global'      => false,
				'capability'  => null,
				'html'        => false,
			)
		);

		if ( $this->should_display_notice( $notice_id, $params ) ) {
			$this->dynamic_admin_notices[ $params['type'] ][ $notice_id ] = array(
				'message' => $message,
				'params'  => $params,
			);
		}
	}

	/**
	 * Registers a dynamic, globally-dismissible notice that will be displayed to all eligible users until any one of them dismisses it.
	 * Basically just a helper wrapper around AdminNoticesHandler::add_admin_notice().
	 *
	 * @param   string  $message    The message that should be displayed to the eligible users.
	 * @param   string  $notice_id  A unique message ID that helps us avoid displaying duplicates.
	 * @param   array   $params     {
	 *      Optional parameters.
	 *
	 *      @type string      type           The type of notice to display.
	 *      @type string|null capability     The capability that a user must possess to be displayed the notice. Null if it doesn't apply.
	 *      @type bool        html           Whether the notice message contains HTML or just plain text.
	 * }
	 */
	public function add_global_admin_notice( string $message, string $notice_id, array $params = array() ) {
		$this->add_admin_notice(
			$message,
			$notice_id,
			array_merge(
				array(
					'global'      => true,
					'dismissible' => true,
				),
				$params
			)
		);
	}

	/**
	 * Adds a message to the current user's meta data that will be displayed to the user on the next page load until they
	 * manually dismiss it.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $message    The message that should be displayed to the current user on the next page load.
	 * @param   string  $notice_id  A unique message ID that helps us avoid displaying duplicates.
	 * @param   array   $params     {
	 *      Optional parameters.
	 *
	 *      @type string type           The type of notice to display.
	 *      @type bool   dismissible    Whether the notice is dismissible or not.
	 *      @type bool   html           Whether the notice message contains HTML or just plain text.
	 *      @type bool   persistent     Whether the notice should show up until manually dismissed or whether it should auto-dismiss on display.
	 * }
	 */
	public function add_admin_notice_to_user( string $message, string $notice_id, array $params = array() ): void {
		$params = wp_parse_args(
			$params,
			array(
				'type'        => self::ERROR,
				'dismissible' => true,
				'html'        => false,
				'persistent'  => false,
			)
		);

		$user_id = get_current_user_id();
		$notices = get_user_meta( $user_id, $this->get_user_notices_meta_key(), true );

		if ( ! is_array( $notices ) ) {
			$notices = array();
		}

		$notices[ $params['type'] ]               = isset( $notices[ $params['type'] ] ) ? $notices[ $params['type'] ] : array();
		$notices[ $params['type'] ][ $notice_id ] = array(
			'message' => $message,
			'params'  => $params,
		);

		update_user_meta( $user_id, $this->get_user_notices_meta_key(), $notices );
	}

	/**
	 * Checks whether a specific notice has been dismissed or not.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string      $notice_id  The ID of the notice to check the dismissed status for.
	 * @param   bool        $global     Whether the notice is dismissible globally or not (aka dismissible per-user).
	 * @param   int|null    $user_id    The ID of the user to check the dismissed status for.
	 *
	 * @return  bool    Whether the given notice has been dismissed by the given user or not.
	 */
	public function is_notice_dismissed( string $notice_id, bool $global = false, ?int $user_id = null ): bool {
		$dismissed_notices = $this->get_dismissed_notices( $global, $user_id );
		return isset( $dismissed_notices[ $notice_id ] ) && boolval( $dismissed_notices[ $notice_id ] );
	}

	/**
	 * Marks a specific notice as dismissed for a specific user.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string      $notice_id  The ID of the notice to dismiss.
	 * @param   bool        $global     Whether the notice dismiss status is global or not (aka per-user).
	 * @param   int|null    $user_id    The ID of the user to dismiss the notice for.
	 */
	public function dismiss_notice( string $notice_id, bool $global = false, ?int $user_id = null ): void {
		$user_id           = $user_id ?? get_current_user_id();
		$dismissed_notices = $this->get_dismissed_notices( $global, $user_id );

		if ( true === $global ) {
			$dismissed_notices[ $notice_id ] = $user_id; // Set the user who dismissed the global notice. Might be useful.
			update_option( $this->get_dismissed_notices_meta_key(), $dismissed_notices );
		} else {
			$dismissed_notices[ $notice_id ] = true; // We obviously know the user id. Just set the dismissed status to true.
			update_user_meta( $user_id, $this->get_dismissed_notices_meta_key(), $dismissed_notices );
		}

		/**
		 * Fires when a user dismisses an admin notice.
		 *
		 * @since   1.0.0
		 * @version 1.0.0
		 *
		 * @param   string  $notice_id  The unique ID of the dismissed notice.
		 * @paarm   bool    $global     Whether the notice was dismissed globally or not (aka per-user).
		 * @param   int     $user_id    The ID of the user dismissing the notice.
		 */
		do_action( 'dws_' . $this->plugin->get_plugin_safe_slug() . '_dismissed_notice', $notice_id, $global, $user_id );
	}

	// endregion

	// region HELPERS

	/**
	 * Outputs a single notice.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $message    The message contained by the notice.
	 * @param   string  $notice_id  The unique ID of the notice.
	 * @param   array   $params     {
	 *      Optional parameters.
	 *
	 *      @type string      type           The type of notice to display.
	 *      @type bool        dismissible    Whether the notice is dismissible or not.
	 *      @type bool        global         Whether the notice's dismissed status is handled globally or per-eligible-user.
	 *      @type bool        html           Whether the notice message contains HTML or just plain text.
	 * }
	 */
	protected function output_admin_notice( string $message, string $notice_id, array $params = array() ): void {
		$params = wp_parse_args(
			$params,
			array(
				'type'        => self::ERROR,
				'dismissible' => true,
				'global'      => false,
				'html'        => false,
			)
		);

		$classes = array(
			'notice',
			'notice-' . $params['type'],
			'dws-framework-notice',
			'dws-framework-notice-' . $this->plugin->get_plugin_slug(),
		);
		if ( $params['dismissible'] ) {
			$classes[] = 'is-dismissible';
		}

		$template = '<div class="%1$s" data-plugin-slug="%2$s" data-plugin-safe-slug="%3$s" data-notice-id="%4$s" data-notice-global="%5$s">%6$s</div>';
		$template = ( ! $params['html'] ) ? str_replace( '%6$s', '<p>%6$s</p>', $template ) : $template;

		echo sprintf(
			$template, // phpcs:ignore
			esc_attr( implode( ' ', $classes ) ),
			esc_attr( $this->plugin->get_plugin_slug() ),
			esc_attr( $this->plugin->get_plugin_safe_slug() ),
			esc_attr( $notice_id ),
			esc_attr( $params['global'] ? 1 : 0 ),
			wp_kses_post( $message )
		);
	}

	/**
	 * Checks whether a specific notice should be displayed for the current user or not.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $notice_id  The ID of the notice to check for.
	 * @param   array   $params     {
	 *      Optional parameters.
	 *
	 *      @type bool        dismissible    Whether the notice is dismissible or not.
	 *      @type bool        global         Whether the notice's dismissed status is handled globally or per-eligible-user.
	 *      @type string|null capability     The capability that a user must possess to be displayed the notice. Null if it doesn't apply.
	 * }
	 *
	 * @return  bool    True if the notice should be displayed, false otherwise.
	 */
	protected function should_display_notice( string $notice_id, array $params = array() ): bool {
		$params = wp_parse_args(
			$params,
			array(
				'dismissible' => true,
				'global'      => false,
				'capability'  => null,
			)
		);

		// Do not display the notice if the user does not have the required capability.
		if ( ! is_null( $params['capability'] ) && ! current_user_can( $params['capability'] ) ) {
			return false;
		}

		// Always display non-dismissible notices.
		if ( ! $params['dismissible'] ) {
			return true;
		}

		return ! $this->is_notice_dismissed( $notice_id, $params['global'] );
	}

	/**
	 * Returns the list of dismissed notices for a given user.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   bool        $global     Whether the notice is dismissible globally or not (aka dismissible per-user).
	 * @param   int|null    $user_id    The user ID that the dismissed notices are being searched for.
	 *
	 * @return  array
	 */
	protected function get_dismissed_notices( bool $global = false, ?int $user_id = null ): array {
		$dismissed_notices = ( true === $global )
			? get_option( $this->get_dismissed_notices_meta_key(), array() )
			: get_user_meta( $user_id ?? get_current_user_id(), $this->get_dismissed_notices_meta_key(), true );

		return is_array( $dismissed_notices )
			? $dismissed_notices : array();
	}

	/**
	 * Ensures no typos take place by encapsulating the dismissed notices meta key in its own helper.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	protected function get_dismissed_notices_meta_key(): string {
		return '_dws_admin_notices_' . $this->plugin->get_plugin_safe_slug() . '_dismissed_notices';
	}

	/**
	 * Ensures no typos take place by encapsulating the user notices meta key in its own helper.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	protected function get_user_notices_meta_key(): string {
		return '_dws_admin_notices_' . $this->plugin->get_plugin_safe_slug();
	}

	// endregion
}
