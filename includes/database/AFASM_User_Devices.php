<?php
/**
 * The UserDevices Class.
 *
 * @package  claud/afa-submission-manager
 * @since 1.0.0
 */

namespace AFASM\Includes\Database;

use AFASM\Includes\Plugins\AFASM_Constant;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class UserDevices
 *
 * Create table user_device
 *
 * @since 1.0.0
 */
class AFASM_User_Devices {

	/**
	 * Table name
	 *
	 * @var string
	 */
	private $table_name;

	/**
	 * UserDevices constructor.
	 */
	public function __construct() {
		global $wpdb;
		$this->table_name = $wpdb->prefix . AFASM_Constant::TABLE_USER_DEVICE;
	}

	/**
	 * Create user_device table.
	 */
	public function create_table() {

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		global $wpdb;

		$sql = 'CREATE TABLE IF NOT EXISTS ' . $this->table_name . ' (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20)  UNSIGNED NOT NULL,
        device_id VARCHAR(100) NOT NULL,
		device_language VARCHAR(2) NOT NULL,
        expo_token VARCHAR(100),
		created_at DATETIME NOT NULL,
        PRIMARY KEY(id),
        FOREIGN KEY(user_id) REFERENCES ' . $wpdb->users . '(ID),
        UNIQUE(device_id),
        UNIQUE(expo_token)
      )' . $wpdb->get_charset_collate();

		dbDelta( $sql );
	}

}
