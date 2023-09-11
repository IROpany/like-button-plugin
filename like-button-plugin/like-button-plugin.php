<?php
/**
 * Plugin Name: My Like Button Plugin
 * Description: Adds a like button to posts.
　　Version: 1.0
　　Author: Iro
 */

// スクリプトとスタイルを読み込む
function my_enqueue_scripts() {
    wp_enqueue_script('jquery'); // jQueryを読み込む
    wp_enqueue_script('my-like-button', plugin_dir_url(__FILE__) . 'js/my-like-button.js', array('jquery'), '1.0.0', true); // カスタムスクリプトを読み込む
}
add_action('wp_enqueue_scripts', 'my_enqueue_scripts');

// いいねボタンのクリックを処理するためのAJAX関数
function my_like_button_ajax() {
    check_ajax_referer('my-like-nonce', 'security'); // セキュリティチェック

    $post_id = intval($_POST['post_id']); // ポストIDを取得
    $likes = get_post_meta($post_id, 'my_like_count', true); // いいね数を取得
    $likes++; // いいね数を増やす
    update_post_meta($post_id, 'my_like_count', $likes); // メタデータを更新
    echo $likes; // 新しいいいね数を返す

    die(); // 処理を終了
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
        $post_id = get_the_ID(); // 投稿IDを取得
        $likes = get_post_meta($post_id, 'my_like_count', true); // いいね数を取得

        $like_button = '<button class="like-button" data-post-id="' . $post_id . '">Like</button>'; // いいねボタン
        $like_count = '<span class="like-count" data-post-id="' . $post_id . '">' . $likes . '</span>'; // いいね数の表示

        $content .= '<div>' . $like_button . ' ' . $like_count . '</div>'; // 投稿コンテンツに追加
    }

    return $content;
}
add_filter('the_content', 'my_add_like_button'); // コンテンツにいいねボタンを追加
?>
