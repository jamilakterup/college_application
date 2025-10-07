/**
 * Notification message & Update row
 */
$('.msg').show().delay(5000).fadeOut();
$('.error_msg').show().delay(5000).fadeOut();



setTimeout( function() { 
    $('.update_row').removeClass('update_row'); 
}, 5000 );

/**
 * Confirm Before Delete action taken
 */
$('.delete').click(function(event) {
    event.preventDefault();
    var r = confirm("Are you sure of taking the action?");
    if (r == true)   {  
       $(this).submit();
    }
});

$(document).ready(function() {
    // When district selection changes
    $('#district_bn').on('change', function() {
        console.log('ok');
        var district = $(this).val();
        if (district) {
            // Clear current upazila options
            $('#upazila_bn').empty();
            
            // Add loading state
            $('#upazila_bn').append('<option>Loading...</option>');
            
            // Make AJAX request
            $.ajax({
                url: App.baseUrl+'/api/get_upazilas/' + district,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Clear loading state
                    $('#upazila_bn').empty();
                    
                    // Add placeholder option
                    $('#upazila_bn').append('<option value="">--Select Upazila--</option>');
                    
                    // Add options from response
                    $.each(response, function(key, value) {
                        $('#upazila_bn').append('<option value="' + key + '">' + value + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error loading upazilas:', error);
                    $('#upazila_bn').empty();
                    $('#upazila_bn').append('<option value="">--Error loading upazilas--</option>');
                }
            });
        } else {
            // If no district selected, clear upazila dropdown
            $('#upazila_bn').empty();
            $('#upazila_bn').append('<option value="">--Select Upazila--</option>');
        }
    });
});

/**
 * Configure DatePicker
 */
$('.datepicker').datepicker( {
    format: 'yyyy-mm-dd',
});

function checkAll(ele) {

    var checkboxes = document.getElementsByTagName('input');
    var selectags = document.getElementsByTagName('select');  

    if (ele.checked) {

        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].type == 'checkbox') {
                checkboxes[i].checked = true;
            }

            if (checkboxes[i].type == 'text') {
                checkboxes[i].readOnly = false;
            }     
        }

        for (var j = 0; j < selectags.length; j++) { 
            selectags[j].disabled = false;
        }  

    } else {

        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].type == 'checkbox') {
                checkboxes[i].checked = false;
            }

            if (checkboxes[i].type == 'text') {
                checkboxes[i].readOnly = true;
            }  
        }

        for (var j = 0; j < selectags.length; j++) { 
            selectags[j].disabled = true;
        }  

    }

}

 

