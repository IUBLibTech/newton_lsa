<?php
//  Load arrays of labels for graph nodes
require "design/chym_ms_arrays.php";

//  DECLARATION of Random number band (0.3 - 0.7)
use Random\Randomizer;
use Random\IntervalBoundary;
$random_band = new Randomizer();
$y_min = 0.4;
$y_max= 0.6;

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
	else 
	{
		// we can write the graph
		// echo "<script>alert('there are returned results  - line 337');</script>";
		require_once("mexitek/Color.php");

		$newgraph = "graph-". $hash_value.".json";
		
		$passageIdx = array();
		$npidIdx = array();
		$nodeNpid = array();
		$npidCounter = 0;
		$nodeIdx = array();
		$nodecounter = 0;
		// first write all the nodes that were selected
		foreach ($nodes as $node) {
			$nodecounter++;
			$nodeIdx[$node] = $nodecounter;
			$dbid = $chunks[$node][0];
			$n_title = $chunks[$node][1];
			$npid = $chunks[$node][2];

			$nodeNpid[$nodecounter] = $npid;

			$paddedCounter = sprintf('%03d', $nodecounter);

			// X will vary with ALCH
			// Y will vary with sort order of n_title in its ALCH
			// to calculate X and Y, sort the list when finished, by ALCH then title
			$sortString = "{$npid}^{$n_title}^{$dbid}^{$paddedCounter}";
			$passageIdx[] = $sortString;
			// and count the number of ALCH's found
			if (!array_key_exists($npid, $npidIdx)) {
				$npidIdx[$npid] = 1;
				$npidCounter++;
			} else {
				$npidIdx[$npid] = $npidIdx[$npid] + 1;
			}
		}
		$npidIdx_count = count($npidIdx);  // number of unique ALCH documents in graph
		// echo "<script type='text/javascript'>console.log(" . $see_npidIdx_count . ");</script>";
		// echo "<script type='text/javascript'>alert('see_npidIdx_count in console?');</script>";

		$hsl = array();  // will support hsl color specs: 'H' hue, 'S' saturation, 'L' lightness/luminosity
		$hsl['H'] = 180;
		$hsl['S'] = 0.8;
		$hsl['L'] = 0.5;

		$positionIdx = array();
		// sort the passage indices
		sort($passageIdx);
		$last_npid = "";
		$n_i = 1;
		$n_j = 1;
		foreach ($passageIdx as $node_row) {
			$node_row_parts = explode("^", $node_row);
			$npid = $node_row_parts[0];
			$n_title = $node_row_parts[1];
			$n_dbid = $node_row_parts[2];
			$n_key = $node_row_parts[3];

			if ($npid == $last_npid) {
				$n_j++;
			} else {
				$last_npid = $npid;
				$n_i++;
				$n_j = 1;
			}
			$n_x = $n_i / ($npidCounter + 2);
			$see_n_x = sprintf("%.5f", $n_x);
			$n_y = 1 - ($n_j / ($npidIdx[$npid] + 2));
			if ($npidIdx[$npid] == 1) {
				// $n_y = $n_y - ($n_i * 0.02);
				$n_y = $random_band->getFloat($y_min, $y_max, IntervalBoundary::ClosedOpen);
			}
			$see_n_y = sprintf("%.5f", $n_y);

			// color
			$n_hue = $n_i * 12;  // adjust the hue value
			$hsl['H'] = $n_hue;
			$n_color = Color::hslToHex($hsl);  // $n_color is a hex string value

			$n_stem = $StemFromNPID[$npid];
			$n_abbrev = $LabelFromNPID[$npid];
			
			$n_label = str_replace($n_stem, $n_abbrev, $n_title);
			$n_label = str_replace("f.", "", $n_label);

			$next_p = "{ \"key\": \"".$n_key."\", \"attributes\": ";
			$next_p = $next_p . "{ \"label\": \"" . $n_label . "\", ";
			$next_p = $next_p . "\"title\": \"" . $n_title . "\", ";
			$next_p = $next_p . "\"abbrev\": \"" . $n_label . "\", ";
			$next_p = $next_p . "\"dbid\": \"" . $n_dbid . "\", ";
			$next_p = $next_p . "\"orig_x\": " . $see_n_x . ", ";
			$next_p = $next_p . "\"orig_y\": " . $see_n_y . ", ";
			$next_p = $next_p . "\"x\": " . $see_n_x . ", ";
			$next_p = $next_p . "\"y\": " . $see_n_y . ", ";
			$next_p = $next_p . "\"type\": \"circle\", ";
			$next_p = $next_p . "\"size\": 10, ";
			$next_p = $next_p . "\"orig_color\": \"#{$n_color}\", ";
			$next_p = $next_p . "\"color\": \"#{$n_color}\" } }";

			$positionIdx[] = $next_p;
		}
		
		sort($positionIdx);
		$see_positionIdx = json_encode($positionIdx);
		// echo "<script type='text/javascript'>console.log(" . $see_positionIdx . ");</script>";
		// echo "<script type='text/javascript'>alert('positionIdx has been assembled');</script>";

		$all_nodes = "";
		$started = 0;
		foreach ($positionIdx as $next_pos) {
			if ($started) {
				$all_nodes = $all_nodes . ",\n" . $next_pos;
			} else {
				$all_nodes = $next_pos;
				$started = 1;
			}
		}
		// echo "<script type='text/javascript'>console.log(" . $all_nodes . ");</script>";
		// echo "<script type='text/javascript'>alert('all_nodes has been assembled');</script>";
		
		// Now the edges.

		$edgeIdx = array();
		$edgeCounter = 1;
		foreach($edges as $bigkey => $edgecorr) {

			$see_edgecorr = json_encode($edgecorr);
			// echo "<script type='text/javascript'>console.log(" . $see_edgecorr . ");</script>";
			// echo "<script type='text/javascript'>alert('see_edgecorr');</script>";

			$ekey2 = extract_key2($bigkey);
			$ekey1 = extract_key1($bigkey, $ekey2);

			$key1_int = $nodeIdx[$ekey1];
			$key1_str = sprintf("%03d", $key1_int);

			$key2_int = $nodeIdx[$ekey2];
			$key2_str = sprintf("%03d", $key2_int);

			if ($key1_int < $key2_int) {
				$source_str = $key1_str;
				$target_str = $key2_str;
			} else {
				$source_str = $key2_str;
				$target_str = $key1_str;
			}

			$edge_key = sprintf("%03d", $edgeCounter);

			// if ($nodeNpid[$e_source] == $nodeNpid[$e_target]) {
			// 	$e_type = "curved";
			// } else {
			// 	$e_type = "line";
			// }
			$e_type = "line";

			$edgefloat = floatval($edgecorr);
			if ($edgefloat >= 0.9) {
				$e_size = 6;
			}
			elseif ($edgefloat >= 0.8) {
				$e_size = 4;
			}
			elseif ($edgefloat >= 0.7) {
				$e_size = 3;
			}
			else {
				$e_size = 2;
			}
			// $roundcorr = sprintf("%.5d", $edgefloat);

			$e_attributes = "\"weight\": {$edgecorr}, \"type\": \"{$e_type}\", \"size\": \"{$e_size}\", \"color\": \"lightgray\"";
			
			$nextEdge = "{ \"key\": \"{$edge_key}\", \"source\": \"{$source_str}\", \"target\": \"{$target_str}\", ";
			$nextEdge = $nextEdge . "\"attributes\": { {$e_attributes} } }";

			$edgeIdx[] = $nextEdge;

			$edgeCounter++;
		}
		
		$see_edgeIdx = json_encode($edgeIdx);

		$started2 = 0;
		$all_edges = "";
		foreach ($edgeIdx as $next_edge) {
			if ($started2) {
				$all_edges = $all_edges . ",\n" . $next_edge;
			} else {
				$all_edges = $next_edge;
				$started2 = 1;
			}
		}

		// the full graph file can be assembled now
		$graph_head = "{ \"attributes\": { \"name\": \"{$newgraph}\" },\n";
		$graph_head = $graph_head . "\"options\": { \"type\": \"undirected\", ";
		$graph_head = $graph_head . "\"multi\": false, \"allowSelfLoops\": false },\n";
		$graph_head = $graph_head . "\"nodes\": [\n";

		$graph_bridge = " ],\n \"edges\": [\n";

		$graph_end = "\n] }";

		$graph_final = $graph_head;
		$graph_final = $graph_final . $all_nodes;
		$graph_final = $graph_final . $graph_bridge;
		$graph_final = $graph_final . $all_edges;
		$graph_final = $graph_final . $graph_end;

		$see_graph_final = json_encode($graph_final);

		// POINT OF RETURN TO index.php for GRAPHS

		echo "<div id='graph_div'>";

		// return the graphology-ready JSON graph to the main page
		echo "<div id='graph_data' style='display:none;'>";
		echo $graph_final;
		// echo $see_graph_final;
		echo "</div>";

		echo "<div style='float: left; width: 360px; border: 2px solid black; box-sizing: border-box; padding: 20px; margin-left: 10px' id='graphPanel'>";
		// make the "neighbors" button visible
		// echo "<script type='text/javascript'>
		// 	let sideBySideBtn = document.querySelector('#lsa-sidebyside');
		// 	sideBySideBtn.style.display = 'block';
		// </script>";
		// instruction text
		echo "<p>&bullet; Click on a node to display that passage's title and display details.</p>";
		echo "<p>&bullet; Right-click on a node to highlight that passage and neighbor passages with
		which it shares significant vocabulary. The base passage turns maroon and its neighbors
		turn indian red.</p>
		<p>&bullet; Click on a neighbor (indian red) to highlight its relationship to the base passage. 
		The selected neighbor will turn to red. You can now view those passages side by side by
		clicking the button below.</p>
		<p>&bullet; Right-click on any highlighted node to end the highlighting.</p>";
		echo "<input type='button' id='lsa-sidebyside' value='Show base node (maroon) and\nselected neighbor (red) side by side' style='style='display: none; height: 40px; width: 500px' onclick='showCounterparts()'>";
		echo "<p>&bullet; Refresh the web page to start another graph or search (Ctl-R or Command-R).</p>";
		echo "<br/><p>NOTE: Each node represents a passage or chunk of about 250 words from one of Newton's
		alchemical manuscripts.</p>
		<p>Each passage begins on indicated folio but many folios contain more than
		250 words, so there can be successive cuts on the same folio. The last cut may include the
		top of the next page while the first cut may not begin at the top of its own folio either.</p>
		<p>Each edge indicates that that pair of nodes or passages has a cosine similarity greater than 
		or equal to the requested cosine threshold.</p>";
		echo "</div>";  // end id='graphPanel

		// define the div where the graph will be drawn
		echo "<div id='sigmaGraph' style='width: 1200px; height: 1000px; background: cornsilk; border: 2px solid black; box-sizing: border-box; float: right' oncontextmenu='event.preventDefault();'></div>";
		
		// hidden storage element for the black "base" nodeId whose neighbors are enlarged and highlighted in red
		echo "<textarea id='baseArea' style='display:none'></textarea>";

		// hidden storage element for dark blue "counterpart" nodeID selected for side-by-side display
		echo "<textarea id='counterpartArea' style='display:none'></textarea>";

		// hidden storage element for the chunk size to choose between 250-word and 1000-word chunks
		echo "<textarea id='chunkSizeArea' style='display:none'>".$frags."</textarea>";

		// hidden storage element for the chunk size to choose between 250-word and 1000-word chunks
		echo "<textarea id='weightArea' style='display:none'></textarea>";

		echo "<script type='text/javascript'>
			const graphdata = document.getElementById('graph_data').textContent;
			const serializedGraphData = JSON.parse(graphdata);

			const Graph = window.graphology;
			const userGraph = new Graph();

			const SigmaRend = window.Sigma;
			// const NodeSquareProgram = window.NodeSquareProgram;
			// const EdgeCurveProgram = window.EdgeCurveProgram;

			userGraph.import(serializedGraphData);

			const container = document.getElementById('sigmaGraph');
			container.addEventListener('contextmenu', function(event) {
				event.preventDefault();
			});

			const sigma = new SigmaRend(userGraph, container, {
				renderLabels: true,
				renderEdgeLabels: false,
				labelSize: 15,
				nodeReducer: (node, data) => {
					return data;
				}
			});

			sigma.on('clickNode', (payload) => {
				const nodeId = payload.node;
				let nodeLabel = userGraph.getNodeAttribute(nodeId, 'label');
				let nodeTitle = userGraph.getNodeAttribute(nodeId, 'title');
				let nodeAbbrev = userGraph.getNodeAttribute(nodeId, 'abbrev');
				let nodeNpid = userGraph.getNodeAttribute(nodeId, 'npid');
				let nodeDbid = userGraph.getNodeAttribute(nodeId, 'dbid');
				let nodeX = userGraph.getNodeAttribute(nodeId, 'x');
				let nodeY = userGraph.getNodeAttribute(nodeId, 'y');
				let nodeColor = userGraph.getNodeAttribute(nodeId, 'color');
				let nodeSize = userGraph.getNodeAttribute(nodeId, 'size');

				if (nodeSize != 15) {
					// Display the node information in an alert or console log
					let bindalert = 'Selected Node ID: ' + nodeId + '\\n';
					bindalert = bindalert + 'Label: ' + nodeLabel + '\\n\\n';
					bindalert = bindalert + 'Title: ' + nodeTitle + '\\n\\n';
					bindalert = bindalert + 'Abbrev: ' + nodeAbbrev + '\\n';
					bindalert = bindalert + 'DBID: ' + nodeDbid + '\\n';
					bindalert = bindalert + 'X: ' + nodeX + ', ';
					bindalert = bindalert + 'Y: ' + nodeY;
					alert(bindalert);
				}
				else if (nodeColor == 'indianred') {
					// first change any previous 'red' back to 'indian red'
					let priorIds = document.getElementById('counterpartArea').textContent;
					if (priorIds != '') {
						let priorIdList = priorIds.split(';');
						let priorNodeId = priorIdList[0];
						let priorNodeDbid = priorIdList[1];

						updateNodeAttribute(userGraph, sigma, priorNodeId, 'color', 'indianred');

						if (priorNodeDbid == nodeDbid) {
							// this means the user has just clicked the same neighbor again, so we will restore the graph and exit this event handler
							restoreGraph(userGraph, sigma);
							return;
						}
					}
					
					// signal to user that node has been selected -- change the node's color
					updateNodeAttribute(userGraph, sigma, nodeId, 'color', 'red');

					let chunkSize = chunkSizeArea.value;

					let baseIdList = document.getElementById('baseArea').textContent;
					// console.log(baseIdList);
					// alert('baseIdList');
					let baseIds = baseIdList.split(';');
					let baseId = baseIds[0];
					let baseIdInt = parseInt(baseId, 10);
					let baseDbid = baseIds[1];
					let baseDbidInt = parseInt(baseDbid, 10);

					let counterpartIds = nodeId + ';' + nodeDbid;
					document.getElementById('counterpartArea').textContent = counterpartIds;

					let nodeIdInt = parseInt(nodeId, 10);
					let nodeDbidInt = parseInt(nodeDbid, 10);

					// console.log(baseIdInt + ', ' + nodeIdInt);
					// alert('baseIdInt and nodeIdInt');
					// console.log(baseDbidInt + ', ' + nodeDbidInt);
					// alert('baseDbidInt and nodeDbidInt');

					let edgeIdDeduced = '000';
					if (baseIdInt < nodeIdInt) {
						edgeIdDeduced = userGraph.edge(baseId, nodeId);
					} else {
						edgeIdDeduced = userGraph.edge(nodeId, baseId);
					}
					// console.log(edgeIdDeduced);
					// alert('edgeIdDeduced');

					let edgeWeight = userGraph.getEdgeAttribute(edgeIdDeduced, 'weight');
					// console.log(edgeWeight);
					// alert('edgeWeight');
					document.getElementById('weightArea').textContent = edgeWeight;

					// everything ready for button request to view base and node texts side-by-side
					return;
				}
			});

			sigma.on('rightClickNode', (payload) => {
				const nodeId = payload.node;
				let nodeDbid = userGraph.getNodeAttribute(nodeId, 'dbid');
				let baseIds = nodeId + ';' + nodeDbid;

				// prevent possibility of opening more than one base node
				// baseArea's contents should be empty to ensure a fresh start
				queryIds = document.getElementById('baseArea').textContent;
				// console.log(queryIds);
				// alert('queryIds');
				if (queryIds != '') {
					restoreGraph(userGraph, sigma);
					return;
				}
				
				document.getElementById('baseArea').textContent = baseIds;

				let size = userGraph.getNodeAttribute(nodeId, 'size');
				let color = userGraph.getNodeAttribute(nodeId, 'color');
				let orig_color = userGraph.getNodeAttribute(nodeId, 'orig_color');

				let nodeTitle = userGraph.getNodeAttribute(nodeId, 'title');
				let nodeAbbrev = userGraph.getNodeAttribute(nodeId, 'abbrev');

				let n_neighbors = userGraph.neighbors(nodeId);
				// console.log(nodeId, n_neighbors);

				let n_edges = userGraph.edges(nodeId);
				// console.log(nodeId, n_edges);

				// if the current color and orig_color are the same, then things can be changed
				// if color and orig_color are different, then orig_color should be restored
				if (color == orig_color) {
					// alert('neighbors of ' + nodeId + ' : ' + n_neighbors);
					updateNodeAttribute(userGraph, sigma, nodeId, 'color', 'maroon');
					updateNodeAttribute(userGraph, sigma, nodeId, 'size', 15);
					updateNodeAttribute(userGraph, sigma, nodeId, 'label', nodeTitle);

					userGraph.forEachNeighbor(nodeId, (neighborId, neighborAttributes) => {
						neighborTitle = userGraph.getNodeAttribute(neighborId, 'title');
						updateNodeAttribute(userGraph, sigma, neighborId, 'color', 'indianred');
						updateNodeAttribute(userGraph, sigma, neighborId, 'size', 15);
						updateNodeAttribute(userGraph, sigma, neighborId, 'label', neighborTitle);
					});

					n_edges.forEach((n_edge) => {
						updateEdgeAttribute(userGraph, sigma, n_edge, 'color', 'red');
						updateEdgeAttribute(userGraph, sigma, n_edge, 'size', 8);
					});
				}
				else {
					restoreGraph(userGraph, sigma);
				}
			});
		</script>";
		echo "</div>";  // end of graph_div div		
	}
}

// all finished
unset($selected);
unset($chunks);
mysqli_free_result($results);
mysqli_close($connection);
return;
?>
