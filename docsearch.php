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

$logfile = "log/docsearch.txt";
$log = fopen($logfile, "w");
/*if (!$log) {
	echo "no log";
	return;
}*/
fwrite($log, "open docsearch: ".memory_get_usage()." at ".date("M d g:i:s")."\n");

$homeSite = $_GET['hs'];
$mdb = $_GET['mdb'];
$list = $_GET['list'];
$frags = $_GET['frags'];
$scope = $_GET['scope'];
$outf = $_GET['outf'];
$bound = $_GET['bound'];
$qs = $_GET['qs'];


// added by TDBOWMAN (to get live path working)
// this probably should be more elegant
if ($mdb == 0) {
	$mdb = 1;
}


$hash_value = md5($mdb."|".$list."|".$frags."|".$scope."|".$bound."|".$qs);

if($outf == "graph" &&  file_exists("graphs/graph-".$hash_value.".nwb")) {
  $downloadpath = "graphs/graph-".$hash_value.".nwb";
      echo  "<br/><br/>
	  		 <small>(Right-click the link and use <em>Save link as...</em> to get a copy for viewing in Network Workbench or an editor.)</small>
			 <br/><br/>
			 <table class='lsa-resultsTable'>
					<tr>
						<td>NWB network graph file (cached):</td><td><a href='".$downloadpath."'>Link for download.</a> Give it a new name.</td>
					</tr>
			 </table>
			 <br/><br/>";
     exit;
}

fwrite($log, "mdb=$mdb, list=$list, frags=$frags, scope=$scope, outf=$outf, bound=$bound, qs=$qs\n");

// open the database
// use $mdb to open $connection
require_once 'functions/mysql_connection.php';

//begin setup
$listTable = "doc250_list";
$correlationTable = "doc250_cosines";
if ($frags == "ch1000") {
	$listTable = "doc1000_list";
	$correlationTable = "doc1000_cosines";
}
fwrite($log, "initialized: memoryused: ".memory_get_usage()."\n");

//  $qs will contain doc IDs (ALCHnn_ALCHnn) or chunk IDs (n_n_n) or ALL
// we'll use an array to track user's selections passed in $qs

// fill $selected by retrieving user choices from doclist
$selected = array();
if ($qs == "ALL") {
	// grab everything from doclist
	$selectalldocs = "SELECT id, ctitle FROM ".$listTable;
	$allset = mysqli_query($connection, $selectalldocs);
	fwrite($log, "allset query made. memory used: ".memory_get_usage()."\n");

	while($row_all = mysqli_fetch_row($allset)) {
		$selected[$row_all[0]] = $row_all[1];
	}
	mysqli_free_result($allset);
	fwrite($log, "allset released, selected array loaded. memory used: ".memory_get_usage()."\n");
}
else {
	$qset = explode("_", $qs);
	fwrite($log, "user query string in qset array. memory used: ".memory_get_usage()."\n");
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
	fwrite($log, "qset unset. memory used: ".memory_get_usage()."\n");
	
	$wherecolumn = "zz";
	if ($list == "wholedocs") {
		$wherecolumn = "alch";
	}
	else if ($list == "chunks") {
		$wherecolumn = "id";
	}
	
	$selectsome = "SELECT id, ctitle FROM ".$listTable." WHERE ".$wherecolumn." IN (".$matchID.")";
	$someset = mysqli_query($connection, $selectsome);
	fwrite($log, "someset query made. memory used: ".memory_get_usage()."\n");

	while($row_some = mysqli_fetch_row($someset)) {
		$selected[$row_some[0]] = $row_some[1];
	}
	mysqli_free_result($someset);
	fwrite($log, "someset released, selected array loaded. memory used: ".memory_get_usage()."\n");
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
	fwrite($log, "matchchunks = ".$matchchunks."\n");
	
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
fwrite($log, "results query executed. memory used: ".memory_get_usage()."\n");

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
fwrite($log, "chunks name array initialized. memory used: ".memory_get_usage()."\n");

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

	fwrite($log, "Entered graph.\n");
	$nodes = array();
	$edges = array();
	
	// hidden loop to create nwb files with all nodes showing---connected and unconnected
	// we can invoke this ouselves to produce specialty graphs but suppress it otherwise
	/*
	$filler = 1;
	foreach ($chunks as $chunkxx) {
		$nodes[] = $filler;
		$filler++;
	}
	*/
	
	$graphstring = "_________<br>";
	
	$outputcount = 0;
	while ($outrow = mysqli_fetch_row($results)) {

		//fwrite($log, "Graph loop".$outputcount.". Memory: ".memory_get_usage()."\n");
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
		$newgraph = "graph-". $hash_value.".nwb";
		$downloadpath = "graphs/$newgraph";
		#$graph = fopen($graphfile, 'w');
		$graph = fopen("graphs/$newgraph", 'w');
		if ($graph == '' || $graph == 0) {
			fwrite($log, "Can't open graph file.\n");
		}
		chmod($graph, 0666);  # make sure the file is user/group writable.
		
		fwrite($graph, '*Nodes'."\n");
		fwrite($graph, 'id*int label*string docid*string lemmaid*string'."\n");
		$graphstring = $graphstring.'*Nodes'."<br>";
		$graphstring = $graphstring.'id*int label*string docid*string lemmaid*string'."<br>";
		
		$nodeIdx = array();
		$nodecounter = 1;
		// first write all the nodes that were selected
		foreach ($nodes as $node) {
			$nodeIdx[$node] = $nodecounter;
			fwrite($graph, $nodecounter.' "'.$chunks[$node][1].'" "'.$chunks[$node][2].'" "z2z"'."\n");
			$graphstring = $graphstring.$nodecounter.' "'.$chunks[$node][1].'" "'.$chunks[$node][2].'" "z2z"'."<br>";
			$nodecounter++;
		}
		
		fwrite($graph, "*UndirectedEdges\n");
		fwrite($graph, "source*int\ttarget*int\tweight*float");
		$graphstring = $graphstring."*UndirectedEdges<br>";
		$graphstring = $graphstring."source*int##target*int##weight*float";
		foreach($edges as $bigkey => $edgecorr) {
			$ekey2 = extract_key2($bigkey);
			$ekey1 = extract_key1($bigkey, $ekey2);
			
			fwrite($graph, "\n".$nodeIdx[$ekey1]."\t".$nodeIdx[$ekey2]."\t".$edgecorr);
			$graphstring = $graphstring."<br>".$nodeIdx[$ekey1]."##".$nodeIdx[$ekey2]."##".$edgecorr;
		}
		
		fclose($graph);
		fwrite($log, "Closed graph file.\n");
		$graphstring = $graphstring."<br>___________";
		
		// change the permissions for the new graph file
		chmod("graphs/$newgraph", 0644);
		
		echo  "<br/><br/>
			   <small>(Right-click the link and use <em>Save link as...</em> to get a copy for viewing in Network Workbench or an editor.)</small>
			   <br/><br/>
			   <table class='lsa-resultsTable'>
				<tr>
					<td>NWB network graph file:</td><td><a href='".$downloadpath."'>Link for download.</a> Give it a new name.</td>
				</tr>
				</table>";
		
	}
}

// all finished
unset($selected);
unset($chunks);
mysqli_free_result($results);
mysqli_close($connection);
fwrite($log, "arrays and results freed, connection closed. memory used: ".memory_get_usage()."\n");
fclose($log);
return;
?>
