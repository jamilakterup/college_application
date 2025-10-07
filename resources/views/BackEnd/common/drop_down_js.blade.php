<script>
    $(document).on("change", '#faculty', function(e) {
            var faculty = $(this).val();
           // hi
if(faculty)
{
    $.ajax({
    type: "GET",
    url: "{{url('students/honours/dropdown')}}",
    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    data:{faculty: faculty},
    success: function(data){
        //alert(data);
        $("#depti").html(data);
    }
    });
}
else
//alert("Please Select Faculty") ;
var data='';
$("#depti").html(data);

        });

$(document).on("click", '#depti', function(e) {


     var dept = $(this).val();
     // console.log(dept);
     if(!dept)
     alert('Select Faculty First');
    /*else
        alert('Select Faculty');
*/
});
//HSC Admision Change District
$(document).on("change", '#present_dist', function(e) {
            var dist = $(this).val();
            //alert(dist);
if(dist)
{
    $.ajax({
    type: "POST",
    url: "districtCh",
    data:{dist: dist},
    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    async: false,
    success: function(data){
       // alert(data);
        $("#present_thana").html(data);
    }
    });
}
else
//alert("Please Select Faculty") ;
var data='';
$("#present_thana").html(data);

        });

$(document).on("click", '#present_thana', function(e) {


     var dept = $(this).val();
     //alert(dept);
     if(!dept)
        //alert('hello');
     alert('Select District First');
    /*else
        alert('Select Faculty');
*/
});



$(document).on("change", '#permanent_district', function(e) {
            var dist = $(this).val();
            //alert(dist);
if(dist)
{
    $.ajax({
    type: "POST",
    url: "districtCh",
    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    data:{dist: dist},
    async: false,
    success: function(data){
       // alert(data);
        $("#permanent_thana").html(data);
    }
    });
}
else
//alert("Please Select Faculty") ;
var data='';
$("#permanent_thana").html(data);

        });

$(document).on("click", '#permanent_thana', function(e) {


     var dept = $(this).val();
     //alert(dept);
     if(!dept)
        //alert('hello');
     alert('Select District First');
    /*else
        alert('Select Faculty');
*/
});



</script>