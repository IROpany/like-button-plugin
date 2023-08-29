
jQuery(document).ready(function($) {
    $('.like-button').on('click', function() {
        var postId = $(this).data('post-id');

        $.ajax({
            type: 'POST',
            url: my_ajax_params.ajaxurl,
            data: {
                action: 'my_like_button',
                security: my_ajax_params.my_ajax_nonce,
                post_id: postId
            },
            success: function(response) {
                $('.like-count[data-post-id="' + postId + '"]').text(response);
            }
        });
    });
});