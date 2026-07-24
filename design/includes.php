<!-- ******************************
BEGIN FAVICON
******************************  -->
<!-- <link rel="icon" href="favicon.ico"> -->
<link rel="shortcut icon" href="https://webapp1.dlib.indiana.edu/newton/favicon.ico" type="image/x-icon">
<!-- ******************************
BEGIN 1140GRID SYSTEM from http://cssgrid.net/ 
******************************  -->
<!--<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link href="1140grid/css/1140.css" rel="stylesheet" type="text/css" media="screen" />-->
<!-- ******************************
BEGIN jQUERY   
    Note: newton_lsa was originally built with jqueryui 1.8.16, ca. 2010, but to solve problems
    loading the select lists and to provide a type-ahead, it needed to use select2.js,
    which required a newer version of jQuery, and 3.7.1 and an older select2.js offered a
    compromise. JQuery-migrate is supposed to catch 1.8.16 commands and translate them to 3.7.1.
    -weh 2026 jul 19
******************************  -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/jquery-migrate@3.6.0/dist/jquery-migrate.min.js"></script>
<!-- Emergency Polyfill for legacy plugins looking for $.browser.msie -->
<script type="text/javascript">
    jQuery.browser = {};
    (function () {
        jQuery.browser.msie = false;
        jQuery.browser.version = 0;
        if (navigator.userAgent.match(/MSIE ([0-9]+)\./) || navigator.userAgent.match(/Trident.*rv:([0-9]+)/)) {
            jQuery.browser.msie = true;
            jQuery.browser.version = RegExp.$1;
        }
    })();
</script>
<!-- ******************************
BEGIN jQUERY UI
******************************  -->
<link href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/flick/jquery-ui.css" rel="stylesheet" type="text/css" media="all" />
<!-- ******************************
BEGIN DROP-DOWN BOX
******************************  -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/css/select2.min.css">
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/js/select2.min.js"></script>
<!--<link href="css.new/search-dropdown.css" rel="stylesheet" type="text/css" media="screen" />-->
<!-- ******************************
BEGIN SYMBOL SEARCH - jQUERY UI DIALOG BOX
******************************  -->
<!--<link href="css.new/keyboard2.css" rel="stylesheet" type="text/css" media="screen" />-->
<!-- ******************************
BEGIN NEWTON PROJECT STYLE
******************************  -->
<link href="css/styles.css" rel="stylesheet" type="text/css" media="screen" />
<link href="css/print.css" rel="stylesheet" type="text/css" media="print" />
