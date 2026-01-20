<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

remove_role( 'side8_social_author' );
remove_role( 'side8_social_approver' );

$admin = get_role( 'administrator' );
if ( $admin ) {
	$admin->remove_cap( 'side8_social_submit' );
	$admin->remove_cap( 'side8_social_approve' );
	$admin->remove_cap( 'side8_social_view_activity' );
	$admin->remove_cap( 'side8_social_schedule' );
	$admin->remove_cap( 'side8_social_retry' );
}
