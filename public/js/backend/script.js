$('.date').datepicker( {
    format: 'yyyy-mm-dd',
});

setTimeout( function() { 
    $('.update_row').removeClass('update_row'); 
}, 5000 );

$('.update_row').focus();

$(document).on('click', '.delete',function(event) {
	event.preventDefault();
	Swal.fire({
	  title: 'Are you sure?',
	  text: "You won't be able to revert this!",
	  icon: 'warning',
	  showCancelButton: true,
	  confirmButtonColor: '#3085d6',
	  cancelButtonColor: '#d33',
	  confirmButtonText: 'Yes, delete it!'
	}).then((result) => {
	  if (result.isConfirmed) {
	    $(this).submit();
	  }
	})
});

function preview_image_url(input) {
  type = input.dataset.type;
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
        $('#'+type+'_image_pre_area').show();
      $('#'+type+'_image_pre').attr('src', e.target.result);
    }
    reader.readAsDataURL(input.files[0]);
  }
}

$(".image_data").change(function() {
  preview_image_url(this);
});
