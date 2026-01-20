<?php

class Side8_Social_Heart_Activator {
	public static function activate() {
		self::add_roles();
		Side8_Social_Heart_Storage::register_post_types();
		flush_rewrite_rules();
	}

	private static function add_roles() {
		add_role(
			'side8_social_author',
			__( 'Social Author', 'side8-social-heart' ),
			array(
				'read' => true,
				'side8_social_submit' => true,
				'side8_social_view_activity' => true,
			)
		);

		add_role(
			'side8_social_approver',
			__( 'Social Approver', 'side8-social-heart' ),
			array(
				'read' => true,
				'side8_social_approve' => true,
				'side8_social_schedule' => true,
				'side8_social_retry' => true,
				'side8_social_submit' => true,
				'side8_social_view_activity' => true,
			)
		);

		$admin = get_role( 'administrator' );
		if ( $admin ) {
			$admin->add_cap( 'side8_social_submit' );
			$admin->add_cap( 'side8_social_approve' );
			$admin->add_cap( 'side8_social_view_activity' );
			$admin->add_cap( 'side8_social_schedule' );
			$admin->add_cap( 'side8_social_retry' );
		}
	}
}
