HelpScribble project file.
13
Nzrevpna Vaqvna `ghqvrf _rfrnepu Vafgvghgr-18P375
0
1
Chymistry of Isaac Newton LSA Help



FALSE

C:\Users\whooper\Dropbox\_wally\CHYMIS~1\LSAHelp\images,C:\DOCUME~1\WALLAC~1\MYDOCU~1\MYDROP~1\_wally\SWINBU~1\LSAHelp\images,C:\Users\whooper\Dropbox\_wally\CHYMIS~1\images
1
BrowseButtons()
0
FALSE

FALSE
TRUE
16777215
0
16711680
8388736
255
FALSE
FALSE
FALSE
1
FALSE
FALSE
Contents
%s Contents
Index
%s Index
Previous
Next
FALSE
C:\Users\whooper\Dropbox\_wally\Chymistry\LSAhelp\ChymistryLSAhelp.htm
38
10
Scribble10
Chymistry of Isaac Newton LSA Help




Writing



FALSE
10
{\rtf1\ansi\ansicpg1252\deff0{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil Arial;}}
{\colortbl ;\red0\green0\blue255;\red0\green128\blue0;\red128\green0\blue0;}
\viewkind4\uc1\pard\cf1\lang1033\b\fs32 Chymistry of Isaac Newton LSA Help\f1 
\par 
\par \cf0\b0\f0\fs24 The \i Chymistry of Isaac Newton Project\i0  has analyzed the entire corpus published on our website using Latent Semantic Analysis (LSA) and is making the results available to interested readers and researchers.
\par 
\par \tab\cf2\strike About Latent Semantic Analysis\cf3\strike0\{linkID=20\}
\par 
\par \tab\cf2\strike Search types\cf3\strike0\{linkID=40\}\cf1\b\f1\fs32 
\par }
20
Scribble20
About Latent Semantic Analysis



:000010
Writing



FALSE
18
{\rtf1\ansi\ansicpg1252\deff0\deflang1033{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil Arial;}{\f2\fnil Courier New;}{\f3\fnil\fcharset0 Courier New;}}
{\colortbl ;\red0\green0\blue255;\red128\green0\blue0;}
\viewkind4\uc1\pard\cf1\b\fs32 About Latent Semantic Analysis\cf0\b0\f1\fs20 
\par 
\par \f0\fs24 Latent semantic analysis (LSA) is a computational method that allows researchers to analyze the relationships between all terms and documents in a corpus.
\par 
\par You can find a brief introduction to LSA in the Wikipedia entry for \cf1\strike\f2\fs20 Latent semantic analysis.\cf2\strike0\{link=*! ExecFile("http://en.wikipedia.org/wiki/Latent_semantic_analysis")\}\f3 .\cf0\f0\fs24 
\par 
\par LSA calculates correlations between documents, correlations between terms, and correlations between terms and documents. The method begins with a \i term-document matrix\i0  whose rows list all the terms in the corpus and whose columns tally the frequencies of the terms in each document.
\par 
\par LSA uses \i singular value decomposition\i0  to produce vectors for each of the terms and each of the documents. Each vector is an \i n-dimensional\i0  description of the terms and the documents. LSA then applies \i rank reduction\i0  to simplify the calculations and expose the most important dimensions that capture the \i latent\i0  structure of the corpus in terms of its semantics.
\par 
\par We use the term vectors and document vectors to calculate the correlations as cosine similarites: the result gives the cosine of the angle between the two term vectors or document vectors, or between a term vector and a document vector. The closer the cosine is to 1.0, the greater the similarity between two documents or the greater the semantic relationship between two terms. As the cosine approaches zero there is no real relationship at all.
\par 
\par The Swinburne LSA component stores all significant term-term, document-document, and term-document correlations in a database and provides a suite of built-in query tools that a user can use to investigate those relationships. There are several million correlations and the tools allow us to make both broad and focused queries
\par 
\par The LSA also supports user queries based on choice of terms or on choice of documents.\f1\fs20 
\par }
30
Scribble30
Using the LSA component




Writing



FALSE
6
{\rtf1\ansi\ansicpg1252\deff0\deflang1033{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil Arial;}}
{\colortbl ;\red0\green0\blue255;}
\viewkind4\uc1\pard\cf1\b\fs32 Using the LSA component\cf0\b0\f1\fs20 
\par 
\par 
\par }
35
Scribble35
Documents are Chunked for Text Analaysis



:000020
Writing



FALSE
15
{\rtf1\ansi\ansicpg1252\deff0\deflang1033{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil Arial;}}
{\colortbl ;\red0\green0\blue255;}
\viewkind4\uc1\pard\cf1\b\fs32 Documents are Chunked for Text Analaysis\cf0\b0\f1\fs20 
\par 
\par \f0 In information retrieval projects, which try to return the best answers for user queries,
\par each individual document in a corpus is usually treated as a unit.
\par 
\par In text analysis in the digital humanities, however, it is more useful to parse documents
\par into much smaller units or \i chunks\i0  for processing, which allows us to work at the level of 
\par passages.
\par 
\par In the Chymistry LSA project, we have parsed the documents, approximately, into
\par 250-word chunks and 1000-word chunks.\f1 
\par 
\par }
37
Scribble37
Summary of Steps in the Query Procedure



:000030
Writing



FALSE
27
{\rtf1\ansi\ansicpg1252\deff0\deflang1033{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil Arial;}}
{\colortbl ;\red0\green0\blue255;}
\viewkind4\uc1\pard\cf1\b\fs32 Summary of Steps in the Query Procedure\cf0\b0\f1\fs20 
\par 
\par \f0 Step 1.  Select a \b Search Type\b0  and select a \b Chunk Size\b0  of 250 words
\par or 1000 words.
\par 
\par Step 2.  Select a \b Results Output Type\b0 .
\par 
\par Step 3.  Constrain the scope of the search with \b Return Scope of Pairs\b0 .
\par 
\par Step 4.  \b Build a Query Set\b0 . Add documents, chunks, or terms to a
\par Query Set using built-in drop-down lists. The term query interface also 
\par provides a Regex tool to select terms, which helps with linguistic inflections.
\par 
\par Step 5. \b Execute the search\b0 . Choose a threshhold level of correlation and
\par then run the search. The correlations are reported as cosines similarities.
\par Pairs of documents or terms are more highly correlated as the cosine value
\par approaches 1.0.
\par 
\par Step 6. \b Work with the List of Results\b0 . When lists of document results are
\par returned, click on the links provided to view the chunks. Shared significant
\par vocabulary is highlighted in the viewing interface. Links to the edited document
\par in the digital corpus are included at the top of the chunks.
\par 
\par \b Clear the Query\b0  to start over.\f1 
\par }
40
Scribble40
Step 1 -- Search types




Writing



FALSE
5
{\rtf1\ansi\ansicpg1252\deff0\deflang1033{\fonttbl{\f0\fnil\fcharset0 Arial;}}
{\colortbl ;\red0\green0\blue255;}
\viewkind4\uc1\pard\cf1\b\fs32 Step 1 -- Search types
\par 
\par }
45
Scribble45
Selection of Search Type



:000040
Writing



FALSE
8
{\rtf1\ansi\ansicpg1252\deff0\deflang1033{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil Arial;}}
{\colortbl ;\red0\green0\blue255;\red128\green0\blue0;}
\viewkind4\uc1\pard\cf1\b\fs32 Selection of Search Type\cf0\b0\f1\fs20 
\par 
\par \f0 Click a radio button on the interface to select a search type. Then, under Chunk size, select  250-word Chunks or 1000-word chunks.
\par 
\par \cf2\{bml Search types.jpg\}\cf0\f1 
\par }
47
Scribble47
About the Search Types




Writing



FALSE
6
{\rtf1\ansi\ansicpg1252\deff0\deflang1033{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil Arial;}}
{\colortbl ;\red0\green0\blue255;}
\viewkind4\uc1\pard\cf1\b\fs32 About the Search Types\cf0\b0\f1\fs20 
\par 
\par 
\par }
50
Scribble50
Document-Document correlations



:000050
Writing



FALSE
24
{\rtf1\ansi\ansicpg1252\deff0\deflang1033{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil Arial;}{\f2\fnil\fcharset2 Symbol;}}
{\colortbl ;\red0\green0\blue255;\red128\green0\blue0;\red0\green128\blue0;}
\viewkind4\uc1\pard\cf1\b\fs32 Document-Document correlations\cf0\b0\f1\fs20 
\par 
\par \f0 Document-document correlations tell us whether two passages
\par share vocabulary, ideas, and semantic structure.
\par 
\par Use a Document-Document search if:
\par \cf2\{html=<br/>\}\cf0 
\par \pard{\pntext\f2\'B7\tab}{\*\pn\pnlvlblt\pnf2\pnindent0{\pntxtb\'B7}}\fi-200\li200\tx200 you're interested in a particular passage and want to find related passages; or\f1 
\par \pard\tx200\cf2\{html=\f0 <br/>\f1\}\cf0 
\par \pard{\pntext\f2\'B7\tab}{\*\pn\pnlvlblt\pnf2\pnindent0{\pntxtb\'B7}}\fi-200\li200\tx200\f0 you want to get an overview of the underlying structure of the whole corpus at\f1 
\par \pard\tx200\f0 the level of passages of 250 words or 1000 words.\f1 
\par \cf2\{html=\f0 <br/>\f1\}
\par \cf0\f0 You will be able to select one or more, or all, documents for your search.
\par The LSA component will return results for all of the chunks in the
\par selected document or documents.
\par 
\par The closer the cosine similarity approaches 1.0, the more closely related the two chunks are
\par in terms of shared vocabulary and ideas.
\par 
\par If you want to narrow your search to one or more particular passages, 
\par use the Chunk-Chunk search instead. \cf3\strike Chunk-Chunk correlations\cf2\strike0\{linkID=60\}\cf0\f1 
\par }
60
Scribble60
Chunk-Chunk correlations



:000060
Writing



FALSE
14
{\rtf1\ansi\ansicpg1252\deff0\deflang1033{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil Arial;}}
{\colortbl ;\red0\green0\blue255;\red0\green128\blue0;\red128\green0\blue0;}
\viewkind4\uc1\pard\cf1\b\fs32 Chunk-Chunk correlations\cf0\b0\f1\fs20 
\par 
\par \f0 The Chunk-Chunk search is an extension of the Document-Document
\par search type.  \cf2\strike Document-Document correlations\cf3\strike0\{linkID=50\}\cf0 
\par 
\par In the Document-Document search, you select a document and
\par the LSA component returns results for all the chunks in that document.
\par 
\par In the Chunk-Chunk search, you can select particular chunks of one or more
\par documents and the LSA component will only return results for the selected chunks
\par and not for whole document or documents.\f1 
\par }
70
Scribble70
Term-Term correlations



:000070
Writing



FALSE
15
{\rtf1\ansi\ansicpg1252\deff0\deflang1033{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil Arial;}}
{\colortbl ;\red0\green0\blue255;}
\viewkind4\uc1\pard\cf1\b\fs32 Term-Term correlations\cf0\b0\f1\fs20 
\par 
\par \f0 Term-Term correlations suggest how often two terms appear in the same chunks
\par or, to put it another way, in the same semantic networks.
\par 
\par \pard\tx200 The closer the cosine similarity approaches 1.0, the more closely related the two terms are
\par in terms of co-occurrence across all the chunks of the corpus.
\par 
\par Term-Term correlations can be used to construct \i concept newtwork graphs.\i0 
\par 
\par 
\par \pard 
\par }
80
Scribble80
Term-Chunk correlations



:000080
Writing



FALSE
20
{\rtf1\ansi\ansicpg1252\deff0\deflang1033{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil Arial;}}
{\colortbl ;\red0\green0\blue255;\red0\green128\blue0;\red128\green0\blue0;}
\viewkind4\uc1\pard\cf1\b\fs32 Term-Chunk correlations\cf0\b0\f1\fs20 
\par 
\par \f0 Term-Chunk correlations tell us how strongly terms are connected
\par with each of the chunks. Taking a single term, its term-chunk correlations
\par provide a measure of where in the corpus its use is significant.
\par 
\par Terms will frequently have strong term-chunk correlations with locations where the term
\par is not present. Words belong to semantic networks and LSA Term-chunk correlations
\par actually detect the presence of the word's semantic network.
\par 
\par When a term is present in a chunk and has a strong correlation, that location
\par will normally be very informative about the usage of that term in the corpus. Locations with
\par weaker correlations will provide less information about its usage.
\par 
\par The user can construct a query based on terms that works in a similar way.
\par See \cf2\strike Query with Terms\cf3\strike0\{linkID=100\}\cf0 
\par \f1 
\par }
90
Scribble90
Chunk-Term correlations



:000090
Writing



FALSE
14
{\rtf1\ansi\ansicpg1252\deff0\deflang1033{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil Arial;}}
{\colortbl ;\red0\green0\blue255;\red0\green128\blue0;\red128\green0\blue0;}
\viewkind4\uc1\pard\cf1\b\fs32 Chunk-Term correlations\cf0\b0\f1\fs20 
\par 
\par \f0 Chunk-Term correlations provide information about which terms
\par are significant in a given chunk with respect to the rest of the corpus.
\par 
\par For instance, if two chunks have a strong chunk-chunk correlation,
\par then their chunk-term correlations should reveal which terms and networks
\par underpin their relationship.
\par 
\par The user can construct a query that works in a similar way.
\par See \cf2\strike Query with Chunks\cf3\strike0\{linkID=110\}\cf0\f1 
\par }
100
Scribble100
Query with Terms



:000100
Writing



FALSE
16
{\rtf1\ansi\ansicpg1252\deff0\deflang1033{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil Arial;}}
{\colortbl ;\red0\green0\blue255;\red0\green128\blue0;\red128\green0\blue0;}
\viewkind4\uc1\pard\cf1\b\fs32 Query with Terms\cf0\b0\f1\fs20 
\par 
\par \f0 Instead of using the built-in correlation searches, users can construct
\par and run their own queries by selecting terms from the term list.
\par 
\par The LSA component treats the user's list as a mini-document and
\par searches for chunks that correlate with it. A user query with terms will
\par return correlated chunks and report which terms are present in those chunks.
\par 
\par This is similar to the Term-Chunk correlatiion search. See \cf2\strike Term-Chunk correlations\cf3\strike0\{linkID=80\}\cf0 
\par 
\par Because the query string will be much shorter than 250 or 1000 words, use a lower
\par correlation value when running the search.\f1 
\par }
110
Scribble110
Query with Chunks



:000110
Writing



FALSE
12
{\rtf1\ansi\ansicpg1252\deff0\deflang1033{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil Arial;}}
{\colortbl ;\red0\green0\blue255;\red0\green128\blue0;\red128\green0\blue0;}
\viewkind4\uc1\pard\cf1\b\fs32 Query with Chunks
\par \cf0\b0\f1\fs20 
\par \f0 Instead of using the built-in Chunk-Term searches, users can construct
\par and run their own queries by selecting chunks from the chunk list.
\par 
\par The LSA component will return correlated terms.
\par 
\par See \cf2\strike Term-Chunk correlations\cf3\strike0\{linkID=80\}\cf0 
\par \f1 
\par }
120
Scribble120
Step 2 -- Results Output Type




Writing



FALSE
6
{\rtf1\ansi\ansicpg1252\deff0\deflang1033{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil Arial;}}
{\colortbl ;\red0\green0\blue255;}
\viewkind4\uc1\pard\cf1\b\fs32 Step 2 -- Results Output Type\cf0\b0\f1\fs20 
\par 
\par 
\par }
130
Scribble130
Selection of Results Output Type



:000120
Writing



FALSE
10
{\rtf1\ansi\ansicpg1252\deff0\deflang1033{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil Arial;}}
{\colortbl ;\red0\green0\blue255;\red128\green0\blue0;}
\viewkind4\uc1\pard\cf1\b\fs32 Selection of Results Output Type\cf0\b0\f1\fs20 
\par 
\par \f0 Click a radio button on the interface to select a search type.
\par 
\par Each Search Type will have its own set of possible outputs.
\par \f1 
\par \cf2\{bml Result output.jpg\}\cf0 
\par }
140
Scribble140
About the Results Output Types




Writing



FALSE
6
{\rtf1\ansi\ansicpg1252\deff0\deflang1033{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil Arial;}}
{\colortbl ;\red0\green0\blue255;}
\viewkind4\uc1\pard\cf1\b\fs32 About the Results Output Types\cf0\b0\f1\fs20 
\par 
\par 
\par }
150
Scribble150
Descending Order



:000130
Writing



FALSE
17
{\rtf1\ansi\ansicpg1252\deff0\deflang1033{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil Arial;}}
{\colortbl ;\red0\green0\blue255;\red128\green0\blue0;}
\viewkind4\uc1\pard\cf1\b\fs32 Descending Order\cf0\b0\f1\fs20 
\par 
\par \f0 When the LSA component returns results in descending order,
\par the results with the highest correlations (i.e. the highest
\par cosiine similarities) are listed first and the least are listed last.
\par 
\par This option is available for every search type and can be regarded
\par as an easy default mode.
\par 
\par Listings look like this example of the results for a Document-document
\par search for \i Keynes MS. 21, "The Method of the Work."\i0  Click
\par the correlation link to view the chunk pair side by side.
\par 
\par \cf2\{bml doc-doc results.jpg\}\cf0\f1 
\par }
160
Scribble160
One Doc in Page Order



:000140
Writing



FALSE
24
{\rtf1\ansi\ansicpg1252\deff0\deflang1033{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil Arial;}}
{\colortbl ;\red0\green0\blue255;\red128\green0\blue0;}
\viewkind4\uc1\pard\cf1\b\fs32 One Doc in Page Order\cf0\b0\f1\fs20 
\par 
\par \f0 This output format is designed to create a reading guide for
\par a single full document. 
\par 
\par This option is only available for Document-document searches.
\par (Chunk-chunk searches are also excluded.) You may only
\par choose one document.
\par 
\par The results are listed in page order for the selected document
\par by its chunks. Then for each chunk in the document, all the
\par correlated passages/chunks above the selected cosine value are
\par listed in catalog order. This allows the user to study the
\par semantic relationships of the document with the rest of the
\par corpus in a systematic way.
\par 
\par The listing looks like this example for \i Keynes MS. 21, 
\par "The Method of the Work."\i0  Click the correlation link to view 
\par the chunk pair side by side.
\par 
\par \cf2\{bml PageOrderResults.jpg\}\cf0\f1 
\par }
170
Scribble170
Term Alpha Order



:000150
Writing



FALSE
17
{\rtf1\ansi\ansicpg1252\deff0\deflang1033{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil Arial;}}
{\colortbl ;\red0\green0\blue255;\red128\green0\blue0;}
\viewkind4\uc1\pard\cf1\b\fs32 Term Alpha Order\cf0\b0\f1\fs20 
\par 
\par \f0 This output format is designed to list term results in
\par alphabetical order to provide a natural and intuitive way
\par to compare and work with word forms. 
\par 
\par This option is only available for Term-Chunk and
\par Chunk-Term searches.
\par 
\par Listings look like this example of a Chunk-term search for 
\par \i Keynes MS. 21,"The Method of the Work," ff. 1r-5v\i0 . Click
\par the correlation link to view the uses of the term in the chunk.
\par 
\par \cf2\{bml TermAlphaOrder-All.jpg\}\cf0 
\par }
180
Scribble180
Doc Catalog Order



:000160
Writing



FALSE
17
{\rtf1\ansi\ansicpg1252\deff0\deflang1033{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil Arial;}}
{\colortbl ;\red0\green0\blue255;\red128\green0\blue0;}
\viewkind4\uc1\pard\cf1\b\fs32 Doc Catalog Order\cf0\b0\f1\fs20 
\par 
\par \f0 This output format is designed to list term results in
\par catalog order to allow the user to study how the terms
\par are used across the corpus in a systematic way.
\par 
\par This option is only available for Term-Chunk and
\par Chunk-Term searches.
\par 
\par Listings look like this example for the term \i leo, lion,\i0  and
\par \i lyon,\i0  and their inflections. Click the correlation link to view the
\par uses of the terms in the chunk.
\par 
\par \cf2\f1\{bml DocCatalogOrder.jpg\}\cf0 
\par }
190
Scribble190
Graph for NWB



:000170
Writing



FALSE
25
{\rtf1\ansi\ansicpg1252\deff0\deflang1033{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil Arial;}}
{\colortbl ;\red0\green0\blue255;\red128\green0\blue0;}
\viewkind4\uc1\pard\cf1\b\fs32 Graph for NWB\cf0\b0\f1\fs20 
\par 
\par \f0 This output format is designed to create network graphs
\par for use in the Network Workbench (NWB) application, which 
\par can be downloaded by following the link on the results page.
\par \cf2 
\par \{bmc NWB splash.jpg\}
\par \cf0 
\par LSA searches can produce hundreds of results. A natural and
\par intuitive way to work with large LSA result sets is to put them
\par into network graphs to visualize connections and clusters.
\par 
\par This option is available for Document-document, Chunk-chunk,
\par and Term-term searches.
\par 
\par NWB allows you to use the mouse to explore the graph and its
\par Pyhon programming interface allows you to modify colors, sizes,
\par and shapes according to defined properties in the graph files.
\par 
\par See NWB documentation for help with its tools.
\par 
\par \cf2\{bmc NWBgraph.jpg\}\cf0 
\par }
195
Scribble195
CSV: XY Term--Doc



:000180
Writing



FALSE
25
{\rtf1\ansi\ansicpg1252\deff0\deflang1033{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil Arial;}}
{\colortbl ;\red0\green0\blue255;}
\viewkind4\uc1\pard\cf1\b\fs32 CSV: XY Term--Doc\cf0\b0\f1\fs20 
\par 
\par \f0 This output format is designed to create
\par XY-scatterplots in comma-separated-value (CSV)
\par format for use in standard spreadsheet applications
\par like Microsoft Excel or OpenOffice Calc, which
\par can import CSV and create scatterplots from the data.
\par 
\par This option is available for Term-chunk and Chunk-term
\par searches. It is intended to help visualize the distribution
\par of terms across the corpus in terms of their cosine
\par correlations with the chunks.
\par 
\par This is more than simple term frequencies. The cosine
\par correlations between terms and chunks convery real
\par information about how important that chunk is to the
\par expression or definition of that term in this corpus.
\par 
\par Terms can be present in locations but not be important
\par there or their use there may be insignificant relative to
\par other passages. The LSA calculation actually provides
\par a measure of the \i locatedness\i0  of terms within the corpus.\f1 
\par }
200
Scribble200
Step 3 -- Return Scope of Pairs




Writing



FALSE
6
{\rtf1\ansi\ansicpg1252\deff0\deflang1033{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil Arial;}}
{\colortbl ;\red0\green0\blue255;}
\viewkind4\uc1\pard\cf1\b\fs32 Step 3 -- Return Scope of Pairs\cf0\b0\f1\fs20 
\par 
\par 
\par }
210
Scribble210
Selection of Return Scope of Pairs



:000190
Writing



FALSE
10
{\rtf1\ansi\ansicpg1252\deff0\deflang1033{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil Arial;}}
{\colortbl ;\red0\green0\blue255;\red128\green0\blue0;}
\viewkind4\uc1\pard\cf1\b\fs32 Selection of Return Scope of Pairs\cf0\b0\f1\fs20 
\par 
\par \f0 Click a radio button on the interface to constrain the scope of search.
\par 
\par Search Type and Output Type will change the options for scope of search.
\par \f1 
\par \cf2\{bml Scope.jpg\}\cf0 
\par }
220
Scribble220
About the Return Scope of Pairs




Writing



FALSE
6
{\rtf1\ansi\ansicpg1252\deff0\deflang1033{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil Arial;}}
{\colortbl ;\red0\green0\blue255;}
\viewkind4\uc1\pard\cf1\b\fs32 About the Return Scope of Pairs\cf0\b0\f1\fs20 
\par 
\par 
\par }
230
Scribble230
All Above Chosen Value



:000200
Writing



FALSE
11
{\rtf1\ansi\ansicpg1252\deff0\deflang1033{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil Arial;}}
{\colortbl ;\red0\green0\blue255;}
\viewkind4\uc1\pard\cf1\b\fs32 All Above Chosen Value\cf0\b0\f1\fs20 
\par 
\par \f0 This scope option returns all results greater than
\par or equal to the user selected correlation level.
\par 
\par This option is available for all search types and all
\par output types and can be regarded as an easy
\par default choice for scope.\f1 
\par }
240
Scribble240
All Between Docs or Terms



:000210
Writing



FALSE
19
{\rtf1\ansi\ansicpg1252\deff0\deflang1033{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil Arial;}}
{\colortbl ;\red0\green0\blue255;}
\viewkind4\uc1\pard\cf1\b\fs32 All Between Docs or Terms\cf0\b0\f1\fs20 
\par 
\par \f0 This scope option only returns result pairs if both members
\par (i.e. both chunks or both terms) are part of the query set
\par selected by the user.
\par 
\par This allows users to view only the connections between the
\par particular documents or terms they're interested in.
\par 
\par This option excludes results from all chunks and terms not in the
\par query set, even if their correlation values are greater than the 
\par chosen correlation level.
\par 
\par This option is only available for Document-document searches
\par and Term-term searches. It is not available for Chunk-chunk
\par searches.\f1 
\par }
250
Scribble250
Within One Document (Doc-Doc)



:000220
Writing



FALSE
16
{\rtf1\ansi\ansicpg1252\deff0\deflang1033{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil Arial;}}
{\colortbl ;\red0\green0\blue255;}
\viewkind4\uc1\pard\cf1\b\fs32 Within One Document (Doc-Doc)\cf0\b0\f1\fs20 
\par 
\par \f0 This scope option only returns chunk pairs from
\par wihtin the same document.
\par 
\par It is designed to allow a user to inspect the internal
\par semantic relationships among the chunks of selected
\par documents. No results between documents will be
\par returned.
\par 
\par This option is only available for Document-document
\par searches.
\par \f1 
\par }
260
Scribble260
All w/Term Presence (Term-Doc)



:000230
Writing



FALSE
26
{\rtf1\ansi\ansicpg1252\deff0\deflang1033{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil Arial;}}
{\colortbl ;\red0\green0\blue255;\red0\green128\blue0;\red128\green0\blue0;}
\viewkind4\uc1\pard\cf1\b\fs32 All w/Term Presence (Term-Doc)\cf0\b0\f1\fs20 
\par 
\par \f0 This scope option is only available for Term-chunk,
\par Chunk-term, Query with Terms, and Query with
\par Chunks searches. It is available for the CSV
\par XY-charts.
\par 
\par When the LSA component returns results for these
\par searches using this scope option, it will report
\par the correlation value and whether or not the term
\par is actually present in the chunk.
\par 
\par Terms will frequently have strong term-chunk correlations
\par with locations where the term is not present. Words belong
\par to semantic networks and LSA Term-chunk correlations
\par actually detect the presence of the word's semantic network.
\par 
\par This is potentially very interesting for investigating the
\par difference that terms make in their contexts.
\par 
\par To suppress chunks where terms are not present,
\par use \cf2\strike Only if Term Present (Term-Doc)\cf3\strike0\{linkID=270\}\cf0 
\par instead.\f1 
\par }
270
Scribble270
Only If Term Present (Term-Doc)



:000240
Writing



FALSE
16
{\rtf1\ansi\ansicpg1252\deff0\deflang1033{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil Arial;}}
{\colortbl ;\red0\green0\blue255;\red0\green128\blue0;\red128\green0\blue0;}
\viewkind4\uc1\pard\cf1\b\fs32 Only If Term Present (Term-Doc)\cf0\b0\f1\fs20 
\par 
\par This scope option is only available for Term-chunk,
\par Chunk-term, Query with Terms, and Query with
\par Chunks searches. It is available for the CSV
\par XY-charts.
\par 
\par When the LSA component returns results for these
\par searches using this scope option, it will report
\par the correlation value only if the term is actually present
\par in the chunk.
\par 
\par \f0 Compare with \cf2\strike\f1 All w/Term Presence (Term-Doc)\cf3\strike0\{linkID=260\}\cf0 
\par }
300
Scribble300
Step 4 -- Build a Query Set




Writing



FALSE
6
{\rtf1\ansi\deff0{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil Arial;}}
{\colortbl ;\red0\green0\blue255;}
\viewkind4\uc1\pard\cf1\lang1033\b\fs32 Step 4 -- Build a Query Set\f1 
\par 
\par 
\par }
310
Scribble310
Document Selection



:000250
Writing



FALSE
23
{\rtf1\ansi\ansicpg1252\deff0\deflang1033{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil Arial;}}
{\colortbl ;\red0\green0\blue255;\red0\green128\blue0;\red128\green0\blue0;}
\viewkind4\uc1\pard\cf1\b\fs32 Document Selection\cf0\b0\f1\fs20 
\par 
\par \f0 The dropdown list includes the titles of all of the poems,
\par essays, and introductions in the Swinburne corpus.
\par 
\par An "All documents" options is available at the top of
\par the dropdown list.
\par 
\par Use the "Add Docs" button to move selected titles or
\par the "All" option to the Query set.
\par 
\par The Document dropdown list appears for Document-Document
\par searches. All the chunks in selected documents are processed
\par during the search.
\par 
\par To narrow the search to particular chunks rather than whole
\par documents, use \cf2\strike Query with Chunks\cf3\strike0\{linkID=110\}\cf0  and
\par its selection interface, \cf2\strike Chunk Selection\cf3\strike0\{linkID=320\}\cf0 .
\par 
\par \cf3\{bml Document choice.jpg\}\cf0\f1 
\par }
320
Scribble320
Chunk Selection



:000260
Writing



FALSE
21
{\rtf1\ansi\ansicpg1252\deff0\deflang1033{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil Arial;}}
{\colortbl ;\red0\green0\blue255;\red128\green0\blue0;}
\viewkind4\uc1\pard\cf1\b\fs32 Chunk Selection\cf0\b0\f1\fs20 
\par 
\par \f0 The dropdown list includes the chunks of all of the poems,
\par essays, and introductions in the Swinburne corpus.
\par 
\par An "All Chunks" options is available at the top of
\par the dropdown list.
\par 
\par Use the "Add Chunk" button to move selected titles or
\par the "All Chunks" option to the Query set.
\par 
\par The Document dropdown list appears for Chunk-Chunk
\par searches, Chunk-Term searches, and Query by Chunk
\par searches.
\par 
\par \cf2\{bmc Chunk choice.jpg\}\cf0 
\par 
\par \f1 
\par }
330
Scribble330
Term Selection



:000270
Writing



FALSE
28
{\rtf1\ansi\ansicpg1252\deff0\deflang1033{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil Arial;}}
{\colortbl ;\red0\green0\blue255;\red128\green0\blue0;}
\viewkind4\uc1\pard\cf1\b\fs32 Term Selection\cf0\b0\f1\fs20 
\par 
\par \f0 The dropdown list contains all of the word forms
\par in the Swinburne corpus except those included in our
\par stop list. There are also no numbers.
\par 
\par You can used the dropdown list to add terms to
\par your Query Set by highlighting them and clicking
\par the Add Term button.
\par 
\par You can also use the Regex search box to select
\par groups of terms from the dropdown list with one click
\par of the Add Matches button. The Regex control gives
\par you the full power of Regex expressions, as in this
\par example search for \i leo, lion, and lyon\i0  and their
\par inflected forms.
\par 
\par This vocabulary list is a full inventory of word forms
\par that the LSA compnent can search---if a word form
\par is not on the list, it is not in the corpus.
\par 
\par This interface is used for Term-term, Term-chunk, and
\par Query by Term searches.
\par 
\par \cf2\{bmc Term choice.jpg\}\cf0\f1 
\par }
400
Scribble400
Step 5 -- Execute the Search



:000280
Writing



FALSE
20
{\rtf1\ansi\ansicpg1252\deff0\deflang1033{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil Arial;}}
{\colortbl ;\red0\green0\blue255;\red128\green0\blue0;}
\viewkind4\uc1\pard\cf1\b\fs32 Step 5 -- Execute the Search\cf0\b0\f1\fs20 
\par 
\par \f0 After completing the Search Type, Output Type,
\par Scope Type selections and creating a Query Set,
\par you are ready to run the search.
\par 
\par Choose a Correlation Threshold from the dropbox.
\par 
\par Then click the Run button.
\par 
\par The LSA component will execute the search on
\par the Swinburne LSA database and return results
\par on the page below this interface.
\par 
\par To start over at this point instead, click Clear Query.
\par 
\par \cf2\{bmc final step 5.jpg\}\cf0\f1 
\par }
500
Scribble500
Step 6 -- Work with the Results List



:000290
Writing



FALSE
31
{\rtf1\ansi\ansicpg1252\deff0\deflang1033{\fonttbl{\f0\fnil\fcharset0 Arial;}{\f1\fnil Arial;}}
{\colortbl ;\red0\green0\blue255;\red128\green0\blue0;}
\viewkind4\uc1\pard\cf1\b\fs32 Step 6 -- Work with the Results List\cf0\b0\f1\fs20 
\par 
\par \f0 When a search succeeds the LSA component will
\par return a list of results like the following list for a
\par Document-Document search.
\par 
\par \cf2\{bmc doc-doc results-top.jpg\}\cf0 
\par 
\par Click one of the correlation links to view the chunk pair
\par side by side.
\par 
\par Significant shared vocabulary will be highlghted in yellow
\par for Document-Document and Chunk-Chunk searches.
\par 
\par In Term-Chunk and Query by term searches, the
\par selected terms will be highlighted in blue.
\par 
\par At the top of each chunk, a link will be provided that
\par will take you to page in the digital edition which contains
\par that text.
\par 
\par \cf2\{bmc doc-doc pair.jpg\}\cf0 
\par 
\par Examination will frequently show that the pairs contain
\par shared text even when that text is not word-for-word identical.
\par LSA can be a very robust search tool.
\par 
\par \cf2\{bmc doc-doc pair-similarities.jpg\}\cf0\f1 
\par }
0
0
0
38
1 Swinburne Project LSA Component Help
2 About Latent Semantic Analysis=Scribble20
2 Using the LSA component
3 Documents are Chunked for Text Analaysis=Scribble35
3 Summary of Steps in the Query Procedure=Scribble37
3 Step 1 -- Search types
4 Selection of Search Type=Scribble45
4 About the Search Types
5 Document-Document correlations=Scribble50
5 Chunk-Chunk correlations=Scribble60
5 Term-Term correlations=Scribble70
5 Term-Chunk correlations=Scribble80
5 Chunk-Term correlations=Scribble90
5 Query with Terms=Scribble100
5 Query with Chunks=Scribble110
3 Step 2 -- Results Output Type
4 Selection of Results Output Type=Scribble130
4 About the Results Output Types
5 Descending Order=Scribble150
5 One Doc in Page Order=Scribble160
5 Term Alpha Order=Scribble170
5 Doc Catalog Order=Scribble180
5 Graph for NWB=Scribble190
5 CSV: XY Term--Doc=Scribble195
3 Step 3 -- Return Scope of Pairs
4 Selection of Return Scope of Pairs=Scribble210
4 About the Return Scope of Pairs
5 All Above Chosen Value=Scribble230
5 All Between Docs or Terms=Scribble240
5 Within One Document (Doc-Doc)=Scribble250
5 All w/Term Presence (Term-Doc)=Scribble260
5 Only If Term Present (Term-Doc)=Scribble270
3 Step 4 -- Build a Query Set
4 Document Selection=Scribble310
4 Chunk Selection=Scribble320
4 Term Selection=Scribble330
3 Step 5 -- Execute the Search=Scribble400
3 Step 6 -- Work with the Results List=Scribble500
7
*InternetLink
16711680
Courier New
0
10
1
....
0
0
0
0
0
0
*ParagraphTitle
-16777208
Arial
0
11
1
B...
0
0
0
0
0
0
*PopupLink
-16777208
Arial
0
8
1
....
0
0
0
0
0
0
*PopupTopicTitle
16711680
Arial
0
10
1
B...
0
0
0
0
0
0
*TopicText
-16777208
Arial
0
10
1
....
0
0
0
0
0
0
*TopicTitle
16711680
Arial
0
16
1
B...
0
0
0
0
0
0
<new macro>
16711680
Arial
0
16
0
B...
0
0
0
0
0
0
