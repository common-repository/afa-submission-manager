<?php
/**
 * The Form Route Class.
 *
 * @package  claud/afa-submission-manager
 * @since 1.0.0
 */

namespace AFASM\Includes\Routes\GF;

use AFASM\Includes\Controllers\GF\AFASM_Entry_Meta_Controller;
use AFASM\Includes\Routes\AFASM_Abstract_Entry_Meta_Route;
use AFASM\Includes\Plugins\AFASM_Config;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class EntryMeta
 *
 * Init all routes
 *
 * @since 1.0.0
 */
class AFASM_Entry_Meta extends AFASM_Abstract_Entry_Meta_Route {

	/**
	 * Get entry_meta by entry id
	 */
	public function entry_meta_by_entry_id() {
		register_rest_route(
			$this->name,
			'/gf/entrymeta/entry_id/(?P<entry_id>[0-9]+)',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( new AFASM_Entry_Meta_Controller(), 'entry_meta_by_entry_id' ),
					'permission_callback' => array( new AFASM_Config(), 'afasm_check_authorization' ),
				),
			)
		);
	}

	/**
	 * Get entry meta by search
	 */
	public function search_entry_meta_answer() {
		register_rest_route(
			$this->name,
			'/gf/entrymeta/search/(?P<answer>\S+)',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( new AFASM_Entry_Meta_Controller(), 'search_entry_meta_answer' ),
					'permission_callback' => array( new AFASM_Config(), 'afasm_check_authorization' ),
				),
			)
		);
	}

}
