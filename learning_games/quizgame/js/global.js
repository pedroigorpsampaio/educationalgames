$(document).ready(function(){
	
	// draw on background frame
	var olddiv;
	var size = '0px';
	var color = '#ffffff';
	var pixelData;
	var dclick = false;
	var mclick = false;
	 
	 $(window).keydown(function(e){
		if (e.ctrlKey  && e.shiftKey && !dclick)
			dclick = true;
		else if(e.ctrlKey && e.shiftKey  && dclick)
			dclick = false;
	});
	 
	$("#body").on({
		'mousemove': function(ev) {
			dclick && mclick && paintDot(ev);
		},
		'mousedown': function(ev) {
			
			mclick = true;
			clickX = ev.pageX;
			clickY = ev.pageY;
			e.preventDefault();

		},
		'mouseup': function() {
			mclick = false;
		}
    });
	
	var paintDot = function(ev) {

		mouseX = ev.pageX;
        mouseY = ev.pageY;
		
		id = mouseX+"."+mouseY;
       
        $("body").append(
            $("<div id = "+id+" style = 'display:inline; z-index: 0;''></div>")
                .css('position', 'absolute')
                .css('top', mouseY + 'px')
                .css('left', mouseX + 'px')
                .css('width', size)
                .css('height', size)
                .css('background-color', color)

        );
	
		//olddiv = document.getElementById(id);
	}
	
	function connect(div1, div2, color, thickness) {
		var off1 = getOffset(div1);
		var off2 = getOffset(div2);
		// bottom right
		var x1 = off1.left + off1.width;
		var y1 = off1.top + off1.height;
		// top right
		var x2 = off2.left + off2.width;
		var y2 = off2.top;
		// distance
		var length = Math.sqrt(((x2-x1) * (x2-x1)) + ((y2-y1) * (y2-y1)));
		// center
		var cx = ((x1 + x2) / 2) - (length / 2);
		var cy = ((y1 + y2) / 2) - (thickness / 2);
		// angle
		var angle = Math.atan2((y1-y2),(x1-x2))*(180/Math.PI);
		// make hr
		var htmlLine = "<div style='padding:0px; margin:0px; height:" + thickness + "px; background-color:" + color + "; line-height:1px; position:absolute; left:" + cx + "px; top:" + cy + "px; width:" + length + "px; -moz-transform:rotate(" + angle + "deg); -webkit-transform:rotate(" + angle + "deg); -o-transform:rotate(" + angle + "deg); -ms-transform:rotate(" + angle + "deg); transform:rotate(" + angle + "deg);' />";
		//
		alert(htmlLine);
		document.body.innerHTML += htmlLine; 
	}

	function getOffset( el ) {
		var rect = el.getBoundingClientRect();
		return {
			left: rect.left + window.pageXOffset,
			top: rect.top + window.pageYOffset,
			width: rect.width || el.offsetWidth,
			height: rect.height || el.offsetHeight
		};
	}
	
	// scroll by mouse drag
	var clicked = false, clickY;
	var scrollspeed = 0.025;
	
	$(document).on({
		'mousemove': function(e) {
			clicked && updateScrollPos(e);
		},
		'mousedown': function(e) {
			clicked = true;
			clickY = e.pageY;
		},
		'mouseup': function() {
			clicked = false;
		}
	});

	var updateScrollPos = function(e) {
		$("#hiddenoverflow").scrollTop($("#hiddenoverflow").scrollTop() + (clickY - e.pageY)*scrollspeed);
	}
	
	// zoom
	var initialzoom = $('#container').css('zoom');
	var zoomspeed = 0.08;
	var zoommax = 1.28;
	var zoommin = 0.64;
	
	$(document).keydown(function(event) {
		
		// 48 key 0 96 num key 0
		// 107 Num Key  +
		// 109 Num Key  -
		// 173 189 Min Key  hyphen/underscor Hey
		// 61 187 Plus key  +/= key
		
		// zoom in
		if (event.ctrlKey==true && (event.which == '107' || event.which == '61' || event.which == '187' )) {
			zoomIn();
		}
		
		// zoom out
		if (event.ctrlKey==true && (event.which == '109' || event.which == '173' || event.which == '189' )) {
			zoomOut();
		}
		
		// zoom default
		if (event.ctrlKey==true && (event.which == '48' || event.which == '96')) {
			event.preventDefault();
			$("#container").css('zoom', initialzoom);
		}
			
	});

	$(window).bind('wheel mousewheel DOMMouseScroll', function (event) {
		if (event.ctrlKey == true) {
			if (event.originalEvent.wheelDelta > 0 || event.originalEvent.detail < 0) {
			// scroll up - zoom in		   
				zoomIn();
			}
			else {
				// scroll down - zoom out
				zoomOut();
			}

		}
	});
	
	function zoomIn() {
		event.preventDefault();
		var csszoom = $('#container').css('zoom');
		var zoomvalue = csszoom*(1.00 + zoomspeed);
		
		//clamp
		if(zoomvalue > zoommax)
			zoomvalue = zoommax;
		
		$("#container").css('zoom', zoomvalue);
	}

	function zoomOut() {
		event.preventDefault();
		var csszoom = $('#container').css('zoom');
		var zoomvalue = csszoom*(1.00 - zoomspeed);
		
		//clamp
		if(zoomvalue < zoommin)
			zoomvalue = zoommin;
		
		$("#container").css('zoom', zoomvalue);
	}
	
	// bgm

	var bgm = document.getElementById("bgm");
	var mute = document.getElementById("mute");

	$("#mute").click( function(e) {
		  
		  if (bgm.muted) {
			bgm.muted = false;
			mute.src = "images/unmute.png";
		  } else {
			bgm.muted = true;
			mute.src = "images/mute.png";
		  }
	});
});