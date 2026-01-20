<?php

class Side8_Social_Heart_Storage {
	public static function register_post_types() {
		self::register_statuses();
		register_post_type(
			SIDE8_SOCIAL_HEART_CPT_SUBMISSION,
			array(
				'label' => __( 'Social Submissions', 'side8-social-heart' ),
				'public' => false,
				'show_ui' => false,
				'supports' => array( 'title', 'editor', 'author' ),
				'capability_type' => 'post',
				'rewrite' => false,
			)
		);

		register_post_type(
			SIDE8_SOCIAL_HEART_CPT_ACTIVITY,
			array(
				'label' => __( 'Social Activity', 'side8-social-heart' ),
				'public' => false,
				'show_ui' => false,
				'supports' => array( 'title', 'editor', 'author' ),
				'capability_type' => 'post',
				'rewrite' => false,
			)
		);
	}

	private static function register_statuses() {
		$statuses = array(
			'side8_draft' => __( 'Draft', 'side8-social-heart' ),
			'side8_submitted' => __( 'Submitted', 'side8-social-heart' ),
			'side8_approved' => __( 'Approved', 'side8-social-heart' ),
			'side8_queued' => __( 'Queued', 'side8-social-heart' ),
			'side8_posted' => __( 'Posted', 'side8-social-heart' ),
			'side8_failed' => __( 'Failed', 'side8-social-heart' ),
		);

		foreach ( $statuses as $status => $label ) {
			register_post_status(
				$status,
				array(
					'label' => $label,
					'public' => false,
					'exclude_from_search' => true,
					'show_in_admin_all_list' => false,
					'show_in_admin_status_list' => false,
				)
			);
		}
	}

	public static function create_submission( $data ) {
		$title = isset( $data['title'] ) ? sanitize_text_field( $data['title'] ) : __( 'New Submission', 'side8-social-heart' );
		$content = isset( $data['caption'] ) ? wp_kses_post( $data['caption'] ) : '';

		$post_id = wp_insert_post(
			array(
				'post_type' => SIDE8_SOCIAL_HEART_CPT_SUBMISSION,
				'post_status' => 'side8_submitted',
				'post_title' => $title,
				'post_content' => $content,
				'post_author' => get_current_user_id(),
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		if ( isset( $data['post_id'] ) ) {
			update_post_meta( $post_id, 'side8_post_id', absint( $data['post_id'] ) );
		}

		if ( isset( $data['channels'] ) && is_array( $data['channels'] ) ) {
			update_post_meta( $post_id, 'side8_channels', array_map( 'sanitize_text_field', $data['channels'] ) );
		}

		update_post_meta( $post_id, 'side8_status', 'submitted' );

		return $post_id;
	}

	public static function log_activity( $submission_id, $action, $message ) {
		$result = wp_insert_post(
			array(
				'post_type' => SIDE8_SOCIAL_HEART_CPT_ACTIVITY,
				'post_status' => 'publish',
				'post_title' => sanitize_text_field( $action ),
				'post_content' => wp_kses_post( $message ),
				'post_author' => get_current_user_id(),
				'meta_input' => array(
					'side8_submission_id' => absint( $submission_id ),
				),
			)
		);

		if ( is_wp_error( $result ) || 0 === $result ) {
			error_log( 'Side8 Social Heart: failed to write activity log entry.' );
			return false;
		}

		return true;
	}

	public static function get_recent_activity() {
		$items = get_posts(
			array(
				'post_type' => SIDE8_SOCIAL_HEART_CPT_ACTIVITY,
				'posts_per_page' => 20,
				'orderby' => 'date',
				'order' => 'DESC',
			)
		);

		return array_map(
			function( $post ) {
				return array(
					'id' => $post->ID,
					'action' => $post->post_title,
					'message' => $post->post_content,
					'created_at' => $post->post_date_gmt,
				);
			},
			$items
		);
	}

	public static function get_recent_submissions() {
		return get_posts(
			array(
				'post_type' => SIDE8_SOCIAL_HEART_CPT_SUBMISSION,
				'posts_per_page' => 20,
				'orderby' => 'date',
				'order' => 'DESC',
				'post_status' => array(
					'side8_draft',
					'side8_submitted',
					'side8_approved',
					'side8_queued',
					'side8_posted',
					'side8_failed',
				),
			)
		);
	}
}
