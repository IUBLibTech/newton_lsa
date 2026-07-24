<?php
/*********************************
Author:  		Wally Hooper
Co-Author:  	Timothy D Bowman

Description:	Latent Semantic Analysis Tool

 
*********************************/

$this_server = $_SERVER['SERVER_NAME'];

$indexDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

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
        <p style="margin: 1em; margin-bottom:0;"><span class="instructionHeading">INSTRUCTIONS:</span> <span class="instructions">Please wait until all the libraries have loaded. (HINT: Component will be ready when Newton has appeared on the tab above.)<br/>
		To begin, choose a search type and chunk size in Step 1.
		Choose an output type for results in Step 2 then select threshold types and scopes in Step 3.
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
								<label>Document-Document similarities
									<input type="radio" value="wholedocs" name="lsa-searchradio" aria-label="wholedocs-searchradio"/>
								</label>
							</li>
							<li>
								<label>Passage-Passage similarities
									<input type="radio" value="chunks" name="lsa-searchradio" aria-label="chunks-searchradio"/>
								</label>
							</li>
							<li>
								<label>Term-Term similarities
									<input type="radio" value="terms" name="lsa-searchradio" aria-label="terms-searchradio"/>
								</label>
							</li>
							<li>
								<label>Term-Passage correlations
									<input type="radio" value="termdoc" name="lsa-searchradio" aria-label="termdoc-searchradio"/>
								</label>
							</li>
							<!-- <li>
								<label>Passage-Term correlations
									<input type="radio" value="chunkterm" name="lsa-searchradio" aria-label="chunkterm-searchradio"/>
								</label>
							</li> -->
							<!-- <li>
								<label>Compose Query w/Terms
									<input type="radio" value="termquery" name="lsa-searchradio" aria-label="termquery-searchradio"/>
								</label>
							</li> -->
							<!-- <li>
								<label>Compose Query w/Chunks
									<input type="radio" value="chunkquery" name="lsa-searchradio" aria-label="chunkquery-searchradio"/>
								</label>
							</li> -->
						</ul>
					</fieldset>
				</form>
			</div>
			<br />
			<div id="lsa-chunkSizeDiv" class="lsa-halfOpacity">
				<form name="lsa-chunksize" id="lsa-chunksize" class="lsa-genericForm">
					<fieldset>
						<legend>Grid Passage Size:</legend>
						<ul class="lsa-formList">
							<li>
								<label>250-word Passages
									<input type="radio" value="ch250" name="lsa-chunkradio" aria-label="ch250-chunkradio"/>
								</label>
							</li>
							<li>
								<label>1000-word Passages
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
			<form name="lsa-beginQuery" id="lsa-beginQuery" class="lsa-genericForm">
				<fieldset>
					<legend></legend>
					<ul class="lsa-formList">
						<li>
							<label>&nbsp;
								<input type="submit" value="Retrieve search options from database" name="lsa-submit" id="lsa-beginQueryButton"/>
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
<!-- ROW #3 --  SELECTION OF TERMS, DOCS, OR CHUNKS --> 
<!--	 	-->
<div class="lsa-row" style="background-color: #FEFEFE;">
	<div id="lsa-rowThree" style="height: 575px">
		<!-- TERM SELECTION -- 250-WORD chunks first -->
		<!-- TERM SELECTION -- Regex Entry -->
		<div id="lsa-regexTermEnv" style="float:left; position: absolute; left: 30%">
			<div id="lsa-regexTermFormDiv">
				<form name="lsa-regexForm" class="lsa-genericForm">
					<legend for="lsa-thePattern">Use a Regex pattern to select terms:</legend><br/>
					<input type="text" name="lsa-thePattern" id="lsa-thePattern" size="25" style="font-family: GentiumNewton; font-size: 20px"/>
				</form>
			</div>
			<!-- TERM SELECTION -- Regex Add Term -->
			<div id="lsa-appendregexTermEnv" style="float:left; display: block; position: absolute; top: 90%">
				<form name="lsa-appendregexTermButtonForm" id="lsa-appendregexTermButtonForm" class="lsa-genericForm">
					<label for="lsa-appendregexTermButton">Add regex matches</label>
					<input type="button" name="lsa-appendregexTermButton" id="lsa-appendregexTermButton" value="Add" />
				</form>
			</div>
			<br style="clear:both;" />
		</div>
		
		<!-- SELECT2 WRAPPER -->	
		<div id="lsa-wholeDocsEnv" style="width: 25%; margin-top: 20px; display: none;">
			<label for="lsa-wholeDocs-select2">Which manuscripts do you want to start from?</label>
			<select id="lsa-wholeDocs-select2" name="lsa-wholeDocs-select2[]" multiple="multiple" style="width: 100%;"></select>
		</div>	
		<div id="lsa-selectChunk250Env" style="width: 25%; margin-top: 20px; display: none;">
			<label for="lsa-chunk250-select2">Which passages do you want to start from?</label>
			<select id="lsa-chunk250-select2" name="lsa-chunk250-select2[]" multiple="multiple" style="width: 100%;"></select>
		</div>	
		<div id="lsa-selectChunk1000Env" style="width: 25%; margin-top: 20px; display: none;">
			<label for="lsa-chunk1000-select2">Which passages do you want to start from?</label>
			<select id="lsa-chunk1000-select2" name="lsa-chunk1000-select2[]" multiple="multiple" style="width: 100%;"></select>
		</div>
		<div id="lsa-selectTerm250Env" style="width: 25%; margin-top: 20px; display: none;">
			<label for="lsa-term250-select2">Which terms do you want to start from?</label>
			<select id="lsa-term250-select2" name="lsa-term250-select2[]" multiple="multiple" style="width: 100%;"></select>
		</div>	
		<div id="lsa-selectTerm1000Env" style="width: 25%; margin-top: 20px; display: none;">
			<label for="lsa-term1000-select2">Which terms do you want to start from?</label>
			<select id="lsa-term1000-select2" name="lsa-term1000-select2[]" multiple="multiple" style="width: 100%;"></select>
		</div>

		<!--	/* THE QUERY SET BOX */  -->
		<div id="lsa-queryEnv" style="position: absolute; left: 55%">
			<form name="lsa-queryFormContinue" id="lsa-queryFormContinue" class="lsa-genericForm" style="float: right">
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
			<form name="lsa-queryForm" id="lsa-queryForm" class="lsa-genericForm">
				<label for="lsa-theQuery">Query Set:</label>
				<select name="lsa-theQuery" id="theQuery" size="8" style="font-family: GentiumNewton; font-size: 17.5px; width:397px" multiple>
				</select>
			</form>
			<form name="lsa-queryRemoveSelected" id="lsa-queryRemoveSelected" class="lsa-genericForm">
				<br/>
				<button id="lsa-queryRemoveSelectedButton" type="button">Remove Selected Options</button>
			</form>
			<form name="lsa-queryFormClear" id="lsa-queryFormClear" class="lsa-genericForm" style="float: right">
				<fieldset>
					<ul class="lsa-formList">
						<li>
							<label>&nbsp;
								<input type="submit" id="lsa-queryFormClearButton" value="Restart the Webapp" />
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
<!-- ROW #4   MANAGING AND INITIATING THE QUERY--> 
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
					<label for="lsa-bounddocs">Document &#x2013; Document Cosine Threshold</label><br/>
					<input type="number" name="lsa-bounddocs" id="lsa-bounddocs" min="0.30" max="1.00" step="0.01" value="0.90" style="font-size: 20px; float:left"><br/><br/>
					<label>Note: 1.0 &#x2248; 'passages are practically identical',<br/>while 0.0 &#x2248; 'they have nothing in common'.<br/>No pair cosines less than 0.3 were stored in order to save space.</label>
				</form>
			</div>
			<div id="lsa-chunkBoundEnv">
				<form name="lsa-chunkboundForm" class="lsa-genericForm">
					<label for="lsa-boundchunk">Passage &#x2013; Passage Cosine Threshold</label><br/>
					<input type="number" name="lsa-boundchunk" id="lsa-boundchunk" min="0.30" max="1.00" step="0.01" value="0.90" style="font-size: 20px; float:left"><br/><br/>
					<label>Note: 1.0 &#x2248; 'passages are practically identical',<br/>while 0.0 &#x2248; 'they have nothing in common'.<br/>No pair cosines less than 0.3 were stored in order to save space.</label>
				</form>
			</div>
			<div id="lsa-term250BoundEnv">
				<form name="lsa-term250boundForm" class="lsa-genericForm">
					<label for="lsa-bound250">Term &#x2013; Term Cosine Threshold</label><br/>
					<input type="number" name="lsa-bound250" id="lsa-bound250" min="0.20" max="1.00" step="0.01" value="0.90" style="font-size: 20px; float:left"><br/><br/>
					<label>(Note: 1.0 &#x2248; 'always together',<br/>while 0.0 &#x2248; 'never together'.<br/>No term cosines less than 0.2 were stored.)</label>
				</form>
			</div>
			<div id="lsa-term1000BoundEnv">
				<form name="lsa-term1000boundForm" class="lsa-genericForm">
					<label for="lsa-bound1000">Term &#x2013; Term Cosine Threshold</label><br/>
					<input type="number" name="lsa-bound1000" id="lsa-bound1000" min="0.20" max="1.00" step="0.01" value="0.90" style="font-size: 20px; float:left"><br/><br/>
					<label>(Note: 1.0 &#x2248; 'always together',<br/>while 0.0 &#x2248; 'never together'.<br/>No term cosines less than 0.2 were stored.)</label>
				</form>
			</div>
			<div id="lsa-termdoc250BoundEnv">
				<form name="lsa-termdoc250boundForm" class="lsa-genericForm">
					<label for="lsa-tdbound250">Term-Document Threshold</label><br/>
					<input type="number" name="lsa-tdbound250" id="lsa-tdbound250" min="0.20" max="1.00" step="0.01" value="0.90" style="font-size: 20px; float: left"><br/><br/>
					<label>(Note: 1.0 &#x2248; 'term correlated with passage',<br/>while 0.0 &#x2248; 'nothing in common'.<br/>No term cosines less than 0.2 were stored.)</label>
				</form>
			</div>
			<div id="lsa-termdoc1000BoundEnv">
				<form name="lsa-termdoc1000boundForm" class="lsa-genericForm">
					<label for="lsa-tdbound1000">Term-Document Threshold</label><br/>
					<input type="number" name="lsa-tdbound1000" id="lsa-tdbound1000" min="0.20" max="1.00" step="0.01" value="0.90" style="font-size: 20px; float: left"><br/><br/>
					<label>(Note: 1.0 &#x2248; 'the two are identical',<br/>while 0.0 &#x2248; 'nothing in common'.<br/>No term cosines less than 0.2 were stored.)</label>
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
						</div>
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
