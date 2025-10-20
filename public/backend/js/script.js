if (typeof datepicker == "function") {
    $(".date").datepicker({
        format: "yyyy-mm-dd",
    });
}

$(".datepickr").flatpickr({
    dateFormat: "Y-m-d",
    allowInput: true,
    disableMobile: true,
});

function viewImage(input, imgId) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = function (e) {
            $("#" + imgId).attr("src", e.target.result);
            $(`#${imgId}_area`).show();
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function PrintElem(elem) {
    Popup($(elem).html());
}

function Popup(data) {
    var mywindow = window.open("", "print_details", "height=562,width=795");
    mywindow.document.write("<html><head><title>print_details</title>");
    mywindow.document.write("</head><body >");
    mywindow.document.write(data);
    mywindow.document.write("</body></html>");
    mywindow.document.close();
    mywindow.print();
    return true;
}

$(document).ready(function () {
    $("tr.update_row").focus();
});

setTimeout(function () {
    $(".update_row").removeClass("update_row");
}, 5000);

// if ($('.opened').length) {
//     setTimeout(function() {
//         $(".opened")[0].click();
//     },1000);
// }

$(document).on("click", ".delete", function (event) {
    event.preventDefault();
    var form = $(this).closest("form");
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
});

function preview_image_url(input) {
    type = input.dataset.type;
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $("#" + type + "_image_pre_area").show();
            $("#" + type + "_image_pre").attr("src", e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

$(".image_data").change(function () {
    preview_image_url(this);
});

$(document).ready(function () {
    if (typeof dataTable == "function") {
        var defDTable = $(".defDTable").dataTable({
            scrollX: true,
            scrollX: "100%",
            // "autoWidth": false,
            scrollY: "60vh",
            searching: true,
            lengthChange: false,
            bSort: false,
            responsive: true,
            paging: false,
            info: false,
        });
    }

    $(".dt-search").keyup(function () {
        console.log("yes");
        defDTable.search($(this).val()).draw();
    });
});

$(document).on("click", ".action-type-a", function () {
    if ($(this).prop("checked")) {
        $(this).parent().nextAll().find("input").prop("readOnly", false);
    } else {
        $(this).parent().nextAll().find("input").prop("readOnly", true);
        $(this).parent().nextAll().find("input").prop("value", "");
    }
});

// $(".select2").select2();

load_select2();

function load_select2() {
    select2 = $(".select2");
    $.each(select2, function (key, select) {
        var attrName = $(this).attr("name");
        var placeholder = $(this).data("placeholder");
        $("select[name='" + attrName + "']").select2({
            placeholder: placeholder,
            allowClear: true,
        });
    });
}

load_selectize();

function load_selectize() {
    selectize = $(".selectize");
    $.each(selectize, function (key, select) {
        var attrName = $(this).attr("name");
        var placeholder = $(this).data("placeholder");
        $("select[name='" + attrName + "']").selectize({
            plugins: ["remove_button"],
            // delimiter: ',',
            // persist: false,
        });
    });
}

function focus_row() {
    return (timeout = setTimeout(function () {
        $(".update_row").removeClass("update_row");
    }, 40000));
}

function checkAll(ele) {
    var checkboxes = document.getElementsByTagName("input");
    var selectags = document.getElementsByTagName("select");

    if (ele.checked) {
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].type == "checkbox") {
                checkboxes[i].checked = true;
            }

            if (checkboxes[i].type == "text") {
                checkboxes[i].readOnly = false;
            }
        }

        for (var j = 0; j < selectags.length; j++) {
            selectags[j].disabled = false;
        }
    } else {
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].type == "checkbox") {
                checkboxes[i].checked = false;
            }

            if (checkboxes[i].type == "text") {
                checkboxes[i].readOnly = true;
            }
        }

        for (var j = 0; j < selectags.length; j++) {
            selectags[j].disabled = true;
        }
    }
}

