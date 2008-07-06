<?php  foreach($WCDATA["posts"] as $post) { ?>

    <h2><a href="<?php echo $post["url"] ?>"><?php echo $post["subject"]; ?></a></h2>

    <p class="prefix">Posted by <?php echo $post["user_name"]; ?> on <?php echo $post["post_date"]; ?></p>

    <div class="body">
        <?php echo $post["body"]; ?>
    </div>

    <p class="suffix">
        <a href="<?php echo $post["url"]; ?>#comments">Comments (<?php echo $post["comment_count"]; ?>)</a>
        &nbsp;&nbsp;
        <a href="<?php echo $post["url"]; ?>">Permalink</a>
        &nbsp;&nbsp;
        <?php if(!empty($post["tags"])) { ?>
            Tags:
            <?php foreach($post["tags"] as $tag) { ?>
                <a href="<?php echo $tag["url"]; ?>"><?php echo $tag["tag"]; ?></a>&nbsp;&nbsp;
            <?php } ?>
        <?php } ?>
    </p>

<?php } ?>

