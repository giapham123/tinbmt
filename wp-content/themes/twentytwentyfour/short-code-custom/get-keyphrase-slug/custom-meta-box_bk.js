jQuery(document).ready(function ($) {
    // Handle button click
    $('#my_custom_button').on('click', function () {
        var metaFieldValue = $('#my_meta_field').val(); // Get the value from the text field
        // Send AJAX request to the server
        $.ajax({
            url: metaBoxData.ajax_url,  // AJAX URL
            type: 'POST',
            data: {
                action: 'my_custom_button_action',  // Action to trigger the PHP handler
                meta_value: metaFieldValue          // Send the value from the text field
            },
            success: function (response) {
                // Display the server response (the value entered)
                $('#response_field').val(response);

              const lines = response
  .split('\n')
  .map(line => line.trim())
  .filter(line => line.length > 0);

// ✅ 1. Find the slug (any line that contains "-")
const slug = lines.find(line => line.includes('-')) || '';

// ✅ 2. Focus keyphrase = the 3rd non-empty line (index 2)
const focusKeyphrase = lines[2] || '';

// ✅ Set React states
setYoastSlug(slug);
setYoastFocusKeyphrase(focusKeyphrase);

console.log("Slug:", slug);
console.log("Focus Keyphrase:", focusKeyphrase);
console.log("Raw Response:", response);

            },
            error: function (xhr, status, error) {
                alert('Error: ' + error); // Show error if AJAX fails
            }
        });
    });

    function setYoastFocusKeyphrase(keyphrase) {
        // Get the input element
        const input = document.querySelector('#focus-keyword-input-metabox');
        if (!input) {
            console.error('Yoast Focus Keyphrase input not found!');
            return;
        }

        // Access React's internal value setter
        const nativeInputValueSetter = Object.getOwnPropertyDescriptor(
            window.HTMLInputElement.prototype,
            "value"
        ).set;

        nativeInputValueSetter.call(input, keyphrase);

        // Dispatch input event to notify React
        const event = new Event('input', { bubbles: true });
        input.dispatchEvent(event);
    }

    function setYoastSlug(slug) {
        const input = document.querySelector('#yoast-google-preview-slug-metabox');
        if (!input) {
            console.error('Yoast Slug input not found!');
            return;
        }

        // Use React's internal setter
        const nativeInputValueSetter = Object.getOwnPropertyDescriptor(
            window.HTMLInputElement.prototype,
            'value'
        ).set;

        nativeInputValueSetter.call(input, slug);

        // Dispatch input event so React updates
        const event = new Event('input', { bubbles: true });
        input.dispatchEvent(event);
    }
});
