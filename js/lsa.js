var hs = 1;
var mdb = 2;
var list = "wholedocs";
var frags = "ch250";
var scope = "all";
var outf = "ranked";
var bound = "0.9";
var qs = "cxhb"; // nonsense string

function getRadio(name) {
	var radios = document.getElementsByName(name);
	for (var i = 0; i < radios.length; i++) {
		if (radios[i].checked) {
			return radios[i].value;
		}
	}
	return null;
}

function setRadio(name, val) {
	var radios = document.getElementsByName(name);
	for (var i = 0; i < radios.length; i++) {
		radios[i].checked = (radios[i].value == val);
	}
}

function getXMLHTTPRequest() {
	try {
		req = new XMLHttpRequest();
	}
	catch(err1) {
		try {
			req = new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch(err2) {
			try {
				req = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch(err3) {
				req = false;
			}
		}
	}
	return req;
}

var http = getXMLHTTPRequest();

function collectSearchSettings() {
	hs = document.mdbForm.mdbValue.value;
	mdb = document.mdbForm.mdbValue.value;
	list = getRadio("searchradio");
	frags = getRadio("chunkradio");
	scope = getRadio("scoperadio");
	outf = getRadio("outputradio");
	if (list == "wholedocs") {
		bound = document.docboundForm.bounddocs.value;
	}
	else if (list == "chunks") {
		bound = document.chunkboundForm.boundchunk.value;
	}
	else if (list == "terms") {
		if (frags == "ch250") {
			bound = document.term250boundForm.bound250.value;
		}
		else if (frags == "ch1000") {
			bound = document.term1000boundForm.bound1000.value;
		}
	}
	else if (list == 'termdoc' || list == 'termquery') {
		if (frags == "ch250") {
			bound = document.termdoc250boundForm.tdbound250.value;
		}
		else if (frags == "ch1000") {
			bound = document.termdoc1000boundForm.tdbound1000.value;
		}
	}
	else if (list == "chunkterm" || list == "chunkquery") {
		bound = document.chunkboundForm.boundchunk.value;
	}
}

function doSearch() {
	if (qs == "") {
		document.getElementById('results').innerHTML = "Empty query! Please construct a query.";
		return;
	}
	// the query set qs is constructed by the toos on the web page
	// collectSearchSetting gets the values from the four radio button sets and three dropdowns
	collectSearchSettings();

	// block searches for "wholedocs", qs="ALL", and $bound < "0.6"
	if (qs == "ALL" && bound < 0.6) {
		alert("To search 'All documents', set the lower bound to 0.6 or higher.");
		return;
	}

	// block searches when outf is pages and there're more than one doc in qs
	if (getRadio("outputradio") == "pages" && queryelements.length > 1) {
		alert("Only one document may be selected when Output type is 'one document in page order'.");
		return;
	}

	// block searches when scope is onlyselected and there's only one doc in qs
	if (getRadio("scoperadio") == "onlyselected" && queryelements.length < 2) {
		alert("Select at least two documents when Scope is 'between selected documents or terms'.");
		return;
	}

	// make results area visible
	document.getElementById('results').style.display = "block";
	
	var searchUrl = 'docsearch.php';
	if (list == "terms") {
		searchUrl = 'termsearch.php';
	}
	else if (list == "termdoc") {
		searchUrl = 'termdocsearch.php';
	}
	else if (list == "chunkterm") {
		searchUrl = 'chunktermsearch.php';
	}
	else if (list == "termquery") {
		searchUrl = 'usersearch.php';
	}
	else if (list == "chunkquery") {
		searchUrl = 'usersearch.php';
		//alert("Still under construction, sorry.");
		//return;
	}
	// note: mdb = 2, defined at top of this file, db-dev
	searchUrl = searchUrl+"?hs="+hs
	searchUrl = searchUrl+"&mdb="+mdb;
	searchUrl = searchUrl+"&list="+list;
	searchUrl = searchUrl+"&frags="+frags;
	searchUrl = searchUrl+"&scope="+scope;
	searchUrl = searchUrl+"&outf="+outf;
	searchUrl = searchUrl+"&bound="+bound;
	searchUrl = searchUrl+"&qs="+qs;
	
	//  now we do the work
	http.open("GET", searchUrl, true);// set the callback function
	http.onreadystatechange = useHttpResponse;
	http.send(null);
	
	// hide the Run search button
	document.getElementById('queryButtons').style.display = "none";
}

function useHttpResponse() {
	if (http.readyState == 4) {
		if (http.status == 200) {
			var thisText = http.responseText;
			document.getElementById('results').innerHTML = thisText;
		}
		else {
			document.getElementById('results').innerHTML = "Problem with request.<br/>"+http.status;
		}
	}
	else {
		if (list == "wholedocs" || list == "chunks") {
			document.getElementById('results').innerHTML = "<img src='http://images.code-head.com/progress-bars/4.gif'/><br/>Working through 2.3 million document-document correlations greater than 0.<br/><br/>";
		}
		else if (list == "terms") {
			document.getElementById('results').innerHTML = "<img src='http://images.code-head.com/progress-bars/4.gif'/><br/>Working through 28.3 million term-term correlations greater than 0.2.<br/><br/>";
		}
		else if (list == "termdoc") {
			document.getElementById('results').innerHTML = "<img src='http://images.code-head.com/progress-bars/4.gif'/><br/>Working through 40.3 million term-document correlations greater than 0.<br/><br/>";
		}
		else if (list == "chunkterm") {
			document.getElementById('results').innerHTML = "<img src='http://images.code-head.com/progress-bars/4.gif'/><br/>Working through 40.3 million chunk-term correlations greater than 0.<br/><br/>";
		}
		else if (list == "termquery" || list == "chunkquery") {
			document.getElementById('results').innerHTML = "<img src='http://images.code-head.com/progress-bars/4.gif'/><br/>Doing real-time calculations across 24,027 term vectors and 2975 chunk vectors.<br/><br/>";
		}
	}
}

function init() {
	document.outputtype.outputradio[1].disabled = false;
	document.outputtype.outputradio[2].disabled = true;
	document.outputtype.outputradio[3].disabled = true;
	document.outputtype.outputradio[4].disabled = false;
	document.outputtype.outputradio[5].disabled = true;
	document.scopetype.scoperadio[1].disabled = false;
	document.scopetype.scoperadio[2].disabled = false;
	document.scopetype.scoperadio[3].disabled = true;
	document.scopetype.scoperadio[4].disabled = true;
	setRadio("searchradio", "wholedocs");
	setRadio("chunkradio", "ch250");
	setRadio("outputradio", "ranked");
	setRadio("scoperadio", "allcorrs");
	clearQuery();
}

function searchTypeClick() {
	setRadio("scoperadio", "allcorrs");
	setRadio("outputradio", "ranked");
	if (getRadio("searchradio") == "wholedocs") {
		document.outputtype.outputradio[1].disabled = false;
		document.outputtype.outputradio[2].disabled = true;
		document.outputtype.outputradio[3].disabled = true;
		document.outputtype.outputradio[4].disabled = false;
		document.outputtype.outputradio[5].disabled = true;
		document.scopetype.scoperadio[1].disabled = false;
		document.scopetype.scoperadio[2].disabled = false;
		document.scopetype.scoperadio[3].disabled = true;
		document.scopetype.scoperadio[4].disabled = true;
		setRadio("outputradio", "ranked");
		setRadio("scoperadio", "allcorrs");
	}
	if (getRadio("searchradio") == "chunks") {
		document.outputtype.outputradio[1].disabled = true;
		document.outputtype.outputradio[2].disabled = true;
		document.outputtype.outputradio[3].disabled = true;
		document.outputtype.outputradio[4].disabled = false;
		document.outputtype.outputradio[5].disabled = true;
		document.scopetype.scoperadio[1].disabled = true;
		document.scopetype.scoperadio[2].disabled = true;
		document.scopetype.scoperadio[3].disabled = true;
		document.scopetype.scoperadio[4].disabled = true;
		setRadio("outputradio", "ranked");
		setRadio("scoperadio", "allcorrs");
	}
	if (getRadio("searchradio") == "terms") {
		document.outputtype.outputradio[1].disabled = true;
		document.outputtype.outputradio[2].disabled = true;
		document.outputtype.outputradio[3].disabled = true;
		document.outputtype.outputradio[4].disabled = false;
		document.outputtype.outputradio[5].disabled = true;
		document.scopetype.scoperadio[1].disabled = false;
		document.scopetype.scoperadio[2].disabled = true;
		document.scopetype.scoperadio[3].disabled = true;
		document.scopetype.scoperadio[4].disabled = true;
		setRadio("outputradio", "ranked");
		setRadio("scoperadio", "allcorrs");
	}
	if (getRadio("searchradio") == "termdoc") {
		document.outputtype.outputradio[1].disabled = true;
		document.outputtype.outputradio[2].disabled = false;
		document.outputtype.outputradio[3].disabled = false;
		document.outputtype.outputradio[4].disabled = true;
		document.outputtype.outputradio[5].disabled = false;
		document.scopetype.scoperadio[1].disabled = true;
		document.scopetype.scoperadio[2].disabled = true;
		document.scopetype.scoperadio[3].disabled = false;
		document.scopetype.scoperadio[4].disabled = false;
		setRadio("outputradio", "bychunks");
		setRadio("scoperadio", "presence");
	}
	if (getRadio("searchradio") == "chunkterm") {
		document.outputtype.outputradio[1].disabled = true;
		document.outputtype.outputradio[2].disabled = false;
		document.outputtype.outputradio[3].disabled = false;
		document.outputtype.outputradio[4].disabled = true;
		document.outputtype.outputradio[5].disabled = false;
		document.scopetype.scoperadio[1].disabled = true;
		document.scopetype.scoperadio[2].disabled = true;
		document.scopetype.scoperadio[3].disabled = false;
		document.scopetype.scoperadio[4].disabled = false;
		setRadio("outputradio", "byterms");
		setRadio("scoperadio", "presentonly");
	}
	if (getRadio("searchradio") == "termquery") {
		document.outputtype.outputradio[1].disabled = true;
		document.outputtype.outputradio[2].disabled = true;
		document.outputtype.outputradio[3].disabled = true;
		document.outputtype.outputradio[4].disabled = true;
		document.outputtype.outputradio[5].disabled = true;
		document.scopetype.scoperadio[1].disabled = true;
		document.scopetype.scoperadio[2].disabled = true;
		document.scopetype.scoperadio[3].disabled = false;
		document.scopetype.scoperadio[4].disabled = false;
		setRadio("outputradio", "ranked");
		setRadio("scoperadio", "presence");
	}
	if (getRadio("searchradio") == "chunkquery") {
		document.outputtype.outputradio[1].disabled = true;
		document.outputtype.outputradio[2].disabled = true;
		document.outputtype.outputradio[3].disabled = true;
		document.outputtype.outputradio[4].disabled = true;
		document.outputtype.outputradio[5].disabled = true;
		document.scopetype.scoperadio[1].disabled = true;
		document.scopetype.scoperadio[2].disabled = true;
		document.scopetype.scoperadio[3].disabled = false;
		document.scopetype.scoperadio[4].disabled = false;
		setRadio("outputradio", "ranked");
		setRadio("scoperadio", "allcorrs");
	}
}

function outputTypeClick() {
	if (getRadio("outputradio") == "pages") {
		document.scopetype.scoperadio[1].disabled = true;
		document.scopetype.scoperadio[2].disabled = true;
	}
	else if (getRadio("searchradio") == "wholedocs") {
		document.scopetype.scoperadio[1].disabled = false;
		document.scopetype.scoperadio[2].disabled = false;
	}
	if (getRadio("searchradio") == "terms") {
		if (getRadio("outputradio") == "pages") {
			setRadio("outputradio", "ranked");
		}
	}
	if (getRadio("scoperadio") == "onlyselected") {
		if (getRadio("outputradio") == "pages") {
			setRadio("scoperadio","allcorrs");
		}
	}
	if (getRadio("scoperadio") == "internal") {
		if (getRadio("outputradio") == "pages") {
			setRadio("scoperadio","allcorrs");
		}
	}
}

function scopeTypeClick() {
	if (getRadio("searchradio") == "terms") {
		if (getRadio("scoperadio") == "internal") {
			alert("'Correlations within one document' is only available when search type is 'Documents.'");
			setRadio("scoperadio", "allcorrs");
		}
	}
	if (getRadio("outputradio") == "pages") {
		if (getRadio("scoperadio") == "onlyselected") {
			alert("'Correlations between selected' not available when output type is 'Page order.'\
					Use 'All correlations' instead.");
			setRadio("scoperadio","allcorrs");
		}
		if (getRadio("scoperadio") == "internal") {
			alert("'Correlations within a document' not available when output type is 'Page order.'\
					Use 'All correlations' instead.");
			setRadio("scoperadio","allcorrs");
		}
	}
}

var lastlist = "wholedocs";
var lastset = "ch250";

function letUserWork() {
	var presentlist = getRadio("searchradio");
	var presentset = getRadio("chunkradio");
	if (presentlist != lastlist || presentset != lastset) {
		clearQuery();
	}
	lastlist = presentlist;
	lastset = presentset;
	
	if (getRadio("searchradio") == "wholedocs") {
		// Documents is checked
		document.getElementById('selectDocEnv').style.display = "block";
		document.getElementById('selectChunk250Env').style.display = "none";
		document.getElementById('selectChunk1000Env').style.display = "none";
		document.getElementById('appendDocEnv').style.display = "block";
		document.getElementById('appendChunkEnv').style.display = "none";
		document.getElementById('docBoundEnv').style.display = "block";
		document.getElementById('chunkBoundEnv').style.display = "none";
		document.getElementById('selectTerm250Env').style.display = "none";
		document.getElementById('appendTerm250Env').style.display = "none";
		document.getElementById('regexTermEnv').style.display = "none";
		document.getElementById('appendregexTermEnv').style.display = "none";
		document.getElementById('term250BoundEnv').style.display = "none";
		document.getElementById('termdoc250BoundEnv').style.display = "none";
		document.getElementById('selectTerm1000Env').style.display = "none";
		document.getElementById('appendTerm1000Env').style.display = "none";
		document.getElementById('term1000BoundEnv').style.display = "none";
		document.getElementById('termdoc1000BoundEnv').style.display = "none";
		// set font families
		document.getElementById("theQuery").style.fontFamily = "GentiumNewton, 'Times New Roman', serif";
		document.getElementById('results').style.fontFamily = "GentiumNewton, 'Times New Roman', serif";
	}
	else if (getRadio("searchradio") == "chunks") {
		// Passage-Term is checked, so which chunk set is being used?
		if (getRadio("chunkradio") == "ch250") {
			// 250-word chunks
			document.getElementById('selectDocEnv').style.display = "none";
			document.getElementById('selectChunk250Env').style.display = "block";
			document.getElementById('selectChunk1000Env').style.display = "none";
			document.getElementById('appendDocEnv').style.display = "none";
			document.getElementById('appendChunkEnv').style.display = "block";
			document.getElementById('docBoundEnv').style.display = "none";
			document.getElementById('chunkBoundEnv').style.display = "block";
			document.getElementById('selectTerm250Env').style.display = "none";
			document.getElementById('appendTerm250Env').style.display = "none";
			document.getElementById('term250BoundEnv').style.display = "none";
			document.getElementById('termdoc250BoundEnv').style.display = "none";
			document.getElementById('selectTerm1000Env').style.display = "none";
			document.getElementById('appendTerm1000Env').style.display = "none";
			document.getElementById('term1000BoundEnv').style.display = "none";
			document.getElementById('termdoc1000BoundEnv').style.display = "none";
		}
		else {
			// 1000-word chunks
			document.getElementById('selectDocEnv').style.display = "none";
			document.getElementById('selectChunk250Env').style.display = "none";
			document.getElementById('selectChunk1000Env').style.display = "block";
			document.getElementById('appendDocEnv').style.display = "none";
			document.getElementById('appendChunkEnv').style.display = "block";
			document.getElementById('docBoundEnv').style.display = "none";
			document.getElementById('chunkBoundEnv').style.display = "block";
			document.getElementById('selectTerm250Env').style.display = "none";
			document.getElementById('appendTerm250Env').style.display = "none";
			document.getElementById('term250BoundEnv').style.display = "none";
			document.getElementById('termdoc250BoundEnv').style.display = "none";
			document.getElementById('selectTerm1000Env').style.display = "none";
			document.getElementById('appendTerm1000Env').style.display = "none";
			document.getElementById('term1000BoundEnv').style.display = "none";
			document.getElementById('termdoc1000BoundEnv').style.display = "none";
		}
		document.getElementById('regexTermEnv').style.display = "none";
		document.getElementById('appendregexTermEnv').style.display = "none";
		document.getElementById("theQuery").style.fontFamily = "GentiumNewton, 'Times New Roman', serif";
		document.getElementById('results').style.fontFamily = "GentiumNewton, 'Times New Roman', serif";
	}
	else if (getRadio("searchradio") == "terms") {
		// Terms is checked, so which chunk set is being used?
		if (getRadio("chunkradio") == "ch250") {
			// 250-word chunks
			document.getElementById('selectDocEnv').style.display = "none";
			document.getElementById('selectChunk250Env').style.display = "none";
			document.getElementById('selectChunk1000Env').style.display = "none";
			document.getElementById('appendDocEnv').style.display = "none";
			document.getElementById('appendChunkEnv').style.display = "none";
			document.getElementById('docBoundEnv').style.display = "none";
			document.getElementById('chunkBoundEnv').style.display = "none";
			document.getElementById('selectTerm250Env').style.display = "block";
			document.getElementById('appendTerm250Env').style.display = "block";
			document.getElementById('term250BoundEnv').style.display = "block";
			document.getElementById('termdoc250BoundEnv').style.display = "none";
			document.getElementById('selectTerm1000Env').style.display = "none";
			document.getElementById('appendTerm1000Env').style.display = "none";
			document.getElementById('term1000BoundEnv').style.display = "none";
			document.getElementById('termdoc1000BoundEnv').style.display = "none";
		}
		else {
			// 1000-word chunks
			document.getElementById('selectDocEnv').style.display = "none";
			document.getElementById('selectChunk250Env').style.display = "none";
			document.getElementById('selectChunk1000Env').style.display = "none";
			document.getElementById('appendDocEnv').style.display = "none";
			document.getElementById('appendChunkEnv').style.display = "none";
			document.getElementById('docBoundEnv').style.display = "none";
			document.getElementById('chunkBoundEnv').style.display = "none";
			document.getElementById('selectTerm250Env').style.display = "none";
			document.getElementById('appendTerm250Env').style.display = "none";
			document.getElementById('term250BoundEnv').style.display = "none";
			document.getElementById('termdoc250BoundEnv').style.display = "none";
			document.getElementById('selectTerm1000Env').style.display = "block";
			document.getElementById('appendTerm1000Env').style.display = "block";
			document.getElementById('term1000BoundEnv').style.display = "block";
			document.getElementById('termdoc1000BoundEnv').style.display = "none";
		}
		document.getElementById('regexTermEnv').style.display = "block";
		document.getElementById('appendregexTermEnv').style.display = "block";
		document.getElementById("theQuery").style.fontFamily = "GentiumNewton, 'Times New Roman', serif";
		document.getElementById('results').style.fontFamily = "GentiumNewton, 'Times New Roman', serif";
	}
	else if (getRadio("searchradio") == "termdoc") {
		// Term-Document is checked, so which chunk set is being used?
		if (getRadio("chunkradio") == "ch250") {
			// 250-word chunks
			document.getElementById('selectDocEnv').style.display = "none";
			document.getElementById('selectChunk250Env').style.display = "none";
			document.getElementById('selectChunk1000Env').style.display = "none";
			document.getElementById('appendDocEnv').style.display = "none";
			document.getElementById('appendChunkEnv').style.display = "none";
			document.getElementById('docBoundEnv').style.display = "none";
			document.getElementById('chunkBoundEnv').style.display = "none";
			document.getElementById('selectTerm250Env').style.display = "block";
			document.getElementById('appendTerm250Env').style.display = "block";
			document.getElementById('term250BoundEnv').style.display = "none";
			document.getElementById('termdoc250BoundEnv').style.display = "block";
			document.getElementById('selectTerm1000Env').style.display = "none";
			document.getElementById('appendTerm1000Env').style.display = "none";
			document.getElementById('term1000BoundEnv').style.display = "none";
			document.getElementById('termdoc1000BoundEnv').style.display = "none";
		}
		else {
			// 1000-word chunks
			document.getElementById('selectDocEnv').style.display = "none";
			document.getElementById('selectChunk250Env').style.display = "none";
			document.getElementById('selectChunk1000Env').style.display = "none";
			document.getElementById('appendDocEnv').style.display = "none";
			document.getElementById('appendChunkEnv').style.display = "none";
			document.getElementById('docBoundEnv').style.display = "none";
			document.getElementById('chunkBoundEnv').style.display = "none";
			document.getElementById('selectTerm250Env').style.display = "none";
			document.getElementById('appendTerm250Env').style.display = "none";
			document.getElementById('term250BoundEnv').style.display = "none";
			document.getElementById('termdoc250BoundEnv').style.display = "none";
			document.getElementById('selectTerm1000Env').style.display = "block";
			document.getElementById('appendTerm1000Env').style.display = "block";
			document.getElementById('term1000BoundEnv').style.display = "none";
			document.getElementById('termdoc1000BoundEnv').style.display = "block";
		}
		document.getElementById('regexTermEnv').style.display = "block";
		document.getElementById('appendregexTermEnv').style.display = "block";
		document.getElementById("theQuery").style.fontFamily = "GentiumNewton, 'Times New Roman', serif";
		document.getElementById('results').style.fontFamily = "GentiumNewton, 'Times New Roman', serif";
	}
	else if (getRadio("searchradio") == "chunkterm") {
		// Passage-Term is checked, so which chunk set is being used?
		if (getRadio("chunkradio") == "ch250") {
			// 250-word chunks
			document.getElementById('selectDocEnv').style.display = "none";
			document.getElementById('selectChunk250Env').style.display = "block";
			document.getElementById('selectChunk1000Env').style.display = "none";
			document.getElementById('appendDocEnv').style.display = "none";
			document.getElementById('appendChunkEnv').style.display = "block";
			document.getElementById('docBoundEnv').style.display = "none";
			document.getElementById('chunkBoundEnv').style.display = "block";
			document.getElementById('selectTerm250Env').style.display = "none";
			document.getElementById('appendTerm250Env').style.display = "none";
			document.getElementById('term250BoundEnv').style.display = "none";
			document.getElementById('termdoc250BoundEnv').style.display = "none";
			document.getElementById('selectTerm1000Env').style.display = "none";
			document.getElementById('appendTerm1000Env').style.display = "none";
			document.getElementById('term1000BoundEnv').style.display = "none";
			document.getElementById('termdoc1000BoundEnv').style.display = "none";
		}
		else {
			// 1000-word chunks
			document.getElementById('selectDocEnv').style.display = "none";
			document.getElementById('selectChunk250Env').style.display = "none";
			document.getElementById('selectChunk1000Env').style.display = "block";
			document.getElementById('appendDocEnv').style.display = "none";
			document.getElementById('appendChunkEnv').style.display = "block";
			document.getElementById('docBoundEnv').style.display = "none";
			document.getElementById('chunkBoundEnv').style.display = "block";
			document.getElementById('selectTerm250Env').style.display = "none";
			document.getElementById('appendTerm250Env').style.display = "none";
			document.getElementById('term250BoundEnv').style.display = "none";
			document.getElementById('termdoc250BoundEnv').style.display = "none";
			document.getElementById('selectTerm1000Env').style.display = "none";
			document.getElementById('appendTerm1000Env').style.display = "none";
			document.getElementById('term1000BoundEnv').style.display = "none";
			document.getElementById('termdoc1000BoundEnv').style.display = "none";
		}
		document.getElementById('regexTermEnv').style.display = "none";
		document.getElementById('appendregexTermEnv').style.display = "none";
		document.getElementById("theQuery").style.fontFamily = "GentiumNewton, 'Times New Roman', serif";
		document.getElementById('results').style.fontFamily = "GentiumNewton, 'Times New Roman', serif";
	}
	else if (getRadio("searchradio") == "termquery") {
		// Term query is checked, so which chunk set is being used?
		if (getRadio("chunkradio") == "ch250") {
			// 250-word chunks
			document.getElementById('selectDocEnv').style.display = "none";
			document.getElementById('selectChunk250Env').style.display = "none";
			document.getElementById('selectChunk1000Env').style.display = "none";
			document.getElementById('appendDocEnv').style.display = "none";
			document.getElementById('appendChunkEnv').style.display = "none";
			document.getElementById('docBoundEnv').style.display = "none";
			document.getElementById('chunkBoundEnv').style.display = "none";
			document.getElementById('selectTerm250Env').style.display = "block";
			document.getElementById('appendTerm250Env').style.display = "block";
			document.getElementById('term250BoundEnv').style.display = "none";
			document.getElementById('termdoc250BoundEnv').style.display = "block";
			document.getElementById('selectTerm1000Env').style.display = "none";
			document.getElementById('appendTerm1000Env').style.display = "none";
			document.getElementById('term1000BoundEnv').style.display = "none";
			document.getElementById('termdoc1000BoundEnv').style.display = "none";
		}
		else {
			// 1000-word chunks
			document.getElementById('selectDocEnv').style.display = "none";
			document.getElementById('selectChunk250Env').style.display = "none";
			document.getElementById('selectChunk1000Env').style.display = "none";
			document.getElementById('appendDocEnv').style.display = "none";
			document.getElementById('appendChunkEnv').style.display = "none";
			document.getElementById('docBoundEnv').style.display = "none";
			document.getElementById('chunkBoundEnv').style.display = "none";
			document.getElementById('selectTerm250Env').style.display = "none";
			document.getElementById('appendTerm250Env').style.display = "none";
			document.getElementById('term250BoundEnv').style.display = "none";
			document.getElementById('termdoc250BoundEnv').style.display = "none";
			document.getElementById('selectTerm1000Env').style.display = "block";
			document.getElementById('appendTerm1000Env').style.display = "block";
			document.getElementById('term1000BoundEnv').style.display = "none";
			document.getElementById('termdoc1000BoundEnv').style.display = "block";
		}
		document.getElementById('regexTermEnv').style.display = "block";
		document.getElementById('appendregexTermEnv').style.display = "block";
		document.getElementById("theQuery").style.fontFamily = "GentiumNewton, 'Times New Roman', serif";
		document.getElementById('results').style.fontFamily = "GentiumNewton, 'Times New Roman', serif";
	}
	else if (getRadio("searchradio") == "chunkquery") {
		// Passage  query is checked, so which chunk set is being used?
		if (getRadio("chunkradio") == "ch250") {
			// 250-word chunks
			document.getElementById('selectDocEnv').style.display = "none";
			document.getElementById('selectChunk250Env').style.display = "block";
			document.getElementById('selectChunk1000Env').style.display = "none";
			document.getElementById('appendDocEnv').style.display = "none";
			document.getElementById('appendChunkEnv').style.display = "block";
			document.getElementById('docBoundEnv').style.display = "none";
			document.getElementById('chunkBoundEnv').style.display = "block";
			document.getElementById('selectTerm250Env').style.display = "none";
			document.getElementById('appendTerm250Env').style.display = "none";
			document.getElementById('term250BoundEnv').style.display = "none";
			document.getElementById('termdoc250BoundEnv').style.display = "none";
			document.getElementById('selectTerm1000Env').style.display = "none";
			document.getElementById('appendTerm1000Env').style.display = "none";
			document.getElementById('term1000BoundEnv').style.display = "none";
			document.getElementById('termdoc1000BoundEnv').style.display = "none";
		}
		else {
			// 1000-word chunks
			document.getElementById('selectDocEnv').style.display = "none";
			document.getElementById('selectChunk250Env').style.display = "none";
			document.getElementById('selectChunk1000Env').style.display = "block";
			document.getElementById('appendDocEnv').style.display = "none";
			document.getElementById('appendChunkEnv').style.display = "block";
			document.getElementById('docBoundEnv').style.display = "none";
			document.getElementById('chunkBoundEnv').style.display = "block";
			document.getElementById('selectTerm250Env').style.display = "none";
			document.getElementById('appendTerm250Env').style.display = "none";
			document.getElementById('term250BoundEnv').style.display = "none";
			document.getElementById('termdoc250BoundEnv').style.display = "none";
			document.getElementById('selectTerm1000Env').style.display = "none";
			document.getElementById('appendTerm1000Env').style.display = "none";
			document.getElementById('term1000BoundEnv').style.display = "none";
			document.getElementById('termdoc1000BoundEnv').style.display = "none";
		}
		document.getElementById('regexTermEnv').style.display = "none";
		document.getElementById('appendregexTermEnv').style.display = "none";
		document.getElementById("theQuery").style.fontFamily = "GentiumNewton, 'Times New Roman', serif";
		document.getElementById('results').style.fontFamily = "GentiumNewton, 'Times New Roman', serif";
	}

	document.getElementById('queryEnv').style.display = "block";
	document.getElementById('queryButtonArea').style.display = "block";
	document.getElementById('queryButtons').style.display = "block";
	document.getElementById('results').innerHTML = "";
}

function clearQuery() {
	queryelements.length = 0;
	qs = "";
	document.queryForm.theQuery.value = "";
	document.getElementById('bound250').selectedIndex = 0;
	document.getElementById('bound1000').selectedIndex = 0;
	document.getElementById('tdbound250').selectedIndex = 0;
	document.getElementById('tdbound1000').selectedIndex = 0;
	document.getElementById('bounddocs').selectedIndex = 0;
	// to aid debugging
	document.getElementById('results').innerHTML = "";
}

//array to hold selected query elements
var queryelements = new Array();

function addTerm250ToQuery() {
	// temporary use of result html element to view the hidden field
	//document.getElementById('results').style.display = "block";

	// what's newly selected?
	selectedValue = document.selectTerm250Form.selectterm250.value;
	selectedOption = document.selectTerm250Form.selectterm250.selectedIndex;

	currentChunks = getRadio("chunkradio");

	// if the user selects or has already selected "All terms" zero out the existing queryelements array
	if (selectedOption == 0 || queryelements[0] == 0) {
		queryelements.length = 0;
	}
	addItemToQuery(selectedOption);
	updateQueryDisplay("terms250");
	return;
}

function addTerm1000ToQuery() {
	// temporary use of result html element to view the hidden field
	//document.getElementById('results').style.display = "block";

	// what's newly selected?
	selectedOption = document.selectTerm1000Form.selectterm1000.selectedIndex;

	currentChunks = getRadio("chunkradio");

	// if the user selects or has already selected "All terms" zero out the existing queryelements array
	if (selectedOption == 0 || queryelements[0] == 0) {
		queryelements.length = 0;
	}
	addItemToQuery(selectedOption);
	updateQueryDisplay("terms1000");
	return;
}

function addDocToQuery() {
	// temporary use of result html element to view the hidden field
	document.getElementById('results').style.display = "block";

	// what's newly selected?
	selectedValue = document.selectDocForm.selectdoc.value;
	selectedOption = document.selectDocForm.selectdoc.selectedIndex;

	// to prevent impossible jobs, get info about current settings
	currentScope = getRadio("scoperadio");
	currentOutput = getRadio("outputradio");
	//alert("scope= "+currentScope+", output= "+currentOutput);
	if (queryelements.length == 1 || selectedOption == 0) {
		if (currentScope == "internal") {
			alert("More than one document is not an option for 'Correlations within one document.'");
			return;
		}
		if (currentOutput == "pages") {
			alert("More than one document is not an option for 'Page order' output.");
			return;
		}
	}

	// if the user selects or has already selected "All documents" zero out the existing queryelements array
	if (selectedOption == 0 || queryelements[0] == 0) {
		queryelements.length = 0;
	}
	addItemToQuery(selectedOption);
	updateQueryDisplay("docs");
	return;
}

function addChunkToQuery() {
	// temporary use of result html element to view the hidden field
	document.getElementById('results').style.display = "block";

	currentChunks = getRadio("chunkradio");
	
	// what's newly selected?
	if (currentChunks == "ch250") {
		selectedValue = document.selectChunk250Form.selectchunk250.value;
		selectedOption = document.selectChunk250Form.selectchunk250.selectedIndex;
	}
	else if (currentChunks == "ch1000") {
		selectedValue = document.selectChunk1000Form.selectchunk1000.value;
		selectedOption = document.selectChunk1000Form.selectchunk1000.selectedIndex;
	}

	// to prevent impossible jobs, get info about current settings
	currentScope = getRadio("scoperadio");
	currentOutput = getRadio("outputradio");
	//alert("scope= "+currentScope+", output= "+currentOutput);

	// if the user selects or has already selected "All document chunks" zero out the existing queryelements array
	if (selectedOption == 0 || queryelements[0] == 0) {
		queryelements.length = 0;
	}
	addItemToQuery(selectedOption);
	
	if (currentChunks == "ch250") {
		updateQueryDisplay("chunk250");
	}
	else if (currentChunks == "ch1000") {
		updateQueryDisplay("chunk1000");
	}
	return;
}

function addRegexPatternToQuery() {
	if (document.getElementById('thePattern').value == "") {
		return;
	}
	regexPattern = new RegExp(document.getElementById('thePattern').value);
	
	// determine current state
	// currentSearch must be "terms" or "termdoc" by construction
	//currentSearch = getRadio("searchradio");
	currentChunks = getRadio("chunkradio");
	// to prevent impossible jobs, get info about current settings
	//currentScope = getRadio("scoperadio");
	//if (currentScope == "presence") {
	//	alert("Regular expressions not available when testing for presence of one term.");
	//	return;
	//}
	currentOutput = getRadio("outputradio");
	//alert("search= "+currentSearch+", chunks= "+currentChunks+", scope= "+currentScope+", output= "+currentOutput);

	if (currentChunks == "ch250") {
		// traverse the term250 list and test them for the pattern
		tcount = document.selectTerm250Form.selectterm250.length;
		for (termi = 0; termi < tcount; termi++) {
			if (regexPattern.exec(document.selectTerm250Form.selectterm250[termi].value)) {
				addItemToQuery(termi);
			}
		}
		//
		updateQueryDisplay("terms250");
	}
	else if (currentChunks == "ch1000") {
		// traverse the term1000 list and test them for the pattern
		tcount = document.selectTerm1000Form.selectterm1000.length;
		for (termi = 0; termi < tcount; termi++) {
			if (regexPattern.exec(document.selectTerm1000Form.selectterm1000[termi].value)) {
				addItemToQuery(termi);
			}
		}
		//
		updateQueryDisplay("terms1000");
	}
	return;	
}

function addItemToQuery(selectedOption) {
	// using array queryelements to hold selected and organize theQuery and queryString values
	if (queryelements.indexOf(selectedOption) > -1) {
		// item is already in queryelements
		return;
	}
	
	queryelements.push(selectedOption);
	queryelements.sort(function (a,b) {return a-b });

	return;
}

function updateQueryDisplay(selectset) {
	qs = "";
	document.queryForm.theQuery.value = "";
	newString = "";
	newDisplay = "";
	for (i = 0; i < queryelements.length; i++) {
		if (i > 0) {
			newString = newString + "_";
			newDisplay = newDisplay + "\n";
		}
		if (selectset == "docs") {
			newString = newString + document.selectDocForm.selectdoc[queryelements[i]].value;
			newDisplay = newDisplay + document.selectDocForm.selectdoc[queryelements[i]].text;
		}
		else if (selectset == "terms250") {
			newString = newString + document.selectTerm250Form.selectterm250[queryelements[i]].value;
			newDisplay = newDisplay + document.selectTerm250Form.selectterm250[queryelements[i]].text;
		}
		else if (selectset == "terms1000") {
			newString = newString + document.selectTerm1000Form.selectterm1000[queryelements[i]].value;
			newDisplay = newDisplay + document.selectTerm1000Form.selectterm1000[queryelements[i]].text;
		}
		else if (selectset == "chunk250") {
			newString = newString + document.selectChunk250Form.selectchunk250[queryelements[i]].value;
			newDisplay = newDisplay + document.selectChunk250Form.selectchunk250[queryelements[i]].text;
		}
		else if (selectset == "chunk1000") {
			newString = newString + document.selectChunk1000Form.selectchunk1000[queryelements[i]].value;
			newDisplay = newDisplay + document.selectChunk1000Form.selectchunk1000[queryelements[i]].text;
		}
	}
	qs = newString;
	//document.getElementById('results').innerHTML = qs;
	document.queryForm.theQuery.value = newDisplay;
}

function openViewer(chset, chunk1, chunk2, correlation) {
	window.open("displaycorrs.php?hs="+hs+"&mdb="+mdb+"&frags="+chset+"&doc1="+chunk1+"&doc2="+chunk2+"&corr="+correlation);
}

function openTDViewer(chset, chunk, term, correlation) {
	window.open("displayTDdoc.php?hs="+hs+"&mdb="+mdb+"&frags="+chset+"&doc="+chunk+"&term="+term+"&corr="+correlation);
}