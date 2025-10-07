$("document").ready(function(){


 $("#admission_step").click(function(){	


 	 $('#admission_step_modal').modal('show'); 

 	  var year = $('#sel1').val();
 	  var id = $('#student_id').val();
	 var program = $('#program').val();
 	  if(id =='')
 	  {
 	  	 $('#next_step_error').html('<span style="color:red;">Enter Student ID ');
         $('#information').hide();
 	  }
 	  else if(year=='')
 	  {
 	  	 $('#next_step_error').html('<span style="color:red;">Select Session ');
         $('#information').hide();
 	  }
 	  else if(program=='')
 	  {
 	  	 $('#next_step_error').html('<span style="color:red;">Select Program ');
         $('#information').hide();
 	  }	  
 	  else
       $.ajax({	
        type:'POST',
        url:'studentpanel/check',
        data:{id: id,year: year, program: program},
        success:function(response){
        	$('#admission_step_modal').modal('hide');
        	 var status= response;
        	//alert(response);
        	if(status==2 )
        	{

               $('#next_step_error').html('<span style="color:red;">Student Not Found');
               $('#information').hide();

            }
            if(status==1 && (program=='Honours' || program=='Masters'))
            {
                    var url ='studentpanel/view';
                    window.location=url;
            }
            if(status==1 && program=='Degree')
            {
                    var url ='studentpanel/degview';
                    window.location=url;
            }

            if(status==1 && program=='HSC')
            {
                    var url ='studentpanel/hscview';
                    window.location=url;
            }            
        }
    });
 });
});