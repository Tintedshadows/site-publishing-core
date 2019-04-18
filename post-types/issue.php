<?php

function issue_init() {
	register_post_type( 'issue', array(
		'labels'            => array(
			'name'                => __( 'Issues', 'bricker-core' ),
			'singular_name'       => __( 'Issue', 'bricker-core' ),
			'all_items'           => __( 'All Issues', 'bricker-core' ),
			'new_item'            => __( 'New issue', 'bricker-core' ),
			'add_new'             => __( 'Add New', 'bricker-core' ),
			'add_new_item'        => __( 'Add New issue', 'bricker-core' ),
			'edit_item'           => __( 'Edit issue', 'bricker-core' ),
			'view_item'           => __( 'View issue', 'bricker-core' ),
			'search_items'        => __( 'Search issues', 'bricker-core' ),
			'not_found'           => __( 'No issues found', 'bricker-core' ),
			'not_found_in_trash'  => __( 'No issues found in trash', 'bricker-core' ),
			'parent_item_colon'   => __( 'Parent issue', 'bricker-core' ),
			'menu_name'           => __( 'Issues', 'bricker-core' ),
		),
		'public'            => true,
		'hierarchical'      => false,
		'show_ui'           => true,
		'show_in_nav_menus' => true,
		'taxonomies'        => array('category', 'post_tag'),
		'supports'          => array( 'title', 'editor', 'thumbnail' ),
		'has_archive'       => true,
		'rewrite'           => array('slug' => 'issue'),
		'query_var'         => true,
		'menu_icon'         => 'dashicons-book',
	) );

}
add_action( 'init', 'issue_init' );

function issue_updated_messages( $messages ) {
	global $post;

	$permalink = get_permalink( $post );

	$messages['issue'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => sprintf( __('Issue updated. <a target="_blank" href="%s">View issue</a>', 'bricker-core'), esc_url( $permalink ) ),
		2 => __('Custom field updated.', 'bricker-core'),
		3 => __('Custom field deleted.', 'bricker-core'),
		4 => __('Issue updated.', 'bricker-core'),
		/* translators: %s: date and time of the revision */
		5 => isset($_GET['revision']) ? sprintf( __('Issue restored to revision from %s', 'bricker-core'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('Issue published. <a href="%s">View issue</a>', 'bricker-core'), esc_url( $permalink ) ),
		7 => __('Issue saved.', 'bricker-core'),
		8 => sprintf( __('Issue submitted. <a target="_blank" href="%s">Preview issue</a>', 'bricker-core'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		9 => sprintf( __('Issue scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview issue</a>', 'bricker-core'),
		// translators: Publish box date format, see http://php.net/date
		date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
		10 => sprintf( __('Issue draft updated. <a target="_blank" href="%s">Preview issue</a>', 'bricker-core'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	);

	return $messages;
}
add_filter( 'post_updated_messages', 'issue_updated_messages' );
