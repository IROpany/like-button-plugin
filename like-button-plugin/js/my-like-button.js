jQuery(document).ready(function($) {
  $('.like-button').on('click', function() {
    var postId = $(this).data('post-id');

    // すでにいいね済みかチェック（ローカルストレージで）
    if (localStorage.getItem('liked_' + postId)) {
      alert('この投稿にはすでに「いいね」しています。');
      return;
    }

    $.ajax({
      type: 'POST',
      url: my_ajax_params.ajaxurl,
      data: {
        action: 'my_like_button',
        post_id: postId,
        security: my_ajax_params.my_ajax_nonce
      },
      success: function(response) {
        if (response.success) {
          // 正しく「いいね」数を表示
          $('.like-count[data-post-id="' + postId + '"]').text(response.data);
          localStorage.setItem('liked_' + postId, true);
        } else {
          if (response.data === 'already_liked') {
            alert('この投稿にはすでに「いいね」しています。');
          } else {
            alert('エラーが発生しました。');
          }
        }
      },
      error: function(xhr, status, error) {
        alert('通信エラー：' + error);
      }
    });
  });
});
