// controls calculating moment
var calculating = false;
var simload = 1500;

$(document).ready(function () {

	var actiondelay = 250;
	var lock = true;
	
	setTimeout(function() { 			
		lock = false;
	return; }, actiondelay);

	// button jump clicked
	$("#jump").click( function(e) {
		if(!lock) {
			$(this).off('click');
			e.preventDefault() ;	
			dispatchAction("jump");
		}
	});
	
	$(".incorrect").click( function(e) {
		if(!lock) {
			$(this).off('click');
			e.preventDefault() ;
			dispatchAction("incorrect", this.id);
		}
	});
	
	$(".correct").click( function(e) {
		if(!lock) {
			$(this).off('click');
			e.preventDefault() ;
			dispatchAction("correct", this.id);
		}
	});
	
	
});

function dispatchAction(action, aid) {
	
	// stops timer (counter from timer.js)
	clearInterval(counter);
	
	// gets infos previously stored by the server, hidden in html
	var n_rounds = parseInt(document.getElementById("n_rounds").getAttribute('value'));
	var questions_category = parseInt(document.getElementById("questions_category").getAttribute('value'));
	var jump_value = parseInt(document.getElementById("jump_value").getAttribute('value'));
	var time_value = parseInt(document.getElementById("time_value").getAttribute('value'));
	var wrong_value = parseInt(document.getElementById("wrong_value").getAttribute('value'));
	var correct_value = parseInt(document.getElementById("correct_value").getAttribute('value'));
	var round = parseInt(document.getElementById("round").getAttribute('value'));
	var score = parseInt(document.getElementById("score").getAttribute('value'));
	var multiplayer = document.getElementById("multiplayer").getAttribute('value');
	var contestant = parseInt(document.getElementById("contestant").getAttribute('value'));
	var seed = document.getElementById("seed").getAttribute('value');
	var lastround = document.getElementById("lastround").getAttribute('value');
	var cname = document.getElementById("cname").getAttribute('value');
	var acceptedchallenge = document.getElementById("acceptedchallenge").getAttribute('value');
	var challenger = parseInt(document.getElementById("challenger").getAttribute('value'));
	var cscore = parseInt(document.getElementById("cscore").getAttribute('value'));
	var nid = parseInt(document.getElementById("nid").getAttribute('value'));
	var practice = document.getElementById("practice").getAttribute('value');
	var a_seed = document.getElementById("a_seed").getAttribute('value');
	
	// gets info needed to calculate time spent (time_spent = round_time - time_left)
	var round_time = parseInt(document.getElementById("round_time").getAttribute('value'));
	var time_left = document.getElementById("timer").innerHTML;
	time_left = time_left.replace("s", "");
	
	// time spent on question
	var time_spent;
	// var timeout
	var timeout = false;
	
	// timeout (timespent = round_time)
	if(time_left == "") 
		time_spent = round_time;
	else
		time_spent = round_time - parseInt(time_left);
	
	// value modifier of the score (depends on user input and time spent)
	var value;

	// switchs on action to find what value 
	// is going to be added to the score
	switch (action) {
		case "incorrect":
			// clamp (avoid 0 division)
			if(time_spent == 0) 
				time_spent = 1;
		
			// calculates time penalty and subtracts wrong value
			value = wrong_value - parseInt(round_time/2 - time_spent/3);
			
			// if practice, appends the type of action/answerIncORCorr+id to the a_seed
			if(practice == "true")
				a_seed = a_seed + "F" + aid;
			
			break;
		case "correct":
			// clamp (avoid 0 division)
			if(time_spent == 0) 
				time_spent = 1;
		
			// calculates time bonuses and adds to correct value
			value = correct_value + parseInt(round_time/2 - time_spent/3);
			
			// if practice, appends the type of action/answerIncORCorr+id to the a_seed
			if(practice == "true")
				a_seed = a_seed + "T" + aid;
			
			break;
		case "jump":
			value = jump_value;
			
			// if practice, appends the type of action/answerIncORCorr+id to the a_seed
			if(practice == "true")
				a_seed = a_seed + "jump";
			
			break;
		case "timeout":
			value = time_value;
			
			// if practice, appends the type of action/answerIncORCorr+id to the a_seed
			if(practice == "true")
				a_seed = a_seed + "timeout";
			
			break;
		default:
			value = 0;
	}

	// adds value to the score
	score = score + value;
	// increases current round
	round++;
	 
	//alert (time_spent + "/" + seed + "/" + score + " / " + value);
	
	// se if it was last round
	if(round == n_rounds + 1)
		lastround = true;
	
	// if it isn`t last round, load another round
	// else, load end game
	
	if(!lastround) {
		
		// appends to a_seed the splitter char ('.')
		a_seed = a_seed  + ".";
		
		$( "#content" ).load( "./game.php?&reload=1" , { "round":  round , "score": score, "multiplayer" : multiplayer,
											"contestant" : contestant, "seed" : seed, "lastround" : lastround,  "cname" : cname,
											"n_rounds" : n_rounds, "questions_category" : questions_category , "jump_value" : jump_value , "time_value" : time_value,
											"wrong_value" : wrong_value, "correct_value" : correct_value, "round_time" : round_time, "a_seed" : a_seed,
											"acceptedchallenge" : acceptedchallenge, "nid" : nid, "cscore" : cscore, "challenger" : challenger} );
		return false;
	}
	else
	{
	
		if(calculating == false) {

			// calculating score (simulating)
			calculating = true;
			
			// loading text and image of calculating score
			document.getElementById("content").innerHTML = 
					"<div style='position:absolute; width: 400px; top: 110px; left: -120%;'><h1>Calculating Score...</h1>\
					<img src = 'images/spinner.gif' width = '64' height = '64' style ='position:absolute; top: -8%; left: 85%;'></img></div>";
			
			setTimeout(function() {
					
					calculating = false;
					
					$( "#content" ).load( "./end.php?&reload=1" , { "round":  round , "score": score, "multiplayer" : multiplayer,
							"contestant" : contestant, "seed" : seed, "lastround" : lastround,  "cname" : cname,
							"n_rounds" : n_rounds, "questions_category" : questions_category , "jump_value" : jump_value , "time_value" : time_value,
							"wrong_value" : wrong_value, "correct_value" : correct_value, "round_time" : round_time, "a_seed" : a_seed, "practice" : practice,
							"acceptedchallenge" : acceptedchallenge, "nid" : nid, "cscore" : cscore, "challenger" : challenger} );
					return false;
					
			}, simload);
			

		}
	}
	
}