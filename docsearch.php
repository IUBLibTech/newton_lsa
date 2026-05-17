<?php
//  FUNCTIONS
//####################
function cosine_sim($vecstring1, $vecstring2, $sumsq1, $sumsq2) {
	//echo "vecstring1: ".$vecstring1."<br/>";
	$vector1 = explode(" ", $vecstring1);
	$vector2 = explode(" ", $vecstring2);
	$numdims = count($vector1);
	//echo "count: ".$numdims.", ";
	$dotproduct = 0.0;
	$increment = 0.0;
	for ($d = 0; $d < $numdims; $d++) {
		$increment = $vector1[$d] * $vector2[$d];
		$dotproduct = $dotproduct + $increment;
	}
	$magnitude = ($sumsq1 * $sumsq2);
	//echo "magn: ".$magnitude.", ";
	$rawcorrelation = $dotproduct / $magnitude;
	//echo "raw corr: ".$rawcorrelation.". ";
	$correlation = round($rawcorrelation, 4);
	return $correlation;
}

function combine_key($int1, $int2) {
	$newkey = $int1 * 1000000000;
	$newkey = $newkey + $int2;
	return $newkey;
}

function extract_key2($keyint) {
	$key2 = $keyint % 1000000000;
	return $key2;
}

function extract_key1($keyint, $mod) {
	$key0 = $keyint - $mod;
	$key1 = $key0 / 1000000000;
	return $key1;
}

//  MAIN PROCEDURE
//####################
// run the search over the correlation matrix

// open the database
$textSite = "";
$connection = null;
require_once 'functions/mysql_connection.php';

$list = $_GET['list'];
$frags = $_GET['frags'];
$scope = $_GET['scope'];
$outf = $_GET['outf'];
$bound = $_GET['bound'];
$qs = $_GET['qs'];

$hash_value = md5($list."|".$frags."|".$scope."|".$bound."|".$qs);

//begin setup
$listTable = "doc250_list";
$correlationTable = "doc250_cosines";
if ($frags == "ch1000") {
	$listTable = "doc1000_list";
	$correlationTable = "doc1000_cosines";
}

//  $qs will contain doc IDs (ALCHnn_ALCHnn) or chunk IDs (n_n_n) or ALL
// we'll use an array to track user's selections passed in $qs

// fill $selected by retrieving user choices from doclist
$selected = array();
if ($qs == "ALL") {
	// grab everything from doclist
	$selectalldocs = "SELECT id, ctitle FROM ".$listTable;
	if (!empty($connection)) {
		$allset = mysqli_query($connection, $selectalldocs);
	}

	while($row_all = mysqli_fetch_row($allset)) {
		$selected[$row_all[0]] = $row_all[1];
	}
	mysqli_free_result($allset);
}
else {
	$qset = explode("_", $qs);
	$matchID = "";
	$matchcount = 0;
	foreach($qset as $qn) {
		if ($matchcount > 0) {
			$matchID = $matchID.",'".$qn."'";
		}
		else {
			$matchID = $matchID."'".$qn."'";
			$matchcount = 1;
		}
	}
	unset($qset);
	
	$wherecolumn = "zz";
	if ($list == "wholedocs") {
		$wherecolumn = "alch";
	}
	else if ($list == "chunks") {
		$wherecolumn = "id";
	}

	$selectsome = "SELECT id, ctitle FROM ".$listTable." WHERE ".$wherecolumn." IN (".$matchID.")";

	$someset = mysqli_query($connection, $selectsome);

	while($row_some = mysqli_fetch_row($someset)) {
		$selected[$row_some[0]] = $row_some[1];
	}
	mysqli_free_result($someset);
}

$results = NULL;
$getresults = "";
if ($outf == "ranked" || $outf == "graph") {
	$getresults = "SELECT * FROM ".$correlationTable." WHERE correlation >= ".$bound. " ORDER BY correlation DESC";
}
else if ($outf == "pages") {
	// first build a WHERE-IN string
	$matchchunks = "";
	$matchchunkcount = 0;
	foreach($selected as $sk => $sv) {
		if ($matchchunkcount > 0) {
			$matchchunks = $matchchunks.",".$sk;
		}
		else {
			$matchchunks = $sk;
			$matchchunkcount = 1;
		}
	}
	
	// create temporary table pages
	$removepagestemp = "DROP TEMPORARY TABLE IF EXISTS pages";
	mysqli_query($connection, $removepagestemp);
	$makepagestemp = "CREATE TEMPORARY TABLE pages (
			correlation TEXT, 
			doc1 INT NOT NULL, 
			doc2 INT NOT NULL)";
	mysqli_query($connection, $makepagestemp);
	
	//  load the temporary table
	$getdoc1 = "INSERT INTO pages (correlation, doc1, doc2)
			SELECT correlation, doc1, doc2 FROM ".$correlationTable." WHERE doc1 IN (".$matchchunks.")";
	$getdoc2 = "INSERT INTO pages (correlation, doc1, doc2)
			SELECT correlation, doc2, doc1 FROM ".$correlationTable." WHERE doc2 IN (".$matchchunks.")";
	mysqli_query($connection, $getdoc1);
	mysqli_query($connection, $getdoc2);
	unset($matchchunks);
	
	$getresults = "SELECT * FROM pages WHERE correlation >=".$bound." ORDER BY doc1, doc2";
}
$results = mysqli_query($connection, $getresults);

