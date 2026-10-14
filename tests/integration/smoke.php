<?php

if ( ! defined( 'ABSPATH' ) || ! class_exists( 'Post_Type_Switcher' ) ) {
	fwrite( STDERR, "Post Type Switcher is not active.\n" );
	exit( 1 );
}

$administrator = get_users(
	array(
		'role'   => 'administrator',
		'number' => 1,
	)
);
if ( empty( $administrator ) ) {
	fwrite( STDERR, "No administrator is available.\n" );
	exit( 1 );
}

wp_set_current_user( $administrator[0]->ID );
$post_id = wp_insert_post(
	array(
		'post_title'  => 'Post Type Switcher smoke test',
		'post_status' => 'draft',
		'post_type'   => 'post',
	)
);
if ( is_wp_error( $post_id ) || ! $post_id ) {
	fwrite( STDERR, "Unable to create the smoke-test post.\n" );
	exit( 1 );
}

$plugin = new Post_Type_Switcher();
$data   = array( 'post_type' => 'post' );
$post   = array( 'ID' => $post_id, 'post_ID' => $post_id, 'post_type' => 'post' );
$_REQUEST['pts_post_type']    = 'page';
$_REQUEST['pts-nonce-select'] = wp_create_nonce( 'post-type-selector' );

$result = $plugin->override_type( $data, $post );
unset( $_REQUEST['pts_post_type'], $_REQUEST['pts-nonce-select'] );
wp_delete_post( $post_id, true );

if ( 'page' !== $result['post_type'] ) {
	fwrite( STDERR, "The authorized post-type override did not succeed.\n" );
	exit( 1 );
}

fwrite( STDOUT, "Post Type Switcher integration smoke test passed.\n" );
