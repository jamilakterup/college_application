$("document").ready(function(){

 $("#showform").click(function(){
      var url ='Form';
      window.location=url;

 });  
   
   
 	  //$('#student_id').number();
 $("#admission_step").click(function(){

 	 $('#admission_step_modal').modal('show'); 

    var roll = $('#roll').val();

 	  if(isNaN(roll) || (roll==''))
 	  {
 	  	 $('#next_step_error').html('<span style="color:red;">Please Input Valid Roll');
         $('#information').hide();
 	  }

 	  else
       $.ajax({	
        type:'POST',
        url:'Masters1st/checkMerit',
        data:{roll: roll},
        success:function(response){
        	$('#admission_step_modal').modal('hide');
        	 var status= response;
        	//alert(status);
        	if(status==2 || status==3)
        	{

               $('#next_step_error').html('<span style="color:red;">Sorry Student Not Found');
               $('#information').hide();

            }

             if(status==4){
               $('#next_step_error').html('<span style="color:red;">আপনার এ্যাডমিশনের রোল নং ভুল হয়েছে। সঠিকভাবে পূরণ করুন।');
               $('#information').hide(); 
             }
            
            if(status==1)
            {
                    var url ='Masters1st/faculty';
                    window.location=url;
            }
        }
    });
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
 
 
 
});