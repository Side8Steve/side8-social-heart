<?php

class Side8_Social_Heart_Shortcode {
	public static function register() {
		add_shortcode( 'side8_social_heart', array( __CLASS__, 'render' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
	}

	public static function enqueue_assets() {
		if ( ! is_singular() ) {
			return;
		}

		global $post;
		if ( ! $post || ! has_shortcode( $post->post_content, 'side8_social_heart' ) ) {
			return;
		}

		wp_enqueue_style(
			'side8-social-heart-portal',
			plugins_url( '../assets/css/portal.css', __FILE__ ),
			array(),
			'0.1.0'
		);

		wp_enqueue_script(
			'side8-social-heart-portal',
			plugins_url( '../assets/js/portal.js', __FILE__ ),
			array( 'wp-api-fetch' ),
			'0.1.0',
			true
		);

		wp_localize_script(
			'side8-social-heart-portal',
			'Side8SocialHeart',
			array(
				'nonce'   => wp_create_nonce( 'wp_rest' ),
			)
		);
	}

	public static function render() {
		if ( ! is_user_logged_in() ) {
			return sprintf(
				'<p>%s</p>',
				wp_kses_post( sprintf( __( 'Please <a href="%s">log in</a> to access the social portal.', 'side8-social-heart' ), esc_url( wp_login_url( get_permalink() ) ) ) )
			);
		}

		if ( ! current_user_can( 'side8_social_submit' ) && ! current_user_can( 'side8_social_view_activity' ) ) {
			return sprintf(
				'<p>%s</p>',
				esc_html__( 'You don’t have access. Please contact your administrator.', 'side8-social-heart' )
			);
		}

		ob_start();
		include __DIR__ . '/../templates/portal.php';
		return ob_get_clean();
	}
}