$("#submit_tot_list").click(function (e) {
    e.preventDefault();
    var groups = $("#groups").val();
    var session = $("#session").val();
    //alert (session);
    if (groups == "" || session == "") {
        $("#tot_list_info").html("Please Select All Fields");
    } else {
        $("#tot_list_info").html(
            '<center style="margin-top:50px">Please wait...<img src="../../img/loader.gif" alt=""/></center>'
        );
        $.ajax({
            type: "POST",
            url: "totlistgenerate",
            data: { groups: groups, session: session },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                //alert(response)   ;
                $("#tot_list_info").html(response);
            },
        });
    }
});

/**
 * Calculate Total Cost
 */
function calTotalCost() {
    var quantity = document.getElementById("quantity");
    var unitPrice = document.getElementById("unit-price");
    var totalCost = document.getElementById("total-cost");

    var quantityValue = quantity.value;
    var unitPriceValue = unitPrice.value;
    var outcomes = quantityValue * unitPriceValue;

    if (quantityValue != "" && unitPriceValue != "") {
        if (isNaN(outcomes)) {
            totalCost.value = "";
        } else {
            totalCost.value = parseFloat(
                Math.round(outcomes * 100) / 100
            ).toFixed(2);
        }
    } else {
        totalCost.value = "";
    }
}

/**
 * Checked then remove readonly
 */
$(".action-type-a").on("click", function () {
    if ($(this).prop("checked")) {
        $(this).parent().nextAll().find("input").prop("readOnly", false);
    } else {
        $(this).parent().nextAll().find("input").prop("readOnly", true);
        $(this).parent().nextAll().find("input").prop("value", "");
    }
});

/**
 * Module Checked then Sub-Module all checked
 */
$(".module").on("click", function () {
    if ($(this).prop("checked")) {
        $(this).parent().nextAll().find("input").prop("checked", true);
        $(this)
            .parent()
            .nextAll()
            .find(".opacity-cover")
            .css("display", "none");
    } else {
        $(this).parent().nextAll().find("input").prop("checked", false);
        $(this)
            .parent()
            .nextAll()
            .find(".opacity-cover")
            .css("display", "block");
    }
});

/**
 * Issue Date Change Return Date Autometically
 */
$("#issuedate").on("change", function () {
    var addDays = parseInt(document.getElementById("issued_days").value);

    var issuedate = document.getElementById("issuedate").value;

    var issuedateYear = issuedate.substring(0, 4);
    var issuedateMonth = issuedate.substring(5, 7);
    var issuedateDay = issuedate.substring(8, 10);

    var issueFormat = issuedateMonth + "/" + issuedateDay + "/" + issuedateYear;

    var date = new Date(issueFormat);

    var returnDate = new Date(date);

    returnDate.setDate(returnDate.getDate() + addDays);

    var dd = returnDate.getDate();
    var mm = returnDate.getMonth() + 1;
    var yyyy = returnDate.getFullYear();

    if (dd < 10) {
        dd = "0" + dd;
    }

    if (mm < 10) {
        mm = "0" + mm;
    }

    if (isNaN(dd) || isNaN(mm) || isNaN(yyyy)) {
        var returnDateFormatted = "";
    } else {
        var returnDateFormatted = yyyy + "-" + mm + "-" + dd;
    }

    document.getElementById("returndate").value = returnDateFormatted;
});

/**
 * printContent() Function
 */
function printContent(el) {
    var restorepage = document.body.innerHTML;
    var printcontent = document.getElementById(el).innerHTML;
    document.body.innerHTML = printcontent;
    window.print();
    document.body.innerHTML = restorepage;
}

/**
 * Toggle compress & uncompress menu
 */
