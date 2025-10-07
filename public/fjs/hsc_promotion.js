$("document").ready(function(){
$("#submit_payment_type").click(function(){ 
var student_id=$("#studentID").val();
var payType=$("#payType").val();
var regNumber = $("#regNumber").val();
if(regNumber==''){alert ('Please enter your registration number'); return false;}
if(payType==''){alert ('Please select a type'); return false;}
    else {  
        $.ajax({    
            type:'POST',
            url:'checktype',
            data:{student_id:student_id,payType:payType, regNumber:regNumber},
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
                    success:function(response){
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
                //alert('sadnsd')   ;           
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

    var roll =$('.roll').val();
    var session =$('.session').val();
    var group =$('.group').val();
      if(roll =='')
      {
         $('#next_step_error').html('<span style="color:red;">Enter Student roll ');
         $('#information').hide();
      }
      else if(session=='')
      {
         $('#next_step_error').html('<span style="color:red;">Select Exam session ');
         $('#information').hide();
      }
      else if(group=='')
      {
         $('#next_step_error').html('<span style="color:red;">Select Exam group ');
         $('#information').hide();
      }   
      
      else
       $.ajax({ 
        type:'POST',
        url:'promotion/check',
        data:{roll:roll,session:session,group:group},
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success:function(response){
            $('#admission_step_modal').modal('hide');
             var status= response;
            //alert(response);
            if(status==2 )
            {

               $('#next_step_error').html('<span style="color:red;">Form Fillup Not Open');
               $('#information').hide();

            }
            if(status==10 )
            {
               $('#next_step_error').html('<span style="color:red;"> Please Contact With College Administration');
               $('#information').hide();

            }
            if(status==7 )
            {
               $('#next_step_error').html('<span style="color:red;">You Are Not Hsc Student');
               $('#information').hide();

            }
            if(status==3 )
            {
                $('#next_step_error').html('<span style="color:red;">Exam Year Not Match');
                $('#information').hide();

            }
            if(status==4)
            {
                 $('#next_step_error').html('<span style="color:red;">Form Fillup Date is Expired');  
                 $('#information').hide(); 
            }
        if(status==5){
        $('#next_step_error').html('<span style="color:red;">Student id is Wrong.');  
        $('#information').hide(); 
        }
            if(status==11)
            {
                 $('#next_step_error').html('<span style="color:red;">Student ID is Wrong.');  
                 $('#information').hide(); 
            }
            if(status==6)
            {
               $('#next_step_error').html('<span style="color:red;">You are not a HSC Student.');  
               $('#information').hide(); 
            }

            if(status==8)
            {
                    var url ='promotion/dbbl_view';
                    window.location=url;
            }
            if(status==1)
            {
                    var url ='promotion/view';
                    window.location=url;
            }
        }
    });
 });
});