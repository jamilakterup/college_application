@php
$main_checkbox = isset($main_checkbox) ? $main_checkbox :'main_checkbox';
$item_checkbox = isset($item_checkbox) ? $item_checkbox : 'item_checkbox';
$deleteAllBtn = isset($deleteAllBtn) ? $deleteAllBtn : 'deleteAllBtn';
$route = isset($route) ? $route : null;
@endphp

<script>
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
            toggledeleteAllBtn();
     });

     $(document).on('change','input[name="{{$item_checkbox}}"]', function(){

         if( $('input[name="{{$item_checkbox}}"]').length == $('input[name="{{$item_checkbox}}"]:checked').length ){
             $('input[name="{{$main_checkbox}}"]').prop('checked', true);
         }else{
             $('input[name="{{$main_checkbox}}"]').prop('checked', false);
         }
         toggledeleteAllBtn();
     });


     function toggledeleteAllBtn(){
         if( $('input[name="{{$item_checkbox}}"]:checked').length > 0 ){
             $('button#{{$deleteAllBtn}}').text('Delete ('+$('input[name="{{$item_checkbox}}"]:checked').length+')').removeClass('d-none');
         }else{
             $('button#{{$deleteAllBtn}}').addClass('d-none');
         }
     }


     $(document).on('click','button#{{$deleteAllBtn}}', function(){
         var checkedItem = [];
         $('input[name="{{$item_checkbox}}"]:checked').each(function(){
             checkedItem.push($(this).data('id'));
         });

         var url = '{{ $route }}';
         if(checkedItem.length > 0){
             swal.fire({
                 title:'Are you sure?',
                 html:'You want to delete <b>('+checkedItem.length+')</b> data ?',
                 showCancelButton:true,
                 showCloseButton:true,
                 confirmButtonText:'Yes, Delete',
                 cancelButtonText:'Cancel',
                 confirmButtonColor:'#556ee6',
                 cancelButtonColor:'#d33',
                 width:300,
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
			                 $.each(checkedItem, function (key, val) {
				                var tr = $('#'+tableid+'').find(`[data-row-id='${val}']`);
				                var row = table.row( tr );
				                if ( row.child.isShown() ) {
				                    // This row is already open - remove it
				                    row.child( false ).remove();
				                }
				                tr.remove();
						     });
		                  trigger_ajax_toastr_msg(xhr);
		                  $('button#{{$deleteAllBtn}}').addClass('d-none');
		                },
		                error: function (error) {
		                    $.LoadingOverlay("hide");
		                    trigger_ajax_toastr_msg(error);
		                }
		            });
                 }
             })
         }
     });
</script>