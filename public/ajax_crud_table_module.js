// setup with basic table
function removeTag(tag){
  if ($(tag).length) {
    $(tag).remove();
  }
}

function getAjaxModalData(button, modalTitle=null, dataId=null){
  event.preventDefault();
  let modalId = modalTitle.replace(/\s/g, '');
  $button = $(button);
  url = $button.attr('data-href');
  $(`#${modalId}`).remove();
  $.ajax({
      type: "GET",
      url: url,
      beforeSend: function() {
        $.LoadingOverlay("show");
      },
      success: function (response , textStatus, xhr) {
        response.modalTitle = modalTitle;
        response.modalId = modalId;
        api_url = '/api/get-ajax-modal';
        $.ajax({
          type: "POST",
          url: App.baseUrl+api_url,
          data: response,
          dataType: "json",
          success: function (modalResponse) {
            $.LoadingOverlay("hide");
            let html = modalResponse.html;
            $('#load-ajax-modal').append(html);
            let modal = $(`#${modalId}`);
            modal.modal('show');
            load_modal_particles();

            let saveButton = modal.find(`[data-button='save']`);
            let buttonValue = 'Save Changes';
            let actionType = $button.data('action');

            let $ajaxForm = modal.find('form[onsubmit^="submitAjaxModalForm"]');
            if($ajaxForm.length > 0) {
              if(actionType != undefined){
                if(actionType == 'update'){
                  let $hidden_id = $('<input type="hidden" name="id"/>').val(dataId);
                  $ajaxForm.append($hidden_id);
                }else if(actionType == 'create'){
                  buttonValue = 'Add New';
                }
                let $action_type = $('<input type="hidden" name="action_type"/>').val(actionType);
                $ajaxForm.append($action_type);
                saveButton.attr('data-value', actionType);
              }

              if(saveButton){
                saveButton.html(`<i class="fa fa-check"></i> ${buttonValue}`);
              }

              $ajaxForm.attr('modal-id', modalId);
            }

          },
          error: function (error) {
            load_modal_particles();
            $.LoadingOverlay("hide");
            trigger_ajax_swal_msg(error);
          }
        });
      },
  
      error: function (error) {
        $.LoadingOverlay("hide");
        trigger_ajax_swal_msg(error);
      }
  });
}

