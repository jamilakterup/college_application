$("document").ready(function(){


 $("#honours_admission_step").click(function(){

 	 $('#admission_step_modal').modal('show'); 

 	  var honours_roll =$('#honours_roll').val();

 	  if(honours_roll =='')
 	  {
 	  	 $('#next_step_error').html('<span style="color:red;">Enter Admission Roll ');
         $('#information').hide();
 	  }

 	  else
       $.ajax({	
        type:'POST',
        url:'Masters/checkMerit',
        data:{honours_roll: honours_roll},
        success:function(response){
        	$('#admission_step_modal').modal('hide');
        	 var status= response;
        	if(status==3 )
        	{

               $('#next_step_error').html('<span style="color:red;">Admission Not Open');
               $('#information').hide();

            }
        	if(status==4 )
        	{

               $('#next_step_error').html('<span style="color:red;">Invoice is not generated. Please Contact to college');
               $('#information').hide();

            }

            if(status==2 )
            {

               $('#next_step_error').html('<span style="color:red;">You Are Not in Merit List');
               $('#information').hide();

            }

            if(status==6 )
            {

               $('#next_step_error').html('<span style="color:red;">Admission is closed!');
               $('#information').hide();

            }           
			
            if(status==1)
            {
                    var url ='Masters/Form';
                    window.location=url;
            }

            if(status==5)
            {
                    var url ='Masters/mscConfirmation';
                    window.location=url;
            }
        }
    });
 });


   $('#fathers_name').keyup(function(){
                        var selValue = $('input[name=guardian_info]:checked').val();
                       // alert(selValue);
                        if (selValue == 'Father') {
                          $('#guardian_name').attr('value', $('#fathers_name').val());
                        }
                      });

   $('#mothers_name').keyup(function(){
                        var selValue = $('input[name=guardian_info]:checked').val();
                        if (selValue == 'Mother') {
                          $('#guardian_name').attr('value', $('#mothers_name').val());
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
                          $('#guardian_name').attr('value', $('#fathers_name').val());
                        } else {
                          $('#guardian_relation').attr('value', 'Mother');
                          $('#guardian_name').attr('value', $('#mothers_name').val());
                        }
                      }
                    });


 }); 


    