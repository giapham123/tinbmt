jQuery(document).ready(function ($) {

    function resetResponse() {
        $('#custom_response').val("⏳ Loading... Please wait...");
    }

    function showError() {
        $('#custom_response').val("❌ Failed to load data. Please try again.");
    }

    // ✅ Handle Input Content -> Button: #handle_link_button
    $('#handle_link_button').on('click', function () {
        var link = $('#custom_link').val();
        resetResponse(); // ✅ Clear for loading

        $.ajax({
            url: ajax_object.ajaxurl,
            type: 'POST',
            data: {
                action: 'handle_get_post_link',
                link: link
            },
            success: function (response) {
                console.log(response,"adadas");
                if (response.trim() === "" || response.includes("Không thể")) {
                    showError(); // ✅ Show error if empty or blocked
                } else {
                    $('#custom_response').val(response);
                }
            },
            error: function () {
                showError();
            }
        });
    });

    // ✅ Handle Input Link -> Button: #handle_link
    $('#handle_link').on('click', function () {
        var link = $('#input_link').val();
        resetResponse(); // ✅ Clear for loading

        $.ajax({
            url: ajax_object.ajaxurl,
            type: 'POST',
            data: {
                action: 'handle_link_button',
                link: link
            },
            success: async function (response) {
                console.log(response);
                console.log(response.trim());
                    $('#custom_response').val(response.trim());

                    var parser = new DOMParser();
                    var doc = parser.parseFromString(response, 'text/html');

                    var description = doc.querySelector('meta[name="description"]')?.getAttribute('content') || '';
                    var keywords = doc.querySelector('meta[name="keywords"]')?.getAttribute('content') || '';

                    let resultMsg = "\n\n--- Update Results ---\n";

                    try {
                        if (setPostTag(keywords)) {
                            resultMsg += "✅ Tag added successfully\n";
                        } else {
                            resultMsg += "❌ Failed to add tag\n";
                        }
                    } catch (e) {
                        resultMsg += "❌ Tag error: " + e.message + "\n";
                    }

                    try {
                        if (setPostExcerpt(description)) {
                            resultMsg += "✅ Excerpt set successfully\n";
                        } else {
                            resultMsg += "❌ Failed to set excerpt\n";
                        }
                    } catch (e) {
                        resultMsg += "❌ Excerpt error: " + e.message + "\n";
                    }

                    // ✅ Append result messages to response
                    $('#custom_response').val(response + resultMsg);

                    console.log('Description:', description);
                    console.log('Keywords:', keywords);
                
            },
            error: function () {
                showError();
            }
        });
    });

    // ✅ Set WordPress Tag
    function setPostTag(tagText) {
        const tagInput = document.querySelector('#new-tag-post_tag');
        const tagButton = document.querySelector('.ajaxtag .tagadd');
        if (!tagInput || !tagButton) {
            console.error('Tag input/button not found!');
            return false;
        }

        const setter = Object.getOwnPropertyDescriptor(
            HTMLInputElement.prototype,
            'value'
        ).set;

        setter.call(tagInput, tagText);

        ['input', 'change', 'keyup', 'keydown', 'keypress'].forEach(evt => {
            tagInput.dispatchEvent(new Event(evt, { bubbles: true }));
        });

        // ✅ Click “Add” button
        setTimeout(() => {
            tagButton.click();
            console.log("✅ Tag added:", tagText);
        }, 300);

        return true;
    }

    // ✅ Set Excerpt
    function setPostExcerpt(text) {
        const textarea = document.querySelector('#excerpt');
        if (!textarea) {
            console.error('Excerpt textarea not found!');
            return false;
        }

        const setter = Object.getOwnPropertyDescriptor(
            HTMLTextAreaElement.prototype,
            'value'
        ).set;

        setter.call(textarea, text);

        ['input', 'change', 'keyup', 'keydown', 'keypress'].forEach(evt => {
            textarea.dispatchEvent(new Event(evt, { bubbles: true }));
        });

        console.log("✅ Excerpt set:", text);
        return true;
    }

});
