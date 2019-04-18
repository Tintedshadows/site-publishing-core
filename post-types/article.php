<?php

function article_init() {
	register_post_type( 'article', array(
		'labels'            => array(
			'name'                => __( 'Articles', 'bricker-core' ),
			'singular_name'       => __( 'Article', 'bricker-core' ),
			'all_items'           => __( 'All Articles', 'bricker-core' ),
			'new_item'            => __( 'New article', 'bricker-core' ),
			'add_new'             => __( 'Add New', 'bricker-core' ),
			'add_new_item'        => __( 'Add New article', 'bricker-core' ),
			'edit_item'           => __( 'Edit article', 'bricker-core' ),
			'view_item'           => __( 'View article', 'bricker-core' ),
			'search_items'        => __( 'Search articles', 'bricker-core' ),
			'not_found'           => __( 'No articles found', 'bricker-core' ),
			'not_found_in_trash'  => __( 'No articles found in trash', 'bricker-core' ),
			'parent_item_colon'   => __( 'Parent article', 'bricker-core' ),
			'menu_name'           => __( 'Articles', 'bricker-core' ),
		),
		'public'            => true,
		'hierarchical'      => false,
		'show_ui'           => true,
		'show_in_nav_menus' => true,
		'supports'          => array( 'title', 'editor', 'thumbnail' ),
		'has_archive'       => true,
		'rewrite'           => true,
		'query_var'         => true,
		'menu_icon'         => 'dashicons-format-aside',
	) );

}
//add_action( 'init', 'article_init' );

function article_updated_messages( $messages ) {
	global $post;

	$permalink = get_permalink( $post );

	$messages['article'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => sprintf( __('Article updated. <a target="_blank" href="%s">View article</a>', 'bricker-core'), esc_url( $permalink ) ),
		2 => __('Custom field updated.', 'bricker-core'),
		3 => __('Custom field deleted.', 'bricker-core'),
		4 => __('Article updated.', 'bricker-core'),
		/* translators: %s: date and time of the revision */
		5 => isset($_GET['revision']) ? sprintf( __('Article restored to revision from %s', 'bricker-core'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('Article published. <a href="%s">View article</a>', 'bricker-core'), esc_url( $permalink ) ),
		7 => __('Article saved.', 'bricker-core'),
		8 => sprintf( __('Article submitted. <a target="_blank" href="%s">Preview article</a>', 'bricker-core'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		9 => sprintf( __('Article scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview article</a>', 'bricker-core'),
		// translators: Publish box date format, see http://php.net/date
		date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
		10 => sprintf( __('Article draft updated. <a target="_blank" href="%s">Preview article</a>', 'bricker-core'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	);

	return $messages;
}
//add_filter( 'post_updated_messages', 'article_updated_messages' );
