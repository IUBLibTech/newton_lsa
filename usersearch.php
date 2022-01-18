<?php
//  FUNCTIONS
//####################
function cosine_sim($vecstring1, $vecstring2, $magn1, $magn2) {
	//echo "vecstring1: ".$vecstring1."<br/>";
	$vector1 = explode(",", $vecstring1);
	$vector2 = explode(",", $vecstring2);
	$numdims = count($vector1);
	//echo "count: ".$numdims.", ";
	$dotproduct = 0.0;
	$increment = 0.0;
	for ($d = 0; $d < $numdims; $d++) {
		$increment = $vector1[$d] * $vector2[$d];
		$dotproduct = $dotproduct + $increment;
	}
	$magnitude = ($magn1 * $magn2);
	//echo "magn: ".$magnitude.", ";
	$rawcorrelation = $dotproduct / $magnitude;
	//echo "raw corr: ".$rawcorrelation.". ";
	$correlation = round($rawcorrelation, 4);
	return $correlation;
}

function cosine_simR($vecstring, $magn, $log) {
	global $request;
	global $requestmagn;
	$vector = explode(",", $vecstring);
	$numdims = count($vector);
	//fwrite($log, "numdims in cosine_simR= ".$numdims."\n");
	//echo "count: ".$numdims.", ";
	$dotproduct = 0.0;
	$increment = 0.0;
	for ($d = 0; $d < $numdims; $d++) {
		$increment = $vector[$d] * $request[$d];
		$dotproduct = $dotproduct + $increment;
	}
	$magnitude = ($magn * $requestmagn);
	//fwrite($log, "magnitude in cosine_simR= ".$magnitude."\n");
	//echo "magn: ".$magnitude.", ";
	$rawcorrelation = $dotproduct / $magnitude;
	//echo "raw corr: ".$rawcorrelation.". ";
	$correlation = round($rawcorrelation, 6);
	return $correlation;
}

function calculate_request($vecstring, $log) {
	global $request;
	global $requestmagn;
	fwrite($log, "vecstring: ".$vecstring."\n");
	$rvector = explode(",", $vecstring);
	$numdims = count($rvector);
	//fwrite($log, "numdims in calculate_request= ".$numdims."\n");
	for ($ri=0; $ri < $numdims; $ri++) {
		$request[$ri] = $request[$ri] + $rvector[$ri];
		fwrite($log, $request[$ri].",");
	}
	//fwrite($log, "\n");
}

function calculate_request_magn($log) {
	global $request;
	global $requestmagn;
	$count = count($request);
	//fwrite($log, "count in calculate_request_magn= ".$count."\n");
	$sumsq = 0.0;
	for ($rm = 0; $rm < $count; $rm++) {
	//fwrite($log, "request[rm]] in calculate_request_magn= ".$request[$rm]."\n");
		$sumsq = $sumsq + pow($request[$rm], 2);
	//fwrite($log, "sumsq in calculate_request_magn= ".$sumsq."\n");
	}
	$requestmagn = sqrt($sumsq);
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

function test_for_presence($connection, $fragset, $term, $chunk) {
	$fragtable = "frag250";
	if ($fragset == "ch1000") {
		$fragtable = "frag1000";
	}
	$fragresult = mysqli_query($connection, "SELECT ctext FROM $fragtable WHERE id=$chunk");
	$fragment = mysqli_fetch_row($fragresult);
	$fstring = $fragment[0];
	
	$lc_term = strtolower($term);
	$lc_string = strtolower($fstring);
	
	$ourterm = "/\b".$lc_term."\b/";
	
	if (preg_match($ourterm, $lc_string)) {
		return 1;
	}
	else {
		return 0;
	}
}

//  MAIN PROCEDURE
//####################
// run the search over the correlation matrix
// open the database
$connection = new mysqli();
require_once("functions/mysql_connection.php");

$logfile = "log/usersearch.txt";
$log = fopen($logfile, "w");
if (!$log) {
	echo "log OPEN FAILED.<br/>";
	return;
}
fwrite($log, "open usersearch: ".memory_get_usage()." at ".date("M d g:i:s")."\n");

$list = $_GET['list'];
$frags = $_GET['frags'];
$scope = $_GET['scope'];
$outf = $_GET['outf'];
$bound = $_GET['bound'];
$qs = $_GET['qs'];

fwrite($log, "list=$list, frags=$frags, scope=$scope, outf=$outf, bound=$bound, qs=$qs\n");

//begin setup
$chunktable = "doc250_list";
$termtable = "term250_list";
$uktable = "uv250_uk";
$uksktable = "uv250_uksk";
$vktable = "uv250_vk";
$vksktable = "uv250_vksk";
$displayscript = "javascript:openTDViewer(\"ch250\", \"";
//$vectordir = "v250/";
if ($frags == "ch1000") {
	//$chunklistfile = "c1000/select-list.txt";
	$chunktable = "doc1000_list";
	$termtable = "term1000_list";
	$uktable = "uv1000_uk";
	$uksktable = "uv1000_uksk";
	$vktable = "uv1000_vk";
	$vksktable = "uv1000_vksk";
	$displayscript = "javascript:openTDViewer(\"ch1000\", \"";
	//$vectordir = "v1000/";
}

$request = array();
for ($r=0; $r < 100; $r++) {
	$request[] = 0.0;
}
$requestmagn = 0.0;
fwrite($log, "request array created, count= ".count($request)."\n");

//  going to try using a temporary table called request to organize the work
// create temporary table request
$removerequesttemp = "DROP TEMPORARY TABLE IF EXISTS request";
mysqli_query($connection, $removerequesttemp);
$makerequesttemp = "CREATE TEMPORARY TABLE request (
		id INT NOT NULL,
		vector TEXT)";
