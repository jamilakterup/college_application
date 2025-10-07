$("document").ready(function(){

   
   
   
 	  //$('#student_id').number();
 $("#admission_step").click(function(){

 	 $('#admission_step_modal').modal('show'); 

 	  var admission_roll =$('#admission_roll').val();
      var faculty =$('#faculty').val();

 	  if(admission_roll =='')
 	  {
 	  	 $('#next_step_error').html('<span style="color:red;">Enter Admission Roll ');
         $('#information').hide();
 	  }
 	  else if(faculty=='')
 	  {
 	  	 $('#next_step_error').html('<span style="color:red;">Select Faculty');
         $('#information').hide();
 	  }

 	  else
       $.ajax({	
        type:'POST',
        url:'Degree/checkMerit',
        data:{admission_roll: admission_roll,faculty: faculty},
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
                    var url ='Degree/Form';
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