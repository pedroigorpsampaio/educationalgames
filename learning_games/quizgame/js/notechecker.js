var checker;

$(document).ready(function () {
	oldNumMessages = 0;
	// timer callback
	checker=setInterval(noteChecker, 10000); //1000 will  run it every 1 second
});

// event timer callback
function noteChecker()
{
	var oldNumMessages = document.getElementById("nummessages").innerHTML;
	var sendee = parseInt(document.getElementById("sendee").getAttribute('value'));
	
	$.ajax({ url: './db.php',
						 data: {action: 'checknote' , sendee: sendee},
						 type: 'post',
						 success: function(output) {
							
							if(parseInt(output) > oldNumMessages) {
								$( "#notification" ).load( "./menu.php?&reload=1 #notification");
								$( "#nummessages").text(output);
							}
						}
			});

}
