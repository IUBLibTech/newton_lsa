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

$logfile = "log/termsearch.txt";
$log = fopen($logfile, "w");
fwrite($log, "open termsearch: ".memory_get_usage()." at ".date("M d g:i:s")."\n");

$mdb = $_GET['mdb'];
$list = $_GET['list'];
$frags = $_GET['frags'];
$scope = $_GET['scope'];
$outf = $_GET['outf'];
$bound = $_GET['bound'];
$qs = $_GET['qs'];

$hash_value = md5($mdb."|".$list."|".$frags."|".$scope."|".$bound."|".$qs);

if($outf == "graph" &&  file_exists("graphs/graph-".$hash_value.".nwb")) {
  $downloadpath = "graphs/graph-".$hash_value.".nwb";
      echo  "<br/><br/>(Right-click the link and use <em>Save link as...</em> to get a copy for viewing in Network Workbench or an editor.)<br/><br/>
				<table>
				<tr><td>NWB network graph file (cached):</td><td><a href='".$downloadpath."'>Link for download.</a> Give it a new name.</td></tr>
				</table><br/><br/>";
     exit;
}

fwrite($log, "mdb=$mdb, list=$list, frags=$frags, scope=$scope, outf=$outf, bound=$bound, qs=$qs\n");

// open the database
require_once("functions/mysql_connection.php");

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
	$selectedterms = str_replace("_", "','", $qs);
	$selectedterms = "'".$selectedterms."'";
	$getselectedtermids = "SELECT id FROM ".$listTable." WHERE wordform IN (".$selectedterms.")";
	$selectedrows = mysqli_query($connection, $getselectedtermids);
	while ($nextid = mysqli_fetch_row($selectedrows)) {
		if (count($selected) > 0) {
			$selectedids = $selectedids.",";
		}
		$selected[] = $nextid[0];
		$selectedids = $selectedids.$nextid[0];
	}
	mysqli_free_result($selectedrows);
}
fwrite($log, "selected array loaded: ".memory_get_usage()."\n");
fwrite($log, "count in selected: ".count($selected)."\n");

fwrite($log, "selectedids: $selectedids\n");

//  going to try using a temporary table called results to organize the work
// create temporary table results
$removeresultstemp = "DROP TEMPORARY TABLE IF EXISTS results";
mysqli_query($connection, $removepagestemp);
$makeresultstemp = "CREATE TEMPORARY TABLE results (
		correlation TEXT, 
		term1 INT NOT NULL, 
		term2 INT NOT NULL)";
mysqli_query($connection, $makeresultstemp);

$pairs6 = 0;
$pairs3 = 0;
$pairs2 = 0;
//  load the temporary table
$getcosines6 = "INSERT INTO results (correlation, term1, term2)
		SELECT correlation, term1, term2 FROM $correlationTable6
		WHERE (correlation >= $bound) AND (
		(term1 IN ($selectedids)) OR
		(term2 IN ($selectedids)) )";
mysqli_query($connection, $getcosines6);
fwrite($log, "getcosines6 query executed: ".memory_get_usage()."\n");
$pairs6 = mysqli_affected_rows();
if ($bound <= 0.6) {
	$getcosines3 = "INSERT INTO results (correlation, term1, term2)
			SELECT correlation, term1, term2 FROM $correlationTable3
			WHERE (correlation >= $bound) AND (
			(term1 IN ($selectedids)) OR 
			(term2 IN ($selectedids)) )";
	mysqli_query($connection, $getcosines3);
	fwrite($log, "getcosines3 query executed: ".memory_get_usage()."\n");
	$pairs3 = mysqli_affected_rows();
}
if ($bound <= 0.3) {
	$getcosines2 = "INSERT INTO results (correlation, term1, term2)
			SELECT correlation, term1, term2 FROM $correlationTable2
			WHERE (correlation >= $bound) AND (
			(term1 IN ($selectedids)) OR 
			(term2 IN ($selectedids)) )";
	mysqli_query($connection, $getcosines2);
	fwrite($log, "getcosines2 query executed: ".memory_get_usage()."\n");
	$pairs2 = mysqli_affected_rows();
}

// load the appropriate term list into memory array for output
$getallterms = "SELECT wordform FROM ".$listTable;
$termliststring = "BASE\n";
$termrows = mysqli_query($connection, $getallterms);
while ($termrow = mysqli_fetch_row($termrows)) {
	$termliststring = $termliststring.$termrow[0]."\n";
}
$termlist = explode("\n", $termliststring);
fwrite($log, "termlist array loaded: ".memory_get_usage()."\n");
unset($termliststring);
mysqli_free_result($termrows);
fwrite($log, "termliststring and termrows freed: ".memory_get_usage()."\n");

echo "<br/><br/>";

