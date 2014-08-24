(function ( $ ) {
	"use strict";

	$(function () {

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
                var url = attachment.url;
                $('#tb_photo_input').val(url);
            })

            image_uploader.open();
        });

	});

}(jQuery));