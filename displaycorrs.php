<?php
/* MANUAL SWITCH TO FORCE TRANSLATION OF FRAG STRINGS FROM GENTIUM FONT TO NEWTON SANS */
$gentium = true;

/* FUNCTIONS */
function & prepstring($strInput)
{
	$punc = array(".", ",", "?", ";", ":", "(", ")", "[", "]");
	$str1 = str_replace($punc, "", $strInput);
	$str2 = strtolower($str1);
	return $str2;
}

function & translateGentium($strInput)
{
	$gentiumChars = [ '\x{E000}', '\x{E008}', '\x{E017}', '\x{E01A}', '\x{E05A}', '\x{E301}', '\x{E302}', '\x{E303}', '\x{E304}', '\x{E305}', '\x{E306}', '\x{E307}', '\x{E308}', '\x{E30A}', '\x{E30B}', '\x{E30C}', '\x{E30D}', '\x{E310}', '\x{E311}', '\x{E314}', '\x{E315}', '\x{E316}', '\x{E317}', '\x{E31C}', '\x{E31D}', '\x{E31F}', '\x{E320}', '\x{E321}', '\x{E322}', '\x{E325}', '\x{E326}', '\x{E327}', '\x{E328}', '\x{E329}', '\x{E32A}', '\x{E32B}', '\x{E32C}', '\x{E32D}', '\x{E32E}', '\x{E330}', '\x{E331}', '\x{E332}', '\x{E333}', '\x{E334}', '\x{E335}', '\x{E339}', '\x{E33E}', '\x{E33F}', '\x{E346}', '\x{E34E}', '\x{E34F}', '\x{E350}', '\x{E351}', '\x{E352}', '\x{E355}', '\x{E35E}', '\x{E367}', '\x{E36B}', '\x{E36E}', '\x{E370}', '\x{E373}' ];
	$newtonSansChars = [ '\x{E005}', '\x{1F772}', '\x{1F72F}', '\x{1F748}', '\x{1F706}', '\x{1F701}', '\x{1F702}', '\x{1F703}', '\x{1F704}', '\x{1F705}', '\x{1F706}', '\x{1F707}', '\x{1F708}', '\x{1F70A}', '\x{1F70B}', '\x{1F70C}', '\x{1F70D}', '\x{1F710}', '\x{1F711}', '\x{1F714}', '\x{1F715}', '\x{1F716}', '\x{1F717}', '\x{1F71C}', '\x{1F71D}', '\x{1F71F}', '\x{1F720}', '\x{1F721}', '\x{1F722}', '\x{1F725}', '\x{1F726}', '\x{1F727}', '\x{1F728}', '\x{1F729}', '\x{1F72A}', '\x{1F72B}', '\x{1F72C}', '\x{1F72D}', '\x{1F72E}', '\x{1F730}', '\x{1F731}', '\x{1F732}', '\x{1F733}', '\x{1F734}', '\x{1F735}', '\x{1F739}', '\x{1F73E}', '\x{1F73F}', '\x{1F746}', '\x{1F74E}', '\x{1F74F}', '\x{1F750}', '\x{1F751}', '\x{1F752}', '\x{1F755}', '\x{1F75E}', '\x{1F767}', '\x{1F76B}', '\x{1F76E}', '\x{1F770}', '\x{1F773}' ];

	$strOutput = str_replace($gentiumChars, $newtonSansChars, $strInput);
	
	return $strOutput;
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

	return $urlBase."text/".$alch."/diplomatic"."#f".$folio;
}

/* STOP LISTS */

$stoplist =" PARAG[[ ]]PARAG EXPAN[[ ]]EXPAN REG[[ ]]REG [[LB]] HEAD[[ ]]HEAD LATIN[[ ]]LATIN ENGLISH[[ ]]ENGLISH FRENCH[[ ]]FRENCH NAME[[ ]]NAME ABBR[[ ]]ABBR CORR[[ ]]CORR";
$passlist =" ADD[[ ]]ADD HI[[ ]]HI FOLIO[[ ]]FOLIO . , ; : ? ( ) [ ] { } p p. p: ";

/* PROGRAM SETUP */

// open the database
$textSite = "";
$connection = new mysqli();
$host = "";
$port = "";
require_once("functions/mysql_connection.php");

