<?php
// uninstall.phpがWordPressによって呼び出されてない場合は終了する
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

$option_name = 'like_button_posts_option';

delete_option( $option_name );

// マルチサイトのサイトオプションの場合
delete_site_option( $option_name );

// 独自のデータベーステーブルを削除する
global $wpdb;
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}like_button_posts" );