mysqli_query($connection, $makerequesttemp);

if ($list == "termquery") {	
	// put the userchoices into a memory array
	$selectedterm_id = array();
	$select_term_string = "";
	$select_termid_string = "";
	if ($qs == "ALL") {
		unset($selectedterm_id);
		echo "ALL not allowed.";
		return;
	}
	else {
		$select_term_string = str_replace("_", "','", $qs);
		$select_term_string = "'".$select_term_string."'";
		$termtablerows = mysqli_query($connection, "SELECT id FROM ".$termtable." WHERE wordform IN (".$select_term_string.")");
		while ($nextid = mysqli_fetch_row($termtablerows)) {
			if (count($selectedterm_id) > 0) {
				$select_termid_string = $select_termid_string.",";
			}
			$select_termid_string = $select_termid_string.$nextid[0];
			
			$selectedterm_id[] = $nextid[0];
		}
	}
	fwrite($log, "selectedterm_id array loaded: ".memory_get_usage()."\n");
	fwrite($log, "select_termid_string: $select_termid_string\n");
	mysqli_free_result($termtablerows);
	fwrite($log, "termtablerows freed: ".memory_get_usage()."\n");
	fwrite($log, "count in selectedterm_id: ".count($selectedterm_id)."\n");
	
	// put the selected vector info from uvNN_uksk into request
	
	mysqli_query($connection, "INSERT INTO request (id, vector)
						SELECT id, vector from $uksktable 
						WHERE id IN (".$select_termid_string.")");
	fwrite($log, "count in request table ".mysqli_affected_rows()."\n");
	fwrite($log, "request table filled.\n");
	
	$requestrows = mysqli_query($connection, "select * from request");
	while ($requestrow = mysqli_fetch_row($requestrows)) {
		$nextvector = $requestrow[1];
		calculate_request($nextvector, $log);
	}
	// now calculate the centroid
	$numrows = count($selectedterm_id);
	for ($ix = 0; $ix < 100; $ix++) {
		$request[$ix] = $request[$ix]/$numrows;
	}
	// and then calculate the magnitude of the centroid
	calculate_request_magn($log);
	fwrite($log, "requestmagn= ".$requestmagn."\n");
	
	$results = array();
	$traverseVk = mysqli_query($connection, "select * from $vktable");
	while ($nextVk = mysqli_fetch_row($traverseVk)) {
		// $nextVk[0] is the chunkID
		// $nextVk[1] is the magnitude of the vector
		// $nextVk[2] is the vector string
		$corr = cosine_simR($nextVk[2], $nextVk[1], $log);
		//fwrite($log, "corr in traversveVk= ".$corr."\n");
		
		if ($corr >= $bound) {
			//fwrite($log, " got a corr= ".$corr."\n");
			$corrx = $corr;
			while (array_key_exists((string)$corrx, $results)) {
				$corrx = $corrx + 0.00000001;
			}
			$corrs = (string)$corrx;
			$results[$corrs] = $nextVk[0];
		}
	}
	fwrite($log, "count of results= ".count($results)."\n");
	
	// return the results
	echo "<table cellpadding=10>";
	if (count($results) < 1) {
		echo "<tr><td>No results greater than or equal to ".$bound." were found.</td></tr>";
	}
	else {
		// load the appropriate chunk list into memory array
		$chunks = array();
		$chunks[] = "base";
		$chunklistrows = mysqli_query($connection, "SELECT * FROM $chunktable");
		while ($chunklistrow = mysqli_fetch_row($chunklistrows)) {
			$chunks[] = $chunklistrow;
		}
		mysqli_free_result($chunklistrows);
		fwrite($log, "chunks array initialized. ".memory_get_usage()."\n");
		
		$qsx = strtr($qs, "'", "@");
		$qsq = explode("_", $qs);

		krsort($results);
		foreach($results as $rcorr => $chunkId) {
			$chunktitle = $chunks[$chunkId][1];
			$chunkfile = $chunks[$chunkId][2];
			fwrite($log, "rcorr= ".$rcorr.", chunkId= ".$chunkId.", chunkfile =".$chunkfile."\n");
			$termisthere = 0;
			$showpresent = "(";
			if ($scope == "presence"|| $scope == "presentonly") {
				foreach($qsq as $qterm) {
					$termtest = 0;
					$termtest = test_for_presence($connection, $frags, $qterm, $chunkId);
					fwrite($log, $termtest."\n");
					$termisthere = $termisthere + $termtest;
					if ($termtest == 1) {
						if ($termisthere > 1) {
							$showpresent = $showpresent.", ";
						}
						$showpresent = $showpresent.$qterm;
					}
				}
				if ($termisthere > 0) {
					$showpresent = $showpresent." present)";
				}
				else {
					$showpresent = "";
				}
			}
			
			if ($scope == "allcorrs" || $termisthere > 0) {
				echo "<td>".$chunktitle."</td>";
				echo "<td><a href ='".$displayscript.$chunkId."\", \"".$qsx."\", \"".$rcorr."\")'>";
				echo $rcorr;
				echo "</a></td>";
				if ($scope == "presence" || $scope == "presentonly") {
					echo "<td>".$showpresent."</td>";
				}
			}
			else {
				if ($scope == "presence") {
					echo "<tr><td>".$chunktitle."</td>";
					echo "<td><a href ='".$displayscript.$chunkId."\", \"".$qsx."\", \"".$rcorr."\")'>";
					echo $rcorr;
					echo "</a></td>";
					echo "<td> </td>";
				}
			}
			echo "</tr>";
		}
	}
	echo "</table>";
}
elseif ($list == "chunkquery") {
	// having the userchoices in memory may not be harmful
	$selectedchunk = array();
	$select_chunkid_string = "";
	if ($qs == "ALL") {
		unset($selectedchunk_id);
		echo "ALL not allowed.";
		return;	
	}
	else {
		$select_chunkid_string = str_replace("_", ",", $qs);
		$selectedchunk = explode("_", $qs);
	}
	// create a pseudo-term centroid from the doc vksk
	// then go through uk looking for term mathes
	// and produce the results, maybe testing for presence
	// prepare to insert qs chunkIDs into userchoice
	
	// having the userchoices in memory may not be harmful
	$chunkindex = array();
	$chidx = 0;
	foreach ($selectedchunk as $thischunk) {
		$chunkindex[$thischunk] = $chidx;
		$chidx++;
	}
	fwrite($log, "selectedchunk and chunkindex arrays loaded: ".memory_get_usage()."\n");
	fwrite($log, "count in selectedchunk: ".count($selectedchunk)."\n");
	
	mysqli_query($connection, "INSERT INTO request (id, vector)
				SELECT id, vector FROM $vksktable
				WHERE id IN ($select_chunkid_string)");
	fwrite($log, "request table filled.\n");
	
	$requestrows = mysqli_query($connection, "select * from request");
	while ($requestrow = mysqli_fetch_row($requestrows)) {
		$nextvector = $requestrow[1];
		calculate_request($nextvector, $log);
	}
	// now calculate the centroid
	$numrows = count($selectedchunk);
	for ($ix = 0; $ix < 100; $ix++) {
		$request[$ix] = $request[$ix]/$numrows;
	}
	// and then calculate the magnitude of the centroid
	calculate_request_magn($log);
	fwrite($log, "requestmagn= ".$requestmagn."\n");
	
	$results = array();
	$traverseUk = mysqli_query($connection, "select * from $uktable");
	while ($nextUk = mysqli_fetch_row($traverseUk)) {
		// $nextUk[0] is the chunkID
		// $nextUk[1] is the magnitude of the vector
		// $nextUk[2] is the vector string
		$corr = cosine_simR($nextUk[2], $nextUk[1], $log);
		//fwrite($log, "corr in traversveUk= ".$corr."\n");
		
		if ($corr >= $bound) {
		//fwrite($log, " got a corr= ".$corr."\n");
			$corrx = $corr;
			while (array_key_exists((string)$corrx, $results)) {
				$corrx = $corrx + 0.00000001;
			}
			$corrs = (string)$corrx;
			$results[$corrs] = $nextUk[0];
		}
	}
	fwrite($log, "count of results= ".count($results)."\n");
		
	// return the results
	echo "<table cellpadding=10>";
	if (count($results) < 1) {
		echo "<tr><td>No results greater than or equal to ".$bound." were found.</td></tr>";
	}
	else {
		if ($scope =="presence") {
			echo "<tr><td></td><td></td><td>(chunks will be indicated below when term is present)</td></tr>";
		}
		// load the appropriate chunk list into memory array
		$displayscript = "javascript:openTDViewer(\"ch250\", \"";
		if ($frags == "ch1000") {
			$displayscript = "javascript:openTDViewer(\"ch1000\", \"";
		}
		// load the appropriate chunk list into memory array
		$chunks = array();
		$chunks[] = "base";
		// set up chunk title and file array
		$chunklist = mysqli_query($connection, "SELECT * FROM $chunktable");
		while ($cc = mysqli_fetch_row($chunklist)) {
			$chunks[] = $cc;
		}
		mysqli_free_result($chunklist);
		fwrite($log, "chunks array initialized. memory= ".memory_get_usage()."\n");
		
		// now get the terms
		$termlist = array();
		$allterms = mysqli_query($connection, "SELECT wordform FROM $termtable");
		$termcount = 1;
		while ($nexterm = mysqli_fetch_row($allterms)) {
			$termlist[$termcount] = $nexterm[0];
			$termcount++;
		}
		mysqli_free_result($allterms);
		
		$qsx = strtr($qs, "'", "@");
		$qsq = explode("_", $qs);

		$printcount = 0;
		krsort($results);
		foreach($results as $rcorr => $termId) {
			$term = $termlist[$termId];
			fwrite($log, "count termlist now= ".count($termlist)."\n");
			fwrite($log, "rcorr= ".$rcorr." termId= ".$termId." term= ".$term."\n");
			$termisthere = 0;
			$showpresent = "(present in ";
			if ($scope == "presence"|| $scope == "presentonly") {
				foreach($selectedchunk as $chunkId) {
					$chunktitle = $chunks[$chunkId][1];
					$chunkfile = $chunks[$chunkId][2];
					fwrite($log, "chunkId= ".$chunkId."\n");
					fwrite($log, "chunk= ".$chunktitle."\n");
					fwrite($log, "chunkfile= ".$chunkfile."\n");
					$termtest = 0;
					$termtest = test_for_presence($connection, $frags, $term, $chunkId);
					fwrite($log, $termtest."\n");
					$termisthere = $termisthere + $termtest;
					if ($termtest == 1) {
						if ($termisthere > 1) {
							$showpresent = $showpresent.", ";
						}
						$showpresent = $showpresent.$chunktitle;
					}
				}
				if ($termisthere > 0) {
					$showpresent = $showpresent.")";
				}
				else {
					$showpresent = "";
				}
			}
			
			if ($scope == "allcorrs" || $termisthere > 0) {
				echo "<tr><td>".$term."</td><td>";
				echo $rcorr;
				echo "</td>";
				if ($scope == "presence" || $scope == "presentonly") {
					echo "<td>".$showpresent."</td>";
				}
				$printcount++;
			}
			else {
				if ($scope == "presence") {
					echo "<tr><td>".$term."</td><td>";
					echo $rcorr;
					echo "</td>";
					echo "<td> </td>";
					$printcount++;
				}
			}
			echo "</tr>";
		}
		fwrite($log, "printcount= ".$printcount."\n");
		if ($printcount == 0) {
			echo "<tr><td>None of the selected chunks contained terms in their centroid region.</td></tr>";
		}
	}
	echo "</table>";
}

mysqli_free_result($results);
mysqli_close($connection);
fwrite($log, "arrays and results freed, connection closed. memory used: ".memory_get_usage()."\n");
fclose($log);
?>