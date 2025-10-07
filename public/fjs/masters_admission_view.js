$("document").ready(function(){

   
   
   
 	  //$('#student_id').number();
 $("#admission_step").click(function(){

 	 $('#admission_step_modal').modal('show'); 

    var roll = $('#roll').val();
    var exam_type=$("#exam_type").val();
    var passing_year=$("#pass_year").val();

 	  if(isNaN(roll) || (roll==''))
 	  {
 	  	 $('#next_step_error').html('<span style="color:red;">Please Input Valid Roll');
         $('#information').hide();
 	  }
 	  else if(exam_type=='')
 	  {
 	  	 $('#next_step_error').html('<span style="color:red;">Exam Type Is Required');
         $('#information').hide();
 	  }
   else if(passing_year==''){
        $('#next_step_error').html('<span style="color:red;">Payssing Year Is Required</span>');
        $('#information').hide();
      }
 	  else
       $.ajax({	
        type:'POST',
        url:'Masters/checkMerit',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data:{roll: roll, exam_type: exam_type, passing_year: passing_year},
        success:function(response){
        	$('#admission_step_modal').modal('hide');
        	 var status= response;
        	//alert(status);
        	if(status==3 )
        	{

               $('#next_step_error').html('<span style="color:red;">You Are Not in Merit List');
               $('#information').hide();

            }
            
            if(status==1)
            {
                    var url ='Masters/Form';
                    window.location=url;
            }
        }
    });
 });
 
 
   $('#fathers_name').keyup(function(){
                        var selValue = $('input[name=guardian_info]:checked').val();
                        //alert(selValue);
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
                          $('#guardian_name').attr('value', $('#father_name').val());
                        } else {
                          $('#guardian_relation').attr('value', 'Mother');
                          $('#guardian_name').attr('value', $('#mother_name').val());
                        }
                      }
                    });
});