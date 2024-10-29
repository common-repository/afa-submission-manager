<?php
/**
 * The Pulbic Route Class.
 *
 * @package  claud/afa-submission-manager
 * @since 1.0.0
 */

namespace AFASM\Includes\Plugins;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Public Route
 *
 * Init all public routes
 *
 * @since 1.0.0
 */
class AFASM_Public_Route {

	/**
	 * The slugs in the URL before the endpoint.
	 *
	 * @var string
	 */
	private $name;

	/**
	 * Add public route.
	 *
	 * @var array[string]
	 */
	private $public_routes = array(
		'/user/login',
		'/user/login/qrcode',
		'/user/tokens/refresh',
		'/ping',
	);

	/**
	 * Route constructor.
	 *
	 * @param string $name The route name space.
	 */
	public function __construct( $name ) {
		$this->name = $name;
	}

	/**
	 * Get public routes
	 *
	 * @return array $public_routes all public route with namespace
	 */
	public function get_public_routes() {
		return array_map(
			function ( $value ) {
				return $this->name . $value;
			},
			$this->public_routes
		);
	}

	/**
	 * Verify if some rote is public or not
	 *
	 * @param string $route The route that route being accessed.
	 *
	 * @return bool
	 */
	public function is_public_route( $route ) {
		return in_array( $route, $this->get_public_routes(), true );
	}
}
