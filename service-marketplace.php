<?php
/**
 * Plugin Name:       Service Marketplace
 * Plugin URI:        https://github.com/your-username/wordpress-service-marketplace
 * Description:       A custom multi-business service marketplace for WordPress.
 * Version:           1.0.0
 * Author:            Your Name
 * Text Domain:       service-marketplace
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

/**
 * Register custom roles on plugin activation.
 */
function sm_register_custom_roles() {
    // Bussiness Role
    add_role(
        'business',
        'Bussiness Owner',
        array(
            'read' => true,
            'upload_files' => true,
            'edit_posts' => true,
        )
    );

    // Customer Role
    add_role(
        'customer',
        'Customer',
        array(
            'read' => true,
            'upload_files' => false,
            'edit_posts' => false,
        )
    );
}

register_activation_hook(
    __FILE__,
    'sm_register_custom_roles'
);

/** 
 * Register 'service' custom post type
 */
function sm_register_service_post_type() {
    $labels = array(
        'name' => 'Services',
        'singular_name' => 'Service',
        'add_new' => 'Add New Service',
        'add_new_item' => 'Add New Service',
        'edit_item' => 'Edit Service',
        'new_item' => 'New Service',
        'all_items' => 'All Services',
        'view_item' => 'View Service',
        'search_items' => 'Search Services',
        'not_found' => 'No services found',
        'not_found_in_trash' => 'No services found in Trash',
        'menu_name' => 'Services',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor'),
        'capability_type' => 'post',
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'services'),
        'hierarchical' => false,
        'menu_icon' => 'dashicons-portfolio',
        'show_in_rest' => false,
    );
    register_post_type('service', $args);
}

add_action('init', 'sm_register_service_post_type');


/**
 * Add Price meta box to the 'service' post type
 */
function sm_add_price_meta_box() {
    add_meta_box(
        'sm_service_price',
        'Price',
        'sm_service_price_meta_box_html',
        'service',
        'normal',
        'high'
    );
}

add_action('add_meta_boxes_service', 'sm_add_price_meta_box');


/**
 * Display Price field
 */
function sm_service_price_meta_box_html($post) {

    $price = get_post_meta($post->ID, '_service_price', true);

    wp_nonce_field(
        'sm_save_service_price',
        'sm_service_price_nonce'
    );
    ?>

    <label for="sm_service_price">Price:</label>

    <input
        type="number"
        id="sm_service_price"
        name="sm_service_price"
        value="<?php echo esc_attr($price); ?>"
        step="0.01"
        min="0"
    >

    <?php
}


/**
 * Save Price meta box data
 */
function sm_save_service_price_meta_box_data($post_id) {

    if (
        !isset($_POST['sm_service_price_nonce']) ||
        !wp_verify_nonce(
            $_POST['sm_service_price_nonce'],
            'sm_save_service_price'
        )
    ) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['sm_service_price'])) {
        update_post_meta(
            $post_id,
            '_service_price',
            sanitize_text_field($_POST['sm_service_price'])
        );
    }
}

add_action(
    'save_post_service',
    'sm_save_service_price_meta_box_data'
);