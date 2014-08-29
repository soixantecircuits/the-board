(function ( $ ) {
  "use strict";

  $(function () {
    $('.tb_image_delete_button').click(function(){
      $('.profile-photo-holder').hide();
      $('.button.tb_image_uploader_button.to-hide').show();
      $('#tb_photo_input').attr('value','');
    });

    $('.profile-photo-holder').hover(function(){
      $('.tb_image_delete_button').show();
      $('.upload-profile-photo').animate({
        'opacity': 1
      }, 500, function() {
        // Animation complete.
      });
    }, function(){
      $('.tb_image_delete_button').hide();
      $('.upload-profile-photo').animate({
        'opacity': 0
      }, 500, function() {
        // Animation complete.
      });
    });
    $(".chosen-select").chosen();
    // Wordpress native image uploader call
    var image_uploader;
    $('.tb_image_uploader_button').click(function (e){
      e.preventDefault();

      // Is it already instantiated ?
      if(image_uploader){
        image_uploader.open();
        return;
      }

      image_uploader = wp.media.frames.file_frame = wp.media({
        title: 'Choose Image',
        button: {
          text: 'Choose Image'
        },
        multiple: false
      });
      image_uploader.on('select', function(){
        $('.profile-photo-holder').css('display', 'block');
        $('.button.tb_image_uploader_button.to-hide').hide();
        var attachment = image_uploader.state().get('selection').first().toJSON();
        var defaultWidth = 150;
        var url ='',
          buttonWidth = 0;

        if (attachment.sizes.thumbnail == undefined){
          url = attachment.sizes.full.url;
        }
        else{
          buttonWidth = defaultWidth;
          url = attachment.sizes.thumbnail.url;
        }
        $('#tb_photo_input').attr('value',url);
        $('#profile_photo').attr('src', url);
        if (buttonWidth == 0)
          buttonWidth = $('#profile_photo').width();

        $('.upload-profile-photo').css({
          'width':buttonWidth,
          'margin-left': (defaultWidth -  $('#profile_photo').width()) / 2
        });
      });
      image_uploader.open();
    });

  });

}(jQuery));