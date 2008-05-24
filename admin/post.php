<?php

include_once "./check_auth.php";
include_once "./admin_functions.php";


// check the mode
if(isset($_POST["mode"])){
    $mode = $_POST["mode"];
} elseif(isset($_GET["mode"])){
    $mode = $_GET["mode"];
} else {
    $mode = "new";
}

if($mode!="new" && $mode!="edit"){
    wc_admin_error("Invalid mode '".htmlspecialchars($mode)."' for post page");
}

if($mode=="edit" && empty($_GET["post_id"]) && empty($_POST["post_id"])){
    wc_admin_error("No post_id provided for edit mode.");
}


// init error to empty
$error = "";


// check for post data
if(count($_POST)){

    if(empty($_POST["subject"]) || empty($_POST["editor"])) {

        $error = "You must fill in a Subject and a Post.";

    }

    if(isset($_POST["custom_date"])){
        $ts = strtotime($_POST["date"]);
        if(empty($ts)){
            $error = "Sorry, I don't recognize the date ".$_POST["date"];
        } else {
            $post_date = date("Y-m-d H:i:s", $ts);
        }
    } elseif($mode=="new"){
        $post_date = date("Y-m-d H:i:s");
    }

    if(empty($error)){

        $post_array = array(
            "user_id"   => $USER["user_id"],
            "post_id"   => $_POST["post_id"],
            "subject"   => $_POST["subject"],
            "body"      => $_POST["editor"],
            "tags"      => $_POST["tags"],
        );

        if(!empty($post_date)){
            $post_array["post_date"] = $post_date;
        }

        $success = wc_db_save_post($post_array);

        if($success){
            wc_admin_message("Post Saved!");
        } else{
            $error = "There was an error saving your post.";
        }
    }

    if(!empty($error)){

        // setup the form with the posted data if there is an error
        $post_id = $_POST["post_id"];
        $post_subject = $_POST["subject"];
        $post_body = $_POST["body"];
        $post_tags = $_POST["tags"];
        $post_custom_date = isset($_POST["custom_date"]);
        $post_date = $_POST["date"];
    }

} else {

    // check for initial edit mode
    if(isset($_GET["post_id"])){

        $post = wc_db_get_post($_GET["post_id"]);

        if(!empty($post)){
            $post_id = $post["post_id"];
            $post_subject = $post["subject"];
            $post_body = $post["body"];
            $post_tags = $post["tags_text"];
            $post_date = strftime("%c", strtotime($post["post_date"]));
        } else {
            wc_admin_error("The post you requested to edit was not found.");
        }

    } else {

        // set up new post form
        $post_id = "";
        $post_subject = "";
        $post_body = "";
        $post_tags = "";
        $post_date = "";
    }

}


// set breadcrumb
$WHEREAMI = ($mode=="edit") ? "Edit Post" : "New Post";


// begin output
include_once "./header.php";

if(!empty($error)){
    wc_admin_error($error, false);
}

?>

<form method="post" action="post.php" id="post-form">

    <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post_id); ?>" />
    <input type="hidden" name="mode" value="<?php echo htmlspecialchars($mode); ?>" />

    <p>
        <strong>Subject:</strong><br />
        <input class="inputgri" type="text" value="<?php echo htmlspecialchars($post_subject); ?>" id="subject" name="subject" maxlength="100" />
    </p>

    <p>
        <strong>Post:</strong><br />
        <textarea id="editor" name="editor" rows="20" cols="75"><?php echo htmlspecialchars($post_body); ?></textarea>
    </p>

    <p>
        <strong>Tags:</strong><br />
        <input class="inputgri" type="text" value="<?php echo htmlspecialchars($post_tags); ?>" id="tags" name="tags" /><br />
        <small>Separate with commas. Example: kids, ball game, park</small>
    </p>

    <p>
        <strong>Post Date:</strong><br />
        <input type="checkbox" name="custom_date" id="custom_date" value="1" <?php if(!empty($post_custom_post)) echo "selected "; ?>/><label for="custom_date">Custom Date</lable><br />
        <input class="inputgri" type="text" value="<?php echo htmlspecialchars($post_date); ?>" id="date" name="date" /><br />
    </p>

    <p>
        <input class="button" type="submit" value="Save" />
    </p>

