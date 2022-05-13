<?php 

    //php part put this code in function.php

    function blog_scripts() {
        // Register the script
        wp_register_script( 'custom-script', get_stylesheet_directory_uri(). '/js/custom.js', array('jquery'), false, true );
    
        // Localize the script with new data
        $script_data_array = array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'security' => wp_create_nonce( 'file_upload' ),
        );
        wp_localize_script( 'custom-script', 'blog', $script_data_array );
    
        // Enqueued script with localized data.
        wp_enqueue_script( 'custom-script' );
    }
    add_action('wp_enqueue_scripts', 'blog_scripts');
    add_action('wp_ajax_file_upload', 'file_upload_callback');
    add_action('wp_ajax_nopriv_file_upload', 'file_upload_callback');

    function file_upload_callback() {
        check_ajax_referer('file_upload', 'security');
        $arr_img_ext = array('image/png', 'image/jpeg', 'image/jpg', 'image/gif');
        if (in_array($_FILES['file']['type'], $arr_img_ext)) {
            $upload = wp_upload_bits($_FILES["file"]["name"], null, file_get_contents($_FILES["file"]["tmp_name"]));
            //$upload['url'] will gives you uploaded file path
        }
        wp_die();
    }
?>


<!-- html part put this code from where you want to upload files to server using ajax--->
<form class="fileUpload" id="formSubmit_ap" enctype="multipart/form-data">
    <div class="form-group">
        <label>Choose File:</label>
        <input type="file" id="file" accept="image/*" />

        <input type="submit">
    </div>
</form>


<!-- js part | put this code in custom .js file which is enqueue using above php script --> 
<script>
    jQuery(function($) {
        $('#formSubmit_ap').on('submit', function(e) {
            e.preventDefault();
            $this = $("#file");
            var file_data = $("#file").prop('files')[0];
            var form_data = new FormData();
            form_data.append('file', file_data);
            form_data.append('action', 'file_upload');
            form_data.append('security', blog.security);
    
            $.ajax({
                url: blog.ajaxurl,
                type: 'POST',
                contentType: false,
                processData: false,
                data: form_data,
                success: function (response) {
                    $this.val('');
                    alert('File uploaded successfully.');
                }
            });
        });
    });
</script>