<?php
/**
 *   @file index.php
 *   @brief Display the subject guides splash page
 *
 *   @author adarby
 *   @date mar 2011
 */
use SubjectsPlus\Control\CompleteMe;
use SubjectsPlus\Control\Querier;
    
$use_jquery = array("ui");

include("../control/includes/config.php");
include("../control/includes/functions.php");
include("../control/includes/autoloader.php");

$page_title = $resource_name;
$description = "The best stuff for your research.  No kidding.";
$keywords = "research, databases, subjects, search, find";
$noheadersearch = TRUE;

$db = new Querier;
    
$guide_path = "guide.php?subject=";

if (isset($_GET['type']) && in_array(($_GET['type']), $guide_types)) {

    // use the submitted value
    $view_type = scrubData($_GET['type']);
} else {
    $view_type = "all";
}

///////////////////////
// Have they done a search?

$search = "";

if (isset($_POST["search"])) {
    $search = scrubData($_POST["search"]);
}

// set up our checkboxes for guide types
$tickboxes = "<ul>";

foreach ($guide_types as $key) {
    $tickboxes .= "<li><input type=\"checkbox\" id=\"show-" . ucfirst($key) . "\" name=\"show$key\"";
    if ($view_type == "all" || $view_type == $key) {
        $tickboxes .= " checked=\"checked\"";
    }
    $tickboxes .= "/>" . ucfirst($key) . " Guides</li></li>\n";
}

$tickboxes .= "</ul>";

// Get the subjects for jquery autocomplete
$suggestibles = "";  // init

$q = "select subject, shortform from subject where active = '1' order by subject";



//initialize $suggestibles
$suggestibles = '';

foreach ($db->query($q) as $myrow) {
    $item_title = trim($myrow[0][0]);


	if(!isset($link))
	{
		$link = '';
	}

    $suggestibles .= "{text:\"" . htmlspecialchars($item_title) . "\", url:\"$link$myrow[1][0]\"}, ";

}

$suggestibles = trim($suggestibles, ', ');


// Get our newest guides

$q2 = "select subject, subject_id, shortform from subject where active = '1' order by subject_id DESC limit 0,5";

//$r2 = $db->query($q2);

$newest_guides = "<ul>\n";

foreach ($db->query($q2) as $myrow2 ) {
    $guide_location = $guide_path . $myrow2[2];
    $newest_guides .= "<li><a href=\"$guide_location\">" . trim($myrow2[0]) . "</a></li>\n";
}

$newest_guides .= "</ul>\n";


// Get our newest databases

$qnew = "SELECT title, location, access_restrictions FROM title t, location_title lt, location l WHERE t.title_id = lt.title_id AND l.location_id = lt.location_id AND eres_display = 'Y' order by t.title_id DESC limit 0,5";

//$rnew = $db->query($qnew);

$newlist = "<ul>\n";
    foreach ($db->query($qnew) as $myrow) {
    $db_url = "";

    // add proxy string if necessary
    if ($myrow[2] != 1) {
        $db_url = $proxyURL;
    }

    $newlist .= "<li><a href=\"$db_url$myrow[1][0]\">$myrow[0]</a></li>\n";
}
$newlist .= "</ul>\n";

// List guides function -- no other page uses it ? //

function listGuides($search = "", $type="all") {
    $db = new Querier();
    
    $andclause = "";
    global $guide_path;

    if ($search != "") {
        $search = scrubData($search);
        $andclause .= " AND subject LIKE '%" . $db->quote($search) . "%'";
    }

    if ($type != "all") {
        $andclause .= " AND type='" . $db->quote($type) . "'";
    }

    $q = "SELECT shortform, subject, type FROM subject WHERE active = '1' " . $andclause . " ORDER BY subject";
   // $r = $db->query($q);
    //print $q;
    $row_count = 0;
    $colour1 = "oddrow";
    $colour2 = "evenrow";

    $db = new Querier;
    print "<table class=\"item_listing\" width=\"98%\">";
    foreach ($db->query($q) as $myrow) {

        $row_colour = ($row_count % 2) ? $colour1 : $colour2;

        $guide_location = $guide_path . $myrow[0];

        print "<tr class=\"zebra $row_colour type-$myrow[2]\" style=\"height: 1.5em;\">
		 <td><a href=\"$guide_location\">" . htmlspecialchars_decode($myrow[1]) . "</a> 
        <div class=\"list_bonus\"></div></td>
        <td class=\"subject\">{$myrow[2]}</td>
         </tr>\n";
        $row_count++; 
    }
    print "</table>";
}

$searchbox = '
<div class="autoC" id="autoC" style="margin: 1em 2em 2em 0;">
    <form id="sp_admin_search" class="pure-form" method="post" action="search.php">
        <span class="titlebar_text">' .  _("Search Research Guides") . '</span>
        <input type="text" placeholder="Search" autocomplete="off" name="searchterm" size="" id="sp_search" class="ui-autocomplete-input autoC"><span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
        <input type="submit" alt="Search" name="submitsearch" id="topsearch_button" class="pure-button pure-button-topsearch" value="Go">
    </form>
</div>
';

////////////////////////////
// Now we are finally read to display the page
////////////////////////////

include("includes/header.php");

?>
<div class="pure-g-r" id="guidesplash">
<div class="pure-u-1-2">
    <div id="letterhead"><?php echo $tickboxes ?></div>
</div>
<div class="pure-u-1-2"></div>
<div class="pure-u-2-3" id="listguides">
<br class="clear-both">
<?php listGuides($search, $view_type); ?>

    </div>

    <div class="pure-u-1-3">
        <div class="pluslet">
            <div class="titlebar">
                <div class="titlebar_text"><?php print _("Newest Guides"); ?></div>
            </div>
            <div class="pluslet_body"> <?php print $newest_guides; ?> </div>
        </div>
        <!-- start pluslet -->
        <div class="pluslet">
            <div class="titlebar">
                <div class="titlebar_text"><?php print _("Newest Databases"); ?></div>
            </div>
            <div class="pluslet_body"> <?php print $newlist; ?> </div>
        </div>
        <!-- end pluslet -->
        <br />

    </div>
</div>
<?php
///////////////////////////
// Load footer file
///////////////////////////

include("includes/footer.php");

?>

<script type="text/javascript" language="javascript">
    $(document).ready(function(){

        // add rowstriping
        stripeR();

/*
        $("[id*=show]").live("change", function() {

            var showtype_id = $(this).attr("id").split("-");
            //alert("u clicked: " + showtype_id[1]);
            unStripeR();
            $(".type-" + showtype_id[1]).toggle();
            stripeR();

        });
*/
        function stripeR() {
            $(".zebra").not(":hidden").filter(":even").addClass("evenrow");
            $(".zebra").not(":hidden").filter(":odd").addClass("oddrow");
        }

        function unStripeR () {
            $(".zebra").removeClass("evenrow");
            $(".zebra").removeClass("oddrow");
        }

    });
</script>
