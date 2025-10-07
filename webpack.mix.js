const mix = require('laravel-mix');

//Backend
mix.styles([
    // Stylesheets
    'public/global/css/bootstrap.min.css',
    'public/global/css/bootstrap-extend.min.css',
    'public/assets/css/site.min.css',
    // Skin tools (demo site only)
    'public/global/css/skintools.min.css',
    // Plugins
    'public/global/vendor/animsition/animsition.min.css',
    'public/global/vendor/asscrollable/asScrollable.min.css',
    'public/global/vendor/switchery/switchery.min.css',
    'public/global/vendor/intro-js/introjs.min.css',
    'public/global/vendor/slidepanel/slidePanel.min.css',
    'public/global/vendor/flag-icon-css/flag-icon.min.css',

        //plugin forms
    'public/global/vendor/select2/select2.min.css',
    'public/global/vendor/bootstrap-tokenfield/bootstrap-tokenfield.min.css',
    'public/global/vendor/bootstrap-tagsinput/bootstrap-tagsinput.min.css',
    'public/global/vendor/bootstrap-select/bootstrap-select.min.css',
    'public/global/vendor/icheck/icheck.min.css',
    'public/global/vendor/asrange/asRange.min.css',
    'public/global/vendor/ionrangeslider/ionrangeslider.min.css',
    'public/global/vendor/asspinner/asSpinner.min.css',
    'public/global/vendor/clockpicker/clockpicker.min.css',
    // 'public/global/vendor/ascolorpicker/asColorPicker.min.css',
    'public/global/vendor/bootstrap-touchspin/bootstrap-touchspin.min.css',
    'public/global/vendor/jquery-labelauty/jquery-labelauty.min.css',
    'public/global/vendor/bootstrap-datepicker/bootstrap-datepicker.min.css',
    'public/global/vendor/bootstrap-maxlength/bootstrap-maxlength.min.css',
    'public/global/vendor/timepicker/jquery-timepicker.min.css',
    'public/global/vendor/jquery-strength/jquery-strength.min.css',
    'public/global/vendor/multi-select/multi-select.min.css',
    'public/global/vendor/typeahead-js/typeahead.min.css',
    'public/global/vendor/formvalidation/formValidation.min.css',

    // Datatable page
    'public/global/vendor/datatables.net-bs4/dataTables.bootstrap4.min.css',
    'public/global/vendor/datatables.net-fixedheader-bs4/dataTables.fixedheader.bootstrap4.min.css',
    'public/global/vendor/datatables.net-fixedcolumns-bs4/dataTables.fixedcolumns.bootstrap4.min.css',
    'public/global/vendor/datatables.net-rowgroup-bs4/dataTables.rowgroup.bootstrap4.min.css',
    'public/global/vendor/datatables.net-scroller-bs4/dataTables.scroller.bootstrap4.min.css',
    'public/global/vendor/datatables.net-select-bs4/dataTables.select.bootstrap4.min.css',
    'public/global/vendor/datatables.net-responsive-bs4/dataTables.responsive.bootstrap4.min.css',
    'public/global/vendor/datatables.net-buttons-bs4/dataTables.buttons.bootstrap4.min.css',

    //
    'public/assets/examples/css/forms/advanced.min.css',
    'public/assets/examples/css/forms/validation.min.css',
    // 'public/assets/examples/css/tables/basic.min.css',
    // 'public/assets/examples/css/tables/datatable.min.css',
    'public/assets/examples/css/advanced/toastr.min.css',

    // Fonts
    'public/global/fonts/web-icons/web-icons.min.css',
    'public/global/fonts/brand-icons/brand-icons.min.css',
    'public/global/fonts/font-awesome/font-awesome.min.css',
    // 'public/global/vendor/toastr/toastr.min.css',
    'public/global/vendor/iziToast/iziToast.min.css',
    'public/global/vendor/bootstrap-sweetalert/sweetalert.min.css',
], 'public/backend/css/app.css');


