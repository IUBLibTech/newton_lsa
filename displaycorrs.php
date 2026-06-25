<?php
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
	// $newtonSansTestInts = [ 57349 => 57349, 128882 => 128882, 128815 => 128815, 128840 => 128840, 128774 => 128774, 128769 => 128769, 128770 => 128770, 128771 => 128526, 128772 => 128772, 128773 => 128773, 128774 => 128774, 128775 => 128775, 128776 => 128776, 128778 => 128778, 128779 => 128779, 128780 => 128780, 128781 => 128781, 128784 => 128784, 128785 => 128785, 128788 => 128788, 128789 => 128789, 128790 => 128790, 128791 => 128791, 128796 => 128796, 128797 => 128797, 128799 => 128799, 128800 => 128800, 128801 => 128801, 128802 => 128802, 128805 => 128805, 128806 => 128806, 128807 => 128807, 128808 => 128808, 128809 => 128809, 128810 => 128810, 128811 => 128811, 128812 => 128812, 128813 => 128813, 128814 => 128814, 128816 => 128816, 128817 => 128817, 128818 => 128818, 128819 => 128819, 128820 => 128820, 128821 => 128821, 128825 => 128825, 128830 => 128830, 128831 => 128831, 128838 => 128838, 128846 => 128846, 128847 => 128847, 128848 => 128848, 128849 => 128849, 128850 => 128850, 128853 => 128853, 128862 => 128862, 128871 => 128871, 128875 => 128875, 128878 => 128878, 128880 => 128880, 128883 => 128883 ];
	$gentium2sans = [ 57344 => 57349, 57352 => 128882, 57367 => 128815, 57370 => 128840, 57434 => 128774, 58113 => 128769, 58114 => 128770, 58115 => 128771, 58116 => 128772, 58117 => 128773, 58118 => 128774, 58119 => 128775, 58120 => 128776, 58122 => 128778, 58123 => 128779, 58124 => 128780, 58125 => 128781, 58128 => 128784, 58129 => 128785, 58132 => 128788, 58133 => 128789, 58134 => 128790, 58135 => 128791, 58140 => 128796, 58141 => 128797, 58143 => 128799, 58144 => 128800, 58145 => 128801, 58146 => 128802, 58149 => 128805, 58150 => 128806, 58151 => 128807, 58152 => 128808, 58153 => 128809, 58154 => 128810, 58155 => 128811, 58156 => 128812, 58157 => 128813, 58158 => 128814, 58160 => 128816, 58161 => 128817, 58162 => 128818, 58163 => 128819, 58164 => 128820, 58165 => 128821, 58169 => 128825, 58174 => 128830, 58175 => 128831, 58182 => 128838, 58190 => 128846, 58191 => 128847, 58192 => 128848, 58193 => 128849, 58194 => 128850, 58197 => 128853, 58206 => 128862, 58215 => 128871, 58219 => 128875, 58222 => 128878, 58224 => 128880, 58227 => 128883 ];

	$inputList = mb_str_split($strInput, 1, 'UTF-8');
	$outputList = array();
	foreach ($inputList as $char) {
		$charCode = mb_ord($char, 'UTF-8');  // returns a decimal int
		if (array_key_exists($charCode, $gentium2sans)) {
			$newCharCode = $gentium2sans[$charCode];
			$outputList[] = mb_chr($newCharCode, 'UTF-8');
		} else {
			$outputList[] = $char;
		}
	}
	$strOutput = implode('', $outputList);
	
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
$d1stringIn = $doc1data[3];
// fwrite($log, "doc1alch = ". $doc1alch . "\n");
// fwrite($log, "doc1title = ". $doc1title . "\n");
// fwrite($log, "doc1mstitle = ". $doc1mstitle . "\n");
// fwrite($log, "d1string = ". $d1string . "\n");

$doc2row = mysqli_query($connection, $getdoc2data);
$doc2data = mysqli_fetch_row($doc2row);

$doc2alch = $doc2data[0];
$doc2title = $doc2data[1];
$doc2mstitle = $doc2data[2];
$d2stringIn = $doc2data[3];
// fwrite($log, "doc2alch = ". $doc2alch . "\n");
// fwrite($log, "doc2title = ". $doc2title . "\n");
// fwrite($log, "doc2mstitle = ". $doc2mstitle . "\n");
// fwrite($log, "d2string = ". $d2string . "\n");

/* MANUAL SWITCH TO FORCE TRANSLATION OF FRAG STRINGS FROM GENTIUM FONT TO NEWTON SANS */
$gentium = true;
// change the font to get correct NewtonSans characters if data was encoded for Gentium Newton
if ($gentium) {
	$d1string = translateGentium($d1stringIn);
	$d2string = translateGentium($d2stringIn);
} else {
	$d1string = $d1stringIn;
	$d2string = $d2stringIn;	
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
<link href="css/style.css" rel="stylesheet" media="all" />
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
