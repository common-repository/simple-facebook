<?php

class Facebook_Utility {

    public function __construct() {
        
    }

    public static function time_elapsed_string($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);
        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full)
            $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago ' : 'just now';
    }

    public static function prepare_content_tags_by_offsets($text, $tags) {
        //echo $text . " - " . print_r($tags, true);
        // $text = strip_tags($text);


        if ($tags != null) {

            $doc = new DOMDocument;
            $doc->appendChild($doc->createTextNode($text));
            if (is_array($tags)) {
                foreach ($tags as $tag) {
                    $start = 0;
                    foreach ($doc->childNodes as $child) {
                        if ($tag['offset'] < $start + strlen($child->nodeValue)) {
                            //     echo $child->nodeValue . " - " . $tag['offset'] . " - " . ($tag['offset'] - $start) . " - " . $tag['length'] . "<br>";
                            $meat = $child->splitText($tag['offset'] - $start);
                            $tail = $meat->splitText($tag['length']);
                            $a = $doc->createElement('a');
                            $a->setAttribute('href', '//facebook.com/' . $tag['id']);
                            $a->setAttribute('title', $tag['name']);
                            $a->setAttribute('target', "_blank");

                            $meat->parentNode->replaceChild($a, $meat);
                            $a->appendChild($meat);
                            break;
                        }
                        $start += strlen($child->nodeValue);
                    }
                }
            }


            $content = trim($doc->saveHTML());
        } else {
            $content = $text;
        }

        $content = nl2br($content);

        //replace urls
        $content = preg_replace('/([^\w\/])(www\.[a-z0-9\-]+\.[a-z0-9\-]+)/i', "$1http://$2", $content); //ads http to the url
        $content = preg_replace('|([\w\d]*)\s?(https?://([\d\w\.-]+\.[\w\.]{2,6})[^\s\]\[\<\>]*/?)|i', '$1 <a href="$2" target="_blank">$3</a>', $content);


        //replace hashtags
        $content = preg_replace('/(^|\s)#(\w*[a-zA-Z_]+\w*)/', '<a href="https://www.facebook.com/hashtag/$2" target="_blank">#$2</a>', $content);


        return $content;
    }

    public function listFeeds() {
        global $wpdb, $_wp_column_headers;
        $query = "SELECT * FROM " . $wpdb->prefix . FB_FEED_TABLE;
        return $wpdb->get_results($query);
    }

}
