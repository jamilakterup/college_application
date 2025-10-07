$("document").ready(function(){

   
   
   
 	  //$('#student_id').number();
 $("#admission_step").click(function(){

 	 $('#admission_step_modal').modal('show'); 

 	  var ssc_roll =$('#ssc_roll').val();
      var ssc_board =$('#ssc_board').val();
      var ssc_passing_year =$('#ssc_passing_year').val();
      var quota_pass =$('#quota_pass').val();
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
        data:{ssc_roll: ssc_roll,ssc_board: ssc_board,ssc_passing_year: ssc_passing_year,quota_pass:quota_pass,"_token": $('meta[name="csrf-token"]').attr('content')},
        success:function(response){
        	$('#admission_step_modal').modal('hide');
        	 var status= response;
        	//alert(status);
        	if(status==3 )
        	{

               $('#next_step_error').html('<span style="color:red;">You Are Not in Merit List');
               $('#information').hide();

            }
         	if(status==5 )
        	{

             $('#next_step_error').html('<span style="color:red;">Please Get Quota Password From Your College');
             $('#information').hide();

          }

          if(status==4 )
          {

             $('#next_step_error').html('<span style="color:red;">Admission is not open!');
             $('#information').hide();

          }

          if(status==4 )
          {

             $('#next_step_error').html('<span style="color:red;">Admission date is expired!');
             $('#information').hide();

          }

          if(status==2 )
          {

             $('#next_step_error').html('<span style="color:red;">Your bill is not generated. Please contact to college');
             $('#information').hide();

          }

          if(status == 7){
            var url ='HSC/HscConfirmation';
            window.location=url;
          }

          if(status==1)
          {
                  var url ='HSC/Form';
                  window.location=url;
          }
        }
    });
 });
});