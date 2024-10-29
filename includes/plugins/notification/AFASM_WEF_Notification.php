<?php
/**
 * The WEF Notification Class.
 *
 * @package  claud/afa-submission-manager
 * @since 1.0.0
 */

namespace AFASM\Includes\Plugins\Notification;

use AFASM\Includes\Models\AFASM_User_Devices_Model;
use AFASM\Includes\Plugins\Notification\AFASM_Abstract_Form_Notification;
use AFASM\Includes\Models\AFASM_Supported_Plugins_Model;
use AFASM\Includes\Models\WEF\AFASM_Entry_Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class WEFNotification
 *
 * Manipulate WEF Notification
 *
 * @since 1.0.0
 */
class AFASM_WEF_Notification extends AFASM_Abstract_Form_Notification {

	/**
	 * Send push notification in bulk
	 *
	 * @param array $meta_value The meta_value of notification.
	 * @param int   $user_id The user ID.
	 *
	 * @return void
	 */
	public function create( $meta_value, $user_id = null ) {

		$supported_plugins_model_register = ( new AFASM_Supported_Plugins_Model() )->get_supported_plugin_by_slug( 'wef' );
		$supported_plugin_id              = 0;

		if ( ! empty( $supported_plugins_model_register ) ) {
			$supported_plugin_id = $supported_plugins_model_register->id;
		}

		$notification_type_id = $this->get_form_submission_notification_type_id();
		$this->notifiacation->create( $notification_type_id, $meta_value, $supported_plugin_id, $user_id );

		$items = $this->prepare_push_notification( $notification_type_id, $meta_value, $user_id );
		$this->push_notification->push_notification_bulk( $items );
	}

	/**
	 * Prepare push notification for each user
	 *
	 * @param int   $notification_type_id The id of notification type.
	 * @param array $meta_value The meta_value of notification.
	 * @param int   $user_id The user ID.
	 *
	 * @return array
	 */
	private function prepare_push_notification( $notification_type_id, $meta_value, $user_id ) {
		$push_notifications_data = array();

		$divices = ( new AFASM_User_Devices_Model() )->get_enabled_register_by_notification_type( $notification_type_id );

		foreach ( $divices as $key => $divice ) {

			$item = array();

			// translators: %1$s is replaced with the site name.
			$push_notification_body = sprintf( __( 'New Form Submission Received from %1$s. Open app to view', 'afa-submission-manager' ), get_bloginfo( 'name' ) );

			$title = __( 'New Form Submission', 'afa-submission-manager' );

			$formatted_tiitle = sprintf( '📩 %s', $title );

			$item['title']          = $formatted_tiitle;
			$item['body']           = $push_notification_body;
			$item['exponent_token'] = $divice->expo_token;

			$push_notifications_data[] = $item;

		}

		return $push_notifications_data;
	}

	/**
	 * Load hooks for notifications
	 *
	 * @param int   $entry_id      The ID of the submitted entry.
	 * @param int   $form_id       The ID of the form that the submission belongs to.
	 * @param int   $page_id       The ID of the page where the form is embedded (if applicable).
	 * @param array $form_settings The settings of the form.
	 *
	 * @return void
	 */
	public function submission_notification( $entry_id, $form_id, $page_id, $form_settings ) {
		$entry_model = new AFASM_Entry_Model();
		$entry       = $entry_model->entry_by_id( $entry_id );

		if ( ! empty( $entry ) ) {

			$notification_data['entry_id']    = (int) $entry['id'];
			$notification_data['post_type']   = '';
			$notification_data['post_tiltle'] = '';

			$notification_data['user_id'] = null;
			if ( ! empty( $entry['created_by'] ) ) {
				$notification_data['user_id'] = (int) $entry['created_by'];
			}

			$this->create( $notification_data, $notification_data['user_id'] );
		}
	}

	/**
	 * Load hooks for notifications
	 *
	 * @return void
	 */
	public function loads_hooks() {
		add_action( 'weforms_entry_submission', array( $this, 'submission_notification' ), 10, 4 );
	}

}
