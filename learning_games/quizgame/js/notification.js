$(document).ready(function () {
	
	var actiondelay = 250;
	var lock = true;
	
	setTimeout(function() { 			
		lock = false;
	return; }, actiondelay);

	$('.notifications').click( function(event) {
		if(!lock) {
			$(this).off('click');
			var nid = event.target.id;
			var type = document.getElementById("type"+nid).getAttribute('value');
			
			if(type == "challenge") {
				
				var challenger = parseInt(document.getElementById("sender"+nid).getAttribute('value'));
				var score = parseInt(document.getElementById("score"+nid).getAttribute('value'));
				var seed = document.getElementById("seed"+nid).getAttribute('value');
				var questions_category = parseInt(document.getElementById("questions_category"+nid).getAttribute('value'));
				var n_rounds = parseInt(document.getElementById("n_rounds"+nid).getAttribute('value'));
				var round_time = parseInt(document.getElementById("round_time"+nid).getAttribute('value'));
				var correct_value = parseInt(document.getElementById("correct_value"+nid).getAttribute('value'));
				var wrong_value = parseInt(document.getElementById("wrong_value"+nid).getAttribute('value'));
				var jump_value = parseInt(document.getElementById("jump_value"+nid).getAttribute('value'));
				var time_value = parseInt(document.getElementById("time_value"+nid).getAttribute('value'));


				$( "#content" ).load( "./confirmation.php?&reload=1" , { "type":  "notechallenge" , "nid": nid , "seed":  seed , 
																	"score":  score , "challenger" : challenger, "n_rounds" : n_rounds,
																	"questions_category" : questions_category , "jump_value" : jump_value , "time_value" : time_value,
																	"wrong_value" : wrong_value, "correct_value" : correct_value, "round_time" : round_time}  );

			}
			else {
				var text = document.getElementById("text"+nid).getAttribute('value');

				$( "#content" ).load( "./confirmation.php?&reload=1" , { "type":  "notemessage" , "nid" : nid, "text" : text}  );

			}
		}
	});
	
});

