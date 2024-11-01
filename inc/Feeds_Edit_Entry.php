<?php
global $wpdb;

if (isset($_GET['status']) && $_GET['status'] == 'save' && isset($_GET['action']) && $_GET['action'] == 'edit') {
    /**
     * storing feed in database
     */
    $wpdb->show_errors();

    if (isset($_GET['feed']) && filter_has_var(INPUT_POST, 'feed_name')) {
        $wpdb->update(
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
            'show_shares' => filter_has_var(INPUT_POST, 'show_shares') ? 'y' : 'n',
            'show_view_link' => filter_has_var(INPUT_POST, 'show_view_link') ? 'y' : 'n',
            'show_share_link' => filter_has_var(INPUT_POST, 'show_share_link') ? 'y' : 'n',
            'template' => filter_input(INPUT_POST, 'feed_template'),
            'text_length' => filter_input(INPUT_POST, 'text_length', FILTER_VALIDATE_INT)
                ), array('id' => $_GET['feed']), array(
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
            '%s', //show_shares
            '%s', //show_view_link
            '%s', //show_share_link
            '%s', //template
            '%d', //text_length
                )
        );
        ?>
        <meta http-equiv="refresh" content="0; url=?page=simple-facebook-configure">

        <?php
    }
}

$feeds = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . FB_FEED_TABLE . " WHERE id = '" . $_GET['feed'] . "'");
$first_feed = $feeds[0];
?>

<form method="post" action="<?php echo sprintf('?page=%s&action=%s&status=%s&feed=%s', $_REQUEST['page'], 'edit', 'save', $_GET['feed']); ?>">
    <table class="form-table">
        <tr valign="top">
            <th scope="row"><?php _e("Feed Name", "simple-facebook"); ?></th>
            <td><input type="text" name="feed_name" value="<?php echo $first_feed->feed_name; ?>" /></td>
        </tr>

        <tr valign="top">
            <th scope="row"><?php _e("Feed Type", "simple-facebook"); ?></th>
            <td>
                <select name="feed_type">
                    <option <?php echo ($first_feed->feed_type == 'username') ? "selected" : ""; ?> value="username">Username</option>
                    <option <?php echo ($first_feed->feed_type == 'tag') ? "selected" : ""; ?> value="tag">Group</option>
                    <option <?php echo ($first_feed->feed_type == 'combined') ? "selected" : ""; ?> value="combined">Page</option>
                </select> 
            </td>
        </tr>

        <tr valign="top">
            <th scope="row"><?php _e("Insert the ID of the Username, Group or Page", "simple-facebook"); ?></th>
            <td><input type="text" name="feed_display_value" value="<?php echo $first_feed->feed_value; ?>" /></td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e("Numer of Photos/Entrys", "simple-facebook"); ?></th>
            <td><input type="text" name="feed_amount_photos" value="<?php echo $first_feed->number_of_photos; ?>" /></td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e("Numer of Columns", "simple-facebook"); ?></th>
            <td>
                <select name="feed_amount_columns">
                    <?php
                    for ($i = 1; $i <= 6; $i++) {
                        ?>
                        <option <?php echo ($first_feed->number_of_columns == $i) ? "selected" : ""; ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php
                    }
                    ?>
                </select> 
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e("Template", "simple-facebook"); ?></th>
            <td>
                <select name="feed_template">
                    <?php
                    $facebook_template_generator = new Facebook_Template_Generator();
                    $templates = $facebook_template_generator->show_templates("", false);
                    foreach ($templates as $template) {
                        ?>
                        <option <?php echo ($first_feed->template == $template) ? "selected" : ""; ?> value="<?php echo $template; ?>"><?php echo $template; ?></option>
                        <?php
                    }
                    ?>
                </select> 
            </td>
        </tr>


        <tr valign="top">
            <th scope="row"><?php _e("Show Likes", "simple-facebook"); ?></th>
            <td><input <?php echo ($first_feed->show_likes == 'y') ? "checked" : ""; ?> type="checkbox" name="show_likes" /></td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e("Show Description", "simple-facebook"); ?></th>
            <td><input <?php echo ($first_feed->show_description == 'y') ? "checked" : ""; ?> type="checkbox" name="show_description" /></td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e("Show Comments", "simple-facebook"); ?></th>
            <td><input <?php echo ($first_feed->show_comments == 'y') ? "checked" : ""; ?> type="checkbox" name="show_comments" /></td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e("Show Usernames", "simple-facebook"); ?></th>
            <td><input <?php echo ($first_feed->show_usernames == 'y') ? "checked" : ""; ?> type="checkbox" name="show_usernames" /></td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e("Show Shares", "simple-facebook"); ?></th>
            <td><input <?php echo ($first_feed->show_shares == 'y') ? "checked" : ""; ?> type="checkbox" name="show_shares" /></td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e("Show Number of Images", "simple-facebook"); ?></th>
            <td><input <?php echo ($first_feed->show_image_counts == 'y') ? "checked" : ""; ?> type="checkbox" name="show_image_counts" /></td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e("Show Images", "simple-facebook"); ?></th>
            <td><input <?php echo ($first_feed->show_images == 'y') ? "checked" : ""; ?> type="checkbox" name="show_images" /></td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e("Show View Link", "simple-facebook"); ?></th>
            <td><input <?php echo ($first_feed->show_view_link == 'y') ? "checked" : ""; ?>  type="checkbox" name="show_view_link" /></td>
        </tr>  
        <tr valign="top">
            <th scope="row"><?php _e("Show Share Link", "simple-facebook"); ?></th>
            <td><input <?php echo ($first_feed->show_share_link == 'y') ? "checked" : ""; ?>  type="checkbox" name="show_share_link" /></td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e("Text Length (-1 for unlimited)", "simple-facebook"); ?></th>
            <td><input type="text" name="text_length" value="<?php echo (isset($first_feed->text_length)) ? $first_feed->text_length : -1; ?>" /></td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php _e("Publish", "simple-facebook"); ?></th>
            <td><input <?php echo ($first_feed->published == "y") ? "checked" : ""; ?> type="checkbox" name="published"/></td>
        </tr>
    </table>

    <?php submit_button(); ?>

</form>