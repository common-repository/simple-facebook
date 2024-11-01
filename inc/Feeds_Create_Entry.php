<?php
if (isset($_GET['status']) && $_GET['status'] == 'save') {
    /**
     * storing feed in database
     */
    global $wpdb;

    $wpdb->show_errors();

    $wpdb->insert(
            $wpdb->prefix . FB_FEED_TABLE, array(
        'feed_name' => filter_input(INPUT_POST, 'feed_name'),
        'published' => filter_has_var(INPUT_POST, 'published') ? 'y' : 'n',
        'feed_type' => filter_input(INPUT_POST, 'feed_type'),
        'feed_value' => filter_input(INPUT_POST, 'feed_display_value'),
        'number_of_photos' => filter_input(INPUT_POST, 'feed_amount_photos'),
        'number_of_columns' => filter_input(INPUT_POST, 'feed_amount_columns'),
        'show_likes' => filter_has_var(INPUT_POST, 'show_likes') ? 'y' : 'n',
        'show_description' => filter_has_var(INPUT_POST, 'show_description') ? 'y' : 'n',
        'show_comments' => filter_has_var(INPUT_POST, 'show_comments') ? 'y' : 'n',
        'show_usernames' => filter_has_var(INPUT_POST, 'show_usernames') ? 'y' : 'n',
        'show_image_counts' => filter_has_var(INPUT_POST, 'show_image_counts') ? 'y' : 'n',
        'show_images' => filter_has_var(INPUT_POST, 'show_images') ? 'y' : 'n',
        'show_shares' => filter_has_var(INPUT_POST, 'show_images') ? 'y' : 'n',
        'show_view_link' => filter_has_var(INPUT_POST, 'show_view_link') ? 'y' : 'n',
        'show_share_link' => filter_has_var(INPUT_POST, 'show_share_link') ? 'y' : 'n',
        'template' => filter_input(INPUT_POST, 'feed_template'),
        'text_length' => filter_input(INPUT_POST, 'text_length', FILTER_VALIDATE_INT)
            ), array(
        '%s', // feed_name
        '%s', //published
        '%s', //feed_type
        '%s', //feed display value
        '%s', //number_of_photos
        '%s', //feed_amount_columns
        '%s', //show_likes
        '%s', //show_description
        '%s', //show_comments
        '%s', //show_usernames
        '%s', //show_image_counts
        '%s', //show_image
        '%s', //show_shares
        '%s', //show_view_link
        '%s', //show_share_link
        '%s', //template
        '%d', //text_length
            )
    );
}
?>

<form method="post" action="<?php echo sprintf('?page=%s&action=%s&status=%s', $_REQUEST['page'], 'create', 'save'); ?>">
    <table class="form-table">
        <tr valign="top">
            <th scope="row"><?php _e("Feed Name", "simple-facebook");?></th>
            <td><input type="text" name="feed_name" value="My Facebook Feed" /></td>
        </tr>

        <tr valign="top">
            <th scope="row"><?php _e("Feed Type", "simple-facebook");?></th>
            <td>
                <select name="feed_type">
                    <option value="username"><?php _e("Username", "simple-facebook");?></option>
                    <option value="tag"><?php _e("Group", "simple-facebook");?></option>
                    <option value="combined"><?php _e("Page", "simple-facebook");?></option>
                </select> 
            </td>
        </tr>

        <tr valign="top">
            <th scope="row"><?php _e("Insert the ID of the Username, Group or Page", "simple-facebook");?></th>
            <td><input type="text" name="feed_display_value" value="" /></td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e("Number of Photos/Pages", "simple-facebook");?></th>
            <td><input type="text" name="feed_amount_photos" value="" /></td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e("Number of Columns", "simple-facebook");?></th>
            <td>
                <select name="feed_amount_columns">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select> 
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e("Template", "simple-facebook");?></th>
            <td>
                <select name="feed_template">
                    <?php
                    $facebook_template_generator = new Facebook_Template_Generator();
                    $templates = $facebook_template_generator->show_templates("", false);
                    foreach ($templates as $template) {
                        ?>
                        <option value="<?php echo $template; ?>"><?php echo $template; ?></option>
                        <?php
                    }
                    ?>
                </select> 
            </td>
        </tr>


        <tr valign="top">
            <th scope="row"><?php _e("Show Likes", "simple-facebook");?></th>
            <td><input type="checkbox" name="show_likes" /></td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e("Show Description", "simple-facebook");?></th>
            <td><input type="checkbox" name="show_description" /></td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e("Show Comments", "simple-facebook");?></th>
            <td><input type="checkbox" name="show_comments" /></td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e("Show Usernames", "simple-facebook");?></th>
            <td><input type="checkbox" name="show_usernames" /></td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e("Show Shares", "simple-facebook");?></th>
            <td><input type="checkbox" name="show_shares" /></td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e("Show Number of Images", "simple-facebook");?></th>
            <td><input type="checkbox" name="show_image_counts" /></td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e("Show Images", "simple-facebook");?></th>
            <td><input type="checkbox" name="show_images" /></td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e("Show View Link", "simple-facebook");?></th>
            <td><input type="checkbox" name="show_view_link" /></td>
        </tr>  
        <tr valign="top">
            <th scope="row"><?php _e("Show Share Link", "simple-facebook");?></th>
            <td><input type="checkbox" name="show_share_link" /></td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e("Text Length (-1 for unlimited)", "simple-facebook");?></th>
            <td><input type="text" name="text_length" value="-1" /></td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e("Publish", "simple-facebook");?></th>
            <td><input type="checkbox" name="published" checked="checked"/></td>
        </tr>
    </table>

    <?php submit_button(); ?>

</form>