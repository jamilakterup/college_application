<script src="{{ asset('js/loadingoverlay.min.js') }}"></script>
<script>

    function load_modal_module(){
        load_selectize();
        load_select2();
    }

    function trigger_ajax_modal(data){
        event.preventDefault();
        url = data.attr('href');
        data_label = data.attr('data-label');
        if(data_label == undefined) data_label = '';
        $.ajax({
            type: "POST",
            url: url,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            beforeSend: function() {
                $.LoadingOverlay("show");
            },
            success: function (response , textStatus, xhr) {
                $.LoadingOverlay("hide");
                modal = loadAjaxModal(response);
                modal.find('.modal-body').html(response.html);
                modal.find('.modal-title').html(data_label);
                modal.modal('show');
                load_modal_module();
            },

            error: function (error) {
                $.LoadingOverlay("hide");
                trigger_ajax_toastr_msg(error);
            }
        });
    }

    $(document).on('submit',$('#adm-form-data'), function(e){
        e.preventDefault();
        postForm = $('#adm-form-data');

        var url = postForm.attr('action');

        if(url != undefined){

          $.ajax({
              data: $('#adm-form-data').serialize(),
              url: url,
              type: "POST",
              beforeSend: function() {
                postForm.find('.invalid-feedback').text('');
                $('#adm-content').prepend('<div class="fa-4x text-center fa-loading"><i class="fas fa-spinner fa-pulse"></i></div>');
              },
              dataType: 'json',
              success: function (response , textStatus, xhr) {
                console.log(xhr)

                if(response.status === 'success'){
                    $('#adm-content').find('.fa-loading').remove();
                }
              },
              error: function (xhr, status, error) {
                $('#adm-content').find('.fa-loading').remove();
                var errors = [];
                var errorKeys = [];
                if(xhr.status === 422 ) {
                  $.each(xhr.responseJSON.errors, function (key, error) 
                    {
                      errors[key] = error[0];
                      errorKeys.push(key);
                    });
                     postForm.find('input,select').each(function(i, v) {
                        var tag = $(this);
                        var keyName = tag.attr('name');
                        fkey = keyName;
                        if (keyName !== undefined) {
                          if ( keyName.indexOf('[]') > -1 ) {
                             fkey = keyName.replace('[]','');
                          }
                        }

                        if ($.inArray(fkey, errorKeys) == -1)
                        {
                          tag.siblings('.invalid-feedback').text('')
                        }else{
                          tag.siblings('.invalid-feedback').text(errors[fkey]);
                        }
                      });
                }
                    trigger_ajax_swal_msg(xhr);
                }
          });
        }
    });
</script>