<?php
/*
   Plugin Name: Like Button Posts Plugin
   Description: Adds a like button to posts.
   Version: 1.0
   Author: Chick
   Author URI: https://iropany.com/Chick/
   Domain Path: languages
   Text Domain: wp-total-hacks
*/

// ファイルへの直接アクセスした場合は終了する
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}


// Enqueue scripts and styles
function my_enqueue_scripts_and_styles() {

    // JavaScriptファイル
    wp_enqueue_script('my-like-button', plugin_dir_url(__FILE__) . 'js/my-like-button.js', array('jquery'), false , true);

    // CSSファイル
    wp_enqueue_style('my-like-button-css', plugin_dir_url(__FILE__) . 'css/style.css', array(), false , 'all');
}
add_action('wp_enqueue_scripts', 'my_enqueue_scripts_and_styles');


// いいねボタンのクリックを処理するためのAJAX関数
function my_like_button_ajax() {
    check_ajax_referer('my-like-nonce', 'security'); // セキュリティチェック

    $post_id = absint($_POST['post_id']); // 投稿IDを取得しサニタイズ
    $user_id = get_current_user_id(); // 現在のログインユーザーIDを取得

    if ($user_id) { // ログインしている場合のみ制限をかける
        $liked_users = get_post_meta($post_id, 'my_liked_users', true); // いいねしたユーザー一覧を取得

        if (!is_array($liked_users)) {
            $liked_users = array();
        }

        if (in_array($user_id, $liked_users)) {
            // すでにいいね済みなのでエラーを返す
            echo 'already_liked';
            wp_die(); // 処理終了
        }

        // いいねしていなければユーザーIDを追加して保存
        $liked_users[] = $user_id;
        update_post_meta($post_id, 'my_liked_users', $liked_users);
    }

    // いいね数を取得
    $likes = get_post_meta($post_id, 'my_like_count', true);
    if (!is_numeric($likes)) {
        $likes = 0;
    }

    $likes++; // いいね数を増やす
    update_post_meta($post_id, 'my_like_count', $likes); // 保存

    echo $likes; // 新しいいいね数を返す
    wp_die(); // 処理終了
}
add_action('wp_ajax_my_like_button', 'my_like_button_ajax'); // ログインユーザー用のAJAXアクション
add_action('wp_ajax_nopriv_my_like_button', 'my_like_button_ajax'); // 非ログインユーザー用のAJAXアクション

// AJAX URLとnonceを含むスクリプトを読み込む
function my_enqueue_ajax_params() {
    wp_enqueue_script('my-like-button'); // カスタムスクリプトを読み込む
    wp_localize_script('my-like-button', 'my_ajax_params', array(
        'ajaxurl' => admin_url('admin-ajax.php'), // AJAX URLを渡す
        'my_ajax_nonce' => wp_create_nonce('my-like-nonce'), // nonceを渡す
    ));
}
add_action('wp_enqueue_scripts', 'my_enqueue_ajax_params');

// 投稿にいいねボタンを追加
function my_add_like_button($content) {
    if (is_singular('post')) { // 投稿ページの場合
        global $post;
        $post_id = $post->ID;
        $likes = get_post_meta($post_id, 'my_like_count', true);

        if (!is_numeric($likes)) {
            $likes = 0;
        }

        $like_button = '<button class="like-button" data-post-id="' . esc_attr($post_id) . '">Like</button>';
        $like_count = '<span class="like-count" data-post-id="' . esc_attr($post_id) . '">' . esc_html($likes) . '</span>';
        $noscript = '<noscript><p>※「いいね」機能を利用するにはJavaScriptを有効にしてください。</p></noscript>';

        $content .= '<div class="my-like-wrapper">' . $like_button . ' ' . $like_count . $noscript . '</div>';
    }

    return $content;
}
add_filter('the_content', 'my_add_like_button'); // コンテンツにいいねボタンを追加
?>
