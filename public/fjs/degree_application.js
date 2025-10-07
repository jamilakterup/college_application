$("document").ready(function(){

$("#submit_payment_type").click(function(){ 
var student_id=$("#student_id").val();
var payType=$("#payType").val();

if(payType==''){alert ('Please select a type'); return false;}
	else {  
		$.ajax({	
			type:'POST',
			url:'checktype',
			data:{student_id:student_id,payType:payType},
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			success:function(response){
		
						var url ='dbbl_view';
						window.location=url;

			}
		});
	}	
 });


	$("#confirm_slip").click(function(){
           
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
      			       $("#download_form_file").modal('show');

              }
          });
  
  });

  $("#download_form").click(function(){
           
      $("#confirm_slidownload_linkp_file").html("Please Wait.Processing..");
          $.ajax({
              type:'POST',
              url:'formId',
              data:{},
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
              beforeSend: function() {
                  $.LoadingOverlay("show");
              },
              success:function(response){
                $.LoadingOverlay("hide");
                $("#download_link1").html(response);
                $("#download_form_file").modal('show');

              }
          });
  
  });

	$("#con_mes").hide();
		/*Student Payment Approve action*/
			$("#submit_payment").click(function(){ 
				$("#submit_payment").attr('value','Please wait...');				
				$("#con_mes").html('অনুগ্রহ করে কিছুক্ষণ অপেক্ষা করুন...');
				var registration_id=$("#studentID").val();				
				var trx_id = 	$("#trxid").val();
				var pay_am_floor = $("#pay_am_floor").val();
				var ans=window.confirm("Are you sure?");
				if(ans){				
				$.ajax({
					type:'POST',
					url:'dbbl_approve',
					data:{registration_id:registration_id,trx_id:trx_id,pay_am_floor:pay_am_floor},
					headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					success:function(response){
						//alert(response)	;
						$("#con_mes").show();
						$("#con_mes").html('');
						$("#con_mes").addClass('alert-danger');
						$("#submit_payment").attr('value','Submit');
						$("#con_mes").html('<h4>'+response+'</h4>');
						
						}					
					
				});
			}/*End of if(ans)*/
		});

 	  //$('#student_id').number();
 $("#admission_step").click(function(){	


 	 $('#admission_step_modal').modal('show'); 

	var roll =$('#admission_roll').val();
	// var current_level = $('#current_level').val();
 	  if(roll =='')
 	  {
 	  	 $('#next_step_error').html('<span style="color:red;">Enter Admission roll ');
         $('#information').hide();
 	  }

 	   else
       $.ajax({	
        type:'POST',
        url:'Degree/check',
        data:{roll:roll},
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success:function(response){
			//alert(response);
             $('#admission_step_modal').modal('hide');
        	 var status= response;
        	 console.log(status);
        	if(status==0 )
        	{
               $('#next_step_error').html('<span style="color:red;">Application Not Open');
               $('#information').hide();

            }
			if(status==5){
				 $('#next_step_error').html('<span style="color:red;">Student ID is Wrong.');  
				 $('#information').hide(); 
			}		 
			if(status==1){
					var url ='Degree/form';
					window.location=url;
			}

      if(status==2){
          $('#next_step_error').html('<span style="color:red;">Payslip not found. please contact to college.');
               $('#information').hide();
      }
      if(status==3){
          $('#next_step_error').html('<span style="color:red;">Degree Application is not opened.');
               $('#information').hide();
      }
      if(status==4){
          $('#next_step_error').html('<span style="color:red;">Degree Application date is expired.');
               $('#information').hide();
      }
      if(status==7){
          $('#next_step_error').html('<span style="color:red;">Invalid Admission Roll');
               $('#information').hide();
      }
      if(status==6){
          var url ='Degree/dbblapplication';
          window.location=url;
      }

        }
    });
 });

 // $('#guardian_mobile').keydown(
 //           function(e){

 //            var value=$(this).val();            
 //            var point_exist=0;
 //            point_exist=value.indexOf(".");            
 //            var key_code=e.which;                   
 //            var point_allowed=1;
 //            if(key_code==46 && point_exist>=0) point_allowed=0;
 //            if((( (key_code!=0 && key_code<48) || key_code>57) &&  key_code!=8 && key_code!=46) || point_allowed==0)
 //                e.preventDefault();
 //    });

 //      $('#guardian_mobile').keydown(
 //           function(e){

 //            var value=$(this).val();            
 //            var point_exist=0;
 //            point_exist=value.indexOf(".");            
 //            var key_code=e.which;                   
 //            var point_allowed=1;
 //            if(key_code==46 && point_exist>=0) point_allowed=0;
 //            if((( (key_code!=0 && key_code<48) || key_code>57) &&  key_code!=8 && key_code!=46) || point_allowed==0)
 //                e.preventDefault();
 //    });

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
                      if (selValue == 'Other') {
                        $('#guardian_name').attr('disabled', false);
                        $('#guardian_relation').attr('disabled', false);
                        $('#guardian_name').attr('value', '');
                        $('#guardian_relation').attr('value', '');
                      } else {
                        $('#guardian_name').attr('disabled', true);
                        $('#guardian_relation').attr('disabled', true);
                        $('#guardian_name').attr('value', $('#'+selValue+'_name').val());
                        if (selValue == 'Father') {
                          $('#guardian_relation').attr('value', 'Father');
                          $('#guardian_name').attr('value', $('#father_name').val());
                        } else {
                          $('#guardian_relation').attr('value', 'Mother');
                          $('#guardian_name').attr('value', $('#mother_name').val());
                        }
                      }
                    });
});