<?php

// functions to make the different feeds

function wc_feed_make_rss($posts, $feed_url, $feed_title, $feed_description) {

    global $WC;

    $buffer = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
    $buffer.= "<rss version=\"2.0\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\">\n";
    $buffer.= "    <channel>\n";
    $buffer.= "        <title>".htmlspecialchars($feed_title, ENT_COMPAT, "utf-8")."</title>\n";
    $buffer.= "        <description>".htmlspecialchars($feed_description, ENT_COMPAT, "utf-8")."</description>\n";
    $buffer.= "        <link>".htmlspecialchars($feed_url, ENT_COMPAT, "utf-8")."</link>\n";
    $buffer.= "        <lastBuildDate>".htmlspecialchars(date("r"), ENT_COMPAT, "utf-8")."</lastBuildDate>\n";
    $buffer.= "        <generator>".htmlspecialchars("Wordcraft ".WC, ENT_COMPAT, "utf-8")."</generator>\n";

    foreach($posts as $post) {

        $title = strip_tags($post["subject"]);
        $date = date("r", strtotime($post["post_date"]));
        $url = wc_get_url("post", $post["post_id"]);
        $body = strtr($post['body'], "\001\002\003\004\005\006\007\010\013\014\016\017\020\021\022\023\024\025\026\027\030\031\032\033\034\035\036\037", "????????????????????????????");

        $buffer.= "        <item>\n";
        $buffer.= "            <guid>".htmlspecialchars($url, ENT_COMPAT, "utf-8")."</guid>\n";
        $buffer.= "            <title>".htmlspecialchars($title, ENT_COMPAT, "utf-8")."</title>\n";
        $buffer.= "            <link>".htmlspecialchars($url, ENT_COMPAT, "utf-8")."</link>\n";
        $buffer.= "            <description><![CDATA[$body]]></description>\n";
        $buffer.= "            <dc:creator>".htmlspecialchars($post['user_name'], ENT_COMPAT, "utf-8")."</dc:creator>\n";
        $buffer.= "            <category>".htmlspecialchars($post["tags_text"], ENT_COMPAT, "utf-8")."</category>\n";
        $buffer.= "            <pubDate>".htmlspecialchars($date, ENT_COMPAT, "utf-8")."</pubDate>\n";
        $buffer.= "        </item>\n";
    }

    $buffer.= "    </channel>\n";
    $buffer.= "</rss>\n";

    return $buffer;
}


function wc_feed_make_atom($posts, $feed_url, $feed_title, $feed_description) {

    global $WC;

    $buffer = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
    $buffer.= "<feed xmlns=\"http://www.w3.org/2005/Atom\">\n";
    $buffer.= "    <title>".htmlspecialchars($feed_title, ENT_COMPAT, "utf-8")."</title>\n";
    $buffer.= "    <subtitle>".htmlspecialchars($feed_description, ENT_COMPAT, "utf-8")."</subtitle>\n";
    $buffer.= "    <link rel=\"self\" href=\"".htmlspecialchars($feed_url, ENT_COMPAT, "utf-8")."\" />\n";
    $buffer.= "    <id>".htmlspecialchars($feed_url, ENT_COMPAT, "utf-8")."</id>\n";
    $buffer.= "    <updated>".htmlspecialchars(date("c"), ENT_COMPAT, "utf-8")."</updated>\n";
    $buffer.= "    <generator>".htmlspecialchars("Wordcraft ".WC, ENT_COMPAT, "utf-8")."</generator>\n";

    foreach($posts as $post) {

        $title = strip_tags($post["subject"]);
        $url = wc_get_url("post", $post["post_id"]);
        $body = strtr($post['body'], "\001\002\003\004\005\006\007\010\013\014\016\017\020\021\022\023\024\025\026\027\030\031\032\033\034\035\036\037", "????????????????????????????");

        $buffer.= "    <entry>\n";
        $buffer.= "        <title type=\"html\">$title</title>\n";
        $buffer.= "        <link href=\"".htmlspecialchars($url, ENT_COMPAT, "utf-8")."\" />\n";
        $buffer.= "        <category term=\"".htmlspecialchars($post["tags_text"], ENT_COMPAT, "utf-8")."\" />\n";
        $buffer.= "        <published>".date("c", strtotime($post["post_date"]))."</published>\n";
        $buffer.= "        <updated>".date("c", strtotime($post["post_date"]))."</updated>\n";
        $buffer.= "        <id>".htmlspecialchars($url, ENT_COMPAT, "utf-8")."</id>\n";
        $buffer.= "        <author>\n";
        $buffer.= "            <name>".htmlspecialchars($post["user_name"], ENT_COMPAT, "utf-8")."</name>\n";
        $buffer.= "        </author>\n";
        $buffer.= "        <summary type=\"html\"><![CDATA[$body]]></summary>\n";
        $buffer.= "    </entry>\n";
    }

    $buffer.= "</feed>\n";

    return $buffer;

}


function wc_feed_make_json($posts, $feed_url, $feed_title, $feed_description) {

    global $WC;

    $array = array(
        "title" => $feed_title,
        "description" => $feed_description,
        "url" => $feed_url,
        "updated" => date("r"),
    );

    foreach($posts as $post) {

        $title = strip_tags($post["subject"]);
        $url = wc_get_url("post", $post["post_id"]);
        $body = strtr($post['body'], "\001\002\003\004\005\006\007\010\013\014\016\017\020\021\022\023\024\025\026\027\030\031\032\033\034\035\036\037", "????????????????????????????");

        $array["posts"][] = array(
            "title" => $title,
            "url" => $url,
            "tags" => $post["tags"],
            "published" => date("r", strtotime($post["post_date"])),
            "author" => $post["user_name"],
            "post" => $body
        );
    }

    return json_encode($array);

}
?>