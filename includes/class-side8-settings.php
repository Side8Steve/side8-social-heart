<?php

class Side8_Social_Heart_Settings {
	public static function register_menu() {
		add_options_page(
			__( 'Side 8 Social Heart', 'side8-social-heart' ),
			__( 'Side 8 Social Heart', 'side8-social-heart' ),
			'manage_options',
			'side8-social-heart',
			array( __CLASS__, 'render_page' )
		);
	}

	public static function register_settings() {
		register_setting( 'side8_social_heart', SIDE8_SOCIAL_HEART_OPTION_TENANT_KEY );
		register_setting( 'side8_social_heart', SIDE8_SOCIAL_HEART_OPTION_SHARED_SECRET );
	}

	public static function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$tenant_key = get_option( SIDE8_SOCIAL_HEART_OPTION_TENANT_KEY, '' );
		$shared_secret = get_option( SIDE8_SOCIAL_HEART_OPTION_SHARED_SECRET, '' );
		$test_url = wp_nonce_url( admin_url( 'options-general.php?page=side8-social-heart&side8_test=1' ), 'side8_social_heart_test' );

		if ( isset( $_GET['side8_test'] ) && check_admin_referer( 'side8_social_heart_test' ) ) {
			$client = new Side8_Social_Heart_Api_Client();
			$result = $client->test_connection();
			if ( is_wp_error( $result ) ) {
				add_settings_error( 'side8_social_heart', 'side8_social_heart_test', $result->get_error_message(), 'error' );
			} else {
				add_settings_error( 'side8_social_heart', 'side8_social_heart_test', __( 'Connection successful.', 'side8-social-heart' ), 'updated' );
			}
		}

		settings_errors( 'side8_social_heart' );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Side 8 Social Heart Settings', 'side8-social-heart' ); ?></h1>
			<form method="post" action="options.php">
				<?php settings_fields( 'side8_social_heart' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="side8-tenant-key"><?php esc_html_e( 'Tenant Key', 'side8-social-heart' ); ?></label></th>
						<td><input type="text" id="side8-tenant-key" name="<?php echo esc_attr( SIDE8_SOCIAL_HEART_OPTION_TENANT_KEY ); ?>" value="<?php echo esc_attr( $tenant_key ); ?>" class="regular-text" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="side8-shared-secret"><?php esc_html_e( 'Shared Secret', 'side8-social-heart' ); ?></label></th>
						<td><input type="password" id="side8-shared-secret" name="<?php echo esc_attr( SIDE8_SOCIAL_HEART_OPTION_SHARED_SECRET ); ?>" value="<?php echo esc_attr( $shared_secret ); ?>" class="regular-text" autocomplete="new-password" /></td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
			<p>
				<a class="button" href="<?php echo esc_url( $test_url ); ?>"><?php esc_html_e( 'Test connection', 'side8-social-heart' ); ?></a>
			</p>
		</div>
		<?php
	}
}
