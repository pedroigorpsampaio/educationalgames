$(document).ready(function(){
	
	// controls save moment
	var saving = false;
	var simload = 2000;
	
	$("#return").click( function(e) {
		if(!saving) {
			clearInterval(checker);
			e.preventDefault();
			$( "#content" ).load( "./menu.php?&reload=1");
		}
	});
	
	$("#save").click( function(e) {	
	
		if(!saving) {
			// loading text and img
			document.getElementById("saveinfo").innerHTML = 
					"saving...<img src = 'images/spinner.gif' width = '40' height = '40' style ='position:absolute; top: -50%;'></img>";
			
			saving = true;
		
			var category = document.getElementById("cat").innerHTML;
			var currentcat = document.getElementById("currentcat").getAttribute('value');
			var currentround = document.getElementById("currentround").getAttribute('value');
			var currenttime = document.getElementById("currenttime").getAttribute('value');
			var n_rounds = $('input[name=rounds]:checked').val();
			var time_limit = $('input[name=time]:checked').val();
			var cid = document.getElementById("cid").getAttribute('value');

			// checks if user is attempting to save the same config
			// that is stored in the db, and prevents unnecessary db query
			if(category==currentcat && n_rounds == currentround && time_limit == currenttime)
			{
				setTimeout(function() { 			
					document.getElementById("saveinfo").innerHTML = "Configuration saved!";
					saving = false;
					return; }, simload);

			}
			
			$.ajax({ url: './db.php',
					 data: {action: 'saveconfig' , cid: cid, n_rounds : n_rounds, time_limit: time_limit, category : category},
					 type: 'post',
					 success: function(output) {
						 
						if(output) {
							$("#currentcat").attr("value", category);
							$("#currentround").attr("value", n_rounds);
							$("#currenttime").attr("value", time_limit);
							setTimeout(function() { 			
								document.getElementById("saveinfo").innerHTML = "Configuration saved!";
								saving = false;
								return; }, simload);
						}
						else {
							alert("An error has ocurred and your configurations were not saved. Try again later" + output);
							saving = false;
							return;
						}
					}
			});
			return false;
		}
	});
	
});

function DropDown(el) {
				this.dd = el;
				this.placeholder = this.dd.children('span');
				this.opts = this.dd.find('ul.dropdown > li');
				this.val = '';
				this.index = -1;
				this.initEvents();
			}
			DropDown.prototype = {
				initEvents : function() {
					var obj = this;

					obj.dd.on('click', function(event){
						$(this).toggleClass('active');
						return false;
					});

					obj.opts.on('click',function(){
						var opt = $(this);
						obj.val = opt.text();
						obj.index = opt.index();
						obj.placeholder.text(obj.val);
					});
				},
				getValue : function() {
					return this.val;
				},
				getIndex : function() {
					return this.index;
				}
			} 
			$(function() {

				var dd = new DropDown( $('#dd') );

				$(document).click(function() {
					// all dropdowns
					$('.wrapper-dropdown-1').removeClass('active');
				});

			});
			