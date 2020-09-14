<?php
/* FUNCTIONS */
function & prepstring($strInput)
{
	$punc = array(".", ",", "?", ";", ":", "(", ")", "[", "]");
	$str1 = str_replace($punc, "", $strInput);
	//$str2 = str_replace("'", "\'", $str1);
	$str2 = strtolower($str1);
	return $str2;
}

// function from PHP manual by Jesse Bussman at gmail
function explodeX($delimiters,$string)
{
    $return_array = Array($string); // The array to return
    $d_count = 0;
    while (isset($delimiters[$d_count])) // Loop to loop through all delimiters
    {
        $new_return_array = Array();
        foreach($return_array as $el_to_split) // Explode all returned elements by the next delimiter
        {
            $put_in_new_return_array = explode($delimiters[$d_count],$el_to_split);
            foreach($put_in_new_return_array as $substr) // Put all the exploded elements in array to return
            {
                $new_return_array[] = $substr;
            }
        }
        $return_array = $new_return_array; // Replace the previous return array by the next version
        $d_count++;
    }
    return $return_array; // Return the exploded elements
}

function makeDocUrl($urlBase, $alchid, $title) {
	$alcharray = array();
	$alchpattern = "/^[ALCH0-9]+/";
	preg_match($alchpattern, $alchid, $alcharray);
	$alch = $alcharray[0];
	
	$folioarray = array();
	$foliopattern = "/f\.[0]*([0-9\.rv]+)/";
	preg_match($foliopattern, $title, $folioarray);
	$folio = $folioarray[1];
	
	return $urlBase.$alch."/#f".$folio; 
}

/* STOP LISTS */

$stoplist =" PARAG[[ ]]PARAG EXPAN[[ ]]EXPAN REG[[ ]]REG [[LB]] HEAD[[ ]]HEAD LATIN[[ ]]LATIN ENGLISH[[ ]]ENGLISH FRENCH[[ ]]FRENCH NAME[[ ]]NAME ABBR[[ ]]ABBR CORR[[ ]]CORR";
$passlist =" ADD[[ ]]ADD HI[[ ]]HI FOLIO[[ ]]FOLIO . , ; : ? ( ) [ ] { } p p. p: ";

/* PROGRAM SETUP */
$frags = "";
if (isset($_GET['frags']) && $_GET['frags'] != "")
{
	$frags = $_GET['frags'];
}
if ($frags == "") {
	return;
}

// identifying the documents
$doc = "";
if (isset($_GET['doc']) && $_GET['doc'] != "")
{
	$doc = $_GET['doc'];
}
$term = "";
if (isset($_GET['term']) && $_GET['term'] != "")
{
	$term = $_GET['term'];
}

// getting the correlation
$corr = "correlated chunks";
if (isset($_GET['corr']) && $_GET['corr'] != 0)
{
	$corr = $_GET['corr'];
}

$mdb = $_GET['mdb'];
$homeSite = $_GET['hs']
// use $homeSite to define $cameFrom
include_once("functions/homesites.php")

// open the database
require_once("functions/mysql_connection.php");

//begin setup
$getdocdata = "SELECT alch, ctitle, mstitle, ctext FROM frag250 WHERE id=".$doc;
if ($frags == "ch1000") {
	$getdocdata = "SELECT alch, ctitle, mstitle, ctext FROM frag1000 WHERE id=".$doc;
}

$docdata = mysqli_query($connection, $getdocdata);
$docrow = mysqli_fetch_row($docdata);

$alch = $docrow[0];
$title = $docrow[1];
$mstitle = $docrow[2];
$dstring = $docrow[3];

// make document URLs
$docUrl = makeDocUrl($cameFrom, $alch, $title);

// pulling out the text
//$dstring = file_get_contents($sourcedir.$doc);

$d = explode("\n", $dstring);
$dsx = prepstring($dstring);
$dcheck = explodeX(Array(" ", "\n"), $dsx);
//zero out big strings that are no longer needed
$dstring = ""; $dsx = "";

// prep the $term variable for apostrophes, etc
$termx = str_replace("@", "'", $term);
$termstring = prepstring($termx);
?>




<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="keywords" content="science,chemistry,history,isaac newton,chymistry,alchemical manuscripts,national science foundation,laboratory,chymistry of isaac newton,indiana university,digtial library,alchemy,newton">
<meta name="description" content="Isaac Newton, like Albert Einstein, is a quintessential symbol of the human intellect and its ability to decode the secrets of nature. Newton wrote and transcribed about a million words on the subject of alchemy, of which only a tiny fraction has today been published. With the support of the National Science Foundation and the National Endowment for the Humanities, The Chymistry of Isaac Newton hosted by Indiana University's Digital Library Program, is producing a scholarly online edition of Newton's alchemical manuscripts integrated with new research on Newton's chymistry.">
<meta name="author" content="Wallace Hooper">
<meta name="generator" content="tei2html stylesheets" />
<!-- Dublin Core Record: start -->
<meta name="DC.title" content="Latent Semantic Analysis Tool" />
<meta name="DC.creator" content="Wally Hooper">
<meta name="DC.designer" content="Timothy D Bowman">
<meta name="DC.type" scheme="DCTERMS.DCMIType" content="Text">
<meta name="DC.Format" scheme="DCTERMS.IMT" content="application/xhtml+xml">
<!-- Dublin Core Record: end -->
<title>LSA Results at <?php echo $corr; ?>: The Chymistry of Isaac Newton Project</title>


<?php require_once 'design/includes.php'; ?>
<!-- LSA Style -->
<link href="css.lsa/style.css" rel="stylesheet" media="all" />
<!-- End LSA Style -->

<style type="text/css" media="all">
.match {
	background-color: yellow;
}
.content {
	font-size: 0.8em;
}
</style>
</head>
<body>


<!-- Newton Skin -->
<?php require_once 'design/header.php'; ?>


<?php 
echo "<div class='content'>";
echo "<a href='".$docUrl."' target='_blank'>".$docUrl." (new window)</a><br/><br/>";
echo "$alch<br/>$title<br/>$mstitle<br/><br/>";
foreach($d as $line1) {
	$words1 = explode(" ", $line1);
	foreach($words1 as $w1) {
		if (strpos($stoplist, $w1) > 0) 
		{ continue; }
		if (strpos($passlist, $w1) > 0)
		{
			print($w1." ");
			continue;
		}
		$w1x = prepstring($w1);
		$regexpattern = "/^".$w1x."$|^".$w1x."_|_".$w1x."_|_".$w1x."$/";
		$regextest = preg_match($regexpattern, $termstring);
		//if ($w1x == $term) {
		if ($regextest) {
			print("<span class=\"match\">$w1</span> ");
		}
		else {
			print($w1." ");
		}
	}
	print("<br/>");
}
echo "</div>";
?>

		</section>	
		</div>
	</div>
</section>

<!-- Newton Skin -->
<?php 
require_once('design/page-footer.php')
require_once 'design/jsfooter.php'; 
?>



</body>
</html>
<?php 
mysqli_free_result($docdata);
mysqli_free_result($docrow);
mysqli_close($connection);
?>
