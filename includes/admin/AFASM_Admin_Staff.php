<?php
/**
 * The Admin staff class for users table
 *
 * @package  claud/afa-submission-manager
 * @since 1.0.0
 */

namespace AFASM\Includes\Admin;

use AFASM\Includes\Models\AFASM_User_Model;
use WP_User;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class AdminStaff
 *
 * Render Staff content
 *
 * @since 1.0.0
 */
class AFASM_Admin_Staff {

	/**
	 * Tab param
	 *
	 * @var string
	 */
	const NONCE = 'afasm_remove_staff';

	/**
	 * AdminStaff constructor.
	 */
	public function __construct() {

		add_filter( 'manage_users_columns', array( $this, 'add_user_staff_column' ) );
		add_filter( 'manage_users_custom_column', array( $this, 'add_user_staff_column_content' ), 10, 3 );

		$this->user_column_content_action();
	}

	/**
	 * Add AFA Staff column to users table
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns The columns to users list table.
	 *
	 * @return array
	 */
	public function add_user_staff_column( $columns ) {
		$all_options = get_option( 'afasm_settings_staff_options', false );
		if ( ! empty( $all_options ) && array_key_exists( 'add_user', $all_options ) ) {
			if ( 'on' === $all_options['add_user'] ) {
				$columns['afasm_user_staff_column'] = __( 'AFA Staff' );
			}
		}
		return $columns;
	}

	/**
	 * Renders the content of our custom column
	 *
	 * @since 1.0.0
	 *
	 * @param string $output The value of column.
	 * @param string $column_name The name of column.
	 * @param string $user_id The User ID.
	 *
	 * @return array
	 */
	public function add_user_staff_column_content( $output, $column_name, $user_id ) {
		if ( 'afasm_user_staff_column' === $column_name ) {

			$user_can_manage_afa = ( new AFASM_User_Model() )->user_can_manage_afa( $user_id );

			if ( $user_can_manage_afa ) {
				if ( user_can( $user_id, 'manage_options' ) ) {
					$output = sprintf( '<span>' . __( 'Administrador' ) . '</span>' );
				} else {
					$nonce   = wp_create_nonce( self::NONCE );
					$output  = sprintf( '<span>' . __( 'AFA Staff' ) . '</span>' );
					$output .= sprintf( "<br><a href='%s' class='remove'>" . __( 'Remover' ) . '</a>', admin_url( 'users.php?action=afasm_remove_staff&user=' . $user_id . '&_wpnonce=' . $nonce ) );
				}
			} else {
					$nonce   = wp_create_nonce( self::NONCE );
					$output  = sprintf( '<span>' . __( 'AFA Staff' ) . '</span>' );
					$output .= sprintf( "<br><a href='%s' class='remove'>" . __( 'Adicionar' ) . '</a>', admin_url( 'users.php?action=afasm_add_staff&user=' . $user_id . '&_wpnonce=' . $nonce ) );
			}
		}
		return $output;
	}

	/**
	 * Process user action
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function user_column_content_action() {

		if ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), self::NONCE ) ) {

			if ( isset( $_GET['action'] ) && isset( $_GET['user'] ) ) {

				$user_id = sanitize_text_field( wp_unslash( $_GET['user'] ) );

				if ( is_numeric( $user_id ) ) {
					if ( 'afasm_add_staff' === sanitize_text_field( wp_unslash( $_GET['action'] ) ) ) {
						$this->add_staff( $user_id );
					}

					if ( 'afasm_remove_staff' === sanitize_text_field( wp_unslash( $_GET['action'] ) ) ) {
						$this->remove_staff( $user_id );
					}
				}
			}
		}

	}

	/**
	 * Add user as staff
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id The ID for user.
	 *
	 * @return void
	 */
	public function add_staff( $user_id ) {

		$user_can_manage_afa = ( new AFASM_User_Model() )->user_can_manage_afa( $user_id );

		if ( ! $user_can_manage_afa ) {
			$user = new WP_User( $user_id );
			if ( $user->exists() ) {
				$user->add_role( 'afasm_staff' );
			}
		}

		$this->safe_redirect();
	}

	/**
	 * Remove user as staff
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id The ID for user.
	 *
	 * @return void
	 */
	public function remove_staff( $user_id ) {
		$user_can_manage_afa = ( new AFASM_User_Model() )->user_can_manage_afa( $user_id );

		if ( $user_can_manage_afa ) {
			$user = new WP_User( $user_id );
			if ( $user->exists() ) {
				$user->remove_role( 'afasm_staff' );
			}
		}

		$this->safe_redirect();
	}

	/**
	 * Save redirect to users
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function safe_redirect() {
		if ( wp_safe_redirect( admin_url( 'users.php' ) ) ) {
			exit;
		}
	}
}

