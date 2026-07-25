<?php
//  FUNCTIONS
//####################
function cosine_sim(string $vecstring1, string $vecstring2, float $sumsq1, float $sumsq2) {
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

function combine_key(int $int1, int $int2) {
	$newkey = $int1 * 1000000000;
	$newkey = $newkey + $int2;
	return $newkey;
}

function extract_key2(int $keyint) {
	$key2 = $keyint % 1000000000;
	return $key2;
}

function extract_key1(int $keyint, int $mod) {
	$key0 = $keyint - $mod;
	$key1 = $key0 / 1000000000;
	return $key1;
}

//  MAIN PROCEDURE
//####################
// run the search over the correlation matrix

// open the database
$connection = null;
require_once("functions/mysql_connection.php");

$list = $_GET['list'];
$frags = $_GET['frags'];
$scope = $_GET['scope'];
$outf = $_GET['outf'];
$boundStr = $_GET['bound'];
$qs = $_GET['qs'];

$bound = (float)$boundStr;

$hash_value = md5($list."|".$frags."|".$scope."|".$boundStr."|".$qs);

// if($outf == "graph" &&  file_exists("graphs/graph-".$hash_value.".nwb")) {
//   $downloadpath = "graphs/graph-".$hash_value.".nwb";
//       echo  "<br/><br/>(Right-click the link and use <em>Save link as...</em> to get a copy for viewing in Network Workbench or an editor.)<br/><br/>
// 				<table>
// 				<tr><td>NWB network graph file (cached):</td><td><a href='".$downloadpath."'>Link for download.</a> Give it a new name.</td></tr>
// 				</table><br/><br/>";
//      exit;
// }

//begin setup
$listTable = "term250_list";
$correlationTable2 = "term250_cosines2";
$correlationTable3 = "term250_cosines3";
$correlationTable6 = "term250_cosines6";
if ($frags == "ch1000") {
	$listTable = "term1000_list";
	$correlationTable2 = "term1000_cosines2";
	$correlationTable3 = "term1000_cosines3";
	$correlationTable6 = "term1000_cosines6";
}

// put the selected term ids in a memory array and terms in a WHERE IN string unless user chose "ALL"
$selected = array();  // array is used when $qs not "ALL" to detect term1 and term2 cases
$selectedterms = "";
$selectedids = "";
if ($qs == "ALL") {
	unset($selected);
	print "There's no point in asking for ALL terms. I'll fix this. Try choosing some terms.";
	return;
}
else {
	$selectedids = str_replace("_", ",", $qs);
	// echo "<script type='text/javascript'>console.log('" . $selectedids . "');</script>";
}

// next convert $selectedids into an int array for sql queries below
$selectedIntArray = array_map('intval', explode(',', $selectedids));
$selectedInts = implode(',', $selectedIntArray);

// echo "<script type='text/javascript'>
// console.log(" . $selectedInts . ");
// alert('selectedInts defined');</script>";


// define sql union queries that may need one, two or three term cosine tables
$getCosFrom6andUp = "SELECT correlation, term1, term2
	FROM $correlationTable6
	WHERE term1 IN ($selectedInts) OR term2 IN ($selectedInts) ORDER BY correlation DESC";
	
$getCosFrom3andUp = "SELECT correlation, term1, term2
	FROM $correlationTable6
	WHERE term1 IN ($selectedInts) OR term2 IN ($selectedInts)
	UNION ALL
	SELECT correlation, term1, term2
	FROM $correlationTable3
	WHERE term1 IN ($selectedInts) OR term2 IN ($selectedInts)
	ORDER BY correlation DESC";

$getCosFrom2andUp = "SELECT correlation, term1, term2
	FROM $correlationTable6
	WHERE term1 IN ($selectedInts) OR term2 IN ($selectedInts)
	UNION ALL
	SELECT correlation, term1, term2
	FROM $correlationTable3
	WHERE term1 IN ($selectedInts) OR term2 IN ($selectedInts)
	UNION ALL
	SELECT correlation, term1, term2 
	FROM $correlationTable2
	WHERE term1 IN ($selectedInts) OR term2 IN ($selectedInts)
	ORDER BY correlation DESC";

// echo "<script type='text/javascript'>alert('query statements defined');</script>";



// create $output based on the depth of the cosine threshold
$pairs6 = 0;
$pairs3 = 0;
$pairs2 = 0;
//  load the temporary table
if ($bound >= 0.6) {
	$query = mysqli_prepare($connection, $getCosFrom6andUp);
}
else if ($bound >= 0.3 && $bound < 0.6) {
	$query = mysqli_prepare($connection, $getCosFrom3andUp);
}
else if ($bound >= 0.2 && $bound < 0.3) {
	$query = mysqli_prepare($connection, $getCosFrom2andUp);
}
else {
	return;
}
mysqli_stmt_execute($query);
$output = mysqli_stmt_get_result($query);
$rows = mysqli_fetch_all($output, MYSQLI_ASSOC);
$numrows = count($rows);

// echo "<script type='text/javascript'>
// console.log(" . $numrows . ");
// console.table(" . json_encode($rows) . ");
// alert('output now fetched into rows');
// </script>";

echo "<br/><br/>";

// echo "<script type='text/javascript'><alert>'output returned'</alert>;</script>";
$neighbors = array();
foreach($rows as $survey) {
	$thisCorrelation = (float)$survey['correlation'];

	if ($thisCorrelation < $bound) {
		break;
	}

	if (!in_array($survey['term1'], $neighbors, true)) {
		$neighbors[] = $survey['term1'];
	}
	if (!in_array($survey['term2'], $neighbors, true)) {
		$neighbors[] = $survey['term2'];
	}
}

// echo "<pre>";
// print_r($neighbors);
// echo "</pre>";

sort($neighbors, SORT_NUMERIC);

$numNeighbors = count($neighbors);
$placeholders = implode(',', array_fill(1, $numNeighbors, '?'));
$sortedNeighbors = implode(',', $neighbors);

// get termlist

$jsonPath = 'json/term250_list.json';
if ($frags == 'ch1000') {
	$jsonPath = 'json/term1000_list.json';
}
$jsonList = file_get_contents($jsonPath);

$jsonData = json_decode($jsonList, true);


// echo "<pre>";
// print_r($jsonData["7"]["text"]);
// echo "</pre>";

// echo "<script type='text/javascript'>
// console.table(" . $jsonData . ");
// alert('jsonContent in console.table');
// </script>";

$termlist = array();
foreach ($jsonData['results'] as $item) {
	$id = $item['id'];
	if (in_array($id, $neighbors)) {
		$termlist[$id] = $item['text'];
	}
}

// echo "<pre>";
// print_r($termlist);
// echo "</pre>";
// ==============================


echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Cosine similarity between two terms is a measure of their co-occurrence across all the passages.<br/><br/><br/>";

// everything is gathered in $output and $termlist, time to work through them
	
if ($outf == "ranked") {
	$outputcount = 0;
	echo "<table cellpadding=10>";
	// while ($outrow = mysqli_fetch_row($output)) {
	foreach($rows as $outrow) {
		//

		$corr = (float)$outrow['correlation'];
		if ($corr < $bound) {
			break;
		}
		$term1 = $outrow['term1'];
		$term2 = $outrow['term2'];

		if ($qs != "ALL") {
			if ($scope == "allcorrs") {
				if (!(in_array($term1, $selectedIntArray) || in_array($term2, $selectedIntArray))) {
					continue;
				}
			}
			else if ($scope == "onlyselected") {
				// $scope == "onlyselected"
				if (!in_array($term1, $selectedIntArray)) {
					continue;
				}
				if (!in_array($term2, $selectedIntArray)) {
					continue;
				}
				//if (!(in_array($term1, $selected) && in_array($term2, $selected))) {
				//	continue;
				//}
			}
		}

		$term1_word = $termlist[$term1];
		$term2_word = $termlist[$term2];
		
		echo "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>". $term1_word ."</td><td>~</td><td>". $term2_word ."</td><td>".$corr."</td></tr>";
		$outputcount++;
	}
	
	if ($outputcount == 0) {
		echo "<tr><td>No results greater than or equal to ".$bound." were found.</td></tr>";
	}
	echo "</table>";
}
else if ($outf == "graph") {
	//

	echo "<script>alert('have entered graph in docsearch');</script>";

	$nodes = array();
	$edges = array();

	$outputcount = 0;
	while ($edgeRow = mysqli_fetch_row($output)) {

		// fwrite($log, "Graph loop ".$outputcount."\n");
		$corr = $edgeRow[0];
		if ($corr < $bound) {
			break;
		}
		$new1 = $edgeRow[1];
		$new2 = $edgeRow[2];
		
		$writethis = 0;
		if ($qs == "ALL") {
			$writethis = 1;
		}
		elseif ($scope == "allcorrs") {
			if (in_array($new1, $selectedIntArray) || in_array($new2, $selectedIntArray)) {
				$writethis = 1;
			}
		}
		elseif ($scope == "onlyselected") {
			if (in_array($new1, $selectedIntArray) && in_array($new2, $selectedIntArray)) {
				$writethis = 1;
			}
		}
		
		if ($writethis == 0) {
			continue;
		}

		$corr = $edgeRow[0];

		$newkey = combine_key($new1, $new2);
		// fwrite($log, "got newkey ".$newkey."\n");
		
		$edges[$newkey] = $corr;
		if (!in_array($new1, $nodes)) {
			$nodes[] = $new1;
		}
		if (!in_array($new2, $nodes)) {
			$nodes[] = $new2;
		}
		// fwrite($log, "loaded nodes and edges.\n");
		
		$outputcount++;
	}
	if ($outputcount == 0) {
		// echo "No results greater than or equal to ".$bound." were found.<br/>";
		echo "<script type='text/javascript'>alert('No results were found.');</script>";
		// fwrite($log, "outputcount was 0.\n");
	}
	else {
		// we can write the graph
		$newgraph = "graph-". $hash_value.".nwb";		

		$graphstring = "*Nodes".PHP_EOL."id*int label*string docid*string";
		// echo "<script>alert(`".$graphstring."`);</script>";
		
		$nodeIdx = array();
		$nodecounter = 1;		
		// first write all the nodes that were selected
		foreach ($nodes as $node) {
			$nodeIdx[$node] = $nodecounter;
			$nextNode = $nodecounter.' "'.$termlist[$node].'"';
			// fwrite($graph, $nodecounter.' '.$termlist[$node]);
			$graphstring = $graphstring . PHP_EOL . $nextNode;
			$nodecounter++;
		}
		echo "<script>alert(`".$graphstring."`);</script>";
		
		// Now the edges.

		$graphstring = $graphstring. PHP_EOL ."*UndirectedEdges";
		$graphstring = $graphstring. PHP_EOL . "source*int\ttarget*int\tweight*float";
		
		// fwrite($graph, "*UndirectedEdges\n");
		// fwrite($graph, "source*int\ttarget*int\tweight*float");		
		
		foreach($edges as $bigkey => $edgecorr) {
			$ekey2 = extract_key2($bigkey);
			$ekey1 = extract_key1($bigkey, $ekey2);
			
			$nextEdge = $nodeIdx[$ekey1]."\t".$nodeIdx[$ekey2]."\t".$edgecorr;
			$graphstring = $graphstring . PHP_EOL . $nextEdge;
			
			// fwrite($graph, "\n".$nodeIdx[$ekey1]."\t".$nodeIdx[$ekey2]."\t".$edgecorr);
		}
		echo "<script type=\'text/javascript\'>console.log(`".$graphstring."`)</script>";
		
		// fclose($graph);
		// fwrite($log, "Closed graph file.\n");
		
		// change the permissions for the new graph file
		// chmod($newgraph, 0644);
		
		// define the graph download function
		echo "<script type=\'text/javascript\'>
			function downloadGraph(contents, filename) {
				const graphBlob = new Blob([contents], { type: \'text/plain\' });
				const graphUrl = URL.createObjectURL(graphBlob);
				const graphLink = document.createElement(\'a\');
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
			<p>&nbsp;&nbsp;&nbsp;&nbsp;Confirm download of the requested graph file, '$newgraph'
			to your browser\'s default download location.</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;Nodes and edges are encoded in Network Work Bench (.nwb) format for 
			use in the Sci<sup>2</sup> network-graph application, but the file is
			plain text, so it can be read in other editors.</p>
			<br/><br/>
			<script type=\'text/javascript\'>
				let permission = confirm(\'Download requested graph file?\');
				if (permission) {
						const graphContents = `$graphstring`;
						const graphFile = '$newgraph';
						downloadGraph(graphContents, graphFile);
					}
			</script>
		";
		
	}
}

mysqli_close($connection);
unset($termlist);
return;
	
?>