@push('styles')
<style type="text/css">
.modal-body .form-group {
    margin-bottom: 0.600rem;
}
</style>
@endpush

<script src="{{ asset('js/loadingoverlay.min.js') }}"></script>
  <script>


    function removeTag(tag){
      if ($(tag).length) {
          $(tag).remove();
      }
    }

    function loadBasicModal(response){
      if(response.modal == 'modal-lg'){
          modal = $('#ajax-basic-modal-lg');
        }else if(response.modal == 'modal-sm'){
          modal = $('#ajax-basic-modal-sm');
        }else{
          modal = $('#ajax-basic-modal');
        }
        return modal;
    }

  $(document).ready(function() {
    modal =$('#ajax-basic-modal');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': "{{csrf_token()}}"
        }
    });

    $(document).on('click','.add_item',function (e) {
      event.preventDefault();

      url = $(this).attr('href');
      $.ajax({
          type: "GET",
          url: url,
          beforeSend: function() {
            $.LoadingOverlay("show");
          },
          success: function (response) {
            $.LoadingOverlay("hide");
            modal = loadBasicModal(response);
            modal.modal('show');
            modal.find('.modal-body').html(response.html);
            ajaxForm = modal.find(`[data-form='ajaxForm']`);
            modal.find(`[data-button='save']`).attr('data-value', 'create');
            modal.find(`[data-button='save']`).val("Add New");
            ajaxForm.trigger("reset");
            modal.find('.modal-title').html("Add New");
            load_modal_particles();

            actionType = modal.find(`[data-button='save']`).attr('data-value');
            if(actionType =='create'){
              $input = $('<input type="hidden" name="action_type"/>').val('create');
              ajaxForm.append($input);
            }
          },

          error: function (error) {
            $.LoadingOverlay("hide");
            trigger_ajax_toastr_msg(error);
          }
      });
    });

    $(document).on('submit',modal.find(`[data-form='ajaxForm']`), function(e){
        e.preventDefault();
        ajaxForm = modal.find(`[data-form='ajaxForm']`);
   
        actionType = modal.find(`[data-button='save']`).attr('data-value');
        var url = ajaxForm.attr('action');
        if(url != undefined){
          $.ajax({
              data: modal.find(`[data-form='ajaxForm']`).serialize(),
              url: url,
              type: "POST",
              beforeSend: function() {
                modal.find(`[data-button='save']`).val('Sending..').attr('disabled', 'disabled');
              },
              dataType: 'json',
              success: function (response , textStatus, xhr) {
                var tableid = response.table;
                var table = $('#'+tableid+'');

                if(xhr.status === 200){

                  if (actionType == "update") {
                      table.find(`[data-row-id='${response.id}']`).replaceWith(response.html);
                  }else{
                    table.find('tbody').prepend(response.html);
                  }

                  table.find(`[data-row-id='${response.id}']`).addClass('update_row');

                  modal.modal('hide');
                  if(response.modal !=''){
                    modal = loadBasicModal(response);
                  }
                  console.log(xhr)
                  trigger_ajax_toastr_msg(xhr);

                  focus_row();
                }
              },
              error: function (xhr, status, error) {
                $.LoadingOverlay("hide");
                if(actionType != 'update'){
                  var btn_val = 'Add New';
                }else{
                  var btn_val = 'Save Changes';
                }
                console.log(xhr);

                modal.find(`[data-button='save']`).val(btn_val).prop("disabled", false);
                var errors = [];
                var errorKeys = [];
                if(xhr.status === 422 ) {
                  $.each(xhr.responseJSON.errors, function (key, error) 
                    {
                      errors[key] = error[0];
                      errorKeys.push(key);
                    });
                     ajaxForm.find('input,select').each(function(i, v) {
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

                  trigger_ajax_toastr_msg(xhr);
                }
          });
        }

    });

  $(document).on('click','.show_item',function (e) {
        event.preventDefault();
        url = $(this).attr('href');
        data_label = $(this).attr('data-label');
        if(data_label == undefined) data_label = '';
        $.ajax({
            type: "GET",
            url: url,
            beforeSend: function() {
              $.LoadingOverlay("show");
            },
            success: function (response) {
              $.LoadingOverlay("hide");
              modal = loadBasicModal(response);
              modal.find('.modal-body').html(response.html);
              modal.find('.modal-title').html(data_label+" Details");
              modal.modal('show');
              load_modal_particles();
            },

            error: function (error) {
              $.LoadingOverlay("hide");
              trigger_ajax_toastr_msg(error);
            }
        });
      });

    $(document).on('click','.duplicate_item', function (e){
      e.preventDefault();
      data_id = $(this).data('id');
      url = $(this).attr('href');
      data_label = $(this).attr('data-label');
      if(data_label == undefined) data_label = '';

      $.ajax({
          type: "GET",
          url: url,
          beforeSend: function() {
            $.LoadingOverlay("show");
          },
          success: function (response) {
            $.LoadingOverlay("hide");
            modal = loadBasicModal(response);
            modal.modal('show');
            modal.find('.modal-body').html(response.html);
            ajaxForm = modal.find(`[data-form='ajaxForm']`);
            modal.find(`[data-button='save']`).attr('data-value', 'duplicate');
            modal.find(`[data-button='save']`).val("Add New");
            modal.find('.modal-title').html("Duplicate "+ data_label);
            modal.find("input[name=id]").val('');
            load_modal_particles();

            actionType = modal.find(`[data-button='save']`).attr('data-value');
            if(actionType =='duplicate'){
              $action_type = $('<input type="hidden" name="action_type"/>').val('duplicate');
              $hidden_id = $('<input type="hidden" name="id"/>').val(data_id);
              ajaxForm.append($action_type);
              ajaxForm.append($hidden_id);
            }
          },
          error: function (error) {
            $.LoadingOverlay("hide");
            trigger_ajax_toastr_msg(error);
          }
      });
    });

    $(document).on('click','.edit_item', function (e){
      e.preventDefault();
      data_id = $(this).data('id');
      url = $(this).attr('href');
      data_label = $(this).attr('data-label');
      if(data_label == undefined) data_label = '';

      $.ajax({
          type: "GET",
          url: url,
          beforeSend: function() {
            $.LoadingOverlay("show");
          },
          success: function (response) {
            $.LoadingOverlay("hide");
            modal = loadBasicModal(response);
            modal.modal('show');
            modal.find('.modal-body').html(response.html);
            ajaxForm = modal.find(`[data-form='ajaxForm']`);
            modal.find(`[data-button='save']`).attr('data-value', 'update');
            modal.find(`[data-button='save']`).val("Save Changes");
            modal.find('.modal-title').html("Update "+data_label);
            load_modal_particles();

            actionType = modal.find(`[data-button='save']`).attr('data-value');
            if(actionType =='update'){
              $action_type = $('<input type="hidden" name="action_type"/>').val('update');
              $hidden_id = $('<input type="hidden" name="id"/>').val(data_id);
              ajaxForm.append($action_type);
              ajaxForm.append($hidden_id);
            }
          },
          error: function (error) {
            $.LoadingOverlay("hide");
            trigger_ajax_toastr_msg(error);
          }
      });
    });

    $(document).on('click','.delete_item', function (e){
      e.preventDefault();
      url = $(this).attr('href');

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
            $.ajax({
                type: "DELETE",
                url: url,
                success: function (response , textStatus, xhr) {
                  var table = eval(response.table);
                  var tableid = response.table;
                  var tr = $('#'+tableid+'').find(`[data-row-id='${response.id}']`);
                  removeTag(tr);
                  trigger_ajax_toastr_msg(xhr);
                },
                error: function (error) {
                    $.LoadingOverlay("hide");
                    trigger_ajax_toastr_msg(error);
                }
            });
          }
        })
    });



  });
  </script>