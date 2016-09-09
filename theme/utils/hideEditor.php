<?php
// Hide editor on specific pages.
add_action( 'admin_init', 'hide_editor' );
function hide_editor() {
	global $App;
	$postType = $App->parameters['supportRequestsPostType'];
	remove_post_type_support($postType, 'editor');
	remove_post_type_support($postType, 'excerpt');
	remove_post_type_support($postType, 'thumbnail');

	if(!isset($_GET['post']) && !isset($_POST['post_ID'])) return;

	$post_id = $_GET['post'] ? $_GET['post'] : $_POST['post_ID'] ;
	if( !isset( $post_id ) ) return;

	$template_file = str_replace('.php','',get_post_meta($post_id, '_wp_page_template', true));

	// hide editor
	$banned = array();

	if(in_array($template_file, $banned)){
		remove_post_type_support('page', 'editor');
	}

	// hide thumbnail
	$banned = array();

	if(in_array($template_file, $banned)){
		remove_post_type_support('page', 'thumbnail');
	}
}
