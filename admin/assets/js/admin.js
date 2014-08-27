(function ( $ ) {
	"use strict";

	$(function () {
        $('.profile-photo-holder').hover(function(){
          $('.upload-profile-photo').fadeIn();
        }, function(){
          $('.upload-profile-photo').fadeOut().css('display', 'none');
        });
        $(".chosen-select").chosen();
		// Wordpress native image uploader call
        var image_uploader;
        $('#tb_image_uploader_button').click(function (e){
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
                var attachment = image_uploader.state().get('selection').first().toJSON();
                var url = attachment.sizes.thumbnail.url;
                $('#tb_photo_input').attr('value',url);
                $('#profile_photo').attr('src', url);
            });

            image_uploader.open();
        });

	});

}(jQuery));