<?php
/* MANUAL SWITCH TO FORCE TRANSLATION OF FRAG STRINGS FROM GENTIUM FONT TO NEWTON SANS */
$gentium = false;

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
	$gentiumChars = [ "\u{E000}", "\u{E008}", "\u{E017}", "\u{E01A}", "\u{E05A}", "\u{E301}", "\u{E302}", "\u{E303}", "\u{E304}", "\u{E305}", "\u{E306}", "\u{E307}", "\u{E308}", "\u{E30A}", "\u{E30B}", "\u{E30C}", "\u{E30D}", "\u{E310}", "\u{E311}", "\u{E314}", "\u{E315}", "\u{E316}", "\u{E317}", "\u{E31C}", "\u{E31D}", "\u{E31F}", "\u{E320}", "\u{E321}", "\u{E322}", "\u{E325}", "\u{E326}", "\u{E327}", "\u{E328}", "\u{E329}", "\u{E32A}", "\u{E32B}", "\u{E32C}", "\u{E32D}", "\u{E32E}", "\u{E330}", "\u{E331}", "\u{E332}", "\u{E333}", "\u{E334}", "\u{E335}", "\u{E339}", "\u{E33E}", "\u{E33F}", "\u{E346}", "\u{E34E}", "\u{E34F}", "\u{E350}", "\u{E351}", "\u{E352}", "\u{E355}", "\u{E35E}", "\u{E367}", "\u{E36B}", "\u{E36E}", "\u{E370}", "\u{E373}" ];
	$newtonSansChars = [ "\u{E005}", "\u{1F772}", "\u{1F72F}", "\u{1F748}", "\u{1F706}", "\u{1F701}", "\u{1F702}", "\u{1F703}", "\u{1F704}", "\u{1F705}", "\u{1F706}", "\u{1F707}", "\u{1F708}", "\u{1F70A}", "\u{1F70B}", "\u{1F70C}", "\u{1F70D}", "\u{1F710}", "\u{1F711}", "\u{1F714}", "\u{1F715}", "\u{1F716}", "\u{1F717}", "\u{1F71C}", "\u{1F71D}", "\u{1F71F}", "\u{1F720}", "\u{1F721}", "\u{1F722}", "\u{1F725}", "\u{1F726}", "\u{1F727}", "\u{1F728}", "\u{1F729}", "\u{1F72A}", "\u{1F72B}", "\u{1F72C}", "\u{1F72D}", "\u{1F72E}", "\u{1F730}", "\u{1F731}", "\u{1F732}", "\u{1F733}", "\u{1F734}", "\u{1F735}", "\u{1F739}", "\u{1F73E}", "\u{1F73F}", "\u{1F746}", "\u{1F74E}", "\u{1F74F}", "\u{1F750}", "\u{1F751}", "\u{1F752}", "\u{1F755}", "\u{1F75E}", "\u{1F767}", "\u{1F76B}", "\u{1F76E}", "\u{1F770}", "\u{1F773}" ];

	$strOutput = "";

	$strOutput = str_replace($strInput, $gentiumChars, $newtonSansChars);
	
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

// change the font to get correct NewtonSans characters if necessary

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