mix.scripts([
    // Core
    'public/global/vendor/babel-external-helpers/babel-external-helpers.js',
    'public/global/vendor/jquery/jquery.min.js',
    'public/global/vendor/popper-js/umd/popper.min.js',
    'public/global/vendor/bootstrap/bootstrap.min.js',
    'public/global/vendor/animsition/animsition.min.js',
    'public/global/vendor/mousewheel/jquery.mousewheel.js',
    'public/global/vendor/asscrollbar/jquery-asScrollbar.min.js',
    'public/global/vendor/asscrollable/jquery-asScrollable.min.js',
    'public/global/vendor/ashoverscroll/jquery-asHoverScroll.min.js',
    // Plugins
    'public/global/vendor/switchery/switchery.min.js',
    'public/global/vendor/intro-js/intro.min.js',
    'public/global/vendor/screenfull/screenfull.js',
    'public/global/vendor/slidepanel/jquery-slidePanel.min.js',
    //advanced form page
    'public/global/vendor/select2/select2.full.min.js',
    'public/global/vendor/bootstrap-tokenfield/bootstrap-tokenfield.min.js',
    'public/global/vendor/bootstrap-tagsinput/bootstrap-tagsinput.min.js',
    'public/global/vendor/bootstrap-select/bootstrap-select.min.js',
    'public/global/vendor/icheck/icheck.min.js',
    'public/global/vendor/asrange/jquery-asRange.min.js',
    'public/global/vendor/ionrangeslider/ion.rangeSlider.min.js',
    'public/global/vendor/asspinner/jquery-asSpinner.min.js',
    'public/global/vendor/clockpicker/bootstrap-clockpicker.min.js',
    'public/global/vendor/ascolor/jquery-asColor.min.js',
    // 'public/global/vendor/asgradient/jquery-asGradient.min.js',
    // 'public/global/vendor/ascolorpicker/jquery-asColorPicker.min.js',
    'public/global/vendor/bootstrap-maxlength/bootstrap-maxlength.min.js',
    'public/global/vendor/jquery-knob/jquery.knob.min.js',
    'public/global/vendor/bootstrap-touchspin/bootstrap-touchspin.min.js',
    'public/global/vendor/jquery-labelauty/jquery-labelauty.js',
    'public/global/vendor/bootstrap-datepicker/bootstrap-datepicker.min.js',
    'public/global/vendor/timepicker/jquery.timepicker.min.js',
    'public/global/vendor/datepair/datepair.min.js',
    'public/global/vendor/datepair/jquery.datepair.min.js',
    'public/global/vendor/jquery-strength/password_strength.js',
    'public/global/vendor/jquery-strength/jquery-strength.min.js',
    'public/global/vendor/multi-select/jquery.multi-select.js',
    'public/global/vendor/typeahead-js/bloodhound.min.js',
    'public/global/vendor/typeahead-js/typeahead.jquery.min.js',
    'public/global/vendor/jquery-placeholder/jquery.placeholder.js',

    //form validation
    'public/global/vendor/formvalidation/formValidation.min.js',
    'public/global/vendor/formvalidation/framework/bootstrap4.min.js',


    //datatable page
    'public/global/vendor/datatables.net/jquery.dataTables.js',
    'public/global/vendor/datatables.net-bs4/dataTables.bootstrap4.js',
    'public/global/vendor/datatables.net-fixedheader/dataTables.fixedHeader.min.js',
    'public/global/vendor/datatables.net-fixedcolumns/dataTables.fixedColumns.min.js',
    'public/global/vendor/datatables.net-rowgroup/dataTables.rowGroup.min.js',
    'public/global/vendor/datatables.net-scroller/dataTables.scroller.min.js',
    'public/global/vendor/datatables.net-responsive/dataTables.responsive.min.js',
    'public/global/vendor/datatables.net-responsive-bs4/responsive.bootstrap4.min.js',
    // 'public/global/vendor/datatables.net-buttons/dataTables.buttons.min.js',
    // 'public/global/vendor/datatables.net-buttons/buttons.html5.min.js',
    // 'public/global/vendor/datatables.net-buttons/buttons.flash.min.js',
    // 'public/global/vendor/datatables.net-buttons/buttons.print.min.js',
    // 'public/global/vendor/datatables.net-buttons/buttons.colVis.min.js',
    // 'public/global/vendor/datatables.net-buttons-bs4/buttons.bootstrap4.min.js',
    'public/global/vendor/asrange/jquery-asRange.min.js',
    'public/global/vendor/bootbox/bootbox.min.js',

    //izitoast
    'public/global/vendor/iziToast/iziToast.min.js',
    //sweet alert
    'public/global/vendor/bootstrap-sweetalert/sweetalert.min.js',

    //query check all
    'public/global/vendor/jquery-check-all/jquery-check-all.min.js',
    

    // Scripts
    'public/global/js/Component.min.js',
    'public/global/js/Plugin.min.js',
    'public/global/js/Base.min.js',
    'public/global/js/Config.min.js',
    'public/assets/js/Section/Menubar.min.js',
    'public/assets/js/Section/GridMenu.min.js',
    'public/assets/js/Section/Sidebar.min.js',
    'public/assets/js/Section/PageAside.min.js',
    'public/assets/js/Plugin/menu.min.js',

    // Config + Config.set('assets')
    'public/global/js/config/colors.min.js',
    'public/assets/js/config/tour.min.js',
    
    // Page
    'public/assets/js/Site.min.js',
    'public/global/js/Plugin/asscrollable.min.js',
    'public/global/js/Plugin/slidepanel.min.js',
    'public/global/js/Plugin/switchery.min.js',
    // 'public/global/js/Plugin/toastr.min.js',
    'public/global/js/Plugin/material.min.js',
    'public/global/js/Plugin/input-group-file.min.js',

    //advanced form user defiend
    'public/global/js/Plugin/select2.min.js',
    'public/global/js/Plugin/bootstrap-tokenfield.min.js',
    'public/global/js/Plugin/bootstrap-tagsinput.min.js',
    'public/global/js/Plugin/bootstrap-select.min.js',
    'public/global/js/Plugin/icheck.min.js',
    'public/global/js/Plugin/switchery.min.js',
    // 'public/global/js/Plugin/asrange.min.js',
    'public/global/js/Plugin/ionrangeslider.min.js',
    'public/global/js/Plugin/asspinner.min.js',
    'public/global/js/Plugin/clockpicker.min.js',
    'public/global/js/Plugin/ascolorpicker.min.js',
    'public/global/js/Plugin/bootstrap-maxlength.min.js',
    'public/global/js/Plugin/jquery-knob.min.js',
    'public/global/js/Plugin/bootstrap-touchspin.min.js',
    'public/global/js/Plugin/card.min.js',
    'public/global/js/Plugin/jquery-labelauty.min.js',
    'public/global/js/Plugin/bootstrap-datepicker.min.js',
    'public/global/js/Plugin/jt-timepicker.min.js',
    'public/global/js/Plugin/datepair.min.js',
    'public/global/js/Plugin/jquery-strength.min.js',
    'public/global/js/Plugin/multi-select.min.js',
    'public/global/js/Plugin/jquery-placeholder.min.js',

    //
    'public/global/js/Plugin/datatables.min.js',
    'public/assets/examples/js/tables/datatable.min.js',
    // 'public/assets/examples/js/forms/validation.min.js',


    // 'public/assets/examples/js/forms/advanced.min.js',

    // extended
    'public/assets/js/extendedScript.js',
], 'public/backend/js/app.js');