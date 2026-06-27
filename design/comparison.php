<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title><?php echo $tabTitle; ?></title>

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

<table class="content" style="margin-left: 10px; font-family: 'GentiumNewton'; font-size: 16px; width: 100%;">
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