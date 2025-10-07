
@push('scripts')
    @if(isset($datatable) && !is_null($datatable))
        @php
            $urlValue= '';
            if(isset($url))
            {
                $urlValue =  $url;
            }
            $setColData = array();
            if(isset($table_columns) && count($table_columns) > 0)
            {
                $setColData = json_encode($table_columns);
            }

            if(isset($datatable)){
                $datatable = $datatable;
            }else{
                $datatable = 'datatable';
            }

        @endphp


        <script>
            var {{$datatable}};
            
            $(document).ready(function(){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                
                {{$datatable}} = $('#{{$datatable}}').DataTable({
                    // start checking ajax search
                    @if(!empty($urlValue))
                        ajax:{
                            url: "{{ $urlValue }}",
                            dataType: "json",
                            type: "POST",
                            @if(isset($data) && count($data) > 0)
                                data: function(d) {
                                    @foreach($data as $key => $value)
                                        d['{{$key}}'] = $($( '#{!! $value !!}' ).get(0)).val();
                                    @endforeach
                                },
                            @endif
                        },
                        @if(isset($table_columns) && count($table_columns) > 0)
                            columns: {!!$setColData!!},
                        @endif
                        serverSide: true,
                        processing: true,
                    @endif
                    // end checking ajax search
                    autoWidth : true,
                    cache: true,
                        @if(isset($data) && count($data) > 0)
                            searching : false,
                        @else
                            searching : true,
                        @endisset
                    deferRender: true,
                    scrollY: '50vh',
                    scrollCollapse: true,
                    aaSorting: [],
                    responsive: true,
                    lengthMenu: [[25, 50, 100, 200, -1], [25, 50, 100, 200, "All"]],
                    iDisplayLength: 50,
                    dom: 'lBfrtip',
                    buttons: [
                        {
                            extend: 'copyHtml5',
                            exportOptions: {
                                columns: [ 0, ':visible' ]
                            }
                            },
                            {
                            extend:    'csvHtml5',
                            exportOptions: {
                                    columns: ':visible'
                                }
                            },
                            {
                                extend: 'pdfHtml5',
                                exportOptions: {
                                columns: ':visible'
                                },
                                orientation: 'landscape',
                                pageSize: 'LEGAL',
                                customize: function(doc) {
                                doc.content[1].margin = [ 100, 0, 100, 0 ] //left, top, right, bottom
                                }
                            },
                            'colvis'
                    ],
                    initComplete: function () {
                        var btns = $('.dt-button');
                        btns.addClass('btn btn-outline-info');
                        btns.removeClass('dt-button');

                        var rowIndex = localStorage.getItem('{{$datatable}}-rowIndex');
                        if (rowIndex !== null) {
                            var page = Math.floor(rowIndex / {{$datatable}}.page.len());
                            var table = {{$datatable}};
                            table.page(page).draw(false).on('draw', function () {
                                var row = {{$datatable}}.row(rowIndex).node();
                                if (row !== null) {
                                    var rowTop = $(row).offset().top - $('#{{$datatable}}').offset().top;
                                    $('#{{$datatable}}_wrapper .dataTables_scrollBody').scrollTop(rowTop);

                                    @if(session()->get('isUpdated'))
                                        $(row).addClass('update_row');
                                    @endif
                                    
                                    }
                                });

                            localStorage.removeItem('{{$datatable}}-rowIndex');
                        }

                        @if(session('drawTo'))
                            let api = {{$datatable}}.ajax.reload(function() {
                                @if(session('drawTo') == 'last')
                                    {{$datatable}}.page('last').draw(false);
                                    var $row = {{$datatable}}.row(':last');
                                @else
                                    {{$datatable}}.page('first').draw(false);
                                    var $row = {{$datatable}}.row(':first');
                                @endif

                                {{$datatable}}.one('draw', function() {
                                    {{$datatable}}.row($row).scrollTo(false);
                                });
                                $row.addClass('update_row');
                            });
                        @endif
                    },
                    fnDrawCallback : function() {
                        resizeTable();
                    },
                    @if(isset($properties) && count($properties) > 0)
                        @foreach($properties as $property => $value)
                            {!!$property!!} : {!!$value === true ? 'true': ($value === false ? 'false' : "'$value'") !!},
                        @endforeach
                    @endif
                });
            });

            $(window).on('resize', function() {
                resizeTable();
            });

            function resizeTable(){
                var dtableDbounceTimer;
                if (typeof {{$datatable}} !== 'undefined' && {{$datatable}} instanceof $.fn.dataTable.Api) {
                    clearTimeout(dtableDbounceTimer);
                    dtableDbounceTimer = setTimeout(function() {
                        {{$datatable}}.columns.adjust().responsive.recalc();
                    }, 250);
                }
            }

            @if(isset($search_btn) && $search_btn == true)
                $(document).on('click','#search_btn', function(){
                    {{$datatable}}.draw();
                });
            @else
                $(document).on('change','.filter', function(){
                    {{$datatable}}.draw();
                });
                
                $(document).on('keyup','.filter', function(){
                    {{$datatable}}.draw();
                });

                $(document).on('blur','select.filter', function(){
                    {{$datatable}}.draw();
                });
            @endif

            function dtRowLocation(button){
                $button = $(button);
                var row = $button.closest('tr');
                var rowIndex = {{$datatable}}.row(row).index();
                localStorage.setItem('{{$datatable}}-rowIndex', rowIndex);

                // Redirect to the perform page
                var url = $button.attr('data-href');
                window.location.href = url;
            }

            $(document).on('click', '.buttons-colvis', function(e){
                var btns = $('.buttons-columnVisibility');
                btns.addClass('btn btn-outline-primary d-block');
                $('.dt-button-collection').find('div[role="menu"]').addClass('d-grid gap-2');
                btns.removeClass('dt-button');
                resizeTable();
            })
        </script>

    @endisset
@endpush