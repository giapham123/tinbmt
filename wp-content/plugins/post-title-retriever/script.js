jQuery(document).ready(function ($) {
    $('#get-title-btn').on('click', function (event) {
        event.preventDefault(); // Prevent default button action
        $('#loading-overlay, #loading-popup').fadeIn(); // Show both overlay and popup

        var postId = $('#post_ID').val(); // Get the current post ID

        $.ajax({
            type: 'POST',
            url: rpt_ajax_obj.ajax_url,
            data: {
                action: 'rpt_get_title',
                post_id: postId
            },
            success: function (response) {
                $('#api-response').val(response.content);
                $('#image-url').val(response.imageUrl);
                $('#link-url').val(response.linkUrl);
                $('#loading-overlay, #loading-popup').fadeOut(); // Hide both overlay and popup

            },
            error: function () {
                $('#loading-overlay, #loading-popup').fadeOut(); // Hide both overlay and popup
                $('#api-response').val('Error calling the API.');
            }
        });
    });

    // New button click event for sending data to the external API
    $('#send-api-btn').on('click', function (event) {
        event.preventDefault(); // Prevent default button action
        //Show popup
        $('#loading-overlay, #loading-popup').fadeIn(); // Show both overlay and popup
        // $('#loading-popup').fadeIn();
        $('#post-title').empty(); // Clear previous content
        //End Show popup
        var message = $('#api-response').val(); // Get the content of the textarea
        var imageUrl = $('#image-url').val(); // Get the content of the textarea
        var linkUrl = $('#link-url').val(); // Get the content of the textarea

        $.ajax({
            type: 'POST',
            url: rpt_ajax_obj.ajax_url,
            data: {
                action: 'rpt_send_to_api',
                message: message,
                imageUrl: imageUrl,
                linkUrl: linkUrl
            },
            success: function (response) {
                // alert(response); // Show success/error message
                // $('#loading-popup').fadeOut();
                if (response.includes('✅') || response.includes('id')) {
                    clearFieldsAndLock('#send-api-btn');
                }
                $('#loading-overlay, #loading-popup').fadeOut(); // Hide both overlay and popup
                $('#title-popup h3').text(response); // Change the popup title dynamically

                $('#title-popup').fadeIn();
            },
            error: function () {
                $('#loading-overlay, #loading-popup').fadeOut(); // Hide both overlay and popup
                alert('Error sending data to the API.');
            }
        });
    });
    $('#send-api-btn-csl').on('click', function (event) {
        event.preventDefault(); // Prevent default button action
        //Show popup
        $('#loading-overlay, #loading-popup').fadeIn(); // Show both overlay and popup
        // $('#loading-popup').fadeIn();
        $('#post-title').empty(); // Clear previous content
        //End Show popup
        var message = $('#api-response').val(); // Get the content of the textarea
        var imageUrl = $('#image-url').val(); // Get the content of the textarea
        var linkUrl = $('#link-url').val(); // Get the content of the textarea

        $.ajax({
            type: 'POST',
            url: rpt_ajax_obj.ajax_url,
            data: {
                action: 'rpt_csl_api',
                message: message,
                imageUrl: imageUrl,
                linkUrl: linkUrl
            },
            success: function (response) {
                if (response.includes('✅') || response.includes('id')) {
                    clearFieldsAndLock('#send-api-btn-csl');
                }
                // alert(response); // Show success/error message
                // $('#loading-popup').fadeOut();
                $('#loading-overlay, #loading-popup').fadeOut(); // Hide both overlay and popup
                $('#title-popup h3').text(response); // Change the popup title dynamically

                $('#title-popup').fadeIn();
            },
            error: function () {
                $('#loading-overlay, #loading-popup').fadeOut(); // Hide both overlay and popup
                alert('Error sending data to the API.');
            }
        });
    });
    function clearFieldsAndLock(buttonId) {
        // Clear the input fields
        $('#api-response').val('');
        $('#image-url').val('');
        $('#link-url').val('');

        // Disable the button to prevent repeated posts
        $(buttonId)
            .prop('disabled', true)
            .text('✔ Posted')
            .css({
                backgroundColor: '#ccc',
                cursor: 'not-allowed',
            });
    }
    $(document).on('click', '#close-popup', function (event) {
        event.preventDefault(); // Prevent the default action

        // Show loading popup
        // $('#loading-popup').fadeIn();

        // Delay closing the title popup
        setTimeout(function () {
            $('#loading-overlay, #loading-popup').fadeOut(); // Hide both overlay and popup
            // $('#get-title-btn').prop('disabled', false); // Re-enable all buttons, or you can specify specific buttons
            $('#title-popup').fadeOut();
            // $('#loading-popup').fadeOut(); // Optionally hide the loading popup
        }, 10); // Adjust the delay as needed
    });
});