</form>

<script>

(function() {
    var Dom = YAHOO.util.Dom,
        Event = YAHOO.util.Event;

    var myConfig = {
        height: '500px',
        width: '770px',
        dompath: true,
        focusAtStart: false,
        handleSubmit: true,
        toolbar: {
            collapse: false,
            titlebar: '',
            draggable: false,
            buttonType: 'advanced',
            buttons: [
                { group: 'textstyle', label: 'Font Style',
                    buttons: [
                        { type: 'push', label: 'Bold CTRL + SHIFT + B', value: 'bold' },
                        { type: 'push', label: 'Italic CTRL + SHIFT + I', value: 'italic' },
                        { type: 'push', label: 'Underline CTRL + SHIFT + U', value: 'underline' },
                        { type: 'separator' },
                        { type: 'push', label: 'Subscript', value: 'subscript', disabled: true },
                        { type: 'push', label: 'Superscript', value: 'superscript', disabled: true },
                        { type: 'separator' },
                        { type: 'color', label: 'Font Color', value: 'forecolor', disabled: true },
                        { type: 'color', label: 'Background Color', value: 'backcolor', disabled: true },
                        { type: 'separator' },
                        { type: 'push', label: 'Remove Formatting', value: 'removeformat', disabled: true },
                        { type: 'push', label: 'Show/Hide Hidden Elements', value: 'hiddenelements' }
                    ]
                },
                { type: 'separator' },
                { group: 'alignment', label: 'Alignment',
                    buttons: [
                        { type: 'push', label: 'Align Left CTRL + SHIFT + [', value: 'justifyleft' },
                        { type: 'push', label: 'Align Center CTRL + SHIFT + |', value: 'justifycenter' },
                        { type: 'push', label: 'Align Right CTRL + SHIFT + ]', value: 'justifyright' },
                        { type: 'push', label: 'Justify', value: 'justifyfull' }
                    ]
                },
                { type: 'separator' },
                { group: 'parastyle', label: 'Paragraph Style',
                    buttons: [
                    { type: 'select', label: 'Normal', value: 'heading', disabled: true,
                        menu: [
                            { text: 'Normal', value: 'none', checked: true },
                            { text: 'Header 1', value: 'h1' },
                            { text: 'Header 2', value: 'h2' },
                            { text: 'Header 3', value: 'h3' },
                            { text: 'Header 4', value: 'h4' },
                            { text: 'Header 5', value: 'h5' },
                            { text: 'Header 6', value: 'h6' }
                        ]
                    }
                    ]
                },
                { type: 'separator' },
                { group: 'indentlist', label: 'Indenting and Lists',
                    buttons: [
                        { type: 'push', label: 'Indent', value: 'indent', disabled: true },
                        { type: 'push', label: 'Outdent', value: 'outdent', disabled: true },
                        { type: 'push', label: 'Create an Unordered List', value: 'insertunorderedlist' },
                        { type: 'push', label: 'Create an Ordered List', value: 'insertorderedlist' }
                    ]
                },
                { type: 'separator' },
                { group: 'insertitem', label: 'Insert Item',
                    buttons: [
                        { type: 'push', label: 'HTML Link CTRL + SHIFT + L', value: 'createlink', disabled: true },
                        { type: 'push', label: 'Insert Image', value: 'insertimage' }
                    ]
                }
            ]
        }
    };

    var myEditor = new YAHOO.widget.Editor('editor', myConfig);
    myEditor._defaultToolbar.buttonType = 'advanced';
    myEditor.render();

})();
</script>

<?php

include_once "./footer.php";

?>