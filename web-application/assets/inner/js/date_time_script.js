$(document).ready(function(){
	var page=$("#pagename").val();
	var authuserdateformat = $("#authuserdateformat").val();
	//authuserdateformat = "dd/mm/yy";
	//alert(authuserdateformat);
	if(page=="contest")
	{
		$("#conteststart").datetimepicker({
			timeFormat: "hh:mm tt",
			//timeFormat: "HH:mm",
			minDate:0,
			dateFormat: authuserdateformat,
			onSelect: function(selected) {
			if(authuserdateformat=='mm/dd/yy')
				{
				var d = new Date(selected); 
				}else{
				rearrange = selected.split("/");
				selected=rearrange[1]+'/'+rearrange[0]+'/'+rearrange[2]; 
				var d = new Date(selected);  				
				}
			var lastdate=$("#contestend").val();
			  $("#contestend").datetimepicker("option","minDate", d);
			  $("#contestend").datetimepicker("option","minDateTime",d);
			  $("#contestend").val(lastdate);
			
			}
			
		});
		$("#contestend").datetimepicker({ 
			timeFormat: "hh:mm tt",
			minDate:0,
			dateFormat: authuserdateformat,
			onSelect: function(selected) {  
				if(authuserdateformat=='mm/dd/yy')
				{
				var d = new Date(selected); 
				}else{
				rearrange = selected.split("/");
				selected=rearrange[1]+'/'+rearrange[0]+'/'+rearrange[2]; 
				var d = new Date(selected);  				
				}
				
								
				 var firstdate=$("#conteststart").val();
				var votestart=$("#votingstart").val();
			$("#conteststart").datetimepicker("option","maxDate", d);
			 $("#conteststart").datetimepicker("option","maxDateTime", d);
			 // $("#votingstart").datetimepicker("option","minDate", d);
			 // $("#votingstart").datetimepicker("option","minDateTime", d);
			  $("#conteststart").val(firstdate);
			   // $("#votingstart").val(votestart);
			  var date1 = $('#contestend').datetimepicker('getDate');
				var date = new Date( Date.parse( date1 ) ); 
				date.setDate( date.getDate() );        
				var newDate = date.toDateString(); 
				newDate = new Date( Date.parse( newDate ) );                      
				$('#votingstart').datetimepicker("option","minDate",newDate);			
				$("#votingstart").val($("#contestend").val()); 		   
				  d.setDate(d.getDate()+1); 
				  $('#votingend').datepicker('setDate', d);
			  
				/*if(authuserdateformat=='mm/dd/yy')
				{
				var date1 = $('#contestend').datetimepicker('getDate');          
				var date = new Date( Date.parse( date1 ) ); 
				date.setDate( date.getDate() );        
				var newDate = date.toDateString(); 
				newDate = new Date( Date.parse( newDate ) );                      
				$('#votingstart').datetimepicker("option","minDate",newDate);			
				$("#votingstart").val($("#contestend").val()); alert(d);			   
				  d.setDate(d.getDate()+1); alert(d);
				  $('#votingend').datepicker('setDate', d);
				}*/
				
				
			}
		});  
		$("#votingstart").datetimepicker({
			timeFormat: "hh:mm tt",
			minDate:0,
			dateFormat: authuserdateformat,
			onSelect: function(selected) {
			if(authuserdateformat=='mm/dd/yy')
				{
				var d = new Date(selected); 
				}else{
				rearrange = selected.split("/");
				selected=rearrange[1]+'/'+rearrange[0]+'/'+rearrange[2]; 
				var d = new Date(selected);  				
				}
			var lastdate=$("#votingend").val();
			$("#votingend").datetimepicker("option","minDate", d);
			  $("#votingend").datetimepicker("option","minDateTime", d);
			  $("#votingend").val(lastdate);
			  
			   d.setDate(d.getDate()+1); 
			   $('#votingend').datepicker('setDate', d);
			   
			}
		});
		$("#votingend").datetimepicker({ 
			timeFormat: "hh:mm tt",
			minDate:0,
			dateFormat: authuserdateformat,
			onSelect: function(selected) {
			if(authuserdateformat=='mm/dd/yy')
				{
				var d = new Date(selected); 
				}else{
				rearrange = selected.split("/");
				selected=rearrange[1]+'/'+rearrange[0]+'/'+rearrange[2]; 
				var d = new Date(selected);  				
				}
			var firstdate=$("#votingstart").val();
				$("#votingstart").datetimepicker("option","maxDate", d);
			   $("#votingstart").datetimepicker("option","maxDateTime", d);
			   $("#votingstart").val(firstdate);
			}
		});  
	}
	else
	{
		//// Edit contest /////
		var enable = $("#enable").val();
		if(enable==1){ //// For admin panel //////
			
			var cstart = $("#conteststart").val().split("/");
				cstart=cstart[1]+'/'+cstart[0]+'/'+cstart[2]; 
			
			var cend = $("#contestend").val().split("/");
				cend=cend[1]+'/'+cend[0]+'/'+cend[2]; 
				
			var vstart = $("#votingstart").val().split("/");
				vstart=vstart[1]+'/'+vstart[0]+'/'+vstart[2]; 
			
			var vend = $("#votingend").val().split("/");
				vend=vend[1]+'/'+vend[0]+'/'+vend[2]; 
			
			var con_start=new Date(cstart);
		var con_end=new Date(cend);
		var vote_start=new Date(vstart);
		var vote_end=new Date(vend);
		
		
		$("#conteststart").datetimepicker({
			timeFormat: "hh:mm tt",
			minDate:0,
			dateFormat: authuserdateformat,
			//minDateTime:con_start,
			//maxDate:con_end,
			//maxDateTime:con_end,
			onSelect: function(selected) {
			if(authuserdateformat=='mm/dd/yy')
				{
				var d = new Date(selected); 
				}else{
				rearrange = selected.split("/");
				selected=rearrange[1]+'/'+rearrange[0]+'/'+rearrange[2]; 
				var d = new Date(selected);  				
				}
			/* var lastdate=$("#contestend").val();
			  $("#contestend").datetimepicker("option","minDate", d);
			  $("#contestend").datetimepicker("option","minDateTime",d);
			  $("#contestend").val(lastdate); */
			}
		});
		$("#contestend").datetimepicker({ 
			timeFormat: "hh:mm tt",
			//minDate:0,
			dateFormat: authuserdateformat,
			minDate:con_start,
			//minDateTime:con_start,
			//maxDate:con_end,
			//maxDateTime:con_end,
			onSelect: function(selected) {  
				if(authuserdateformat=='mm/dd/yy')
				{
				var d = new Date(selected); 
				}else{
				rearrange = selected.split("/");
				selected=rearrange[1]+'/'+rearrange[0]+'/'+rearrange[2]; 
				var d = new Date(selected);  				
				}
				var firstdate=$("#conteststart").val();
				var votestart=$("#votingstart").val();
			 /* $("#conteststart").datetimepicker("option","maxDate", d);
			   $("#conteststart").datetimepicker("option","maxDateTime", d);
			   $("#votingstart").datetimepicker("option","minDate", d);
			   $("#votingstart").datetimepicker("option","minDateTime", d);
			   $("#conteststart").val(firstdate); */
			 
			  $("#votingstart").val($("#contestend").val());
			  
			 
			/* var date1 = $('#contestend').datetimepicker('getDate');
				var date = new Date( Date.parse( date1 ) ); 
				date.setDate( date.getDate() ); */

			d.setDate(d.getDate()+1);  // console.log(selected);   console.log(d);
			
			 $('#votingend').datetimepicker('setDate', d);  //console.log($('#votingend').val());
			
			}
		}); 
		
		$("#votingstart").datetimepicker({
			timeFormat: "hh:mm tt",
			minDate:0,
			dateFormat: authuserdateformat,
			minDate:con_end,
			//minDateTime:con_end,
			//maxDate:vote_end,
			//maxDateTime:vote_end,
			onSelect: function(selected) {
			if(authuserdateformat=='mm/dd/yy')
				{
				var d = new Date(selected); 
				}else{
				rearrange = selected.split("/");
				selected=rearrange[1]+'/'+rearrange[0]+'/'+rearrange[2]; 
				var d = new Date(selected);  				
				}
			var lastdate=$("#votingend").val();
			/*$("#votingend").datetimepicker("option","minDate", d);
			  $("#votingend").datetimepicker("option","minDateTime", d);
			  $("#votingend").val(lastdate);
			*/
			
			 d.setDate(d.getDate()+1); 
			   $('#votingend').datepicker('setDate', d);
			
			}
		});
		$("#votingend").datetimepicker({ 
			timeFormat: "hh:mm tt",
			//minDate:0,
			dateFormat: authuserdateformat,
			minDate:new Date(vstart),
			//minDate:vote_start,
			//minDateTime:vote_start,
			onSelect: function(selected) {
			if(authuserdateformat=='mm/dd/yy')
				{
				var d = new Date(selected); 
				}else{
				rearrange = selected.split("/");
				selected=rearrange[1]+'/'+rearrange[0]+'/'+rearrange[2]; 
				var d = new Date(selected);  				
				}
			var firstdate=$("#votingstart").val();
			/*	$("#votingstart").datetimepicker("option","maxDate", d);
			   $("#votingstart").datetimepicker("option","maxDateTime", d);
			   $("#votingstart").val(firstdate); */
			}
		});  


		}else{  //// For Normal user /////
		
					var con_start=new Date($("#conteststart").val());
				var con_end=new Date($("#contestend").val());
				var vote_start=new Date($("#votingstart").val());
				var vote_end=new Date($("#votingend").val());
				$("#conteststart").datetimepicker({
					timeFormat: "hh:mm tt",
					minDate:0,
					dateFormat: authuserdateformat,
					minDateTime:con_start,
					maxDate:con_end,
					maxDateTime:con_end,
					onSelect: function(selected) {
					if(authuserdateformat=='mm/dd/yy')
					{
						var d = new Date(selected); 
					}else{
						rearrange = selected.split("/");
						selected=rearrange[1]+'/'+rearrange[0]+'/'+rearrange[2]; 
						var d = new Date(selected);  				
					}
					var lastdate=$("#contestend").val();
					  $("#contestend").datetimepicker("option","minDate", d);
					  $("#contestend").datetimepicker("option","minDateTime",d);
					  $("#contestend").val(lastdate);
					}
				});
				$("#contestend").datetimepicker({ 
					timeFormat: "hh:mm tt",
					minDate:con_start,
					dateFormat: authuserdateformat,
					minDateTime:con_start,
					maxDate:con_end,
					maxDateTime:con_end,
					onSelect: function(selected) {  
							if(authuserdateformat=='mm/dd/yy')
						{
							var d = new Date(selected); 
						}else{
							rearrange = selected.split("/");
							selected=rearrange[1]+'/'+rearrange[0]+'/'+rearrange[2]; 
							var d = new Date(selected);  				
						}
						var firstdate=$("#conteststart").val();
						var votestart=$("#votingstart").val();
						$("#conteststart").datetimepicker("option","maxDate", d);
					   $("#conteststart").datetimepicker("option","maxDateTime", d);
					   $("#votingstart").datetimepicker("option","minDate", d);
					   $("#votingstart").datetimepicker("option","minDateTime", d);
					   $("#conteststart").val(firstdate);
					 
					  $("#votingstart").val($("#contestend").val());
					  d.setDate(d.getDate()+1);  // console.log(selected);   console.log(d);
					
					 $('#votingend').datetimepicker('setDate', d);  //console.log($('#votingend').val());
					
					}
				}); 
				
				$("#votingstart").datetimepicker({
					timeFormat: "hh:mm tt",
					minDate:con_end,
					dateFormat: authuserdateformat,
					minDateTime:con_end,
					maxDate:vote_end,
					maxDateTime:vote_end,
					onSelect: function(selected) {
					if(authuserdateformat=='mm/dd/yy')
					{
						var d = new Date(selected); 
					}else{
						rearrange = selected.split("/");
						selected=rearrange[1]+'/'+rearrange[0]+'/'+rearrange[2]; 
						var d = new Date(selected);  				
					}
					var lastdate=$("#votingend").val();
					$("#votingend").datetimepicker("option","minDate", d);
					  $("#votingend").datetimepicker("option","minDateTime", d);
					  $("#votingend").val(lastdate);
					
					 d.setDate(d.getDate()+1); 
					   $('#votingend').datepicker('setDate', d);
					
					}
				});
				$("#votingend").datetimepicker({ 
					timeFormat: "hh:mm tt",
					minDate:vote_start,
					dateFormat: authuserdateformat,
					minDateTime:vote_start,
					onSelect: function(selected) {
					if(authuserdateformat=='mm/dd/yy')
					{
						var d = new Date(selected); 
					}else{
						rearrange = selected.split("/");
						selected=rearrange[1]+'/'+rearrange[0]+'/'+rearrange[2]; 
						var d = new Date(selected);  				
					}
					var firstdate=$("#votingstart").val();
						$("#votingstart").datetimepicker("option","maxDate", d);
					   $("#votingstart").datetimepicker("option","maxDateTime", d);
					   $("#votingstart").val(firstdate);
					}
				});  

		
		
		}
		
		
	}
});