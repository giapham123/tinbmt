jQuery(document).ready(function ($) {

    function resetResponse() {
        $('#response_field').val("⏳ Loading... Please wait...");
    }

    function showError() {
        $('#response_field').val("❌ Failed to load data. Please try again.");
    }

    // ✅ Handle button click
    $('#my_custom_button').on('click', function () {
        var metaFieldValue = $('#my_meta_field').val();

        resetResponse(); // ✅ Clear + show loading

        $.ajax({
            url: metaBoxData.ajax_url,
            type: 'POST',
            data: {
                action: 'my_custom_button_action',
                meta_value: metaFieldValue
            },
            success: function (response) {

                if (!response || response.trim().length === 0) {
                    showError();
                    return;
                }

                // ✅ Show initial response
                $('#response_field').val(response);

                const lines = response
                    .split('\n')
                    .map(line => line.trim())
                    .filter(line => line.length > 0);

                const slug = lines.find(line => line.includes('-')) || '';
                const focusKeyphrase = lines[2] || '';
                const title = lines[0] || '';

                // ✅ Collect update results
                let resultMsg = "\n\n--- Update Results ---\n";

                if (setYoastSlug(slug)) {
                    resultMsg += "✅ Slug set successfully\n";
                } else {
                    resultMsg += "❌ Failed to set slug\n";
                }

                if (setYoastFocusKeyphrase(focusKeyphrase)) {
                    resultMsg += "✅ Focus keyphrase set successfully\n";
                } else {
                    resultMsg += "❌ Failed to set focus keyphrase\n";
                }

                if (setPostTitle(title)) {
                    resultMsg += "✅ Title set successfully\n";
                } else {
                    resultMsg += "❌ Failed to set title\n";
                }

                // ✅ Append results to response field
                $('#response_field').val(response + resultMsg);

                console.log("Slug:", slug);
                console.log("Focus Keyphrase:", focusKeyphrase);
                console.log("Raw Response:", response);
            },

            error: function () {
                showError();
            }
        });
    });

    // ✅ Yoast: set focus keyphrase
    function setYoastFocusKeyphrase(keyphrase) {
        const input = document.querySelector('#focus-keyword-input-metabox');
        if (!input) {
            console.error('Yoast Focus Keyphrase input not found!');
            return false;
        }

        const setter = Object.getOwnPropertyDescriptor(
            HTMLInputElement.prototype,
            "value"
        ).set;

        setter.call(input, keyphrase);

        ['input','change','keyup','keydown','keypress'].forEach(evt => {
            input.dispatchEvent(new Event(evt, { bubbles: true }));
        });

        console.log("✅ Focus keyphrase set:", keyphrase);
        return true;
    }

    // ✅ Yoast: set slug
    function setYoastSlug(slug) {
        const input = document.querySelector('#yoast-google-preview-slug-metabox');
        if (!input) {
            console.error('Yoast Slug input not found!');
            return false;
        }

        const setter = Object.getOwnPropertyDescriptor(
            HTMLInputElement.prototype,
            'value'
        ).set;

        setter.call(input, slug);
        input.dispatchEvent(new Event('input', { bubbles: true }));

        console.log("✅ Slug set:", slug);
        return true;
    }

    // ✅ WordPress: set title
    function setPostTitle(title) {
        const input = document.querySelector('#title');
        if (!input) {
            console.error('Title input not found!');
            return false;
        }

        const nativeSetter = Object.getOwnPropertyDescriptor(
            window.HTMLInputElement.prototype,
            'value'
        ).set;

        nativeSetter.call(input, title);

        ['input', 'change', 'keyup', 'keydown', 'keypress'].forEach(evt => {
            input.dispatchEvent(new Event(evt, { bubbles: true }));
        });

        console.log("✅ Title set:", title);
        return true;
    }

});
