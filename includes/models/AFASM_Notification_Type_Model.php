<?php
/**
 * The Notification Type Model Class.
 *
 * @package  claud/afa-submission-manager
 * @since 1.0.0
 */

namespace  AFASM\Includes\Models;

use AFASM\Includes\Plugins\AFASM_Constant;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class NotificationTypeModel
 *
 * Hendler with notification_type table
 *
 * @since 1.0.0
 */
class AFASM_Notification_Type_Model {

	/**
	 * Table name
	 *
	 * @var string
	 */
	private $table_name;

	/**
	 * NotificationTypeModel constructor.
	 */
	public function __construct() {
		global $wpdb;
		$this->table_name = $wpdb->prefix . AFASM_Constant::TABLE_NOTIFICATION_TYPE;
	}

	/**
	 * Get notification type list
	 *
	 * @return array
	 */
	public function get_all_notification_type() {
		global $wpdb;
		$sql     = 'SELECT * FROM %i';
		$sql     = $wpdb->prepare( $sql, array( $this->table_name ) );// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$results = $wpdb->get_results( $sql, OBJECT ); // phpcs:ignore

		return $results;
	}

	/**
	 * Get notification type by id
	 *
	 * @param int $id The notification type id.
	 *
	 * @return object
	 */
	public function get_notification_type_by_id( $id ) {

		global $wpdb;

		$sql = 'SELECT * FROM %i WHERE id=%d';

		$sql = $wpdb->prepare( $sql, array( $this->table_name, $id ) );// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		// phpcs:ignore
		$results = $wpdb->get_results( $sql, OBJECT );

		if ( count( $results ) > 0 ) {
			return $results[0];
		}

		return null;
	}

	/**
	 * Get notification type by type
	 *
	 * @param string $type The notification type.
	 *
	 * @return object
	 */
	public function get_notification_type_by_type( $type ) {

		global $wpdb;

		$sql = 'SELECT * FROM %i WHERE `type`=%s';

		$sql = $wpdb->prepare( $sql, array( $this->table_name, $type ) );// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		// phpcs:ignore
		$results = $wpdb->get_results( $sql, OBJECT );

		if ( count( $results ) > 0 ) {
			return $results[0];
		}

		return null;
	}

}
