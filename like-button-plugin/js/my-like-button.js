
jQuery(document).ready(function($) {
    // ページが読み込まれた際に実行
    $('.like-button').on('click', function() {
        // 「いいね」ボタンがクリックされた時の処理
        var postId = $(this).data('post-id'); // 投稿IDを取得

        // AJAXリクエストを送信
        $.ajax({
            type: 'POST', // POSTリクエスト
            url: my_ajax_params.ajaxurl, // AJAXのURL（WordPressのadmin-ajax.php）
            data: {
                action: 'my_like_button', // サーバーサイドで実行するアクション
                security: my_ajax_params.my_ajax_nonce, // セキュリティチェック用のnonce
                post_id: postId // 投稿ID
            },
            success: function(response) {
                // AJAXリクエストが成功した場合の処理
                // レスポンスとして新しいいいね数が返される
                $('.like-count[data-post-id="' + postId + '"]').text(response); // いいね数を更新して表示
            }
        });
    });
});
