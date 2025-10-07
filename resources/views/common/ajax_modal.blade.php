<div class="modal fade modal-primary" id="{{$data['modalId'] ?? 'ajaxModal'}}" aria-hidden="true" aria-labelledby="{{$data['modalId'] ?? 'ajaxModal'}}Label" aria-hidden="true" role="dialog" tabindex="-1" @if(isset($data['static']) && $data['static']) data-backdrop="static" data-keyboard="false" @endif>
  <div class="modal-dialog modal-{{$data['modal'] ?? null}}">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>

        <h4 class="modal-title" id="{{$data['modalId'] ?? 'ajaxModal'}}Label">
          {{$data['modalTitle'] ?? 'Showing Details'}}
        </h4>
      </div>
      <div class="modal-body">
        {!!$data['html']!!}
      </div>
    </div>
  </div>
</div>