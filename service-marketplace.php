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