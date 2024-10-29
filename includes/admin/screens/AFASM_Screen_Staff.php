<?php
/**
 * The staff tab item for configuration screen
 *
 * @package  claud/afa-submission-manager
 * @since 1.0.0
 */

namespace AFASM\Includes\Admin\Screens;

use AFASM\Includes\Admin\Screens\AFASM_Screen;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class ScreenStaff
 *
 * Render Staff content
 *
 * @since 1.0.0
 */
class AFASM_Screen_Staff extends AFASM_Screen {

	/**
	 * Tab param
	 *
	 * @var string
	 */
	const ID = 'staff';

	/**
	 * ScreenStaff constructor.
	 */
	public function __construct() {
		$this->id    = self::ID;
		$this->label = __( 'Staff', 'afa-submission-manager' );
		add_action( 'admin_init', array( $this, 'settings' ) );
	}

	/**
	 * Show staff content
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render() {
		$this->save_settings();
		$nonce = wp_create_nonce( 'sreen-staff' );

		?>	
			<div>
				<p>
					<?php esc_html_e( 'Manage User Permissions', 'afa-submission-manager' ); ?>
				</p>
			</div>
			<form action="<?php echo esc_html( admin_url( 'admin.php?page=afasm_settings&tab=staff' ) ); ?>" method="POST">
				<table class="form-table">
					<?php do_settings_fields( 'afasm_settings', 'afasm_settings_staff_section' ); ?>
				</table>
				<div >
					<p>
						<button name="save" class="button-primary" type="submit" value="Salvar alterações"> <?php esc_html_e( 'Save Changes', 'afa-submission-manager' ); ?></button>
						<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo esc_html( $nonce ); ?>">
					</p>
				</div>
			</form>
		<?php
	}

	/**
	 * Add seetings fields
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function settings() {
		$all_options = get_option( 'afasm_settings_staff_options', false );

		if ( empty( $all_options ) ) {
			$all_options = array();
		}

		add_settings_field(
			'afasm_add_user',
			__( 'Add Users', 'afa-submission-manager' ),
			array( $this, 'input_add_user_render' ),
			'afasm_settings',
			'afasm_settings_staff_section',
			$all_options
		);
	}

	/**
	 * Render check field
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The setting value.
	 *
	 * @return void
	 */
	public function input_add_user_render( array $args ) {

		$checked = '';
		if ( array_key_exists( 'add_user', $args ) ) {
			$value = $args['add_user'];
			if ( 'on' === $value ) {
				$checked = 'checked';
			}
		}

		?>
			<fieldset>
				<label for="afasm_add_user">
					<input name="afasm_add_user" id="afasm_add_user" type="checkbox" class="" value="on" <?php echo esc_html( $checked ); ?>> 
					<?php esc_html_e( 'Add Users', 'afa-submission-manager' ); ?>
				</label> 
				<p class="description"><?php esc_html_e( 'To allow non-admin users to access this app, grant them the required permission', 'afa-submission-manager' ); ?></p>
			</fieldset>
		<?php
	}

	/**
	 * Save settings
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function save_settings() {
		$all_options = array();
		if ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'sreen-staff' ) ) {

			if ( ! empty( $_POST['afasm_add_user'] ) ) {
				$add_user                = sanitize_text_field( wp_unslash( $_POST['afasm_add_user'] ) );
				$all_options['add_user'] = $add_user;
			}

			update_option( 'afasm_settings_staff_options', $all_options );

			if ( wp_safe_redirect( admin_url( 'admin.php?page=afasm_settings&tab=staff' ) ) ) {
				exit;
			}
		}

	}
}

