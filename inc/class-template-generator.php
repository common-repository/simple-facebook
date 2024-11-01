<?php

class Facebook_Template_Generator {

    protected $templates;
    protected $feed;
    protected $fb;

    public function __construct() {
        $this->templates = 'framework/templates/facebook/';
        $this->fb = new Facebook\Facebook([
            'app_id' => FB_APP_ID,
            'app_secret' => FB_APP_SECRET,
            'default_graph_version' => FB_GRAPH_VERSION,
        ]);
    }

    public function show_templates($start_slug = "", $echo = true) {
        $items = array();
        foreach (scandir(SSF_PATH . "/templates/") as $item) {
            if (preg_match("/.php/i", $item)) {
                if ($echo)
                    echo $item;
                $items[] = pathinfo($item, PATHINFO_FILENAME);
            }
        }
        return $items;
    }

    public function set($key, $value) {
        $this->values[$key] = $value;
    }

    public function generate_output() {
        if ($this->feed != '') {
            try {
                ob_start();
                $request = $this->fb->get('/' . $this->feed->feed_value . '/feed?fields=id,caption,created_time,description,from,icon,link,message,message_tags,name,object_id,picture,shares,source,story,story_tags,to,type,status_type,admin_creator,child_attachments,application,with_tags,likes.limit(1).summary(true),sharedposts,comments.limit(1).summary(true)&limit=' . $this->feed->number_of_photos, get_option('fb_access_token', 'CAACEdEose0cBAKi76HXEHoiCMvehQFBhOq5WOkuRzYEFSAmwiKiBZB8ZCvst13EsZCE1jjyiToP9lZAENRNmOf551ymPKEbt79FPC0FfxAZCPG4OJBg8Aooao6zSwfxgIuqAkUZChOng5gf4ZCrJqWW9xW57dcPZCVBbBzrI7nNdRpu6gE1IJpcxJr6UybeiZCml8lFh0ZCwKBSFG5ZAyWcIcow'));
                include(SSF_PATH . "/templates/" . $this->feed->template . ".php");
                $var = ob_get_contents();
                ob_get_clean();
                return preg_replace('/^\s+|\n|\r|\s+$/m', '', $var);
            } catch (Exception $ex) {
                echo $ex->getMessage();
            }
        }
    }

    function getTemplates() {
        return $this->templates;
    }

    function getFeed() {
        return $this->feed;
    }

    function setTemplates($templates) {
        $this->templates = $templates;
    }

    function setFeed($feed) {
        $this->feed = $feed;
    }

}