$(document).ready(function () {
    $("#minav").click(function () {
        $("#logo").removeClass("col-xs-4 col-sm-3 col-md-3 col-lg-2");
        $("#logo").addClass("col-xs-2 col-sm-2 col-md-1 col-lg-1");

        $("#side-nav").removeClass("col-xs-4 col-sm-3 col-md-3 col-lg-2");
        $("#side-nav").addClass("col-xs-2 col-sm-2 col-md-1 col-lg-1");

        $("#left-footer").removeClass("col-xs-4 col-sm-3 col-md-3 col-lg-2");
        $("#left-footer").addClass("col-xs-2 col-sm-2 col-md-1 col-lg-1");

        $("#top-nav").removeClass("col-xs-8 col-sm-9 col-md-9 col-lg-10");
        $("#top-nav").addClass("col-xs-10 col-sm-10 col-md-11 col-lg-11");

        $("#content").removeClass("col-xs-8 col-sm-9 col-md-9 col-lg-10");
        $("#content").addClass("col-xs-10 col-sm-10 col-md-11 col-lg-11");

        $("#right-footer").removeClass("col-xs-8 col-sm-9 col-md-9 col-lg-10");
        $("#right-footer").addClass("col-xs-10 col-sm-10 col-md-11 col-lg-11");

        $("#side-nav ul.nav li").addClass("custom");
        $("#side-nav ul.nav li a").addClass("custom");
        $("#side-nav ul.nav li a i").addClass("custom");

        $("#l-link").text("ECM");
        $(this).hide();
        $("#maxnav").show();
    });

    $("#maxnav").click(function () {
        $("#logo").removeClass("col-xs-2 col-sm-2 col-md-1 col-lg-1");
        $("#logo").addClass("col-xs-4 col-sm-3 col-md-3 col-lg-2");

        $("#side-nav").removeClass("col-xs-2 col-sm-2 col-md-1 col-lg-1");
        $("#side-nav").addClass("col-xs-4 col-sm-3 col-md-3 col-lg-2");

        $("#left-footer").removeClass("col-xs-2 col-sm-2 col-md-1 col-lg-1");
        $("#left-footer").addClass("col-xs-4 col-sm-3 col-md-3 col-lg-2");

        $("#top-nav").removeClass("col-xs-10 col-sm-10 col-md-11 col-lg-11");
        $("#top-nav").addClass("col-xs-8 col-sm-9 col-md-9 col-lg-10");

        $("#content").removeClass("col-xs-10 col-sm-10 col-md-11 col-lg-11");
        $("#content").addClass("col-xs-8 col-sm-9 col-md-9 col-lg-10");

        $("#right-footer").removeClass(
            "col-xs-10 col-sm-10 col-md-11 col-lg-11"
        );
        $("#right-footer").addClass("col-xs-8 col-sm-9 col-md-9 col-lg-10");

        $("#side-nav ul.nav li").removeClass("custom");
        $("#side-nav ul.nav li a").removeClass("custom");
        $("#side-nav ul.nav li a i").removeClass("custom");

        $("#l-link").text("EasyCollegeMate");
        $(this).hide();
        $("#minav").show();
    });
});

/**
 * To adjust side-nav height
 */
var mainContent = document.getElementById("main-content");
// var mcHeight = mainContent.clientHeight;
var sideNav = document.getElementById("side-nav");
// sideNav.style.minHeight = mcHeight + 'px';

var rightFooter = document.getElementById("right-footer");
// var rfHeight = rightFooter.clientHeight;
var leftFooter = document.getElementById("left-footer");
// leftFooter.style.minHeight = rfHeight + 'px';
/**
 * certificate create
 */

$("#character").click(function () {
    $("#characterDIV").show();
    $("#testimonialDIV").hide();
    $("#transferDIV").hide();
    $("#appearedDIV").hide();
    $("#studentshipDIV").hide();
});

$("#testimonial").click(function () {
    $("#testimonialDIV").show();
    $("#characterDIV").hide();
    $("#transferDIV").hide();
    $("#appearedDIV").hide();
    $("#studentshipDIV").hide();
});

