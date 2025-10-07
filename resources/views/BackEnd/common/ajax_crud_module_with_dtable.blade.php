@push('styles')
<style type="text/css">
.modal-body .form-group {
    margin-bottom: 0.600rem;
}
</style>
@endpush
  <script>
    function removeTag(tag){
      if ($(tag).length) {
          $(tag).remove();
      }
    }

  $(document).ready(function() {

    modal =$('#ajax-modal');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': "{{csrf_token()}}"
        }
    });

    $(document).on('click','.add_new',function (e) {
      event.preventDefault();
      data_label = $(this).attr('data-label');
      if(data_label == undefined) data_label = '';
      url = $(this).attr('href');
      $.ajax({
          type: "GET",
          url: url,
          beforeSend: function() {
            $.LoadingOverlay("show");
          },
          success: function (response , textStatus, xhr) {
            $.LoadingOverlay("hide");
            modal = loadAjaxModal(response);
            modal.modal('show');
            modal.find('.modal-body').html(response.html);
            postForm = modal.find(`[data-form='postForm']`);
            modal.find(`[data-button='save']`).attr('data-value', 'create');
            modal.find(`[data-button='save']`).val("Add New");
            postForm.trigger("reset");
            modal.find('.modal-title').html("Add New "+data_label);
            load_modal_particles();

            actionType = modal.find(`[data-button='save']`).attr('data-value');
            if(actionType =='create'){
              $input = $('<input type="hidden" name="action_type"/>').val('create');
              postForm.append($input);
            }
          },

          error: function (error) {
            $.LoadingOverlay("hide");
            trigger_ajax_toastr_msg(error);
          }
      });
    });

    $(document).on('submit',modal.find(`[data-form='postForm']`), function(e){
        e.preventDefault();
        postForm = modal.find(`[data-form='postForm']`);
   
        actionType = modal.find(`[data-button='save']`).attr('data-value');
        var url = postForm.attr('action');

        if(url != undefined){

          $.ajax({
              data: modal.find(`[data-form='postForm']`).serialize(),
              url: url,
              type: "POST",
              beforeSend: function() {
                modal.find(`[data-button='save']`).val('Sending..').attr('disabled', 'disabled');
                postForm.find('.invalid-feedback').text('');
              },
              dataType: 'json',
              success: function (response , textStatus, xhr) {

                if(xhr.status === 200){

                  var table = eval(response.table);
                  var tableid = response.table;
                  if (actionType != "update") {
                      table.draw();
                      table.on('draw', function () {
                        $('#'+tableid+'').find(`[data-row-id='${response.id}']`).addClass('update_row');
                      });

                  }else{
                    
                    scrollPos = $('#'+tableid+'').parents('div.dataTables_scrollBody').scrollTop();
                    table.ajax.reload(function() {
                        $('#'+tableid+'').parents('div.dataTables_scrollBody').scrollTop(scrollPos);
                        $('#'+tableid+'').find(`[data-row-id='${response.id}']`).addClass('update_row');
                    },false);

                  }

                  modal.modal('hide');
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

                modal.find(`[data-button='save']`).val(btn_val).prop("disabled", false);
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

                  trigger_ajax_toastr_msg(xhr);
                }
          });
        }
    });

  $(document).on('click','.show_data',function (e) {
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
            success: function (response , textStatus, xhr) {
              $.LoadingOverlay("hide");
              modal = loadAjaxModal(response);
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

    $(document).on('click','.duplicate_data', function (e){
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
          success: function (response , textStatus, xhr) {
            $.LoadingOverlay("hide");
            modal = loadAjaxModal(response);
            modal.modal('show');
            modal.find('.modal-body').html(response.html);
            postForm = modal.find(`[data-form='postForm']`);
            modal.find(`[data-button='save']`).attr('data-value', 'duplicate');
            modal.find(`[data-button='save']`).val("Add New");
            modal.find('.modal-title').html("Duplicate "+ data_label);
            modal.find("input[name=id]").val('');
            load_modal_particles();

            actionType = modal.find(`[data-button='save']`).attr('data-value');
            if(actionType =='duplicate'){
              $action_type = $('<input type="hidden" name="action_type"/>').val('duplicate');
              $hidden_id = $('<input type="hidden" name="id"/>').val(data_id);
              postForm.append($action_type);
              postForm.append($hidden_id);
            }
          },
          error: function (error) {
            $.LoadingOverlay("hide");
            trigger_ajax_toastr_msg(error);
          }
      });
    });

    $(document).on('click','.edit_data', function (e){
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
          success: function (response , textStatus, xhr) {
            $.LoadingOverlay("hide");
            modal = loadAjaxModal(response);
            modal.modal('show');
            modal.find('.modal-body').html(response.html);
            postForm = modal.find(`[data-form='postForm']`);
            modal.find(`[data-button='save']`).attr('data-value', 'update');
            modal.find(`[data-button='save']`).val("Save Changes");
            modal.find('.modal-title').html("Update "+data_label);
            load_modal_particles();

            actionType = modal.find(`[data-button='save']`).attr('data-value');
            if(actionType =='update'){
              $action_type = $('<input type="hidden" name="action_type"/>').val('update');
              $hidden_id = $('<input type="hidden" name="id"/>').val(data_id);
              postForm.append($action_type);
              postForm.append($hidden_id);
            }
          },
          error: function (error) {
            $.LoadingOverlay("hide");
            trigger_ajax_toastr_msg(error);
          }
      });
    });

    $(document).on('click','.delete_data', function (e){
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
                  var row = table.row( tr );
               
                  if ( row.child.isShown() ) {
                      // This row is already open - remove it
                      row.child( false ).remove();
                  }

                  tr.remove();
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

    if(typeof(table) != "undefined" && table !== null) {
        table.on('draw', function(){
              $('input[name="item_checkbox"]').each(function(){this.checked = false;});
              $('input[name="main_checkbox"]').prop('checked', false);
              $('button#deleteAllBtn').addClass('d-none');
          });
    }

  });

  </script>