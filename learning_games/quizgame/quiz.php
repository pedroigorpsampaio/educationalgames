<?php

// OBS: 1 JQUERY import for all php documents, since all documents are loaded in div content from this document
echo('
<!DOCTYPE html>
<html>

    <head>
        <meta content="text/html; charset=utf-8" http-equiv="content-type">
				
		<link rel="stylesheet" href="css/style.css">  
		<link rel="stylesheet" href="css/sexybuttons.css">
		
		<script src="js/jquery1_11_13.js" type="text/javascript"></script>
		
		');
		
// randoms a bgm
$rand = rand(0, 100);

if ($rand % 2 == 0)
	$bgm = "sounds/bgm2";
else
	$bgm = "sounds/bgm1";
		
echo('
		<div style = "position: fixed; z-index: 500;">
			<audio id = "bgm" autoplay loop>
				<source src="'.$bgm.'.mp3" type="audio/mpeg; codecs="mp3"">
				<source src="'.$bgm.'.ogg" type="audio/ogg; codecs="vorbis"">
			</audio>
	
			<img id = "mute" src = "images/unmute.png" width = 50 height = 50></img>
		</div>
				
		<script src="js/global.js" type="text/javascript"></script>
		
	<div id="container">
		<div id="header"></div>
		<div id="body">
			<div id ="hiddenoverflow">
				<div id = "content" >
							
						<script type = "text/javascript">
							$(document).ready(function() {				
								$("#init").click( function(e) {
									e.stopImmediatePropagation();
									$(this).off("click");
									$( "#content" ).load( "./menu.php?&reload=1");
								});
								
								$("#init").trigger("click");
							});
						</script>
						
						<div style="display: none;"><button id = "init"></button></div>
				   
				</div>
			</div>
		</div>
	</div>
</div>
<html>

');