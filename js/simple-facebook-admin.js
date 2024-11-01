jQuery(document).ready(function ($) {

    tinymce.PluginManager.add('simple_facebook_button', function (editor, url) {
        editor.addButton('simple_facebook_button', {
            title: 'Add Facebook Snippet',
            icon: 'icon dashicons-facebook',
            onclick: function () {
                editor.windowManager.open({
                    title: 'Facebook',
                    body: [
                        {type: 'listbox', name: 'facebook_feed', label: 'Facebook Feed', 'values': shortcode_facebook_feeds}
                    ],
                    onsubmit: function (e) {
                        editor.insertContent('[simple-facebook id=' + e.data.facebook_feed + '/]');
                    }
                });
            }
        });
    });

});
  