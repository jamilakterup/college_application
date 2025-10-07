<script>
function trigger_ajax_proccessing(data){
  $.ajax({
    type: 'POST',
    processData: false,
    contentType: false,
    url: data.attr('href'),
    cache: false,
    data: {},
    beforeSend: function() {
      Swal.fire({
        title: data.data('action'),
        text: "Please Wait",
        imageUrl: '{{ asset('img/load.gif') }}',
        showConfirmButton: false,
        allowOutsideClick: false
      });
    },
    success: function(response , textStatus, xhr){
    	trigger_ajax_toastr_msg(xhr)
      	Swal.close();
    },
    error: function (error) {
      	Swal.close();
        $.LoadingOverlay("hide");
        trigger_ajax_toastr_msg(error);
    }
    
  });
}


function getThanaOption(district){
    value = district.value;
    if(value == ''){
      value = 'empty';
    }
    data_for = $(district).attr('data-for');
    $.ajax({
        type:"POST",
        url:"{{url('api/get_thana_options')}}/"+value,
        success:function(result){
            var my_data = result.data;
            if(my_data != undefined){
                if(my_data){
                    $(`#${data_for}`).html(my_data);
                  }else{
                    $(`#${data_for}`).html('');
                }
            }
            if(present_ps !=undefined && present_ps != ''){
              $('#permanent_ps').val(present_ps).change();
              present_ps = '';
            }
        },
        error:function(error){
            trigger_ajax_swal_msg(error);
        }
    });
}
</script>