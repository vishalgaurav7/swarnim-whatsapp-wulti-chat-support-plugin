<?php
/*
Plugin Name: Swarnim Multi-Chat Support
Description: Adds a customizable swarnim chat button with multi-user and group chat support.
Version: 1.0
Author: Vishal Gaurav
License: GPL2
*/

if (!defined('ABSPATH')) exit;

// Function to get the plugin version dynamically
function swarnim_get_plugin_version() {
    $plugin_data = get_file_data(__FILE__, array('Version' => 'Version'), 'plugin');
    return isset($plugin_data['Version']) ? $plugin_data['Version'] : '1.0.0';
}

// Enqueue styles and scripts
// Frontend Script
function swarnim_multi_chat_enqueue_scripts() {
    wp_enqueue_script(
        'swarnim-multi-chat-frontend',
        plugin_dir_url(__FILE__) . 'swarnim-multi-chat.js',
        array('jquery'),
        swarnim_get_plugin_version(),
        true
    );
}
add_action('wp_enqueue_scripts', 'swarnim_multi_chat_enqueue_scripts');

// Admin Script
function swarnim_multi_chat_enqueue_admin_scripts($hook) {
    if (strpos($hook, 'swarnim-multi-chat') !== false) {
        wp_enqueue_script(
            'swarnim-multi-chat-admin',
            plugin_dir_url(__FILE__) . 'swarnim-multi-chat.js',
            array('jquery'),
            swarnim_get_plugin_version(),
            true
        );
    }
}
add_action('admin_enqueue_scripts', 'swarnim_multi_chat_enqueue_admin_scripts');
function swarnim_multi_chat_enqueue_admin_styles($hook) {
    if ($hook === 'toplevel_page_swarnim-multi-chat') {
        wp_enqueue_style(
            'swarnim-multi-chat-admin-style',
            plugins_url('swarnim-multi-chat.css', __FILE__),
            array(),
            swarnim_get_plugin_version()
        );
    }
}
add_action('admin_enqueue_scripts', 'swarnim_multi_chat_enqueue_admin_styles');



// Display the Swarnim Chat Button
// Display the Swarnim Chat Button
function swarnim_multi_chat_button_display() {
    $users = get_option('swarnim_multi_chat_users', []);

    echo '<div class="swarnim-multi-chat-container">';
    foreach ($users as $user) {
        $phone = $user['phone'] ?? '';
        $name = $user['name'] ?? 'Support';
        $message = urlencode($user['message'] ?? 'Hello! How can we help you?');
        $position = $user['position'] ?? 'bottom-right';
        $container_bg_color = isset($user['container_bg_transparent']) && $user['container_bg_transparent'] ? 'transparent' : ($user['container_bg_color'] ?? '#FFFFFF');
        $text_color = $user['text_color'] ?? '#FFFFFF';
        $size = $user['size'] ?? '60px';
        $icon_id = $user['icon_id'] ?? ''; // Use an attachment ID for the icon_id

        $font_size = $user['font_size'] ?? '14px';
        $font_style = $user['font_style'] ?? 'normal';
        $font_weight = $user['font_weight'] ?? 'normal';

        // Build the chat button
        echo '<div class="swarnim-multi-chat-button ' . esc_attr($position) . '" 
                  style="background-color: ' . esc_attr($container_bg_color) . '; padding: 10px; border-radius: 8px; display: inline-flex; align-items: center; margin: 10px; box-shadow: 0px 4px 6px rgba(0,0,0,0.1);">
                <a href="https://wa.me/' . esc_attr($phone) . '?text=' . esc_attr($message) . '" target="_blank" data-ga-label="' . esc_attr($name) . '" 
                   style="display: flex; align-items: center; text-decoration: none;">';

        // Display the image
        if ($icon_id) {
    echo wp_get_attachment_image(
        $icon_id,
        'thumbnail',
        false,
        [
            'style' => 'width:' . esc_attr($size) . '; height:' . esc_attr($size) . '; margin-right: 10px; border-radius: 50%;',
            'alt'   => esc_attr($name),
        ]
    );
} else {
    // Fallback to a default icon registered as an attachment
    $fallback_icon_id = get_option('swarnim_fallback_icon_id'); // Retrieve saved fallback ID
    if ($fallback_icon_id) {
        echo wp_get_attachment_image(
            $fallback_icon_id,
            'thumbnail',
            false,
            [
                'style' => 'width:' . esc_attr($size) . '; height:' . esc_attr($size) . '; margin-right: 10px; border-radius: 50%;',
                'alt'   => esc_attr($name),
            ]
        );
    }
}


        // Display the name
        echo '<span style="color:' . esc_attr($text_color) . '; font-size:' . esc_attr($font_size) . '; font-style:' . esc_attr($font_style) . '; font-weight:' . esc_attr($font_weight) . '; line-height: 1.2;">
                        ' . esc_html($name) . '
                    </span>
                </a>
              </div>';
    }
    echo '</div>';
}

add_action('wp_footer', 'swarnim_multi_chat_button_display');