$("#submit_tot_list").click(function(e){
                e.preventDefault();
                var groups=$("#groups").val();
                var session=$("#session").val();
                //alert (session);
                if(groups=='' || session==''){
                    $("#tot_list_info").html('Please Select All Fields');
                }
                else{
                    $("#tot_list_info").html('<center style="margin-top:50px">Please wait...<img src="../../img/loader.gif" alt=""/></center>');
                        $.ajax({
                                type:'POST',
                                     url:"totlistgenerate",
                                     data:{groups:groups,session:session},
                                     headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                        success:function(response){ 
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

    var quantity = document.getElementById('quantity');
    var unitPrice = document.getElementById('unit-price');
    var totalCost = document.getElementById('total-cost');

    var quantityValue = quantity.value;
    var unitPriceValue = unitPrice.value;
    var outcomes = quantityValue * unitPriceValue;

    if(quantityValue != '' && unitPriceValue != '') {

        if(isNaN(outcomes)) {
            totalCost.value = '';        
        }
        else {
             totalCost.value = parseFloat(Math.round(outcomes * 100) / 100).toFixed(2);
        }

    }
    else {
        totalCost.value = '';          
    }

}

/**
 * Checked then remove readonly
 */
$('.action-type-a').on('click', function () {
    if ($(this).prop('checked')) {
        $(this).parent().nextAll().find('input').prop('readOnly', false);
    } else {
        $(this).parent().nextAll().find('input').prop('readOnly', true);
        $(this).parent().nextAll().find('input').prop('value', '');
    }
});

/**
 * Module Checked then Sub-Module all checked
 */
$('.module').on('click', function () {
    if ($(this).prop('checked')) {
        $(this).parent().nextAll().find('input').prop('checked', true);
        $(this).parent().nextAll().find('.opacity-cover').css('display', 'none');
    } else {
        $(this).parent().nextAll().find('input').prop('checked', false);
        $(this).parent().nextAll().find('.opacity-cover').css('display', 'block');        
    }
});

/**
 * Issue Date Change Return Date Autometically
 */
$('#issuedate').on('change', function () {

    var addDays = parseInt(document.getElementById('issued_days').value);

    var issuedate = document.getElementById('issuedate').value;

    var issuedateYear = issuedate.substring(0,4);
    var issuedateMonth = issuedate.substring(5,7);
    var issuedateDay = issuedate.substring(8,10);

    var issueFormat = issuedateMonth + '/' + issuedateDay + '/' + issuedateYear;

    var date = new Date(issueFormat);

    var returnDate = new Date(date);

    returnDate.setDate(returnDate.getDate() + addDays);
    
    var dd = returnDate.getDate();
    var mm = returnDate.getMonth() + 1;
    var yyyy = returnDate.getFullYear();

    if(dd<10){
        dd='0'+dd;
    } 

    if(mm<10){
        mm='0'+mm;
    }     

    if(isNaN(dd) || isNaN(mm) || isNaN(yyyy)) {
        var returnDateFormatted = '';
    }
    else {
        var returnDateFormatted = yyyy + '-' + mm + '-' + dd;
    }
    
    document.getElementById('returndate').value = returnDateFormatted;
    
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
$(document).ready(function(){

    $('#minav').click(function(){

        $('#logo').removeClass('col-xs-4 col-sm-3 col-md-3 col-lg-2');    	
        $('#logo').addClass('col-xs-2 col-sm-2 col-md-1 col-lg-1');

        $('#side-nav').removeClass('col-xs-4 col-sm-3 col-md-3 col-lg-2');      
        $('#side-nav').addClass('col-xs-2 col-sm-2 col-md-1 col-lg-1');  

        $('#left-footer').removeClass('col-xs-4 col-sm-3 col-md-3 col-lg-2');       
        $('#left-footer').addClass('col-xs-2 col-sm-2 col-md-1 col-lg-1');              

        $('#top-nav').removeClass('col-xs-8 col-sm-9 col-md-9 col-lg-10');    	
        $('#top-nav').addClass('col-xs-10 col-sm-10 col-md-11 col-lg-11');

        $('#content').removeClass('col-xs-8 col-sm-9 col-md-9 col-lg-10');   	
        $('#content').addClass('col-xs-10 col-sm-10 col-md-11 col-lg-11');  

        $('#right-footer').removeClass('col-xs-8 col-sm-9 col-md-9 col-lg-10');   	
        $('#right-footer').addClass('col-xs-10 col-sm-10 col-md-11 col-lg-11');  

        $('#side-nav ul.nav li').addClass('custom');
        $('#side-nav ul.nav li a').addClass('custom');
        $('#side-nav ul.nav li a i').addClass('custom');        

        $('#l-link').text('ECM');
        $(this).hide(); 
        $('#maxnav').show();

    });

    $('#maxnav').click(function(){

        $('#logo').removeClass('col-xs-2 col-sm-2 col-md-1 col-lg-1');
        $('#logo').addClass('col-xs-4 col-sm-3 col-md-3 col-lg-2');

        $('#side-nav').removeClass('col-xs-2 col-sm-2 col-md-1 col-lg-1'); 
        $('#side-nav').addClass('col-xs-4 col-sm-3 col-md-3 col-lg-2');

        $('#left-footer').removeClass('col-xs-2 col-sm-2 col-md-1 col-lg-1');  
        $('#left-footer').addClass('col-xs-4 col-sm-3 col-md-3 col-lg-2');       

        $('#top-nav').removeClass('col-xs-10 col-sm-10 col-md-11 col-lg-11');  
        $('#top-nav').addClass('col-xs-8 col-sm-9 col-md-9 col-lg-10');  

        $('#content').removeClass('col-xs-10 col-sm-10 col-md-11 col-lg-11');  
        $('#content').addClass('col-xs-8 col-sm-9 col-md-9 col-lg-10');     

        $('#right-footer').removeClass('col-xs-10 col-sm-10 col-md-11 col-lg-11');  
        $('#right-footer').addClass('col-xs-8 col-sm-9 col-md-9 col-lg-10');

        $('#side-nav ul.nav li').removeClass('custom');
        $('#side-nav ul.nav li a').removeClass('custom');
        $('#side-nav ul.nav li a i').removeClass('custom');        

        $('#l-link').text('EasyCollegeMate');
        $(this).hide(); 
        $('#minav').show();

    });

});

/**
 * To adjust side-nav height
 */
var mainContent = document.getElementById('main-content');
var mcHeight = mainContent.clientHeight;
var sideNav = document.getElementById('side-nav');
sideNav.style.minHeight = mcHeight + 'px';

var rightFooter = document.getElementById('right-footer');
var rfHeight = rightFooter.clientHeight;
var leftFooter = document.getElementById('left-footer');
leftFooter.style.minHeight = rfHeight + 'px';
/**
 * certificate create 
 */

	$("#character").click( function(){
		$("#characterDIV").show();
		$("#testimonialDIV").hide();
		$("#transferDIV").hide();
		$("#appearedDIV").hide()
		$("#studentshipDIV").hide();
	});


	$("#testimonial").click( function(){

		$("#testimonialDIV").show();
		$("#characterDIV").hide();
		$("#transferDIV").hide();
		$("#appearedDIV").hide();
		$("#studentshipDIV").hide();
	});


	$("#transfer").click( function(){

		$("#transferDIV").show();
		$("#characterDIV").hide();
		$("#testimonialDIV").hide();
		$("#appearedDIV").hide();
		$("#studentshipDIV").hide();
	});


	$("#appeared").click( function(){

		$("#appearedDIV").show();
		$("#characterDIV").hide();
		$("#transferDIV").hide();
		$("#testimonialDIV").hide();
		$("#studentshipDIV").hide();
	});


	$("#studentship").click( function(){

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
     // });.
