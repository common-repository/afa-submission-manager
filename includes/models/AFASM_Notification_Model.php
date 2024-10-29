<?php
/**
 * The Notification Model Class.
 *
 * @package  claud/afa-submission-manager
 * @since 1.0.0
 */

namespace AFASM\Includes\Models;

use AFASM\Includes\Plugins\AFASM_Constant;
use AFASM\Includes\Models\AFASM_User_Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class NotificationModel
 *
 * Hendler with notification table
 *
 * @since 1.0.0
 */
class AFASM_Notification_Model {

	/**
	 * Table name
	 *
	 * @var string
	 */
	private $table_name;

	/**
	 * NotificationModel constructor.
	 */
	public function __construct() {
		global $wpdb;
		$this->table_name = $wpdb->prefix . AFASM_Constant::TABLE_NOTIFICATION;
	}

	/**
	 * Create notification
	 *
	 * @param int   $notification_type_id The id of notification type.
	 * @param array $meta_value The meta_value of notification.
	 * @param int   $supported_plugin_id The supported plugin ID to filter the notifications.
	 * @param int   $user_id The user ID.
	 *
	 * @return int|false
	 */
	public function create( $notification_type_id, $meta_value, $supported_plugin_id = 0, $user_id = 0 ) {

		global $wpdb;

		$item = array(
			'notification_type_id' => $notification_type_id,
			'meta_value'           => maybe_serialize( $meta_value ), // phpcs:ignore
			'user_id'              => $user_id,
			'supported_plugin_id'  => $supported_plugin_id,
			'created_at'           => gmdate( 'Y-m-d H:i:s' ),
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$results = $wpdb->insert(
			$this->table_name,
			$item
		);

		return $results;
	}

	/**
	 * Get notifications
	 *
	 * @param int $supported_plugin_id The supported plugin ID to filter the notifications.
	 * @param int $offset The offset.
	 * @param int $number_of_records_per_page The notifications per page.
	 *
	 * @return object
	 */
	public function notifications( $supported_plugin_id, $offset, $number_of_records_per_page = 20 ) {

		global $wpdb;

		$table_notification_type = $wpdb->prefix . AFASM_Constant::TABLE_NOTIFICATION_TYPE;

		$sql = 'SELECT afasm_tn.id, afasm_tn.user_id, afasm_tn.notification_type_id, afasm_tn.meta_value, afasm_tn.created_at, afasm_tnt.type, afasm_tnt.title FROM %i afasm_tn INNER JOIN %i afasm_tnt ON afasm_tn.notification_type_id = afasm_tnt.id WHERE afasm_tn.supported_plugin_id IN (0, %d) ORDER BY id DESC LIMIT %d,%d';

		$sql = $wpdb->prepare( $sql, array( $this->table_name, $table_notification_type, $supported_plugin_id, $offset, $number_of_records_per_page ) );// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		// phpcs:ignore 
		$results = $wpdb->get_results( $sql, OBJECT );

		$modified_results = array();

		$user_model = new AFASM_User_Model();

		foreach ( $results as $result ) {
			$result->meta_value   = maybe_unserialize( $result->meta_value );
			$result->user_created = $user_model->user_info_by_id( $result->user_id );
			$user_name            = $this->get_username_or_email( $result->user_created );
			$result->message      = $this->message_for_submission( $result->meta_value['entry_id'], $user_name );
			$modified_results[]   = $result;
		}

		return $modified_results;
	}

	/**
	 * Get number of notifications
	 *
	 * @param int $supported_plugin_id The supported plugin ID to filter the notifications.
	 *
	 * @return int
	 */
	public function mumber_of_items( $supported_plugin_id ) {

		global $wpdb;

		$sql = 'SELECT count(*) as number_of_rows FROM %i WHERE supported_plugin_id = %d ';

		$sql = $wpdb->prepare( $sql, array( $this->table_name, $supported_plugin_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		// phpcs:ignore 
		$results = $wpdb->get_results( $sql );

		$number_of_rows = intval( $results[0]->number_of_rows );

		return $number_of_rows;
	}

	/**
	 * Generates a notification message for a new form submission.
	 *
	 * This function creates a notification message to inform users about a new form submission.
	 *
	 * @param int    $entry_id         The ID of the form submission entry.
	 * @param string $user_name        The name of the user who filled out the form.
	 *
	 * @return string  The notification message with placeholders for React Native styling.
	 */
	private function message_for_submission( $entry_id, $user_name = '' ) {

		if ( empty( $user_name ) ) {
			// translators: %1$s is replaced with the entry_id.
			$notification_message = sprintf( __( 'Someone filled out a new form: {bold}%1$s{/bold}', 'afa-submission-manager' ), $entry_id );
		} else {
			// translators: %1$s is replaced with the user_name and %1$s entry_id.
			$notification_message = sprintf( __( '{bold}%1$s{/bold} filled out a new form: {bold}%2$s{/bold}', 'afa-submission-manager' ), $user_name, $entry_id );
		}

		return $notification_message;
	}

	/**
	 * Get the user_name or user_email from the provided object.
	 *
	 * This function takes an object or an empty array as input and returns the user_name or user_email
	 * if they are available in the input object.
	 *
	 * @param array|object $user_object The object or array containing user information.
	 *
	 * @return string The user_name or user_email from the input object. Returns an empty string if the input is empty or not an array.
	 */
	private function get_username_or_email( $user_object ) {

		if ( empty( $user_object ) || ! is_array( $user_object ) ) {
			return '';
		}

		$user_name = isset( $user_object['user_name'] ) ? $user_object['user_name'] : '';

		$user_email = isset( $user_object['user_email'] ) ? $user_object['user_email'] : '';

		return ! empty( $user_name ) ? $user_name : $user_email;
	}
}
