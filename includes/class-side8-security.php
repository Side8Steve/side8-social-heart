<?php

class Side8_Social_Heart_Security {
	public static function rest_permission_check( WP_REST_Request $request, $capability ) {
		if ( ! is_user_logged_in() ) {
			return new WP_Error( 'side8_unauthorized', __( 'You must be logged in.', 'side8-social-heart' ), array( 'status' => 401 ) );
		}

		if ( ! current_user_can( $capability ) ) {
			return new WP_Error( 'side8_forbidden', __( 'You do not have permission.', 'side8-social-heart' ), array( 'status' => 403 ) );
		}

		$nonce = $request->get_header( 'X-WP-Nonce' );
		if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			return new WP_Error( 'side8_nonce_invalid', __( 'Invalid nonce.', 'side8-social-heart' ), array( 'status' => 403 ) );
		}

		return true;
	}
}
