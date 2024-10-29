<?php
/**
 * The Language Class.
 *
 * @package  claud/afa-submission-manager
 * @since 1.0.0
 */

namespace AFASM\Includes\Plugins;

use AFASM\Includes\Plugins\AFASM_Constant;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Language
 *
 * Setup plugin language
 *
 * @since 1.0.0
 */
class AFASM_Language {

	/**
	 * Add supported language.
	 *
	 * @var array
	 */
	const LANGUAGES = array(
		'de' => AFASM_Constant::PLUGIN_LANGUAGE_DOMAIN . '-de',
		'en' => AFASM_Constant::PLUGIN_LANGUAGE_DOMAIN . '-en',
		'es' => AFASM_Constant::PLUGIN_LANGUAGE_DOMAIN . '-es',
		'it' => AFASM_Constant::PLUGIN_LANGUAGE_DOMAIN . '-it',
		'pt' => AFASM_Constant::PLUGIN_LANGUAGE_DOMAIN . '-pt',
		'fr' => AFASM_Constant::PLUGIN_LANGUAGE_DOMAIN . '-fr',
	);

	/**
	 * Get language by key
	 *
	 * @since 1.0.0
	 *
	 * @param string $key The key language.
	 *
	 * @return string|null
	 */
	private function get_language_by_key( $key ) {

		if ( array_key_exists( $key, self::LANGUAGES ) ) {
			return self::LANGUAGES[ $key ];
		}

		return null;
	}

	/**
	 * Load text domaain by language key
	 *
	 * @since 1.0.0
	 *
	 * @param string $key The key language.
	 *
	 * @return boolean
	 */
	public function load_textdomain_by_language_key( $key ) {

		$textdomanin = $this->get_language_by_key( $key );

		if ( empty( $textdomanin ) ) {
			return false;
		}

		$result = load_textdomain( 'afa-submission-manager', AFASM_PLUGIN_PATH . 'languages/' . $textdomanin . '.mo' );

		return $result;
	}

	/**
	 * Filters a plugin’s locale.
	 *
	 * @since 1.0.0
	 *
	 * @param string $locale The plugin's current locale.
	 * @param string $domain Text domain, Unique identifier for retrieving translated strings.
	 *
	 * @return string
	 */
	public function enforce_locale( $locale, $domain ) {

		if ( AFASM_Constant::PLUGIN_LANGUAGE_DOMAIN === $domain ) {

			$language_code = substr( $locale, 0, 2 );

			$supported_languages = array(
				'pt' => 'pt',
				'en' => 'en',
				'es' => 'es',
				'fr' => 'fr',
				'de' => 'de',
				'it' => 'it',
			);

			if ( array_key_exists( $language_code, $supported_languages ) ) {
				$locale = $supported_languages[ $language_code ];
			}
		}

		return $locale;
	}

	/**
	 * Load the plugin text domain.
	 *
	 * @since 1.0.0
	 */
	public function all_forms_load_textdomain() {
		load_plugin_textdomain( 'afa-submission-manager', false, AFASM_PLUGIN_LANGUAGE_FOLDER );
	}

	/**
	 * Check if a specific language key exists in the LANGUAGES array.
	 *
	 * @param string $language_key The language key to check (e.g., 'en', 'es', 'pt', etc.).
	 * @return bool True if the language key exists, false otherwise.
	 */
	public function is_supported_language( $language_key ) {
		return array_key_exists( $language_key, self::LANGUAGES );
	}
}
