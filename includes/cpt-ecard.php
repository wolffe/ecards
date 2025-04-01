<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Register eCard CPT
 */
function wpe_ecard_cpt() {
    $labels = [
        'name'                  => _x( 'eCards', 'Post Type General Name', 'ecards' ),
        'singular_name'         => _x( 'eCard', 'Post Type Singular Name', 'ecards' ),
        'menu_name'             => __( 'eCards', 'ecards' ),
        'name_admin_bar'        => __( 'eCard', 'ecards' ),
        'archives'              => __( 'eCard Archives', 'ecards' ),
        'attributes'            => __( 'eCard Attributes', 'ecards' ),
        'parent_item_colon'     => __( 'Parent eCard:', 'ecards' ),
        'all_items'             => __( 'All eCards', 'ecards' ),
        'add_new_item'          => __( 'Add New eCard', 'ecards' ),
        'add_new'               => __( 'Add New', 'ecards' ),
        'new_item'              => __( 'New eCard', 'ecards' ),
        'edit_item'             => __( 'Edit eCard', 'ecards' ),
        'update_item'           => __( 'Update eCard', 'ecards' ),
        'view_item'             => __( 'View eCard', 'ecards' ),
        'view_items'            => __( 'View eCards', 'ecards' ),
        'search_items'          => __( 'Search eCard', 'ecards' ),
        'not_found'             => __( 'Not found', 'ecards' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'ecards' ),
        'featured_image'        => __( 'Featured Image', 'ecards' ),
        'set_featured_image'    => __( 'Set featured image', 'ecards' ),
        'remove_featured_image' => __( 'Remove featured image', 'ecards' ),
        'use_featured_image'    => __( 'Use as featured image', 'ecards' ),
        'insert_into_item'      => __( 'Insert into eCard', 'ecards' ),
        'uploaded_to_this_item' => __( 'Uploaded to this eCard', 'ecards' ),
        'items_list'            => __( 'eCards list', 'ecards' ),
        'items_list_navigation' => __( 'eCards list navigation', 'ecards' ),
        'filter_items_list'     => __( 'Filter eCards list', 'ecards' ),
    ];

    $args = [
        'label'               => __( 'eCard', 'ecards' ),
        'description'         => __( 'An eCard', 'ecards' ),
        'labels'              => $labels,
        'supports'            => [ 'title', 'editor', 'author', 'custom-fields' ],
        'hierarchical'        => false,
        'public'              => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 25,
        'menu_icon'           => 'dashicons-email-alt',
        'show_in_admin_bar'   => false,
        'show_in_nav_menus'   => false,
        'can_export'          => false,
        'has_archive'         => false,
        'exclude_from_search' => true,
        'publicly_queryable'  => false,
        'rewrite'             => false,
        'capability_type'     => 'post',
        'show_in_rest'        => true,
        'capabilities'        => [
            'create_posts' => 'do_not_allow', // Removes support for the "Add New" function
        ],
    ];

    register_post_type( 'ecard', $args );
}

add_action( 'init', 'wpe_ecard_cpt', 0 );

// Add noindex meta tag for eCard posts
function ecard_add_noindex_meta() {
    if ( is_singular( 'ecard' ) ) {
        echo '<meta name="robots" content="noindex,nofollow">' . "\n";
    }
}
add_action( 'wp_head', 'ecard_add_noindex_meta', 1 );

// Prevent eCard posts from being included in sitemaps
function ecard_exclude_from_sitemap( $post_types ) {
    unset( $post_types['ecard'] );

    return $post_types;
}
add_filter( 'wp_sitemaps_post_types', 'ecard_exclude_from_sitemap' );
