<?php

// Check that this file is not loaded directly.
if ( basename( __FILE__ ) == basename( $_SERVER["PHP_SELF"] ) ) exit();

// if init has not loaded, quit the file
if(!defined("WC")) return;

include_once dirname(__FILE__)."/url.php";

/**
 * Controls the output of the public facing site
 *
 * @param   $page       The page we are currently rendering
 * @param   $WCDATA    The data that the page needs to render
 * @return  none
 *
 */
function wc_output($page, $WCDATA="") {

    wc_build_common_data($WCDATA);

    // we don't want to pull $WC in here as a global
    $template = basename($GLOBALS["WC"]["template"]);

    $page = basename($page);

    if(file_exists("./templates/$template/$page.php")){

        include_once "./templates/$template/header.php";

        include_once "./templates/$template/$page.php";

        include_once "./templates/$template/footer.php";

    } else {

        // check special pages
        switch($page) {
            case "notfound":
                header('HTTP/1.1 404 Not Found');
                header('Status: 404 Not Found');
                include_once "./templates/$template/header.php";
                echo "The page you requested was not found.";
                include_once "./templates/$template/footer.php";
                break;
            case "error":
                header('HTTP/1.1 500 Internal Server Error');
                header('Status: 500 Internal Server Error');
                include_once "./templates/$template/header.php";
                echo $WCDATA["error"];
                include_once "./templates/$template/footer.php";
                break;
            default:
                trigger_error("Invalid page name $page.", E_TRIGGER_ERROR);
                break;
        }

    }

}


/**
 * Builds common data that most/all template pages need
 *
 * @param   $WCDATA    A reference to the data array that will be
 *                      used later to render the page.
 * @return  none
 *
 */
function wc_build_common_data(&$WCDATA) {

    global $WC;

    $WCDATA["base_url"] = wc_get_url("main");

    if(empty($WCDATA["feed_url"])){
        $WCDATA["feed_url"] = wc_get_url("feed");
    }

    $WCDATA["nav_pages"] = wc_db_get_nav_pages();
    foreach($WCDATA["nav_pages"] as $key=>$page){
        $WCDATA["nav_pages"][$key]["url"] = wc_get_url("page", $page["page_id"]);
    }

    $WCDATA["tags"] = wc_db_get_tags();
    foreach($WCDATA["tags"] as $key=>$tag){
        $WCDATA["tags"][$key]["url"] = wc_get_url("tag", $tag["tag"]);
    }

    if(empty($WCDATA["title"])){
        $WCDATA["title"] = $WC["default_title"];
    }

    if(empty($WCDATA["description"])){
        $WCDATA["description"] = $WC["default_description"];
    }

}

?>
