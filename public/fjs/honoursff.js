$("document").ready(function () {
    $("#submit_payment_type").click(function () {
        var student_id = $("#student_id").val();
        var payType = $("#payType").val();

        if (payType == "") {
            alert("Please select a type");
            return false;
        } else {
            $.ajax({
                type: "POST",
                url: "checktype",
                data: { student_id: student_id, payType: payType },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (response) {
                    var url = "dbbl_view";
                    window.location = url;
                },
            });
        }
    });

    $("#confirm_slip").click(function () {
        $("#confirm_slidownload_linkp_file").html("Please Wait.Processing..");
        $.ajax({
            type: "POST",
            url: "confirmslip",
            data: {},
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            beforeSend: function () {
                $.LoadingOverlay("show");
            },
            success: function (response) {
                $.LoadingOverlay("hide");
                $("#download_link1").html(response);
                $("#confirm_slip_file").modal("show");
            },
        });
    });

    $("#con_mes").hide();
    /*Student Payment Approve action*/
    $("#submit_payment").click(function () {
        $("#submit_payment").attr("value", "Please wait...");
        $("#con_mes").html("অনুগ্রহ করে কিছুক্ষণ অপেক্ষা করুন...");
        var registration_id = $("#studentID").val();
        var trx_id = $("#trxid").val();
        var pay_am_floor = $("#pay_am_floor").val();
        var ans = window.confirm("Are you sure?");
        if (ans) {
            $.ajax({
                type: "POST",
                url: "dbbl_approve",
                data: {
                    registration_id: registration_id,
                    trx_id: trx_id,
                    pay_am_floor: pay_am_floor,
                },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (response) {
                    //alert(response)	;
                    $("#con_mes").show();
                    $("#con_mes").html("");
                    $("#con_mes").addClass("alert-danger");
                    $("#submit_payment").attr("value", "Submit");
                    $("#con_mes").html("<h4>" + response + "</h4>");
                },
            });
        } /*End of if(ans)*/
    });

    //$('#student_id').number();
    $("#admission_step").click(function () {
        $("#admission_step_modal").modal("show");

        var roll = $("#student_id").val();
        var examyear = $("#sel1").val();
        var current_level = $("#current_level").val();
        if (roll == "") {
            $("#next_step_error").html(
                '<span style="color:red;">Enter Student roll '
            );
            $("#information").hide();
        }
        // if(examyear =='')
        // {
        // 	 $('#next_step_error').html('<span style="color:red;">Select Year ');
        //      $('#information').hide();
        // }

        if (current_level == "") {
            $("#next_step_error").html(
                '<span style="color:red;">Select Formfillup Level '
            );
            $("#information").hide();
        } else
            $.ajax({
                type: "POST",
                url: "formfillup/check",
                data: {
                    roll: roll,
                    examyear: examyear,
                    current_level: current_level,
                },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (response) {
                    //alert(response);
                    $("#admission_step_modal").modal("hide");
                    var status = response;
                    if (status == 0) {
                        $("#next_step_error").html(
                            '<span style="color:red;">Form Fillup Not Open'
                        );
                        $("#information").hide();
                    }
                    if (status == 5) {
                        $("#next_step_error").html(
                            '<span style="color:red;">Registration ID is Wrong.'
                        );
                        $("#information").hide();
                    }
                    if (status == 3) {
                        // $('#next_step_error').html('<span style="color:red;">Your Payment not Completed.');
                        // $('#information').hide();
                        var url = "formfillup/payment_view";
                        window.location = url;
                    }

                    if (status == 2) {
                        var url = "formfillup/next_step";
                        window.location = url;
                    }
                    if (status == 1) {
                        var url = "formfillup/view";
                        window.location = url;
                    }
                },
            });
    });
});