// Admin settings for configuring multiple users
function swarnim_multi_chat_settings() {
    add_menu_page(
        'Swarnim Multi-Chat Settings',      // Page title
        'Swarnim Multi-Chat',               // Menu title
        'manage_options',                   // Capability
        'swarnim-multi-chat',               // Menu slug
        'swarnim_multi_chat_settings_page', // Callback function
        'dashicons-admin-generic',          // Icon (can be customized)
        50                                  // Position (optional, default is 80)
    );
}
add_action('admin_menu', 'swarnim_multi_chat_settings');

// Settings page HTML
function swarnim_multi_chat_settings_page() {
    ?>
    <div class="wrap">
        <h1>Swarnim Multi-Chat Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('swarnim_multi_chat_options');
            do_settings_sections('swarnim_multi_chat');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register settings and fields
function swarnim_multi_chat_settings_init() {
    register_setting('swarnim_multi_chat_options', 'swarnim_multi_chat_users');

    add_settings_section(
        'swarnim_multi_chat_section',
        'User Settings',
        function () {
            echo '<p>Configure Swarnim chat buttons for each user below.</p>';
        },
        'swarnim_multi_chat'
    );

    add_settings_field(
        'swarnim_multi_chat_users',
        'Add Chat Users',
        'swarnim_multi_chat_users_callback',
        'swarnim_multi_chat',
        'swarnim_multi_chat_section'
    );
}
add_action('admin_init', 'swarnim_multi_chat_settings_init');

// Settings page fields
function swarnim_multi_chat_users_callback() {
    $users = get_option('swarnim_multi_chat_users', []);
    ?>
    <div id="swarnim-multi-chat-users">
        <?php foreach ($users as $index => $user) { ?>
            <div class="user" data-index="<?php echo esc_attr($index); ?>">
                <h4>User <?php echo esc_html($index + 1); ?></h4>
                <label>Name:</label>
                <input type="text" name="swarnim_multi_chat_users[<?php echo esc_attr($index); ?>][name]" value="<?php echo esc_attr($user['name'] ?? ''); ?>" />
                
                <label>Phone Number:</label>
                <input type="text" name="swarnim_multi_chat_users[<?php echo esc_attr($index); ?>][phone]" value="<?php echo esc_attr($user['phone'] ?? ''); ?>" />
                
                <label>Default Message:</label>
                <input type="text" name="swarnim_multi_chat_users[<?php echo esc_attr($index); ?>][message]" value="<?php echo esc_attr($user['message'] ?? ''); ?>" />
                
                <label>Container Background Color:</label>
                <input type="color" name="swarnim_multi_chat_users[<?php echo esc_attr($index); ?>][container_bg_color]" value="<?php echo esc_attr($user['container_bg_color'] ?? '#FFFFFF'); ?>" />
                <label>
                    <input type="checkbox" name="swarnim_multi_chat_users[<?php echo esc_attr($index); ?>][container_bg_transparent]" value="1" 
                           <?php checked(!empty($user['container_bg_transparent'])); ?>> Transparent
                </label>
                
                <label>Text Color:</label>
                <input type="color" name="swarnim_multi_chat_users[<?php echo esc_attr($index); ?>][text_color]" value="<?php echo esc_attr($user['text_color'] ?? '#FFFFFF'); ?>" />
                
                <label>Button Size (e.g., 60px):</label>
                <input type="text" name="swarnim_multi_chat_users[<?php echo esc_attr($index); ?>][size]" value="<?php echo esc_attr($user['size'] ?? '60px'); ?>" />
                
                <label>Font Size (e.g., 14px):</label>
                <input type="text" name="swarnim_multi_chat_users[<?php echo esc_attr($index); ?>][font_size]" value="<?php echo esc_attr($user['font_size'] ?? '14px'); ?>" />
                
                <label>Font Style:</label>
                <select name="swarnim_multi_chat_users[<?php echo esc_attr($index); ?>][font_style]">
                    <option value="normal" <?php selected($user['font_style'] ?? '', 'normal'); ?>>Normal</option>
                    <option value="italic" <?php selected($user['font_style'] ?? '', 'italic'); ?>>Italic</option>
                </select>
                
                <label>Font Weight:</label>
                <select name="swarnim_multi_chat_users[<?php echo esc_attr($index); ?>][font_weight]">
                    <option value="normal" <?php selected($user['font_weight'] ?? '', 'normal'); ?>>Normal</option>
                    <option value="bold" <?php selected($user['font_weight'] ?? '', 'bold'); ?>>Bold</option>
                </select>
                
                <button type="button" class="remove-user">Remove User</button>
            </div>
        <?php } ?>
        
    </div>
	<button type="button" id="add-user">Add User</button>
    <?php
}
// Add settings link to the Plugins page
function swarnim_multi_chat_settings_link($links) {
    $settings_link = '<a href="admin.php?page=swarnim-multi-chat">Settings</a>';
    array_unshift($links, $settings_link); // Add the link to the beginning of the array
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'swarnim_multi_chat_settings_link');
