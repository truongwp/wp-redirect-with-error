<?php
/**
 * Class Truongwp_Redirect_With_Error
 *
 * Handle error when redirecting to other URL in WordPress.
 * Use simple URL parameter and nonce, don't use SESSION or COOKIE which not advised in WordPress.
 *
 * @package Truongwp_Redirect_With_Error
 */

/**
 * Class Truongwp_Redirect_With_Error
 */
class Truongwp_Redirect_With_Error {

	/**
	 * Assoc array with key is error code and value is error message.
	 *
	 * @var array
	 */
	protected $errors = array();

	/**
	 * Name of URL parameter for error.
	 *
	 * @var string
	 */
	protected $error_key = 'error-code';

	/**
	 * Name of URL parameter for nonce.
	 *
	 * @var string
	 */
	protected $nonce_key = 'token';

	/**
	 * Nonce action name.
	 *
	 * @var string
	 */
	protected $nonce_action = 'truongwp-redirect-with-error';

	/**
	 * Template for displaying error, use %1$s for error code and %2$s for error message.
	 *
	 * @var string
	 */
	protected $template = '<div class="alert alert-danger error-%1$s">%2$s</div>';

	/**
	 * Set new error key.
	 *
	 * @param string $key New error key.
	 */
	public function set_error_key( $key ) {
		$this->error_key = $key;
	}

	/**
	 * Set new nonce key.
	 *
	 * @param string $key New nonce key.
	 */
	public function set_nonce_key( $key ) {
		$this->nonce_key = $key;
	}

	/**
	 * Set new nonce action name,
	 *
	 * @param string $action New nonce action name.
	 */
	public function set_nonce_action( $action ) {
		$this->nonce_action = $action;
	}

	/**
	 * Set new template markup.
	 *
	 * @param string $template New template markup.
	 */
	public function set_template( $template ) {
		$this->template = $template;
	}

	/**
	 * Register an error,
	 *
	 * @param string $error_code    Error code.
	 * @param string $error_message Error message.
	 */
	public function register_error( $error_code, $error_message ) {
		$this->errors[ $error_code ] = strval( $error_message );
	}

	/**
	 * Get error message from error code.
	 *
	 * @param string $error_code Error code.
	 * @return string|false Error message. False if error code is not exists.
	 */
	public function get_error( $error_code ) {
		if ( isset( $this->errors[ $error_code ] ) ) {
			return $this->errors[ $error_code ];
		}

		return false;
	}

	/**
	 * Pass an error to URL.
	 *
	 * @param string $url        Redirect URL.
	 * @param string $error_code Error code.
	 * @return string URL with error.
	 */
	public function add_error( $url, $error_code ) {
		$nonce = wp_create_nonce( $this->nonce_action );

		$new_url = add_query_arg( array(
			$this->error_key => $error_code,
			$this->nonce_key => $nonce,
		), $url );
		return $new_url;
	}

	/**
	 * Display error.
	 *
	 * @param string $code Only display a specific error code. Default is null.
	 * @param bool   $echo Print error of return.
	 * @return string|null Error HTML if $echo is false.
	 */
	public function show_error( $code = null, $echo = true ) {
		if ( empty( $_GET[ $this->error_key ] ) || empty( $_GET[ $this->nonce_key ] ) ) {
			return;
		}

		// Verify nonce to prevent error displayed when only pass error code to URL.
		$nonce = $_GET[ $this->nonce_key ]; // WPCS: sanitization ok.
		if ( ! wp_verify_nonce( $nonce, $this->nonce_action ) ) {
			return;
		}

		$error_code = sanitize_text_field( wp_unslash( $_GET[ $this->error_key ] ) );

		if ( $code && $error_code !== $code ) {
			return;
		}

		$error_message = $this->get_error( $error_code );
		if ( ! $error_message ) {
			return;
		}

		$html = sprintf(
			$this->template,
			esc_attr( $error_code ),
			wp_kses_post( $error_message )
		);

		if ( $echo ) {
			echo wp_kses_post( $html );
		}

		return $html;
	}
}
