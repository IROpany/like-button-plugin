jQuery(document).ready(function($) {
  $('.like-button').on('click', function() {
    var postId = $(this).data('post-id');

    // 既にいいね済みなら処理を止める（連打・重複防止）
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
        $('.like-count[data-post-id="' + postId + '"]').text(response);
        localStorage.setItem('liked_' + postId, true); // いいね済みを記録
      }
    });
  });
});
