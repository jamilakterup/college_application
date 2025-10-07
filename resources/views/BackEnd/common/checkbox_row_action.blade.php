@php
$main_checkbox = isset($main_checkbox) ? $main_checkbox :'main_checkbox';
$item_checkbox = isset($item_checkbox) ? $item_checkbox : 'item_checkbox';
$checkboxAllBtn = isset($checkboxAllBtn) ? $checkboxAllBtn : 'checkboxAllBtn';
$route = isset($route) ? $route : null;
@endphp

<script>
    label = $('#checkboxAllBtn').attr('data-action');

    if(!label){
        label = 'Proccess';
    }
	$(document).on('click','input[name="{{$main_checkbox}}"]', function(){
            if(this.checked){
              $('input[name="{{$item_checkbox}}"]').each(function(){
                  this.checked = true;
              });
            }else{
               $('input[name="{{$item_checkbox}}"]').each(function(){
                   this.checked = false;
               });
            }
            togglecheckboxAllBtn();
     });

     $(document).on('change','input[name="{{$item_checkbox}}"]', function(){

         if( $('input[name="{{$item_checkbox}}"]').length == $('input[name="{{$item_checkbox}}"]:checked').length ){
             $('input[name="{{$main_checkbox}}"]').prop('checked', true);
         }else{
             $('input[name="{{$main_checkbox}}"]').prop('checked', false);
         }
         togglecheckboxAllBtn();
     });


     function togglecheckboxAllBtn(){
         if( $('input[name="{{$item_checkbox}}"]:checked').length > 0 ){
             $('button#{{$checkboxAllBtn}}').text(label + '('+$('input[name="{{$item_checkbox}}"]:checked').length+')').removeClass('d-none');
         }else{
             $('button#{{$checkboxAllBtn}}').addClass('d-none');
         }
     }


     $(document).on('click','button#{{$checkboxAllBtn}}', function(){
         var checkedItem = [];
         $('input[name="{{$item_checkbox}}"]:checked').each(function(){
             checkedItem.push($(this).data('id'));
         });

         var url = '{{ $route }}';
         if(checkedItem.length > 0){
            var length = checkedItem.length;
             swal.fire({
                 title:'Are you sure?',
                 html:`You want to ${label} <b>(${length})</b> data ?`,
                 showCancelButton:true,
                 showCloseButton:true,
                 confirmButtonText:`Yes, ${label}`,
                 cancelButtonText:'Cancel',
                 confirmButtonColor:'#556ee6',
                 cancelButtonColor:'#d33',
                 width:400,
                 allowOutsideClick:false
             }).then(function(result){
                 if(result.value){
                     $.ajax({
		                type: "post",
		                url: url,
		                data: {ids:checkedItem},
		                success: function (response , textStatus, xhr) {
		                	var table = eval(response.table);
                  			var tableid = response.table;
                            if(tableid){
                                scrollPos = $('#'+tableid+'').parents('div.dataTables_scrollBody').scrollTop();
                                table.ajax.reload(function() {
                                    $('#'+tableid+'').parents('div.dataTables_scrollBody').scrollTop(scrollPos);
                                },false);
                            }
                            trigger_ajax_swal_msg(xhr);
		                  $('button#{{$checkboxAllBtn}}').addClass('d-none');
		                },
		                error: function (error) {
		                    $.LoadingOverlay("hide");
		                    trigger_ajax_swal_msg(error);
		                }
		            });
                 }
             })
         }
     });
</script>