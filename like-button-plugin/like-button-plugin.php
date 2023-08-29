<?php
/**
 * Plugin Name: My Like Button Plugin
 * Description: Adds a like button to posts.
 */

// Enqueue scripts and styles
function my_enqueue_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('my-like-button', plugin_dir_url(__FILE__) . 'js/my-like-button.js', array('jquery'), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'my_enqueue_scripts');

// AJAX function to handle like button clicks
function my_like_button_ajax() {
    check_ajax_referer('my-like-nonce', 'security');

    $post_id = intval($_POST['post_id']);
    $likes = get_post_meta($post_id, 'my_like_count', true);
    $likes++;
    update_post_meta($post_id, 'my_like_count', $likes);
    echo $likes;

    die();
}
add_action('wp_ajax_my_like_button', 'my_like_button_ajax');
add_action('wp_ajax_nopriv_my_like_button', 'my_like_button_ajax');

// Enqueue script for AJAX URL and nonce
function my_enqueue_ajax_params() {
    wp_enqueue_script('my-like-button');
    wp_localize_script('my-like-button', 'my_ajax_params', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'my_ajax_nonce' => wp_create_nonce('my-like-nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'my_enqueue_ajax_params');

// Add like button to posts
function my_add_like_button($content) {
    if (is_singular('post')) {
        $post_id = get_the_ID();
        $likes = get_post_meta($post_id, 'my_like_count', true);

        $like_button = '<button class="like-button" data-post-id="' . $post_id . '">Like</button>';
        $like_count = '<span class="like-count" data-post-id="' . $post_id . '">' . $likes . '</span>';

        $content .= '<div>' . $like_button . ' ' . $like_count . '</div>';
    }

    return $content;
}
add_filter('the_content', 'my_add_like_button');
?>