$("#transfer").click(function () {
    $("#transferDIV").show();
    $("#characterDIV").hide();
    $("#testimonialDIV").hide();
    $("#appearedDIV").hide();
    $("#studentshipDIV").hide();
});

$("#appeared").click(function () {
    $("#appearedDIV").show();
    $("#characterDIV").hide();
    $("#transferDIV").hide();
    $("#testimonialDIV").hide();
    $("#studentshipDIV").hide();
});

$("#studentship").click(function () {
    $("#studentshipDIV").show();
    $("#characterDIV").hide();
    $("#transferDIV").hide();
    $("#appearedDIV").hide();
    $("#testimonialDIV").hide();
});

//    $("#char_submit").click( function(){
//    var text = CKEDITOR.instances.char_text.getData();
//    $.ajax({
//    type:'POST',
//    url:'hscCharacterAction',
//    data:{text:text},
//    success:function(response){
//        alert(response);
//    }

//    });
// });

if (typeof datepicker == "function") {
    $(".input-daterange input").datepicker({
        format: "yyyy-mm-dd",
    });
}

$(document).on("hidden.bs.modal", ".modal", function () {
    $(".modal:visible").length && $(document.body).addClass("modal-open");
});

$(document).on("change blur", ".get_options", function (e) {
    id = $(this).val();
    options_for = $(this).data("options-for");
    refresh = $(this).data("selected");
    var $select = $(options_for).selectize();
    if ($select.length < 1) {
        var $select = $(`[data-options='${options_for}']`).selectize();
    }
    if ($select[0] != undefined && id != "") {
        url = $(this).data("options-url") + `/${id}`;
        getSelectizeOptions(url, $select, refresh);
    } else if ($select[0] != undefined && id == "") {
        $select[0].selectize.clearOptions();
    }
});

async function getSelectizeOptions(url, $select, refresh = null) {
    var selectize = $select[0].selectize;
    $.ajax({
        type: "POST",
        url: url,
        data: {},
        success: function (result) {
            var my_data = result.data;
            if (my_data != undefined) {
                if (my_data) {
                    selectize.renderCache = {};
                    selectize.clear();
                    selectize.clearOptions();
                    for (var i = 0; i < my_data.length; i++) {
                        var item = my_data[i];
                        var data = {
                            value: item.id,
                            text: item.name,
                        };
                        selectize.addOption(data);
                    }
                    selectize.refreshOptions();
                }
            }
        },
        error: function (error) {
            trigger_ajax_swal_msg(error);
        },
    });
}

$(document).on("click", "#same_as_present", function (e) {
    var same_address = 0;
    if ($(this).is(":checked")) {
        present_ps = $("#present_ps").val();
        $("#permanent_village").attr("readonly", "readonly");
        $("#permanent_village").val($("#present_village").val()).change();
        $("#permanent_po").attr("readonly", "readonly");
        $("#permanent_po").val($("#present_po").val()).change();
        $("#permanent_dist").attr("disabled", "disabled");
        $("#permanent_dist").val($("#present_dist").val()).change();
        $("#permanent_ps").val(present_ps).change();
        $("#permanent_ps").attr("disabled", "disabled");
        same_address = 1;
    } else {
        $("#permanent_village").removeAttr("readonly");
        $("#permanent_po").removeAttr("readonly");
        $("#permanent_dist").removeAttr("disabled");
        $("#permanent_ps").removeAttr("disabled");
        same_address = 0;
    }
});

/** add active class and stay opened when selected */
$(document).ready(function () {
    const active_menu = $(".site-menu-sub li.active");

    if (active_menu.length > 0) {
        menuDropdown = active_menu.get(0).closest(".has-sub");
        menuDropdown.classList.add("active");
        menuDropdown.classList.add("open");

        let childSub = $(menuDropdown).find(".site-menu-sub");
        let siteActiveMenu = childSub.closest(".site-menu-item active");
        if ($(siteActiveMenu).length > 0) {
            $(siteActiveMenu).classList.add("is-shown");
        }
    }
});
