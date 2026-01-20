<?php

class Side8_Social_Heart_Rest_Routes {
	public static function register_routes() {
		register_rest_route(
			'side8/v1',
			'/submit',
			array(
				'methods' => 'POST',
				'callback' => array( __CLASS__, 'handle_submit' ),
				'permission_callback' => array( __CLASS__, 'can_submit' ),
			)
		);

		register_rest_route(
			'side8/v1',
			'/activity',
			array(
				'methods' => 'GET',
				'callback' => array( __CLASS__, 'handle_activity' ),
				'permission_callback' => array( __CLASS__, 'can_view_activity' ),
			)
		);
	}

	public static function can_submit( $request ) {
		return Side8_Social_Heart_Security::rest_permission_check( $request, 'side8_social_submit' );
	}

	public static function can_view_activity( $request ) {
		return Side8_Social_Heart_Security::rest_permission_check( $request, 'side8_social_view_activity' );
	}

	public static function handle_submit( $request ) {
		$params = $request->get_json_params();
		$submission_id = Side8_Social_Heart_Storage::create_submission( $params );
		$warning = null;

		if ( is_wp_error( $submission_id ) ) {
			return new WP_Error(
				'side8_submission_failed',
				__( 'Unable to create submission.', 'side8-social-heart' ),
				array( 'status' => 500 )
			);
		}

		if ( ! Side8_Social_Heart_Storage::log_activity( $submission_id, 'submitted', __( 'Submission created.', 'side8-social-heart' ) ) ) {
			$warning = 'activity_log_failed';
		}

		$client = new Side8_Social_Heart_Api_Client();
		$engine_response = $client->post( '/api/submit', array(
			'submission_id' => $submission_id,
			'payload' => $params,
		) );

		if ( is_wp_error( $engine_response ) ) {
			if ( ! Side8_Social_Heart_Storage::log_activity( $submission_id, 'engine_error', $engine_response->get_error_message() ) ) {
				$warning = 'activity_log_failed';
			}
			$reason = 'unknown';
			$status = 500;
			if ( 'http_request_failed' === $engine_response->get_error_code() ) {
				$reason = 'unreachable';
				$status = 502;
			} elseif ( 'side8_request_failed' === $engine_response->get_error_code() ) {
				$reason = 'rejected';
				$status = 502;
			}

			$data = array(
				'status' => $status,
				'reason' => $reason,
			);

			if ( $warning ) {
				$data['warning'] = $warning;
			}

			return new WP_Error(
				'side8_engine_failed',
				__( 'The posting service is currently unavailable.', 'side8-social-heart' ),
				$data
			);
		}

		$response = array(
			'submission_id' => $submission_id,
			'status' => 'submitted',
		);

		if ( $warning ) {
			$response['warning'] = $warning;
		}

		return rest_ensure_response( $response );
	}

	public static function handle_activity() {
		$activity = Side8_Social_Heart_Storage::get_recent_activity();

		return rest_ensure_response( $activity );
	}
}