// load the chunk list so we can write out titles and files names for displaycorrs.php
$getchunksdata = "SELECT id, ctitle, alch FROM doc250_list";
$displayscript = "javascript:openViewer(\"ch250\", \"";
if ($frags == "ch1000") {
	$getchunksdata = "SELECT id, ctitle, alch FROM doc1000_list";
	$displayscript = "javascript:openViewer(\"ch1000\", \"";
}
$chunksdata = mysqli_query($connection, $getchunksdata);

$chunks = array();
$chunks[] = "base"; // fills that pesky $chunks[0] member
while ($outchunk = mysqli_fetch_array($chunksdata)) {
	$chunks[] = $outchunk;
}

// now we produce the outputs and send them to the user
if ($outf == "ranked") {
	$outputcount = 0;
	echo "<table class='lsa-resultsTable'>";
	while ($outrow = mysqli_fetch_row($results)) {

		$new1 = $outrow[1];
		$new2 = $outrow[2];
		
		$writethis = 0;
		if ($qs == "ALL") {
			$writethis = 1;
		}
		elseif ($scope == "allcorrs") {
			if (array_key_exists($new1, $selected) || array_key_exists($new2, $selected)) {
				$writethis = 1;
			}
		}
		elseif ($scope == "onlyselected") {
			if (array_key_exists($new1, $selected) && array_key_exists($new2, $selected)) {
				if ($selected[$new1] != $selected[$new2]) {
					$writethis = 1;
				}
			}
		}
		elseif ($scope == "internal") {
			if (array_key_exists($new1, $selected) && array_key_exists($new2, $selected)) {
				// the user interface only allows one document/ALCH value when scope is "internal"
				// so if both keys are in $selected, both belong to the chosen document.
				//if ($selected[$new1] == $selected[$new2]) {
					$writethis = 1;
				//}
			}
		}
		
		if ($writethis == 0) {
			continue;
		}

		$corr = $outrow[0];
		
		$item1_title = $chunks[$new1][1];
		$item1_file = $chunks[$new1][0];
		$item2_title = $chunks[$new2][1];
		$item2_file = $chunks[$new2][0];
		echo "<tr><td><a href ='".$displayscript.$item1_file."\", \"".$item2_file."\", \"".$corr."\")'>";
		echo $corr;
		echo "</a></td><td>".$item1_title."</td><td>".$item2_title."</td></tr>";
		$outputcount++;
	}
	if ($outputcount == 0) {
		echo "<tr><td>No results greater than or equal to ".$bound." were found.</td></tr>";
	}
	echo "</table>";
}  //  end of "ranked" jobs
elseif ($outf == "pages") {
	echo "<table class='lsa-resultsTable'>";
	while ($outrow = mysqli_fetch_row($results)) {

		$new1 = $outrow[1];
		$new2 = $outrow[2];
		
		if ($selected[$new2] == $qs) {
			continue;
		}
		
		$writethis = 0;
		if (array_key_exists($new1, $selected) || array_key_exists($new2, $selected)) {
			if ($selected[$new1] != $selected[$new2]) {
				$writethis = 1;
			}
		}
		
		if ($writethis == 0) {
				continue;
		}
	
		$outputcount = 0;
		
		$corr = $outrow[0];
		
		$item1_file = $chunks[$new1][0];
		$item1_title = $chunks[$new1][1];
		$item2_file = $chunks[$new2][0];
		$item2_title = $chunks[$new2][1];
		echo "<tr><td width='200'>".$item1_title."</td><td width='200'>".$item2_title."</td>";
		echo "<td><a href ='".$displayscript.$item1_file."\", \"".$item2_file."\", \"".$corr."\")'>";
		echo $corr;
		echo "</a></td></tr>";
		$outputcount++;
	}

	if ($outputcount == 0) {
		echo "<tr><td>No results greater than or equal to ".$bound." were found.</td></tr>";
	}
	echo "</table>";
}
elseif ($outf == "graph") {

	$nodes = array();
	$edges = array();
	
	// hidden loop to create nwb files with all nodes showing---connected and unconnected
	// we can invoke this ouselves to produce specialty graphs but suppress it otherwise

	// echo "<script>alert('have entered graph in docsearch');</script>";
	
	$outputcount = 0;
	while ($outrow = mysqli_fetch_row($results)) {

		$new1 = $outrow[1];
		$new2 = $outrow[2];
		
		$writethis = 0;
		if ($qs == "ALL") {
			$writethis = 1;
		}
		elseif ($scope == "allcorrs") {
			if (array_key_exists($new1, $selected) || array_key_exists($new2, $selected)) {
				$writethis = 1;
			}
		}
		elseif ($scope == "onlyselected") {
			if (array_key_exists($new1, $selected) && array_key_exists($new2, $selected)) {
				//if ($selected[$new1] != $selected[$new2]) {
					$writethis = 1;
				//}
			}
		}
		elseif ($scope == "internal") {
			if (array_key_exists($new1, $selected) && array_key_exists($new2, $selected)) {
				//if ($selected[$new1] == $selected[$new2]) {
					$writethis = 1;
				//}
			}
		}
		
		if ($writethis == 0) {
			continue;
		}

		$corr = $outrow[0];
		$item1_title = $chunks[$new1][1];
		$item1_file = $chunks[$new1][2];
		$item2_title = $chunks[$new2][1];
		$item2_file = $chunks[$new2][2];

		$newkey = combine_key($new1, $new2);
		$edges[$newkey] = $corr;
		if (!in_array($new1, $nodes)) {
			$nodes[] = $new1;
		}
		if (!in_array($new2, $nodes)) {
			$nodes[] = $new2;
		}
		
		$outputcount++;
	}
	if ($outputcount == 0) {
		echo "No results greater than or equal to ".$bound." were found.<br/>";
	}
	else {
		// we can write the graph
		// echo "<script>alert('there are returned results  -line 384');</script>";
		
		$newgraph = "graph-". $hash_value.".nwb";

		$graphstring = "*Nodes".PHP_EOL."id*int label*string docid*string";
		// echo "<script>alert(`".$graphstring."`);</script>";
		
		$nodeIdx = array();
		$nodecounter = 1;
		// first write all the nodes that were selected
		foreach ($nodes as $node) {
			$nodeIdx[$node] = $nodecounter;
			$nextNode = $nodecounter.' "'.$chunks[$node][1].'" "'.$chunks[$node][2].'"';

			$graphstring = $graphstring . PHP_EOL . $nextNode;
			$nodecounter++;
		}
		// echo "<script>alert(`".$graphstring."`);</script>";
		
		// Now the edges.

		$graphstring = $graphstring. PHP_EOL ."*UndirectedEdges";
		$graphstring = $graphstring. PHP_EOL . "source*int\ttarget*int\tweight*float";

		foreach($edges as $bigkey => $edgecorr) {
			$ekey2 = extract_key2($bigkey);
			$ekey1 = extract_key1($bigkey, $ekey2);

			$nextEdge = $nodeIdx[$ekey1]."\t".$nodeIdx[$ekey2]."\t".$edgecorr;
			$graphstring = $graphstring . PHP_EOL . $nextEdge;
		}
		// echo "<script type='text/javascript'>console.log(`".$graphstring."`)</script>";

		// define the graph download function
		echo "<script type='text/javascript'>
			function downloadGraph(contents, filename) {
				const graphBlob = new Blob([contents], { type: 'text/plain' });
				const graphUrl = URL.createObjectURL(graphBlob);
				const graphLink = document.createElement('a');
				graphLink.href = graphUrl;
				graphLink.download = filename;
				document.body.appendChild(graphLink);
				graphLink.click();
				document.body.removeChild(graphLink);
				URL.revokeObjectURL(graphUrl);
			}
		</script>";

		// start downloading the graph and inform the user
		echo  "<br/><br/>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;The requested graph file, named '$newgraph' should have downloaded to your browser's default download location.</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;Nodes and edges are encoded in Network Work Bench (.nwb) format for 
				use in the Sci<sup>2</sup> network-graph application, but the file is
				plain text, so it can be read in other editors.</p>
				<br/><br/>
				<script type='text/javascript'>
					const graphContents = `$graphstring`;
					const graphFile = '$newgraph';
					downloadGraph(graphContents, graphFile);
				</script>";
		
	}
}

// all finished
unset($selected);
unset($chunks);
mysqli_free_result($results);
mysqli_close($connection);
return;
?>
