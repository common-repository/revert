<?php
/*
Plugin Name:  Revert
Description:  A one-click button to revert a post to the previous revision.
Plugin URI:   http://lud.icro.us/wordpress-plugin-revert/
Version:      1.0
Author:       John Blackbourn
Author URI:   http://johnblackbourn.com/

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

*/

function restore_previous_revision_box() {

	global $post;

	if ( isset( $_GET['revision'] ) or !post_type_supports( $post->post_type, 'revisions' ) )
		return;

	$revisions = wp_get_post_revisions( $post->ID );

	# Ignores posts that only have one revision (the first revision has blank content) and no revisions
	if ( count( $revisions ) < 2 )
		return;

	foreach ( $revisions as $r ) {
		if ( !wp_is_post_autosave( $r ) ) {
			$revision = $r;
			break;
		}
	}

	if ( !isset( $revision) or !current_user_can( 'read_post', $revision->ID ) )
		return;

	echo '<p>';
	printf( __( 'Restore previous revision:<br /><a href="%1$s">%2$s</a>', 'revertplugin' ), wp_nonce_url( add_query_arg( array( 'revision' => $revision->ID, 'action' => 'restore' ), admin_url( 'revision.php' ) ), "restore-post_{$post->ID}|{$revision->ID}" ), wp_post_revision_title( $revision, false ) );
	echo '</p>';

}
add_action( 'post_submitbox_start', 'restore_previous_revision_box' );

?>