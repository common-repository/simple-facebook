<h2>Facebook Settings</h2>



<?php
/**
 * delete credentials
 */
if (isset($_POST['action']) && filter_input(INPUT_POST, 'action') == 'delete') {
    delete_option('fb_access_token');
    delete_option('fb_name');
    delete_option('fb_id');
}


$fb = new Facebook\Facebook([
    'app_id' => FB_APP_ID,
    'app_secret' => FB_APP_SECRET,
    'default_graph_version' => FB_GRAPH_VERSION,
        ]);

if (filter_has_var(INPUT_GET, 'code')) {
    update_option('fb_access_token', filter_input(INPUT_GET, 'code'));
}

$access_token = get_option('fb_access_token', '');
if ($access_token == '') {
    ?>
    <a href="http://api.seiboldsoft.de/facebook/?source=<?php echo "http://" . filter_input(INPUT_SERVER, 'HTTP_HOST') . filter_input(INPUT_SERVER, 'REQUEST_URI'); ?>">Request a Token and paste it below</a>
    <?php
} else {


    try {
        // Returns a `Facebook\FacebookResponse` object
        $response = $fb->get('/me?fields=birthday,id,name,link,location,bio,email', $access_token);
    } catch (Facebook\Exceptions\FacebookResponseException $e) {
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }

    $user = $response->getGraphUser();


    try {
        $response = $fb->get('/me/albums', $access_token);
        //   var_dump($response);
    } catch (Exception $ex) {
        
    }
}
?>


<form method="post" action="">
    <table class="form-table">
        <tr valign="top">
            <th scope="row"><?php _e("Facebook Access Token", "simple-facebook");?></th>
            <td><textarea name="fb_access_token"><?php echo get_option("fb_access_token", ''); ?></textarea></td>
        </tr>
        <?php
        if (isset($user) && $user != "") {
            ?>
            <tr valign="top">
                <th scope="row"><?php _e("Name", "simple-facebook");?></th>
                <td><input type="text" name="fb_user_name" value="<?php echo $user['name']; ?>" /></td>
            </tr>
        <?php } ?>
    </table>
    <?php submit_button(); ?>

</form>

<form method="post" action="<?php echo sprintf('?page=%s', $_REQUEST['page']); ?>">
    <input type="hidden" name="action" value="delete" />
    <input type="submit" value="Delete Credentials" />
</form>