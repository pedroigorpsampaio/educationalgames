$(document).ready(function(){
	
	// on click, opens the plugin interface
	$("#pluginlogo").click(function(){
		// gets course id
		var cid = getUrlParameter('id');
		// check if cid exists, meaning block was clicked
		// in a course context, the desired context for the plugin
		if(typeof cid !== "undefined") {
			var loc = window.location.pathname;
			var dir = loc.substring(0, loc.lastIndexOf('/course'));
			var win = window.open(dir + '/blocks/games/games.php?id='+cid, '_blank');
			if(win){
				//Browser has allowed it to be opened
				win.focus();
			}else{
				//Broswer has blocked it
				alert('Please allow popups for this site');
			}
		}
		else {
			// alerts the user of wrong context for the plugin
			alert("This plugin block should be put in a course context!");
		}
	});

	// on mouse hover, changes do pointer cursor
	$('#pluginlogo').css( 'cursor', 'pointer' );

});

function PopupCenter(url, title, w, h) {
    // Fixes dual-screen position                         Most browsers      Firefox
    var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
    var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

    width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
    height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

    var left = ((width / 2) - (w / 2)) + dualScreenLeft;
    var top = ((height / 2) - (h / 2)) + dualScreenTop;
    var newWindow = window.open(url, title, 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);

    // Puts focus on the newWindow
    if (window.focus) {
        newWindow.focus();
    }
}

// gets params from url
function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};