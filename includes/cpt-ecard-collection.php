<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Register eCard Collection CPT
 */
function wpe_ecard_collection_cpt() {
    $labels = [
        'name'                  => _x( 'eCard Collections', 'Post Type General Name', 'ecards' ),
        'singular_name'         => _x( 'eCard Collection', 'Post Type Singular Name', 'ecards' ),
        'menu_name'             => __( 'eCard Collections', 'ecards' ),
        'name_admin_bar'        => __( 'eCard Collection', 'ecards' ),
        'archives'              => __( 'eCard Collection Archives', 'ecards' ),
        'attributes'            => __( 'eCard Collection Attributes', 'ecards' ),
        'parent_item_colon'     => __( 'Parent eCard Collection:', 'ecards' ),
        'all_items'             => __( 'All eCard Collections', 'ecards' ),
        'add_new_item'          => __( 'Add New eCard Collection', 'ecards' ),
        'add_new'               => __( 'Add New', 'ecards' ),
        'new_item'              => __( 'New eCard Collection', 'ecards' ),
        'edit_item'             => __( 'Edit eCard Collection', 'ecards' ),
        'update_item'           => __( 'Update eCard Collection', 'ecards' ),
        'view_item'             => __( 'View eCard Collection', 'ecards' ),
        'view_items'            => __( 'View eCard Collections', 'ecards' ),
        'search_items'          => __( 'Search eCard Collection', 'ecards' ),
        'not_found'             => __( 'Not found', 'ecards' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'ecards' ),
        'featured_image'        => __( 'Featured Image', 'ecards' ),
        'set_featured_image'    => __( 'Set featured image', 'ecards' ),
        'remove_featured_image' => __( 'Remove featured image', 'ecards' ),
        'use_featured_image'    => __( 'Use as featured image', 'ecards' ),
        'insert_into_item'      => __( 'Insert into eCard collection', 'ecards' ),
        'uploaded_to_this_item' => __( 'Uploaded to this eCard collection', 'ecards' ),
        'items_list'            => __( 'eCard collections list', 'ecards' ),
        'items_list_navigation' => __( 'eCard collections list navigation', 'ecards' ),
        'filter_items_list'     => __( 'Filter eCard collections list', 'ecards' ),
    ];

    $args = [
        'label'               => __( 'eCard Collection', 'ecards' ),
        'description'         => __( 'A collection of eCards', 'ecards' ),
        'labels'              => $labels,
        'supports'            => [ 'title', 'editor' ],
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
    ];

    register_post_type( 'ecard_collection', $args );
}

add_action( 'init', 'wpe_ecard_collection_cpt', 0 );

// Add noindex meta tag for eCard Collection posts
function ecard_collection_add_noindex_meta() {
    if ( is_singular( 'ecard_collection' ) ) {
        echo '<meta name="robots" content="noindex,nofollow">' . "\n";
    }
}
add_action( 'wp_head', 'ecard_collection_add_noindex_meta', 1 );

// Prevent eCard Collection posts from being included in sitemaps
function ecard_collection_exclude_from_sitemap( $post_types ) {
    unset( $post_types['ecard_collection'] );

    return $post_types;
}
add_filter( 'wp_sitemaps_post_types', 'ecard_collection_exclude_from_sitemap' );
