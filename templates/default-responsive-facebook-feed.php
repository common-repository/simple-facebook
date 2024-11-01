<div class="qa-message-list" id="wallmessages">
    <?php
    $GraphEdges = $request->getGraphEdge();

    $posts = 0;
    foreach ($GraphEdges as $graphNode) {
        //type == link, status, photo, video, offer
        //status_type == mobile_status_update, created_note, added_photos, added_video, shared_story, created_group, created_event, wall_post, app_created_story, published_story, tagged_in_photo, approved_friend
        // https://developers.facebook.com/docs/graph-api/reference/v2.5/post
        ?>

        <?php
        switch ($graphNode['type']) {
            case 'link':
                // add your own templates
                //get_template_part("templates/facebook/box", "responsive-link-photo");
                break;
            case 'status':
                break;
            case 'photo':
                break;
            case 'video':
                break;
            case 'offer':
                break;
            case 'event':
                break;
            case 'page':
                break;
            default:
        }
        ?>

        <?php
        $created_time = $graphNode['created_time'];
        $converted_date_time = $created_time->format('Y-m-d H:i:s');
        $ago_value = Facebook_Utility::time_elapsed_string($converted_date_time);
        $url_pieces = explode("_", $graphNode->getField("id"));
        $message = ($graphNode->getField('message') != "") ? $graphNode->getField('message') : $graphNode->getField('description');

        switch ($graphNode['type']) {
            case 'event':
                $post_type = "events";
                break;
            default:
                $post_type = $this->feed->feed_value . "/posts";
        }
        ?>
        <div class="message-item">
            <div class="message-inner">

                <?php if (isset($this->feed->show_usernames) && $this->feed->show_usernames == 'y') { ?>
                    <div class="message-head clearfix">
                        <div class="avatar pull-left"><img src="http://graph.facebook.com/<?php echo $graphNode['from']['id']; ?>/picture" class="pull-left" alt="<?php echo $graphNode['from']['id']; ?>" /></div>

                        <div class="user-detail">
                            <span class="user-title">
                                <a href="https://www.facebook.com/<?php echo $graphNode['from']['id']; ?>" target="_blank"><?php echo $graphNode['from']['name']; ?></a></span>

                            <div class="post-summary">
                                <?php if (isset($this->feed->show_likes) && $this->feed->show_likes == 'y' && isset($graphNode["likes"])) { ?><i class="fa fa-thumbs-up"></i> <?php echo $graphNode->getProperty("likes")->getTotalCount(); ?> <?php } ?> 
                                <?php if (isset($this->feed->show_comments) && $this->feed->show_comments == 'y' && isset($graphNode["comments"])) { ?> <i class="fa fa-comment"></i> <?php echo $graphNode->getProperty("comments")->getTotalCount(); ?> <?php } ?>
                                <?php if (isset($this->feed->show_shares) && $this->feed->show_shares == 'y' && isset($graphNode["shares"])) { ?> <i class="fa fa-share"> <?php echo $graphNode["shares"]["count"]; ?></i> <?php } ?>
                            </div>
                            <div class="post-meta">
                                <div class="asker-meta">
                                    <span class="qa-message-what"></span>
                                    <span class="qa-message-when"><span class="qa-message-when-data"><?php echo $ago_value; ?></span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <div class="qa-message-content">
                    <?php if ($this->feed->show_images == 'y' && isset($graphNode['picture'])) { ?>
                        <div class='post-picture'>
                            <img class='img-responsive' src='<?php echo $graphNode['picture']; ?>'  alt="Facebook Picture"/>
                        </div>
                    <?php } ?>
                    <div class='post-info'>
                        <?php if (isset($graphNode['name'])) { ?>
                            <div class='post-info-name'><a href='<?php echo isset($graphNode['link']) ? $graphNode['link'] : "https://www.facebook.com/" . $graphNode['id']; ?>' target='_blank' class='post-link'><?php echo $graphNode['name']; ?></a></div> 
                        <?php } ?>

                        <?php if (isset($this->feed->show_description) && $this->feed->show_description == 'y' && $message != "") { ?>
                            <div class='post-info-description'><?php
                                if (isset($graphNode['message_tags']) && $graphNode['type'] != 'link') {
                                    echo Facebook_Utility::prepare_content_tags_by_offsets($message, $graphNode->getField('message_tags')->asArray());
                                } else {
                                    echo Facebook_Utility::prepare_content_tags_by_offsets($message, null);
                                }
                                ?>
                            </div>            
                        <?php } ?>
                    </div>
                    <?php ?>
                </div>
                <?php
                if (
                        (isset($this->feed->show_view_link) && $this->feed->show_view_link == 'y') ||
                        (isset($this->feed->show_share_link) && $this->feed->show_share_link == 'y')
                ) {
                    ?>
                    <div class="qa-message-footer">
                        <?php if ($this->feed->show_view_link == 'y') { ?>
                            <a href="http://facebook.com/<?php echo $post_type . "/" . $url_pieces[1] ?>" target="_blank">View on Facebook</a> 
                        <?php } ?>
                        - 
                        <?php if ($this->feed->show_share_link == 'y') { ?>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=https://www.facebook.com/<?php echo $url_pieces[0]; ?>/posts/<?php echo $url_pieces[1]; ?>" target="_blank">Share</a>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>

        <?php
    }
    ?>
</div>

<?php