// need to test for the source database because the Library version still uses the older Gentium font rather than Newton Sans
$gentium = true;
if ($host == "sasrdsmp01.uits.iu.edu") {
	$gentium = false;
}

$frags = "";
if (isset($_GET['frags']) && $_GET['frags'] != "")
{
	$frags = $_GET['frags'];
}
if ($frags == "") {
	return;
}

// identifying the documents by id
$doc1 = "";
if (isset($_GET['doc1']) && $_GET['doc1'] != "")
{
	$doc1 = $_GET['doc1'];
}
$doc2 = "";
if (isset($_GET['doc2']) && $_GET['doc2'] != "")
{
	$doc2 = $_GET['doc2'];
}

// getting the correlation
$corr = "correlated chunks";
if (isset($_GET['corr']) && $_GET['corr'] != 0)
{
	$corr = $_GET['corr'];
}

/****************************
Write to a log file 
****************************/
// $logfile = "log/displaycorrs.txt";
// $log = fopen($logfile, "w");
// fwrite($log, "host = ". $host . ", port = ". $port."\n");
// fwrite($log, "open viewcorrs with ".memory_get_usage()." RAM at ".date('M d g:i:s')."\n");
// if ($connection) {
	// fwrite($log, "mysql connected\n"); 
// }
// fwrite($log, "doc1 = ". $doc1 . "\n");
// fwrite($log, "doc2 = ". $doc2 . "\n");

//begin setup
$getdoc1data = "SELECT alch, ctitle, mstitle, ctext FROM frag250 WHERE id=".$doc1;
$getdoc2data = "SELECT alch, ctitle, mstitle, ctext FROM frag250 WHERE id=".$doc2;
$getallterms = "SELECT wordform FROM term250_list";
if ($frags == "ch1000") {
	$getdoc1data = "SELECT alch, ctitle, mstitle, ctext FROM frag1000 WHERE id=".$doc1;
	$getdoc2data = "SELECT alch, ctitle, mstitle, ctext FROM frag1000 WHERE id=".$doc2;
	$getallterms = "SELECT wordform FROM term1000_list";
}

$doc1row = mysqli_query($connection, $getdoc1data);
$doc1data = mysqli_fetch_row($doc1row);

$doc1alch = $doc1data[0];
$doc1title = $doc1data[1];
$doc1mstitle = $doc1data[2];
$d1string = $doc1data[3];
// fwrite($log, "doc1alch = ". $doc1alch . "\n");
// fwrite($log, "doc1title = ". $doc1title . "\n");
// fwrite($log, "doc1mstitle = ". $doc1mstitle . "\n");
// fwrite($log, "d1string = ". $d1string . "\n");

$doc2row = mysqli_query($connection, $getdoc2data);
$doc2data = mysqli_fetch_row($doc2row);

$doc2alch = $doc2data[0];
$doc2title = $doc2data[1];
$doc2mstitle = $doc2data[2];
$d2string = $doc2data[3];
// fwrite($log, "doc2alch = ". $doc2alch . "\n");
// fwrite($log, "doc2title = ". $doc2title . "\n");
// fwrite($log, "doc2mstitle = ". $doc2mstitle . "\n");
// fwrite($log, "d2string = ". $d2string . "\n");

// change the font to get correct NewtonSans characters if data was encoded for Gentium Newton
if ($gentium) {
	$d1string = translateGentium($d1string);
	$d2string = translateGentium($d2string);
}

// setting up the term list
$termstring = "";
$termrows = mysqli_query($connection, $getallterms);
while ($termrow = mysqli_fetch_row($termrows)) {
	$termstring = $termstring.$termrow[0]."\n";
}

//$termstring = file_get_contents($termsfile);
$termsx = prepstring($termstring);
$terms = explode("\n", $termsx);

// make document URLs
$doc1url = makeDocUrl($textSite, $doc1alch, $doc1title);
$doc2url = makeDocUrl($textSite, $doc2alch, $doc2title);

// pulling out the text
$d1 = explode("\n", $d1string);
$d2 = explode("\n", $d2string);
$d1sx = prepstring($d1string);
$d2sx = prepstring($d2string);
$d1check = explodeX(Array(" ", "\n"), $d1sx);
$d2check = explodeX(Array(" ", "\n"), $d2sx);
//zero out big strings that are no longer needed
$d1string = ""; $d2string = ""; $d1sx = ""; $d2sx = "";
?>


