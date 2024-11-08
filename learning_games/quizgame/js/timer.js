var count;
var counter;

$(document).ready(function () {
	
	count = parseInt(document.getElementById("round_time").getAttribute('value'));
	
	// timer callback
	counter=setInterval(timer, 1000); //1000 will  run it every 1 second
});

// event timer callback
function timer()
{
	count=count-1;

	if (count < 0)
	{
		// time limit reach
		clearInterval(counter);
		dispatchAction("timeout");
		return;
	}

		document.getElementById("timer").innerHTML=count + "s";

	if (count == 0)
	{
		// update time to time`s up
		document.getElementById("timer").innerHTML="";
		document.getElementById("time").innerHTML="<img src = 'images/timeout.png' width = '205' height = '45' >";
	}
}
