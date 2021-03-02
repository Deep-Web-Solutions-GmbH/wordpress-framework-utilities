<?php

namespace DeepWebSolutions\Framework\Utilities\AdminNotices;

use DeepWebSolutions\Framework\Foundations\Actions\OutputtableInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Describes a way to store, retrieve and output admin notices.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\AdminNotices
 */
interface AdminNoticesHandlerInterface extends AdminNoticesStoreFactoryAwareInterface, OutputtableInterface {
	// region GETTERS

	/**
	 * Returns the type of notices the instance handles.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	public function get_notices_type(): string;

	/**
	 * Returns all the stored notices of the handler's type within a given store.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $store      The name of the store to retrieve the notices from.
	 * @param   array   $params     Any parameters needed to retrieve the notices.
	 *
	 * @return  AdminNoticeInterface[]
	 */
	public function get_notices( string $store, array $params ): array;

	// endregion
}
