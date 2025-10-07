$("document").ready(function(){

   //$('.datepicker').datepicker( {
   // format: 'yyyy-mm-dd',
    //});
   
   $('#guardian_mobile').keydown(
           function(e){

            var value=$(this).val();            
            var point_exist=0;
            point_exist=value.indexOf(".");            
            var key_code=e.which;                   
            var point_allowed=1;
            if(key_code==46 && point_exist>=0) point_allowed=0;
            if((( (key_code!=0 && key_code<48) || key_code>57) &&  key_code!=8 && key_code!=46) || point_allowed==0)
                e.preventDefault();
    });

      $('#guardian_mobile').keydown(
           function(e){

            var value=$(this).val();            
            var point_exist=0;
            point_exist=value.indexOf(".");            
            var key_code=e.which;                   
            var point_allowed=1;
            if(key_code==46 && point_exist>=0) point_allowed=0;
            if((( (key_code!=0 && key_code<48) || key_code>57) &&  key_code!=8 && key_code!=46) || point_allowed==0)
                e.preventDefault();
    });

   $('#father_name').keyup(function(){
                        var selValue = $('input[name=guardian_info]:checked').val();
                        //alert(selValue);
                        if (selValue == 'Father') {
                          $('#guardian_name').attr('value', $('#father_name').val());
                        }
                      });

   $('#mother_name').keyup(function(){
                        var selValue = $('input[name=guardian_info]:checked').val();
                        if (selValue == 'Mother') {
                          $('#guardian_name').attr('value', $('#mother_name').val());
                        }
                      });

   $('input[name=guardian_info]').change(function(){
                      var selValue = $('input[name=guardian_info]:checked').val();
                      //alert(selValue);
                      if (selValue == 'Other') {
                        $('#guardian_name').attr('disabled', false);
                        $('#guardian_relation').attr('disabled', false);
                        $('#guardian_name').attr('value', '');
                        $('#guardian_relation').attr('value', '');
                      } else {
                        $('#guardian_name').attr('disabled', true);
                        $('#guardian_relation').attr('disabled', true);
                        $('#guardian_name').attr('value', $('#'+selValue+'s_name').val());
                        if (selValue == 'Father') {
                          $('#guardian_relation').attr('value', 'Father');
                          $('#guardian_name').attr('value', $('#father_name').val());
                        } else {
                          $('#guardian_relation').attr('value', 'Mother');
                          $('#guardian_name').attr('value', $('#mother_name').val());
                        }
                      }
                    });



 



/*      $('#confirm_password').keydown(
    function(e){     

            var value=$(this).val();            
            var point_exist=0;
            point_exist=value.indexOf(".");            
            var key_code=e.which;                   
            var point_allowed=1;
            if(key_code==46 && point_exist>=0) point_allowed=0;
            if((( (key_code!=0 && key_code<48) || key_code>57) &&  key_code!=8 && key_code!=46) || point_allowed==0)
                e.preventDefault();
    });*/

   
	$("#confirm_slip").click(function(){
            //alert();
            $("#confirm_slidownload_linkp_file").html("Please Wait.Processing..");
                $.ajax({
                    type:'POST',
                    url:'confirmslip',
                    data:{},
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    beforeSend: function() {
                        $.LoadingOverlay("show");
                    },
                    success:function(response){
                    	$.LoadingOverlay("hide");
                        $("#download_link1").html(response);
            			$("#confirm_slip_file").modal('show');

                    }
                });
        
        });

	$("#con_mes").hide();
		/*Student Payment Approve action*/
			$("#submit_payment").click(function(){ 
				$("#submit_payment").attr('value','Please wait...');				
				$("#con_mes").html('অনুগ্রহ করে কিছুক্ষণ অপেক্ষা করুন...');
				var student_id=$("#studentID").val();				
				var trx=$("#trxID").val();
				//alert(ref)	;			
				var ans=window.confirm("Are you sure?");
				if(ans){				
				$.ajax({
					type:'POST',
					url:'dbbl_approve',
					data:{student_id:student_id,trx:trx},
          headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					success:function(response){
						$("#con_mes").show();
						$("#con_mes").html('');
						$("#con_mes").addClass('alert-danger');
						$("#submit_payment").attr('value','Submit');
						$("#con_mes").html('<h4>'+response+'</h4>');
						$("#trxID").val('');
						}					
					
				});
			}/*End of if(ans)*/
		});

 	  //$('#student_id').number();
 $("#admission_step").click(function(){

 	 $('#admission_step_modal').modal('show'); 

 	  var ssc_roll =$('#ssc_roll').val();
      var ssc_board =$('#ssc_board').val();
      var ssc_passing_year =$('#ssc_passing_year').val();

 	  if(ssc_roll =='')
 	  {
 	  	 $('#next_step_error').html('<span style="color:red;">Enter SSC Roll ');
         $('#information').hide();
 	  }
 	  else if(ssc_board=='')
 	  {
 	  	 $('#next_step_error').html('<span style="color:red;">Select SSC Board ');
         $('#information').hide();
 	  }
       else if(ssc_passing_year=='')
      {
         $('#next_step_error').html('<span style="color:red;">Select SSC Passing Year ');
         $('#information').hide();
      }
 	  else
       $.ajax({	
        type:'POST',
        url:'HSC/checkMerit',
        data:{ssc_roll: ssc_roll,ssc_board: ssc_board,ssc_passing_year: ssc_passing_year},
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success:function(response){
        	$('#admission_step_modal').modal('hide');
        	 var status= response;

        	if(status==3 )
        	{

               $('#next_step_error').html('<span style="color:red;">You Are Not in Merit List');
               $('#information').hide();

            }
            
            if(status==1)
            {
                    var url ='HSC/Form';
                    window.location=url;
            }
        }
    });
 });




    //HSC Group Change Function
    $('#hsc_group').on('change',function(){
      var group = $(this).val();
      //ajax start
      //$('#myModal').modal('show');
      $.ajax({
        type:'POST',
        url:'hscGroupChange',
        data:{group:group,course:0},
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        async: false,
        success:function(response){

          $('.compulsory_course_codes').html(response);
        }
      });//end of ajax
      //ajax start
   
          //alert('cffhgf');
      $.ajax({
        type:'POST',
        url:'hscGroupChange',
        data:{group:group,course:1},
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        async: false,
        success:function(response){
          $('.selective_course_codes').html(response);
        }
      });//end of ajax
      //ajax start
      $.ajax({
        type:'POST',
        url:'hscGroupChange',
        data:{group:group,course:2},
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        async: false,
        success:function(response){
          $('.optional_course_codes').html(response);
        }
      });//end of ajax


      }); 


 }); 


    