<?php
/**
 * The Form Controller Class.
 *
 * @package  claud/afa-submission-manager
 * @since 1.0.0
 */

namespace AFASM\Includes\Controllers\EFB;

use AFASM\Includes\Models\EFB\AFASM_Form_Model;
use AFASM\Includes\Controllers\AFASM_Abstract_Form_Controllers;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class FormController
 *
 * Init all routes
 *
 * @since 1.0.0
 */
class AFASM_Form_Controller extends AFASM_Abstract_Form_Controllers {

	/**
	 * The form model
	 *
	 * @var AFASM_Form_Model
	 */
	private $form_model;

	/**
	 * Form Controllers constructor
	 */
	public function __construct() {
		parent::__construct();
		$this->form_model = new AFASM_Form_Model();
	}

	/**
	 * GF forms.
	 *
	 * @return array $forms GF forms.
	 */
	public function forms() {
		$count = $this->form_model->mumber_of_items();

		$offset = 0;

		$forms = $this->form_model->forms( $offset, $this->number_of_records_per_page );

		$forms_results = $this->pagination_helper->prepare_data_for_rest_with_pagination( $count, $forms );

		return rest_ensure_response( $forms_results );
	}

	/**
	 * GF forms.
	 *
	 * @param WP_REST_Request $request The request.
	 *
	 * @return aobject $form GF form.
	 */
	public function form_by_id( $request ) {
		$id = absint( $request['id'] );

		$form = $this->form_model->form_by_id( $id );

		return rest_ensure_response( $form );
	}

	/**
	 * GF forms.
	 *
	 * @param WP_REST_Request $request The request.
	 *
	 * @return array $forms GF forms.
	 */
	public function forms_pagination( $request ) {
		$page = absint( $request['page_number'] );

		$count = $this->form_model->mumber_of_items();

		$offset = $this->pagination_helper->get_offset( $page );

		$forms = $this->form_model->forms( $offset, $this->number_of_records_per_page );

		$forms_results = $this->pagination_helper->prepare_data_for_rest_with_pagination( $count, $forms );

		return rest_ensure_response( $forms_results );
	}

	/**
	 * GF forms.
	 *
	 * @param WP_REST_Request $request The request.
	 *
	 * @return array $forms GF forms.
	 */
	public function search_forms( $request ) {
		$post_name = sanitize_text_field( urldecode( $request['post_name'] ) );

		$forms_results = array();

		$offset = 0;

		$forms = $this->form_model->search_forms( $post_name, $offset, $this->number_of_records_per_page );

		$info          = array();
		$info['count'] = $this->form_model->mumber_of_items_by_search( $post_name );
		$info['pages'] = ceil( $info['count'] / $this->number_of_records_per_page );

		$forms_results['info']    = $info;
		$forms_results['results'] = $forms;

		return rest_ensure_response( $forms_results );

	}

}
