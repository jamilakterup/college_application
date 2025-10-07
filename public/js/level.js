$(document).on("change", '#program_id', function(e) {
            var program = $(this).val();
            //alert(program);
if(program==2)
{
  $.ajax({
    type: "POST",
    url: "/hscdrop",
    data:{program: program},
    success: function(data){
        //alert(data);
        $("#department_id").html(data);
        //$("#department_id").html(data);
    }
    });  
}
else
{
    $.ajax({
    type: "POST",
    url: "/hscdropdown",
    data:{program: program},
    success: function(data){
        //alert(data);
        $("#department_id").html(data);
        //$("#department_id").html(data);
    }
    }); 
}

          
if(program)
{
    $.ajax({
    type: "POST",
    url: "/level",
    data:{program: program},
    success: function(data){
        //alert(data);
        $("#current_level").html(data);
        //$("#department_id").html(data);
    }
    });
}
else
//alert("Please Select Faculty") ;
var data='';
$("#current_level").html(data);

        });

$(document).on("click", '#current_level', function(e) {


     var current_level = $(this).val();

     if(!current_level)
        //alert('hello');
     alert('Select Program First');
    /*else
        alert('Select Faculty');
*/
});