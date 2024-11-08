$(document).ready(function(){
	
	currentorder = document.getElementById("currentorder").getAttribute('value');
	
	var actiondelay = 250;
	var lock = true;
	
	setTimeout(function() { 			
		lock = false;
	return; }, actiondelay);

	$("#"+currentorder+"").css("text-decoration", "underline");

	$("#wins").click( function(e) {
		if(!lock) {
			e.preventDefault();
			if(currentorder != "wins")
				$( "#content" ).load( "./ranking.php?&reload=1" ,  { "order":  "wins"});
		}
	});
	
	$("#defeats").click( function(e) {
		if(!lock) {
			e.preventDefault();
			if(currentorder != "defeats")
				$( "#content" ).load( "./ranking.php?&reload=1" ,  { "order":  "defeats"});
		}
	});
	
	$("#scoresum").click( function(e) {	
		if(!lock) {
			e.preventDefault();
			if(currentorder != "scoresum")
				$( "#content" ).load( "./ranking.php?&reload=1" ,  { "order":  "scoresum"});
		}
	});
	
	$("#ratio").click( function(e) {	
		if(!lock) {
			e.preventDefault();
			if(currentorder != "ratio")
				$( "#content" ).load( "./ranking.php?&reload=1" ,  { "order":  "ratio"});
		}
	});
	
});