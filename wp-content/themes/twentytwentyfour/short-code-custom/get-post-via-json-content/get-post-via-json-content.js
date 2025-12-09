jQuery(document).ready(function ($) {

    function resetResponse() {
        $('#custom_response').val("⏳ Loading... Please wait...");
    }

    function showError() {
        $('#custom_response').val("❌ Failed to load data. Please try again.");
    }

    // ✅ Handle Input Link -> Button: #handle_link
    $('#handle_button_parse_content').on('click', function () {
        var content = $('#input_content').val();
        resetResponse(); // ✅ Clear for loading

        $.ajax({
            url: ajax_object.ajaxurl,
            type: 'POST',
            data: {
                action: 'handle_link_button_parse_content',
                content: content
            },
            success: async function (response) {

                if (!response.success || !response.data) {
                    showError();
                    return;
                }

                let bodyHTML = response.data.body || "";

                $('#custom_response').val(bodyHTML);

                // ================================
                // 🔥 FILL BODY EDITOR
                // ================================
                fillBodyEditor(bodyHTML);

                    var description =response.data.meta;
                    var keywords = response.data.tags;

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
                 $('#custom_response_parse_json').val(
                    "Meta: " + meta + "\n" +
                    "Tags:: " + tags
                );
                    console.log('Description:', description);
                    console.log('Keywords:', keywords);
                
            },
            error: function () {
                showError();
            }
        });
    });


     // ==============================================================
    // 🔥 FUNCTION: FILL BODY CONTENT INTO .wp-editor-area + TinyMCE
    // ==============================================================
    function fillBodyEditor(html) {
        console.log("👉 Filling into editor:", html);

        // ===============================
        // 1) Classic Editor: TinyMCE
        // ===============================
        if (typeof tinyMCE !== 'undefined' && tinyMCE.get('content')) {
            tinyMCE.get('content').setContent(html);
            console.log("✔ Filled into TinyMCE editor");
        }

        // ===============================
        // 2) Textarea .wp-editor-area (classic & fallback)
        // ===============================
        let wpEditor = document.querySelector('.wp-editor-area');
        if (wpEditor) {
            wpEditor.value = html;

            ['input', 'change', 'keyup'].forEach(evt =>
                wpEditor.dispatchEvent(new Event(evt, { bubbles: true }))
            );

            console.log("✔ Filled into .wp-editor-area");
        }

        // ===============================
        // 3) Gutenberg Block Editor
        // ===============================
        if (wp?.data && wp.data.dispatch('core/editor')) {
            wp.data.dispatch('core/editor').editPost({ content: html });
            console.log("✔ Filled Gutenberg editor");
        }

        console.log("🎉 BODY UPDATED!");
    }

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
