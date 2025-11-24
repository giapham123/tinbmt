(function() {
    tinymce.PluginManager.add('line_spacing', function(editor, url) {
        // Function to apply line spacing to all block-level elements
        function applyLineSpacing(lineSpacing) {
            // Remove existing line-spacing styles
            editor.getBody().querySelectorAll('[data-line-spacing]').forEach(function(el) {
                el.style.lineHeight = '';
                el.removeAttribute('data-line-spacing');
            });

            // Apply line spacing to all block-level elements
            editor.getBody().querySelectorAll('h1, h2, h3, h4, h5, h6, p, div').forEach(function(el) {
                el.style.lineHeight = lineSpacing;
                el.setAttribute('data-line-spacing', 'true');
            });
        }

        // Add button to the editor's toolbar
        editor.addButton('line_spacing', {
            text: 'Line Spacing',
            icon: 'icon dashicons-editor-alignleft',
            onclick: function() {
                // Open a dialog when the button is clicked
                editor.windowManager.open({
                    title: 'Line Spacing',
                    body: [
                        {
                            type: 'listbox',
                            name: 'lineSpacing',
                            label: 'Line Spacing',
                            values: [
                                { text: 'Normal', value: '' },
                                { text: '1.0', value: '1.0' },
                                { text: '1.5', value: '1.5' },
                                { text: '2.0', value: '2.0' },
                                { text: '2.5', value: '2.5' },
                                { text: '3.0', value: '3.0' }
                            ],
                            value: localStorage.getItem('lineSpacing') || '', // Load saved line spacing from localStorage
                            style: 'width: 200px;' // Adjust the width here
                        }
                    ],
                    onsubmit: function(e) {
                        var lineSpacing = e.data.lineSpacing;
                        
                        // Save the selected line spacing in localStorage
                        localStorage.setItem('lineSpacing', lineSpacing);

                        // Apply line spacing to all block-level elements
                        applyLineSpacing(lineSpacing);
                    }
                });
            }
        });

        // Apply saved line spacing when the editor is initialized
        editor.on('init', function() {
            var savedLineSpacing = localStorage.getItem('lineSpacing');
            if (savedLineSpacing) {
                applyLineSpacing(savedLineSpacing);
            }
        });
    });
})();
