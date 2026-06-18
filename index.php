<?php
/*********************************
Author:  		Wally Hooper
Co-Author:  	Timothy D Bowman

Description:	Latent Semantic Analysis Tool

 
*********************************/

$this_server = $_SERVER['SERVER_NAME'];

/****************************
Set up variable values 
****************************/

/****************************************************
Define database and calling Newton site using a private file
*****************************************************/
// The mysql database connection and predefined path to the digital edition
// will be defined externally in functions/mysql_connection.php
// but they are initialized in this file to reduce error messages
// in the development environment.
//
// $connection will contain the handle for the mysql database.
//
// $textSite will contain the web path to the Chymistry P5 digital edition.
// The component displays replicas of two passages side by side and includes
// links to those folio anchors in the digital edition.
// The component uses $textSite (which must end with a '/') to create those
// links.

$connection = null;  //  initializing empty mysql connection
$host = "";  // initializing variable to report in log file
$port = "";  // initializing variable to report in log file
$textSite = "";    //  initializing variable for web URL of relevant digital edition

/******************************************************************************
Connect to the MySQL database and digital edition using a private file
*******************************************************************************/
include "functions/mysql_connection.php";

/*****************************************
Write setup info to a log file
*****************************************/
// $logfile = "log/mainpage.txt";
// $log = ""; // fopen($logfile, "w");
// fwrite($log, "this_server = $this_server\n");
// fwrite($log, "cameFrom = $cameFrom\n");
// fwrite($log, "textSite = $textSite\n");
// fwrite($log, "flag = ".$flag."\n");
// fwrite($log, "host = ". $host . ", port = ". $port."\n");
// fwrite($log, "open viewcorrs with ".memory_get_usage()." RAM at ".date('M d g:i:s')."\n");
// if ($connection) {
	// fwrite($log, "mysql connected\n"); }


/*****************************************
HTML document begins here
 *****************************************/
?>
<!doctype html>
<html lang="en">
<head>
	<title>Latent Semantic Analysis of Newton's Chymistry</title>
	<script src="https://cdn.jsdelivr.net/npm/graphology@0.26.0/dist/graphology.umd.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/graphology-library@0.6.0/dist/graphology-library.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/sigma.js/3.0.2/sigma.min.js"></script>
	<!-- <script src="https://cdn.jsdelivr.net/npm/@sigma/node-square@3.0.0/+esm"></script> -->
	<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/sigma.js/3.0.2/sigma.min.js"></script> -->

	<?php
	require_once 'design/includes.php';
	?>

	<!-- LSA Style -->
	<link href="css/style.css" rel="stylesheet" media="all" />
	<!-- End LSA Style -->

	<!-- Newton Skin -->
	<?php require_once('design/header.php') ?>
 
</head>
<body>

<?php require_once('design/uniform-title.php') ?>

<!--	 			--> 
<!-- ROW #0 (HELP) locating "HELP Documentation" Button 	--> 
<!--	 			-->
<div class="lsa-row">
	<div id="lsa-rowZero">
		<div class="alignRight border paddingSmall" style="background-color: #7D100B;"><a href="help.php" title="HELP" class="helpLink">HELP Documentation</a></div>
        <p style="margin: 1em; margin-bottom:0;"><span class="instructionHeading">INSTRUCTIONS:</span> <span class="instructions">To begin, choose a search type and chunk size in Step 1. Choose an output type for results in Step 2 then select threshold types and scopes in Step 3.<br/>
		After Step 3, click the Continue button and proceed to Step 4 when the window reorganizes.</span></p>
        
	</div>
</div>

<!--	 			--> 
<!-- ROW #1A (TAB) locating "Show Query Tool" Button 	--> 
<!--	 			-->
<div class="lsa-row">
	<div id="lsa-rowOneA">
		<div id="lsa-rightSideTab"><a href="#"><span title="show">Show</span><span title="hide" style="display:none;">Hide</span> Query Tool</a></div>
	</div>
</div>

