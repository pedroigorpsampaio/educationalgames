$(document).ready(function () {
	
	var actiondelay = 250;
	var lock = true;
	
	setTimeout(function() { 			
		lock = false;
	return; }, actiondelay);
	
	$('.challengee').click( function(e) {
		if(!lock) {
			$(this).off();
			$( "#content" ).load( "./confirmation.php?&reload=1" , { "type":  "challenge" , "contestant" : e.target.id}  );	
		}
	});

});