function submitAjaxModalForm(form){
  event.preventDefault();
  let $ajaxForm = $(form);
  let actionType = $ajaxForm.find(`[data-button='save']`).attr('data-value');
  let url = $ajaxForm.attr('action');
  let saveButton = $ajaxForm.find(`[data-button='save']`);
  let modalId = $ajaxForm.attr('modal-id');
  let modal = $('#' + modalId);
  let buttonValue = 'Save Changes';
  if(actionType == 'create'){
      buttonValue = 'Add New';
  }

  if(url != undefined){
    var formData = new FormData($ajaxForm.get(0));
    $.ajax({
        data: formData,
        url: url,
        type: "POST",
        processData: false,
        contentType: false,
        beforeSend: function() {
          $ajaxForm.find('.invalid-feedback').remove();
          saveButton.html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ${buttonValue}`).attr('disabled', 'disabled');
        },
        dataType: 'json',
        success: function (response , textStatus, xhr) {
          load_modal_particles();
          if(xhr.status === 200){
            let table = eval(response.table);
            let tableid = response.table;
            if(table){
              
              if (actionType != "update") {
                let api = table.ajax.reload(function() {
                  let $row = $('#' + tableid).find(`[data-row-id='${response.id}']`);
                  if (response.drawTo == 'last') {
                    api.page('last').draw(false);
                  } else {
                    let index = table.row($row).index();
                    let pageInfo = api.page.info();
                    if (index >= pageInfo.start && index < pageInfo.end) {
                      $row.addClass('update_row');
                    } else {
                      api.page.jumpToData(response.id, 0);
                    }
                  }
                  table.one('draw', function() {
                    table.row($row).scrollTo(false);
                  });
                  $row.addClass('update_row');
                });
              }else{
                let scrollPos = $('#'+tableid+'').parents('div.dataTables_scrollBody').scrollTop();
                table.ajax.reload(function() {
                    $('#'+tableid+'').parents('div.dataTables_scrollBody').scrollTop(scrollPos);
                    $('#'+tableid+'').find(`[data-row-id='${response.id}']`).addClass('update_row');
                },false);
              }
            }

            modal.modal('hide');
            trigger_ajax_swal_msg(xhr);
            updateRowTimer();
          }
        },
        error: function (xhr, status, error) {
          load_modal_particles();
          $.LoadingOverlay("hide");
          saveButton.html(`<i class="fa fa-check"></i> ${buttonValue}`).attr('disabled', false);
          let errors = [];
          let errorKeys = [];
          if(xhr.status === 422 ) {
            $.each(xhr.responseJSON.errors, function (key, error)
              {
                errors[key] = error[0];
                errorKeys.push(key);
              });
              $ajaxForm.find('input,select,textarea').filter('[name]').each(function() {
                const tag = $(this);
                const keyName = tag.attr('name').replace('[]', '');
                const feedback = tag.siblings('.invalid-feedback').length ? tag.siblings('.invalid-feedback') : $('<div>').addClass('invalid-feedback');
                $.inArray(keyName, errorKeys) == -1 ? feedback.remove() : feedback.text(errors[keyName]);
                feedback.appendTo(tag.parent());
              });
          }else{
            trigger_ajax_swal_msg(xhr);
          }
        }
    });
  }
}

function deleteAjaxData(button, title='Delete This'){
  $button = $(button);
  url = $button.attr('data-href');

  Swal.fire({
    title:'Are you sure?',
    html:`You want to ${title} Record ?`,
    showCancelButton:true,
    showCloseButton:true,
    confirmButtonText:`Yes, ${title}`,
    cancelButtonText:'Cancel',
    confirmButtonColor:'#556ee6',
    cancelButtonColor:'#d33',
    width:400,
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        type: "DELETE",
        url: url,
        success: function (response , textStatus, xhr) {
          if(response.table){
            var table = eval(response.table);
            var tableid = response.table;
            var tr = $('#'+tableid+'').find(`[data-row-id='${response.id}']`);
            var row = table.row( tr );
            
            if ( row.child.isShown() ) {
              // This row is already open - remove it
              row.child( false ).remove();
            }
            
            tr.remove();
            trigger_ajax_swal_msg(xhr);
          }
          if(response.page_reload){
            window.location.reload(true);
          }
          
        },
        error: function (error) {
          $.LoadingOverlay("hide");
          trigger_ajax_swal_msg(error);
        }
      });
    }
  })
}

function loadBasicModal(response){
  if(response.modal == 'modal-lg'){
    modal = $('#ajax-basic-modal-lg');
  }else if(response.modal == 'modal-sm'){
    modal = $('#ajax-basic-modal-sm');
  }else if(response.modal == 'modal-xl'){
    modal = $('#ajax-basic-modal-xl');
  }else{
    modal = $('#ajax-basic-modal');
  }
  return modal;
}

$(document).ready(function() {
  modal =$('#ajax-basic-modal');
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  
  $(document).on('click','.add_item',function (e) {
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
        modal.modal('show');
        modal.find('.modal-body').html(response.html);
        ajaxForm = modal.find(`[data-form='ajaxForm']`);
        modal.find(`[data-button='save']`).attr('data-value', 'create');
        modal.find(`[data-button='save']`).val("Add New");
        ajaxForm.trigger("reset");
        modal.find('.modal-title').html("Add New "+data_label);
        load_modal_particles();
        
        actionType = modal.find(`[data-button='save']`).attr('data-value');
        if(actionType =='create'){
          $input = $('<input type="hidden" name="action_type"/>').val('create');
          ajaxForm.append($input);
        }
      },
      
      error: function (error) {
        $.LoadingOverlay("hide");
        trigger_ajax_swal_msg(error);
      }
    });
  });
  
  $(document).on('submit',modal.find(`[data-form='ajaxForm']`), function(e){
    if(modal.find(`[data-form='ajaxForm']`).length > 0){
      e.preventDefault();
      ajaxForm = modal.find(`[data-form='ajaxForm']`);
      
      actionType = modal.find(`[data-button='save']`).attr('data-value');
      var url = ajaxForm.attr('action');
      if(url != undefined){
        var formData = new FormData(ajaxForm.get(0));
        $.ajax({
          data: formData,
          url: url,
          type: "POST",
          processData: false,
          contentType: false,
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
                if(response.insert == 'append'){
                  table.find('tbody').append(response.html);
                }else{
                  table.find('tbody').prepend(response.html);
                }
              }
              
              table.find(`[data-row-id='${response.id}']`).addClass('update_row');
              
              modal.modal('hide');
              if(response.modal !=''){
                modal = loadBasicModal(response);
              }
              trigger_ajax_swal_msg(xhr);
              
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
              ajaxForm.find('input,textarea,select').each(function(i, v) {
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
            }else{
              trigger_ajax_swal_msg(xhr);
            }
            
          }
        });
      }
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
        trigger_ajax_swal_msg(error);
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
        trigger_ajax_swal_msg(error);
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
        trigger_ajax_swal_msg(error);
      }
    });
  });
  
  $(document).on('click','.delete_item', function (e){
    e.preventDefault();
    url = $(this).attr('href');
    
    Swal.fire({
      title:'Are you sure?',
      html:'You want to delete Record ?',
      showCancelButton:true,
      showCloseButton:true,
      confirmButtonText:'Yes, Delete',
      cancelButtonText:'Cancel',
      confirmButtonColor:'#556ee6',
      cancelButtonColor:'#d33',
      width:300,
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
            trigger_ajax_swal_msg(xhr);
          },
          error: function (error) {
            $.LoadingOverlay("hide");
            trigger_ajax_swal_msg(error);
          }
        });
      }
    })
  });
});


// setup with datatable

$(document).ready(function() {
  
  modal =$('#ajax-modal');
  
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
        trigger_ajax_swal_msg(error);
      }
    });
  });
  
  $(document).on('submit',modal.find(`[data-form='postForm']`), function(e){
    if(modal.find(`[data-form='postForm']`).length > 0){
      e.preventDefault();
      postForm = modal.find(`[data-form='postForm']`);
      
      actionType = modal.find(`[data-button='save']`).attr('data-value');
      var url = postForm.attr('action');
      
      if(url != undefined){
        var formData = new FormData(postForm.get(0));
        $.ajax({
          data: formData,
          url: url,
          type: "POST",
          processData: false,
          contentType: false,
          beforeSend: function() {
            modal.find(`[data-button='save']`).val('Sending..').attr('disabled', 'disabled');
            postForm.find('.invalid-feedback').text('');
          },
          dataType: 'json',
          success: function (response , textStatus, xhr) {
            
            if(xhr.status === 200){
              if(response.table){
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
                trigger_ajax_swal_msg(xhr);
                
                focus_row();
              }
              
              if(response.page_reload){
                window.location.reload(true);
              }
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
              
              postForm.find('input,textarea,select').each(function(i, v) {
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
                  $.each(errorKeys, function (key, name)
                  {
                    arrayKey = name.split(".");
                    if(arrayKey.length > 1){
                      newArrKey = errorKeys[key] = `${arrayKey[0]}[${arrayKey[1]}]`;
                      newErKey = `${arrayKey[0]}.${arrayKey[1]}`
                      tag = $(`[name="${newArrKey}"]`);
                      tag.siblings('.invalid-feedback').text(errors[newErKey]);
                    }
                  });
                }else{
                  tag.siblings('.invalid-feedback').text(errors[fkey]);
                }
              });
            }else{
              trigger_ajax_swal_msg(xhr);
            }
            
          }
        });
      }
    }
  });
  
  $(document).on('click','.show_data',function (e) {
    event.preventDefault();
    url = $(this).attr('href');
    data_label = $(this).attr('data-label');
    if(data_label == undefined) {data_label = ''}else{ data_label = data_label+ " Details"};

    if(data_label == ''){
      data_title = $(this).attr('data-title');
      if(data_title == undefined) data_title = '';
    }else{
      data_title = data_label;
    }

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
        modal.find('.modal-title').html(data_title);
        modal.modal('show');
        load_modal_particles();
      },
      
      error: function (error) {
        $.LoadingOverlay("hide");
        trigger_ajax_swal_msg(error);
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
        trigger_ajax_swal_msg(error);
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
        trigger_ajax_swal_msg(error);
      }
    });
  });
  
  $(document).on('click','.delete_data', function (e){
    e.preventDefault();
    url = $(this).attr('href');
    title = $(this).attr('title');
    
    if(title != undefined && title != ''){
    }else{
      var title = 'Delete';
    }
    
    Swal.fire({
      title:'Are you sure?',
      html:`You want to ${title} Record ?`,
      showCancelButton:true,
      showCloseButton:true,
      confirmButtonText:`Yes, ${title}`,
      cancelButtonText:'Cancel',
      confirmButtonColor:'#556ee6',
      cancelButtonColor:'#d33',
      width:400,
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          type: "DELETE",
          url: url,
          success: function (response , textStatus, xhr) {
            if(response.table){
              var table = eval(response.table);
              var tableid = response.table;
              var tr = $('#'+tableid+'').find(`[data-row-id='${response.id}']`);
              var row = table.row( tr );
              
              if ( row.child.isShown() ) {
                // This row is already open - remove it
                row.child( false ).remove();
              }
              
              tr.remove();
              trigger_ajax_swal_msg(xhr);
            }
            if(response.page_reload){
              window.location.reload(true);
            }
            
          },
          error: function (error) {
            $.LoadingOverlay("hide");
            trigger_ajax_swal_msg(error);
          }
        });
      }
    })
  });
  
  
  $(document).on('click','.row_action', function (e){
    e.preventDefault();
    url = $(this).attr('href');
    title = $(this).attr('title');
    
    if(title != undefined && title != ''){
    }else{
      var title = 'Action this';
    }
    
    Swal.fire({
      title:'Are you sure?',
      html:`You want to ${title} ?`,
      showCancelButton:true,
      showCloseButton:true,
      confirmButtonText:`Yes`,
      cancelButtonText:'Cancel',
      confirmButtonColor:'#556ee6',
      cancelButtonColor:'#d33',
      width:350,
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          type: "POST",
          url: url,
          success: function (response , textStatus, xhr) {
            if(xhr.status === 200){
              if(response.table!= ''){
                var table = eval(response.table);
                var tableid = response.table;
                scrollPos = $('#'+tableid+'').parents('div.dataTables_scrollBody').scrollTop();
                table.ajax.reload(function() {
                  $('#'+tableid+'').parents('div.dataTables_scrollBody').scrollTop(scrollPos);
                  $('#'+tableid+'').find(`[data-row-id='${response.id}']`).addClass('update_row');
                },false);
              }
              
            }
            trigger_ajax_swal_msg(xhr);
            focus_row();
          },
          error: function (error) {
            trigger_ajax_swal_msg(error);
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

function loadAjaxModal(response){
  if(response.modal == 'modal-lg'){
    modal = $('#ajax-modal-lg');
  }else if(response.modal == 'modal-sm'){
    modal = $('#ajax-modal-sm');
  }else if(response.modal == 'modal-xl'){
    modal = $('#ajax-modal-xl');
  }else{
    modal = $('#ajax-modal');
  }
  return modal;
}


function trigger_ajax_toastr_msg(data){
  message = data.responseJSON.message;
  error = data.responseJSON.error;
  response = data.responseJSON;
  if(message != ''){
    if(response.status == 'warning'){
      iziToast.warning({
        title: 'Warning*',
        message: message,
        position: 'center'
      });
    }
    
    if(response.status == 'info'){
      iziToast.info({
        title: 'Info*',
        message: message,
        position: 'center'
      });
    }
    
    if(response.status == 'error'){
      iziToast.error({
        title: 'Error*',
        message: message,
        position: 'center'
      });
    }
    
    if(response.status == 'success' && data.status===200){
      iziToast.success({
        title: 'Success*',
        message: message,
        position: 'center'
      });
    }
    
    if(data.status === 500){
      iziToast.error({
        title: 'Error*',
        message: message,
        position: 'center'
      });
    }
    
    if(data.status === 400){
      //HTTP_BAD_REQUEST
      iziToast.error({
        title: 'Error*',
        message: error,
        position: 'center'
      });
    }
    
    if(data.status === 406 && response.status === 'error'){
      //HTTP_NOT_ACCEPTABLE
      iziToast.error({
        title: 'Error*',
        message: error,
        position: 'topRight'
      });
    }else if(data.status === 406){
      iziToast.warning({
        title: 'Warning*',
        message: error,
        position: 'topRight'
      });
    }
  }
  
}

function trigger_ajax_swal_msg(data){
  message = data.responseJSON.message;
  error = data.responseJSON.error;
  response = data.responseJSON;
  if(message != '' || data.statusText !=''){
    if(response.status == 'warning'){
      icon = 'warning';
      title = 'Warning*';
    }
    
    if(response.status == 'info'){
      icon = 'info';
      title = 'Info*';
    }
    
    if(response.status == 'error'){
      icon = 'error';
      title = 'Oppps...';
    }
    
    if(response.status == 'success' && data.status===200){
      icon = 'success';
      title = 'Success*';
    }
    
    if(data.status === 500){
      icon = 'error';
      title = 'Oppps...';
      
    }
    
    if(data.status === 400){
      //HTTP_BAD_REQUEST
      icon = 'error';
      title = 'Oppps...';
      message = error;
    }
    
    if(data.status == 404){
      //HTTP_NOT_FOUND
      icon = 'error';
      title = 'Oppps...';
      message = data.statusText;
    }
    
    if(data.status === 406 && response.status === 'error'){
      //HTTP_NOT_ACCEPTABLE
      icon = 'error';
      title = 'Oppps...';
      message = error;
    }else if(data.status === 406){
      icon = 'warning';
      title = 'Warning*';
      message = error;
    }
    
    Swal.fire({
      icon: icon,
      title: title,
      timer: 3000,
      html: `<b>${message}</b>`,
      width: '25em'
    });
    
  }
  
}

function load_modal_particles(){
  load_selectize();
  load_select2();
  $('.date').datepicker( {
      format: 'yyyy-mm-dd',
  });
  $(".datepickr").flatpickr({
      dateFormat: "Y-m-d",
      allowInput: true,
      disableMobile: true
  });

}

function updateRowTimer() {
  if (typeof timerId !== 'undefined') {
      clearTimeout(timerId);
  }
  // Set the timer to remove the class after 5 seconds
  timerId = setTimeout(function() {
      $('.update_row').removeClass('update_row');
  }, 1000000);
  $('.update_row').focus();
}
updateRowTimer();