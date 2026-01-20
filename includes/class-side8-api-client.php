<?php

class Side8_Social_Heart_Api_Client {
	public function post( $path, $payload ) {
		$tenant_key = get_option( SIDE8_SOCIAL_HEART_OPTION_TENANT_KEY, '' );
		$secret = get_option( SIDE8_SOCIAL_HEART_OPTION_SHARED_SECRET, '' );

		if ( empty( $tenant_key ) || empty( $secret ) ) {
			return new WP_Error( 'side8_missing_credentials', __( 'Tenant key and shared secret are required.', 'side8-social-heart' ) );
		}

		$body = wp_json_encode( $payload );
		$signature = hash_hmac( 'sha256', $body, $secret );

		$args = array(
			'headers' => array(
				'Side8-Client' => 'wordpress',
				'Content-Type' => 'application/json',
				SIDE8_SOCIAL_HEART_HEADER_TENANT => $tenant_key,
				SIDE8_SOCIAL_HEART_HEADER_SIGNATURE => $signature,
			),
			'body' => $body,
			'timeout' => 15,
		);

		$response = wp_remote_post( trailingslashit( SIDE8_SOCIAL_HEART_ENGINE_BASE ) . ltrim( $path, '/' ), $args );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = wp_remote_retrieve_response_code( $response );
		if ( $code >= 400 ) {
			return new WP_Error( 'side8_request_failed', __( 'The engine request failed.', 'side8-social-heart' ), $response );
		}

		return $response;
	}

	public function test_connection() {
		return $this->post( '/api/ping', array( 'source' => 'wordpress' ) );
	}
}
