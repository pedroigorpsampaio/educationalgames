$(document).ready(function () {	

	var actiondelay = 250;
	var lock = true;
	
	setTimeout(function() { 			
		lock = false;
	return; }, actiondelay);

	var type = document.getElementById("type").getAttribute('value');
	var contestant, cname, nid, challenger, score, seed;

	$("#confirmyes").one( "click", function(e) {
		
		if(!lock) {
			e.preventDefault();
			
			if(type == "challenge") { // challenge player
				contestant = parseInt(document.getElementById("contestant").getAttribute('value'));
				cname = document.getElementById("cname").getAttribute('value');

				$( "#content" ).load( "./game.php?&reload=1" , { "round":  1 , "score": 0, "multiplayer" : "true",
																"lastround" : "false", "contestant" : contestant, "cname" : cname}  );

			}
			else if(type == "notechallenge") { // accept challenge
				nid = parseInt(document.getElementById("nid").getAttribute('value'));
				challenger = parseInt(document.getElementById("challenger").getAttribute('value'));
				cscore = parseInt(document.getElementById("score").getAttribute('value'));
				seed = document.getElementById("seed").getAttribute('value');
				var questions_category = parseInt(document.getElementById("questions_category").getAttribute('value'));
				var n_rounds = parseInt(document.getElementById("n_rounds").getAttribute('value'));
				var round_time = parseInt(document.getElementById("round_time").getAttribute('value'));
				var correct_value = parseInt(document.getElementById("correct_value").getAttribute('value'));
				var wrong_value = parseInt(document.getElementById("wrong_value").getAttribute('value'));
				var jump_value = parseInt(document.getElementById("jump_value").getAttribute('value'));
				var time_value = parseInt(document.getElementById("time_value").getAttribute('value'));
				

				$( "#content" ).load( "./game.php?&reload=1" , { "round":  1 , "score": 0, "acceptedchallenge" : "true", "seed" : seed,
																	"lastround" : "false", "challenger" : challenger, "cscore" : cscore, "nid" : nid, "n_rounds" : n_rounds,
																	"questions_category" : questions_category , "jump_value" : jump_value , "time_value" : time_value,
																	"wrong_value" : wrong_value, "correct_value" : correct_value, "round_time" : round_time}  );

			}
			else { // delete notification
				nid = parseInt(document.getElementById("nid").getAttribute('value'));
				$.ajax({ url: './db.php',
						 data: {action: 'notedelete' , nid: nid},
						 type: 'post',
						 success: function(output) {
							$( "#content" ).load( "./notification.php?&reload=1" );
						}
				});
				
				return false;
			}
		}
    });
	
	$("#confirmno").one( "click", function(e) {
		if(!lock) {
			e.preventDefault();
		
			if(type == "challenge") {
				$( "#content" ).load( "./multiplayer.php?&reload=1" );
			}
			else {
				// challenge refusal - must delete it from notification db
				if(type == "notechallenge")
				{
					nid = parseInt(document.getElementById("nid").getAttribute('value'));
					challenger = parseInt(document.getElementById("challenger").getAttribute('value'));
					$.ajax({ url: './db.php',
							 data: {action: 'notedelete' , nid: nid, actionext : 'sendrefusal', sendee: challenger},
							 type: 'post',
							 success: function(output) {
								$( "#content" ).load( "./notification.php?&reload=1" );
							}
					});

				}
				else { // if not it is a normal message, return and do not delete current note
					$( "#content" ).load( "./notification.php?&reload=1" );
				}
				return false;
			}
		}
	});
	
});