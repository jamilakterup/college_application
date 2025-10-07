

	/*$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });*/
	$("document").ready(function(){


			$("#admission_submenu").show();
			$("#submit_tot_list").click(function(e){
				e.preventDefault();
				var groups=$("#groups").val();
				var session=$("#session").val();
				//alert (session);
				if(groups=='' || session==''){
					$("#tot_list_info").html('Please Select All Fields');
				}
				else{
					$("#tot_list_info").html('<center style="margin-top:50px">Please wait...<img src="../../img/loader.gif" alt=""/></center>');
						$.ajax({
								type:'POST',
		           					 url:"totlistgenerate",
		           					 data:{groups:groups,session:session},
		            					success:function(response){	
		            					//alert(response)	;
											$("#tot_list_info").html(response);
										},
										
					
								});
				}
				
			
			
			});
			
			
	});

