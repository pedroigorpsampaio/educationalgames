$(document).ready(function(){
	
	var delay = 250;
	var lock = true;
	
	setTimeout(function() { 			
		lock = false;
	return; }, delay);
	
	$("#menu").click( function(e) {
		if(!lock) { // waits for the transition to be able to be clicked
			$(this).off('click');
			clearInterval(checker);
			e.preventDefault();
			e.stopImmediatePropagation();
			
			$( "#content" ).load( "./menu.php?&reload=1");

		}
	});
	
	$("#single").click( function(e) {	
		if(!lock) { // waits for the transition to be able to be clicked
			$(this).off('click');
			clearInterval(checker);
			e.preventDefault();
			e.stopImmediatePropagation();
			
			$( "#content" ).load( "./game.php?&reload=1" , { "round":  1 , "score": 0, "multiplayer" : "false",
												"lastround" : "false"}  );
												
		}
	});
	
	$("#multi").click( function(e) {
		if(!lock) {
			$(this).off('click');
			clearInterval(checker);
			e.preventDefault();
			e.stopImmediatePropagation();
			$( "#content" ).load( "./multiplayer.php?&reload=1");
		}
	});
	
	$("#options").click( function(e) {
		if(!lock) {
			$(this).off('click');
			clearInterval(checker);
			e.preventDefault();
			e.stopImmediatePropagation();
			$( "#content" ).load( "./options.php?&reload=1");
		}
	});
	
	$("#notification").click( function(e) {
		if(!lock) {
			$(this).off('click');
			clearInterval(checker);
			e.preventDefault();
			e.stopImmediatePropagation();
	
				$( "#content" ).load( "./notification.php?&reload=1");

		}
	});
	
	$("#achievement").click( function(e) {
		if(!lock) {
			$(this).off('click');
			clearInterval(checker);
			e.preventDefault();
			e.stopImmediatePropagation();
			$( "#content" ).load( "./achievement.php?&reload=1");
		}
	});
	
	$("#ranking").click( function(e) {
		if(!lock) {
			$(this).off('click');
			clearInterval(checker);
			e.preventDefault();
			e.stopImmediatePropagation();
			$( "#content" ).load( "./ranking.php?&reload=1");
		}
	});

});