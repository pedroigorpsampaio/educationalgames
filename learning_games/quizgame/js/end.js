$(document).ready(function () {
	
	var actiondelay = 250;
	var lock = true;
	
	setTimeout(function() { 			
		lock = false;
	return; }, actiondelay);

	$('#feedback').click( function(event) {
		if(!lock) {
			$(this).off('click');
			
			var seed = document.getElementById("seed").getAttribute('value');
			var a_seed = document.getElementById("a_seed").getAttribute('value');
			var questions_category = parseInt(document.getElementById("questions_category").getAttribute('value'));
			
			$( "#content" ).load( "./feedback.php?&reload=1" , { "seed":  seed , "a_seed":  a_seed, "questions_category" : questions_category }  );
		}
	});
	
});