<!DOCTYPE html>
<html lang="en">
<head>
<!-- <meta charset="utf-8" />
<meta name="keywords" content="science,chemistry,history,isaac newton,chymistry,alchemical manuscripts,national science foundation,laboratory,chymistry of isaac newton,indiana university,digtial library,alchemy,newton">
<meta name="description" content="Isaac Newton, like Albert Einstein, is a quintessential symbol of the human intellect and its ability to decode the secrets of nature. Newton wrote and transcribed about a million words on the subject of alchemy, of which only a tiny fraction has today been published. With the support of the National Science Foundation and the National Endowment for the Humanities, The Chymistry of Isaac Newton hosted by Indiana University's Digital Library Program, is producing a scholarly online edition of Newton's alchemical manuscripts integrated with new research on Newton's chymistry.">
<meta name="author" content="Wallace Hooper">
<meta name="generator" content="tei2html stylesheets" /> -->
<!-- Dublin Core Record: start -->
<!-- <meta name="DC.title" content="Latent Semantic Analysis Tool" />
<meta name="DC.creator" content="Wally Hooper">
<meta name="DC.designer" content="Timothy D Bowman">
<meta name="DC.type" scheme="DCTERMS.DCMIType" content="Text">
<meta name="DC.Format" scheme="DCTERMS.IMT" content="application/xhtml+xml"> -->
<!-- Dublin Core Record: end -->


<title>LSA Results at <?php echo $corr; ?>: The Chymistry of Isaac Newton Project</title>

<?php require_once 'design/includes.php'; ?>
<?php require_once 'design/header.php'; ?>
<!-- LSA Style -->
<link href="css.lsa/style.css" rel="stylesheet" media="all" />
<!-- End LSA Style -->
<style type="text/css" media="all">
.match {
	color: firebrick;
	<!-- background-color: yellow; -->
}
.content {
	font-size:small;
}
</style>
</head>
<body>


<!-- Newton Skin -->
<?php require_once("design/uniform-title.php"); ?>

<table class="content" style="margin-left: 10px">
<tr>
<td width="40%" valign="top">
<?php 
echo "<a href='".$doc1url."' target='_blank'>".$doc1url." (new window)</a><br/><br/>";
echo "$doc1alch<br/>$doc1title<br/>$doc1mstitle<br/><br/>";
foreach($d1 as $line1) {
	if ($line1 == "") {
		print("<br/>");
		continue;
	}
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
		if (in_array($w1x, $terms) && in_array($w1x, $d2check)) {
		//if (in_array($w1x, $terms) && strpos($d2sx, $w1x)>0) {
			//if (in_array($wx1, $terms) && preg_match("/\s$wx1\s/", $d2sx)) {
			print("<span class=\"match\">$w1</span> ");
		}
		else {
			print($w1." ");
		}
	}
	print("<br/>");
}
?>
</td>
<td width="40%" valign="top">
<?php 
echo "<a href='".$doc2url."' target='_blank'>".$doc2url." (new  window)</a><br/></br>";
echo "$doc2alch<br/>$doc2title<br/>$doc2mstitle<br/><br/>";
foreach($d2 as $line2) {
	if ($line2 == "") {
		print("<br/>");
		continue;
	}
	$words2 = explode(" ", $line2);
	foreach($words2 as $w2) {
		if (strpos($stoplist, $w2) > 0) 
		{ continue; }
		if (strpos($passlist, $w2) > 0)
		{
			print($w2." ");
			continue;
		}
		$w2x = prepstring($w2);
		if (in_array($w2x, $terms) && in_array($w2x, $d1check)) {
		//if (in_array($w2x, $terms) && strpos($d1sx, $w2x)>0) {
			print("<span class=\"match\">$w2</span> ");
		}
		else {
			print($w2." ");
		}
	}
	print("<br/>");
}
?></td>
</tr>
</table>

		</section>	
		</div>
	</div>
</section>

<!-- Newton Skin -->
<?php 
require_once('design/page-footer.php');
require_once 'design/jsfooter.php'; 
?>
</body>
</html>
<?php 

mysqli_close($connection);
?>
