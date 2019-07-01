/* 
 * JS file to make the changes at widget backend.
 */

jQuery( document ).ready( function($){

//  jQuery function to upload media to existing post
        function media_upload(button_selector) {
            var _custom_media = true,
                _orig_send_attachment = wp.media.editor.send.attachment;
            $('body').on('click', button_selector, function () {
              var button_id = $(this).attr('id');
              wp.media.editor.send.attachment = function (props, attachment) {
                if (_custom_media) {
                  $('.' + button_id + '_img').attr('src', attachment.url);
                  $('.' + button_id + '_url').val(attachment.url);
                } else {
                  return _orig_send_attachment.apply($('#' + button_id), [props, attachment]);
                }
              }
              wp.media.editor.open($('#' + button_id));
              return false;
            });
        }

//  Call to media function
        media_upload('.js_custom_upload_media');
    
});