<!--	 	--> 
<!-- ROW #2 locating the four radio-button panels of "step" 1 --> 
<!--	 	-->
<div class="lsa-row" style="background-color: #FEFEFE;">
	<div id="lsa-rowTwo">
		<div id="lsa-searchChunkDiv" class="lsa-boxOne">
			<div id="lsa-searchtypeDiv">
				<form name="lsa-searchtype" id="lsa-searchtype" class="lsa-genericForm">
					<fieldset>
						<legend>Search Type:</legend>
						<ul class="lsa-formList">
							<li>
								<label>Document-Document
									<input type="radio" value="wholedocs" name="lsa-searchradio" aria-label="wholedocs-searchradio"/>
								</label>
							</li>
							<li>
								<label>Chunk-Chunk
									<input type="radio" value="chunks" name="lsa-searchradio" aria-label="chunks-searchradio"/>
								</label>
							</li>
							<li>
								<label>Term-Term
									<input type="radio" value="terms" name="lsa-searchradio" aria-label="terms-searchradio"/>
								</label>
							</li>
							<li>
								<label>Term-Chunk
									<input type="radio" value="termdoc" name="lsa-searchradio" aria-label="termdoc-searchradio"/>
								</label>
							</li>
							<li>
								<label>Chunk-Term
									<input type="radio" value="chunkterm" name="lsa-searchradio" aria-label="chunkterm-searchradio"/>
								</label>
							</li>
							<li>
								<label>Compose Query w/Terms
									<input type="radio" value="termquery" name="lsa-searchradio" aria-label="termquery-searchradio"/>
								</label>
							</li>
							<li>
								<label>Compose Query w/Chunks
									<input type="radio" value="chunkquery" name="lsa-searchradio" aria-label="chunkquery-searchradio"/>
								</label>
							</li>
						</ul>
					</fieldset>
				</form>
			</div>
			<br />
			<div id="lsa-chunkSizeDiv" class="lsa-halfOpacity">
				<form name="lsa-chunksize" id="lsa-chunksize" class="lsa-genericForm">
					<fieldset>
						<legend>Chunk Size:</legend>
						<ul class="lsa-formList">
							<li>
								<label>250-word Chunks
									<input type="radio" value="ch250" name="lsa-chunkradio" aria-label="ch250-chunkradio"/>
								</label>
							</li>
							<li>
								<label>1000-word Chunks
									<input type="radio" value="ch1000" name="lsa-chunkradio" aria-label="ch1000-chunkradio"/>
								</label>
							</li>
						</ul>
					</fieldset>
				</form>
			</div>
		</div>
		<div id="lsa-outputEnv" class="lsa-halfOpacity lsa-boxTwo">
			<form name="lsa-outputtype" id="lsa-outputtype" class="lsa-genericForm">
				<fieldset>
					<legend>Results Output Type:</legend>
					<ul class="lsa-formList">
						<li>
							<label>Network Graph of Similar Pairs
								<input type="radio" value="graph" name="lsa-outputradio" aria-label="graph-outputradio"/>
							</label>
						</li>
						<li>
							<label>List of Pairs in Descending Order
								<input type="radio" value="ranked" name="lsa-outputradio" aria-label="ranked-outputradio"/>
							</label>
						</li>
						<li>
							<label>All Pairs from One Doc in Page Order
								<input type="radio" value="pages" name="lsa-outputradio" aria-label="pages-outputradio"/>
							</label>
						</li>
						<li>
							<label>List of Term Pairs in Alpha Order
								<input type="radio" value="byterms" name="lsa-outputradio" aria-label="byterms-outputradio"/>
							</label>
						</li>
						<li>
							<label>List of Pairs in Catalog Order
								<input type="radio" value="bychunks" name="lsa-outputradio" aria-label="bychunks-outputradio"/>
							</label>
						</li>
						<!-- <li>
							<label>CSV: XY Term &#x2194; Doc
								<input type="radio" value="TDcsv" name="lsa-outputradio" aria-label="TDcsv-outputradio"/>
							</label>
						</li> -->
					</ul>
				</fieldset>
			</form>
		</div>
		<div id="lsa-scopetypediv" class="lsa-halfOpacity lsa-boxThree">
			<form name="lsa-scopetype" id="lsa-scopetype" class="lsa-genericForm">
				<fieldset>
					<legend>Return Scope of Pairs:</legend>
					<ul class="lsa-formList">
						<li>
							<label>All Above Chosen Value
								<input type="radio" value="allcorrs" name="lsa-scoperadio" aria-label="allcorrs-scoperadio"/>
							</label>
						</li>
						<li>
							<label>All Between Docs or Terms
								<input type="radio" value="onlyselected" name="lsa-scoperadio" aria-label="onlyselected-scoperadio"/>
							</label>
						</li>
						<li>
							<label>Within One Document (Doc-Doc)
								<input type="radio" value="internal" name="lsa-scoperadio" aria-label="internal-scoperadio"/>
							</label>
						</li>
						<li>
							<label>All w/Term Presence (Term &#x2194; Doc)
								<input type="radio" value="presence" name="lsa-scoperadio" aria-label="presence-scoperadio"/>
							</label>
						</li>
						<li>
							<label>Only if Term Present (Term &#x2194; Doc)
								<input type="radio" value="presentonly" name="lsa-scoperadio" aria-label="presentonly-scoperadio"/>
							</label>
						</li>
					</ul>
					<br style="clear:both;"/>
				</fieldset>
			</form>
			<br/>
			<br/>
			<form name="lsa-submitQuery" id="lsa-submitQuery" class="lsa-genericForm">
				<fieldset>
					<legend></legend>
					<ul class="lsa-formList">
						<li>
							<label>&nbsp;
								<input type="submit" value="Continue" name="lsa-submit" id="lsa-submitQueryButton"/>
							</label>
						</li>
						<li>
					</ul>
				</fieldset>
			</form>
		</div>
	</div>
</div>
<!--	 	--> 
<!-- ROW #3 --> 
<!--	 	-->
<div class="lsa-row" style="background-color: #FEFEFE;">
	<div id="lsa-rowThree">
		<div id="lsa-selectTerm250Env">
			<div id="lsa-selectTerm250FormDiv">
				<form name="lsa-selectTerm250Form" id="lsa-selectTerm250Form" class="lsa-genericForm">
					<fieldset>
						<legend>Terms</legend>
						<ul class="lsa-formList">
							<li>
								<label>Choose one or more</label>
								<select aria-label="Terms selection list" name="lsa-selectterm250" id="lsa-selectterm250" size="15">
									<?php
									// open the document chunk names file and read it into an array
									$term250list = mysqli_query($connection, "SELECT wordform FROM term250_list");
									while ($term250 = mysqli_fetch_row($term250list)) {
										if (strpos($term250[0],"'") > -1) {
											$term_string = str_replace("'", "&#8217;", $term250[0]);
										}
										else $term_string = $term250[0];

										print("<option value='$term_string' style='font-family: Newton Sans'>$term_string</option>");
									}
									mysqli_free_result($term250list);
									// fwrite($log, "term250list loaded.\n");
									?>
								</select>
							</li>
						</ul>
					</fieldset>
				</form>
				<?php 
                unset($termliststring);
                unset($termlist);
                unset($termselect);
                ?>
		</div>
			<div id="lsa-appendTerm250Env">
				<form name="lsa-appendTerm250Button" id="lsa-appendTerm250Button" class="lsa-genericForm">
					<fieldset>
						<ul class="lsa-formList">
							<li>
								<label>Add term
									<input type="button" value="Add Term" />
								</label>
								
							</li>
						</ul>
					</fieldset>
				</form>
			</div>
			<br style="clear:both;"/>
		</div>
		<div id="lsa-selectTerm1000Env">
			<div id="lsa-selectTerm1000FormDiv">
				<form name="lsa-selectTerm1000Form" id="lsa-selectTerm1000Form" class="lsa-genericForm">
					<fieldset>
						<legend>Terms</legend>
						<ul class="lsa-formList">
							<li>
								<label>Choose One or More
									<select name="lsa-selectterm1000" id="lsa-selectterm1000" size="15" >
								</label>
		<?php
		// open the document chunk names file and read it into an array
		//   style='font-family: Liberation Sans Alchemy'
		$term1000list = mysqli_query($connection, "SELECT wordform FROM term1000_list");
		while ($term1000 = mysqli_fetch_row($term1000list)) {
            if (strpos($term1000[0],"'") > -1) {
                $term_string = str_replace("'", "&#8217;", $term1000[0]);
            }
            else $term_string = $term1000[0];

            print("<option value='$term_string' style='font-family: Newton Sans'>$term_string</option>");
		}
		mysqli_free_result($term1000list);
		// fwrite($log, "term1000list loaded.\n");
		?>
								</select>
		<label>(Browsers other than Firefox may not render alchemical symbols.)</label>
							</li>
						</ul>
					</fieldset>
				</form>
				<?php 
                unset($termliststring);
                unset($termlist);
                unset($termselect);
                ?>
			</div>
			<div id="lsa-appendTerm1000Env">
				<form name="lsa-appendTerm1000Button" id="lsa-appendTerm1000Button" class="lsa-genericForm">
					<fieldset>
						<ul class="lsa-formList">
							<li>
								<label>Add term
									<input type="button" value="Add Term" />
								</label>
							</li>
						</ul>
					</fieldset>
				</form>
			</div>
			<br style="clear:both;" />
		</div>
		<div id="lsa-regexTermEnv">
			<div id="lsa-regexTermFormDiv">
				<form name="lsa-regexForm" class="lsa-genericForm">
					<fieldset>
						<legend>Regex pattern to select from list of terms:</legend>
						<ul class="lsa-formList">
							<li> <label>/
								<input type="text" name="lsa-thePattern" id="lsa-thePattern" size="30"/>
								/</label></li>
						</ul>
					</fieldset>
				</form>
			</div>
			<div id="lsa-appendregexTermEnv">
				<form name="lsa-appendregexTermButton" id="lsa-appendregexTermButton" class="lsa-genericForm">
					<fieldset>
						<ul class="lsa-formList">
							<li>
								<label>Add matches
									<input type="button" value="Add Matches" />
								</label>
							</li>
						</ul>
					</fieldset>
				</form>
			</div>
			<br style="clear:both;" />
		</div>
		
		<!-- BREAK0 -->
		<div id="lsa-break0"></div>
		<div id="lsa-selectDocEnv" style="z-index:10;">
			<form name="lsa-selectDocForm" class="lsa-genericForm">
				<fieldset>
					<ul class="lsa-formList">
						<li>
							<label>Choose One or More, or All
							<select name="lsa-selectdoc" id="lsa-selectdoc" size="15">
								<option value="ALL">All documents</option>
							</label>
		<?php
		// open the document chunk names file and read it into an array
		$corpuslist = mysqli_query($connection, "SELECT * FROM corpus_list");
		while ($docinfo = mysqli_fetch_row($corpuslist)) {
			print("<option value=\"$docinfo[2]\">$docinfo[0]</option>");
		}
		mysqli_free_result($corpuslist);
		// fwrite($log, "corpuslist loaded.\n");
		?>
							</select>
						</li>
					</ul>
				</fieldset>
			</form>
			<?php 
            unset($docliststring);
            unset($doclist);
            unset($wholeselect);
            ?>
		</div>
		<div id="lsa-selectChunk250Env" style="z-index:10;">
			<form name="lsa-selectChunk250Form" class="lsa-genericForm">
				<fieldset>
					<legend>Document Chunks:</legend>
					<ul class="lsa-formList">
						<li>
							<label>Choose One or More, or All
								<select name="lsa-selectchunk250" id="lsa-selectchunk250" size="15" >
								<option value = "ALL">All chunks</option>
							</label>
		<?php
		// open the document chunk names file and read it into an array
		$doc250count = 1;
		$doc250list = mysqli_query($connection, "SELECT ctitle FROM doc250_list");
		while ($doc250 = mysqli_fetch_row($doc250list)) {
			print("<option value=\"$doc250count\">$doc250[0]</option>");
		$doc250count++;
		}
		// fwrite($log, "doc250list loaded.\n");
		?>
							</select>
						</li>
					</ul>
				</fieldset>
			</form>
			<?php 
            unset($chunk250liststring);
            unset($chunk250list);
            unset($chunk250select);
            ?>
		</div>
		<div id="lsa-selectChunk1000Env" style="z-index:10;">
			<form name="lsa-selectChunk1000Form" class="lsa-genericForm">
				<fieldset>
					<legend>Document Chunks:</legend>
					<ul class="lsa-formList">
						<li>
							<label>Choose One or More, or All
								<select name="lsa-selectchunk1000" id="lsa-selectchunk1000" size="15" >
									<option></option>
									<option value="ALL">All chunks</option>
							</label>
		<?php
		// open the document chunk names file and read it into an array
		$doc1000count = 1;
		$doc1000list = mysqli_query($connection, "SELECT ctitle FROM doc1000_list");
		while ($doc1000 = mysqli_fetch_row($doc1000list)) {
			print("<option value=\"$doc1000count\">$doc1000[0]</option>");
		$doc1000count++;
		}
		// fwrite($log, "doc1000list loaded.\n");
		?>
							</select>
						</li>
					</ul>
				</fieldset>
			</form>
			<?php 
            unset($chunk1000liststring);
            unset($chunk1000list);
            unset($chunk1000select);
            ?>
		</div>
		
		
		<!--	/* ADD BUTTONS */  -->
		<div id="lsa-appendDocEnv">
			<form name="lsa-appendDocButton" class="lsa-genericForm">
				<fieldset>
					<ul class="lsa-formList">
						<li>
							<label>&nbsp;
								<input type="button" id="lsa-appendDocPress" value="Add Doc to Query Set" />
							</label>
						</li>
					</ul>
				</fieldset>
			</form>
		</div>
		<div id="lsa-appendChunkEnv">
			<form name="lsa-appendChunkButton" class="lsa-genericForm">
				<fieldset>
					<ul class="lsa-formList">
						<li>
							<label>Add passage
								<input type="button" id="lsa-appendChunkPress" value="Add Chunk" />
							</label>
						</li>
					</ul>
				</fieldset>
			</form>
		</div>
		
		<br style="clear:both;" />
		
		<!--	/* THE QUERY BOX */  -->
		<div id="lsa-queryEnv">
			<form name="lsa-queryForm" id="lsa-queryForm" class="lsa-genericForm">
				<fieldset>
					<ul class="lsa-formList">
						<li>
							<label>Query Set:
								<textarea name="lsa-theQuery" id="lsa-theQuery" style="font-family: Newton Sans" readonly></textarea>
							</label>
						</li>
					</ul>
				</fieldset>
			</form>
			<form name="lsa-queryFormClear" id="lsa-queryFormClear" class="lsa-genericForm">
				<fieldset>
					<ul class="lsa-formList">
						<li>
							<label>&nbsp;
								<input type="submit" id="lsa-queryFormClearButton" value="Clear Query Set" />
							</label>
							
						</li>
						
					</ul>
				</fieldset>
			</form>
			<form name="lsa-queryFormContinue" id="lsa-queryFormContinue" class="lsa-genericForm">
				<fieldset>
					<ul class="lsa-formList">
						<li>
							<label>&nbsp;
								<input type="submit" id="lsa-queryFormContinuePress" value="Continue to thresholds" />
							</label>
						</li>
						
					</ul>
				</fieldset>
			</form>
		</div>
		<br style="clear:both;"/>
	</div>
</div>

<!--	 	--> 
<!-- ROW #4 --> 
<!--	 	-->
<div class="lsa-row" style="background-color: #FEFEFE;">
	<div id="lsa-rowFour">
		<div id="lsa-break1"></div>
		<div id="lsa-queryArea">
			<div id="lsa-queryButtonArea" >
				<div id="lsa-queryButtons" >
					<div id="lsa-runSearch">
						<form name="lsa-search" class="lsa-genericForm">
							<fieldset>
								<ul class="lsa-formList">
									<li>
										<label>Run search
											<input type="button" value="RUN" id="lsa-runSearchPress"/>
										</label>
									</li>
								</ul>
							</fieldset>
						</form>
					</div>
					&nbsp; &nbsp;
					<div id="lsa-clearQueryEnv">
						<form name="lsa-clearQueryButton" class="lsa-genericForm">
							<fieldset>
								<ul class="lsa-formList">
									<li>
										<label>Clear query
											<input type="button" value="CLEAR QUERY" id="lsa-clearSearchPress" />
										</label>
										
									</li>
								</ul>
							</fieldset>
						</form>
					</div>
				</div>
			</div>
			<div id="lsa-docBoundEnv">
				<form name="lsa-docboundForm" class="lsa-genericForm">
					<label>Doc-Doc Correlation Threshold
					<input type="number" name="lsa-bounddocs" id="lsa-bounddocs" min="0.00" max="1.00" step="0.01" value="0.90">
					<!-- <fieldset>
						<ul class="lsa-formList">
							<li>
								<label>Doc-Doc Correlation Threshold
								<select name="lsa-bounddocs" id="lsa-bounddocs">
									<option value=""></option>
									<option value="0.9">0.9 (fewer results)</option>
									<option value="0.8">0.8</option>
									<option value="0.7">0.7</option>
									<option value="0.6">0.6</option>
									<option value="0.5">0.5</option>
									<option value="0.4">0.4</option>
									<option value="0.3">0.3</option>
									<option value="0.2">0.2</option>
									<option value="0.1">0.1 (more results)</option>
									<option value="0">0 (lowest available correlations)</option>
								</select>
								</label>
							</li>
						</ul>
					</fieldset> -->
				</form>
			</div>
			<div id="lsa-chunkBoundEnv">
				<form name="lsa-chunkboundForm" class="lsa-genericForm">
					<fieldset>
						<ul class="lsa-formList">
							<li>
								<label>Chunk &#x2013; Term Threshold
								<select name="lsa-boundchunk" id="lsa-boundchunk">
									<option value=""></option>
									<option value="0.9">0.9 (fewer results)</option>
									<option value="0.8">0.8</option>
									<option value="0.7">0.7</option>
									<option value="0.6">0.6</option>
									<option value="0.5">0.5</option>
									<option value="0.4">0.4</option>
									<option value="0.3">0.3</option>
									<option value="0.2">0.2</option>
									<option value="0.1">0.1 (more results)</option>
									<option value="0">0 (lowest available correlations)</option>
								</select>
								</label>
							</li>
						</ul>
					</fieldset>
				</form>
			</div>
			<div id="lsa-term250BoundEnv">
				<form name="lsa-term250boundForm" class="lsa-genericForm">
					<fieldset>
						<ul class="lsa-formList">
							<li>
								<label>Term-Term Threshold
								<select name="lsa-bound250" id="lsa-bound250">
									<option value=""></option>
									<option value="0.9">0.9 (fewer results)</option>
									<option value="0.8">0.8</option>
									<option value="0.7">0.7</option>
									<option value="0.6">0.6</option>
									<option value="0.5">0.5</option>
									<option value="0.4">0.4</option>
									<option value="0.3">0.3 (more results)</option>
									<option value="0.2">0.2 (lowest available correlations)</option>
								</select>
								</label>
							</li>
						</ul>
					</fieldset>
				</form>
			</div>
			<div id="lsa-termdoc250BoundEnv">
				<form name="lsa-termdoc250boundForm" class="lsa-genericForm">
					<fieldset>
						<ul class="lsa-formList">
							<li>
								<label>Term-Document Threshold
								<select name="lsa-tdbound250" id="lsa-tdbound250">
									<option value=""></option>
									<option value="0.9">0.9 (fewer results)</option>
									<option value="0.8">0.8</option>
									<option value="0.7">0.7</option>
									<option value="0.6">0.6</option>
									<option value="0.5">0.5</option>
									<option value="0.4">0.4</option>
									<option value="0.3">0.3</option>
									<option value="0.2">0.2</option>
									<option value="0.1">0.1 (more results)</option>
									<option value="0.0">0.0 (lowest available correlations)</option>
								</select>
								</label>
							</li>
						</ul>
					</fieldset>
				</form>
			</div>
			<div id="lsa-term1000BoundEnv">
				<form name="lsa-term1000boundForm" class="lsa-genericForm">
					<fieldset>
						<ul class="lsa-formList">
							<li>
								<label>Term-Term Threshold
								<select name="lsa-bound1000" id="lsa-bound1000">
									<option value=""></option>
									<option value="0.9">0.9 (fewer results)</option>
									<option value="0.8">0.8</option>
									<option value="0.7">0.7</option>
									<option value="0.6">0.6</option>
									<option value="0.5">0.5</option>
									<option value="0.4">0.4</option>
									<option value="0.3">0.3 (more results)</option>
									<option value="0.2">0.2 (lowest available correlations)</option>
								</select>
								</label>
							</li>
						</ul>
					</fieldset>
				</form>
			</div>
			<div id="lsa-termdoc1000BoundEnv">
				<form name="lsa-termdoc1000boundForm" class="lsa-genericForm">
					<fieldset>
						<ul class="lsa-formList">
							<li>
								<label>Term-Document Threshold
								<select name="lsa-tdbound1000" id="lsa-tdbound1000">
									<option value=""></option>
									<option value="0.9">0.9 (fewer results)</option>
									<option value="0.8">0.8</option>
									<option value="0.7">0.7</option>
									<option value="0.6">0.6</option>
									<option value="0.5">0.5</option>
									<option value="0.4">0.4</option>
									<option value="0.3">0.3</option>
									<option value="0.2">0.2</option>
									<option value="0.1">0.1 (more results)</option>
									<option value="0.0">0.0 (lowest available correlations)</option>
								</select>
								</label>
							</li>
						</ul>
					</fieldset>
				</form>
			</div>
		</div>
		
		<!-- BREAK2 -->
		<div id="lsa-break2"></div>
		<br style="clear:both;"/>
	</div>
</div>

<!--	 	--> 
<!-- ROW #5 --> 
<!--	 	-->
<div class="lsa-row" style="background-color: #FEFEFE;">
	<div id="lsa-rowFive" style="display:none;">
		<div id="lsa-spinningImageHolder"><img id="lsa-spinningLogo" src="images/ajax-loader.gif" alt="Waiting for Results" title="Waiting for Results" /> <span title="message"></span> </div>
		<!-- graph-related elements not visible to other user choices -->
		<!-- <textarea id='baseArea' style='display:none'>None</textarea> -->
		<!-- <textarea id='counterpartArea' style='display:none'>None</textarea> -->
		<!-- <textarea id='chunkSizeArea' style='display:none'></textarea> -->
		<!-- <textarea id='weightArea' style='display:none'></textarea> -->
		<!-- <input type="button" value="Show base node and neighbor side by side" id="lsa-sidebyside" style="display: none; height: 40px; width: 500px"/> -->
		
		<!-- target div for ajax operations -->
		<div id="lsa-results"></div>
		
		<br style="clear:both;"/>
	</div>
</div>
		
		
		
		
				
				<!--	 	--> 
				<!-- ROW #6 --> 
				<!--	 	-->
				<div class="lsa-row" style="background-color: #FEFEFE;">
					<div id="lsa-rowSix">
						<div id="lsa-info"> 
							NSF Project #0620868 &mdash; Science and Technology Studies<br/><br/>
							<em>&#x2022; If you have problems seeing the alchemical symbols correctly, please install the Newton Sans TTF font </em>(NewtonSans-UnicodeFont-2025-09-09.tff)<em> directly onto your machine. <a href="font/NewtonSans-UnicodeFont-2025-09-09.zip">Download font zip file.</a><br/>
							<br/></div>
					</div>
				</div>

		</section>	
		</div>
	</div>
</section>

<!-- CONTENT -->
	
<!-- Newton Skin -->
<?php 
require_once('design/page-footer.php');
require_once('design/jsfooter.php'); ?>

</body>
</html>	
	
<?php 
mysqli_close($connection);
?>