// everything is gathered in results, time to work through them
$getoutput = "SELECT * FROM results ORDER BY correlation DESC";
$output = mysqli_query($connection, $getoutput);
fwrite($log, "getoutput query executed. memory used: ".memory_get_usage()."\n");
//$output = $db->query("select * from results order by correlation desc");
	
if ($outf == "ranked") {
	$outputcount = 0;
	//fwrite($log, "output array initialized. ".memory_get_usage()."\n");
	echo "<table cellpadding=10>";
	while ($outrow = mysqli_fetch_row($output)) {
		//

		$corr = $outrow[0];		
		//if ($corr < $bound) {
		//	break;
		//}
		$term1 = $outrow[1];
		$term2 = $outrow[2];

		if ($qs != "ALL") {
			if ($scope == "allcorrs") {
				if (!(in_array($term1, $selected) || in_array($term2, $selected))) {
					continue;
				}
			}
			else if ($scope == "onlyselected") {
				// $scope == "onlyselected"
				if (!in_array($term1, $selected)) {
					continue;
				}
				if (!in_array($term2, $selected)) {
					continue;
				}
				//if (!(in_array($term1, $selected) && in_array($term2, $selected))) {
				//	continue;
				//}
			}
		}
			
		echo "<tr><td>".$termlist[$term1]."</td><td>~</td><td>".$termlist[$term2]."</td><td>".$corr."</td></tr>";
		$outputcount++;
	}
	
	if ($outputcount == 0) {
		echo "<tr><td>No results greater than or equal to ".$bound." were found.</td></tr>";
	}
	echo "</table>";
}
else if ($outf == "graph") {
	//

	fwrite($log, "Entered graph.\n");
	$nodes = array();
	$edges = array();
	
	$outputcount = 0;
	while ($outrow = mysqli_fetch_row($output)) {

		fwrite($log, "Graph loop ".$outputcount."\n");
		$corr = $outrow[0];
		if ($corr < $bound) {
			break;
		}
		$new1 = $outrow[1];
		$new2 = $outrow[2];
		
		$writethis = 0;
		if ($qs == "ALL") {
			$writethis = 1;
		}
		elseif ($scope == "allcorrs") {
			if (in_array($new1, $selected) || in_array($new2, $selected)) {
				$writethis = 1;
			}
		}
		elseif ($scope == "onlyselected") {
			if (in_array($new1, $selected) && in_array($new2, $selected)) {
				//if ($selected[$new1] != $selected[$new2]) {
					$writethis = 1;
				//}
			}
		}
		
		if ($writethis == 0) {
			continue;
		}
		fwrite($log, "will writethis.\n");

		$corr = $outrow[0];

		$newkey = combine_key($new1, $new2);
		fwrite($log, "got newkey ".$newkey."\n");
		
		$edges[$newkey] = $corr;
		if (!in_array($new1, $nodes)) {
			$nodes[] = $new1;
		}
		if (!in_array($new2, $nodes)) {
			$nodes[] = $new2;
		}
		fwrite($log, "loaded nodes and edges.\n");
		
		$outputcount++;
	}
	if ($outputcount == 0) {
		echo "No results greater than or equal to ".$bound." were found.<br/>";
		fwrite($log, "outputcount was 0.\n");
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
		
		$nodeIdx = array();
		$nodecounter = 1;		
		// first write all the nodes that were selected
		foreach ($nodes as $node) {
			$nodeIdx[$node] = $nodecounter;
			fwrite($graph, $nodecounter.' "'.$termlist[$node].'" "d2d" "z2z"'."\n");
			$nodecounter++;
		}
		
		fwrite($graph, "*UndirectedEdges\n");
		fwrite($graph, "source*int\ttarget*int\tweight*float");		
		
		foreach($edges as $bigkey => $edgecorr) {
			$ekey2 = extract_key2($bigkey);
			$ekey1 = extract_key1($bigkey, $ekey2);
			
			fwrite($graph, "\n".$nodeIdx[$ekey1]."\t".$nodeIdx[$ekey2]."\t".$edgecorr);
		}
		
		fclose($graph);
		fwrite($log, "Closed graph file.\n");
		
		// change the permissions for the new graph file
		chmod($newgraph, 0644);
		
		echo  "<br/><br/>(Right-click the link and use <em>Save link as...</em> to get a copy for viewing in Network Workbench or an editor.)<br/><br/>
				<table>
				<tr><td>NWB network graph file:</td><td><a href='".$downloadpath."'>Link for download.</a> Give it a new name.</td></tr>
				</table><br/><br/>";
	}
}

mysqli_close($connection);
unset($termlist);
//$db->close();
fwrite($log, "quitting, memory now ".memory_get_usage().".\n");
fclose($log);
return;
	
?>