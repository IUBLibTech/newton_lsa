// JavaScript Document
function getID(elem) {
    var myId = $(elem).attr('id');
    //console.log('div#'+myId+'-content');    
    return $('div#' + myId + '-inside').html();
}

$(document).ready(function () {
    $('.popUpNote').bt({
        positions: 'top',
        contentSelector: "getID($(this));", /*run function*/
        trigger: 'click',
        centerPointX: .9,
        fill: '#FEFA76',
        strokeStyle: '#000',
        strokeWidth: 1,
        positions:[ 'right', 'top', 'left', 'bottom']
    });

    $('.tooltip').bt({
       positions:[ 'right', 'top', 'left', 'bottom']
    });
    
    $('a.MetsNavigator').click(function (e) {
        e.preventDefault();
        
        var myDoc = $(this).attr('title');
        var page = $(this).attr('page');
        var myURL = '/newton-dev/html/metsnav3.html#mets=http://purl.dlib.indiana.edu/iudl/newton/mets/' + myDoc + '&page=' + page;
				
		parameters ="location=0,menubar=0,height=500,width=650,toolbar=0,scrollbars=1,status=0,resizable=1,left=20,top=20";

		window.open( myURL, myDoc, parameters ).focus(); 
    });
});