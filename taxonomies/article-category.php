<?php

function article_category_init() {
	register_taxonomy( 'article-category', array( 'article' ), array(
		'hierarchical'      => true,
		'public'            => true,
		'show_in_nav_menus' => true,
		'show_ui'           => true,
		'show_admin_column' => false,
		'query_var'         => true,
		'rewrite'           => true,
		'capabilities'      => array(
			'manage_terms'  => 'edit_posts',
			'edit_terms'    => 'edit_posts',
			'delete_terms'  => 'edit_posts',
			'assign_terms'  => 'edit_posts'
		),
		'labels'            => array(
			'name'                       => __( 'Article categories', 'bricker-publishing-core' ),
			'singular_name'              => _x( 'Article category', 'taxonomy general name', 'bricker-publishing-core' ),
			'search_items'               => __( 'Search article categories', 'bricker-publishing-core' ),
			'popular_items'              => __( 'Popular article categories', 'bricker-publishing-core' ),
			'all_items'                  => __( 'All article categories', 'bricker-publishing-core' ),
			'parent_item'                => __( 'Parent article category', 'bricker-publishing-core' ),
			'parent_item_colon'          => __( 'Parent article category:', 'bricker-publishing-core' ),
			'edit_item'                  => __( 'Edit article category', 'bricker-publishing-core' ),
			'update_item'                => __( 'Update article category', 'bricker-publishing-core' ),
			'add_new_item'               => __( 'New article category', 'bricker-publishing-core' ),
			'new_item_name'              => __( 'New article category', 'bricker-publishing-core' ),
			'separate_items_with_commas' => __( 'Article categories separated by comma', 'bricker-publishing-core' ),
			'add_or_remove_items'        => __( 'Add or remove article categories', 'bricker-publishing-core' ),
			'choose_from_most_used'      => __( 'Choose from the most used article categories', 'bricker-publishing-core' ),
			'not_found'                  => __( 'No article categories found.', 'bricker-publishing-core' ),
			'menu_name'                  => __( 'Article categories', 'bricker-publishing-core' ),
		),
	) );

}
//add_action( 'init', 'article_category_init' );
