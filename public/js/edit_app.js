$("document").ready(function(){


	    $(".Edit").click(function(){
        var id=$(this).attr("id");
		
		$.ajax({	
        type:'POST',
        url: 'http://easycollegemate.com/ecmrgwc/public/students/hsc/infoedit',
        data:{id: id},
        success:function(response){
	    $("#edit_hscnew_form").dialog('option','title','Student Edit-'+id).dialog('open'); 
		$("#edit_hscnew_form").html(response);		     
		
        }
        });

	});

	    $(".EditHons").click(function(){
        var id=$(this).attr("id");
		//alert(id);

		$.ajax({	
        type:'POST',
        url:'http://easycollegemate.com/ecmrgwc/public/students/honours/infoedit',
        data:{id: id},
        success:function(response){
	    $("#edit_honsnew_form").dialog('option','title','Student Edit-'+id).dialog('open'); 
		$("#edit_honsnew_form").html(response);
		     
		
        }
        });

	});



	  $("#GFFR").click(function(){	  	   
				
		$.ajax({	
        type:'POST',
        url:'ffreport',
   
        success:function(response){
        				    
			$("#bulk_report_preview").dialog('open'); 
        	$("#bulk_report_preview").html(response);    	     
		
        }
        });

	});

	$(".ViewHons").click(function(){
        var id=$(this).attr("id");
		
		$.ajax({	
        type:'GET',
        url:'http://easycollegemate.com/ecmrgwc/public/students/honours/viewdata',
        data:{id: id},
        success:function(response){
	    $("#details_show").dialog('open'); 
		$("#details_show").html(response);
		     
		
        }
        });

	});

	    
	     $(".SearchEdit").click(function(){
        var id=$(this).attr("id");
		

		$.ajax({	
        type:'POST',
        url:'infoedit',
        data:{id: id},
        success:function(response){
	    $("#edit_hscnew_form").dialog('option','title','Student Edit-'+id).dialog('open'); 
		$("#edit_hscnew_form").html(response);
		     
		
        }
        });

	});

	    $(".View").click(function(){
        var id=$(this).attr("id");
		//alert(id);

		$.ajax({	
        type:'POST',
        url:'http://easycollegemate.com/ecmrgwc/public/students/hsc/viewdata',
        data:{id: id},
        success:function(response){
	    $("#details_show").dialog('open'); 
		$("#details_show").html(response);
		     
		
        }
        });

	});

	    $(".Show").click(function(){
        var id=$(this).attr("id");
		//alert(id);

		$.ajax({	
        type:'POST',
        url:'viewdata',
        data:{id: id},
        success:function(response){
	    $("#details_show").dialog('open'); 
		$("#details_show").html(response);
		     
		
        }
        });

	});

	    



	    $("#details_show").dialog({
			autoOpen: false,
            modal: true,
			show: "blind",
            width:"700",
            height:"700"
			//hide: "explode"
		});

	
$("#bulk_report_preview").dialog({

			autoOpen: false,
            modal: true,
			show: "blind",
            width:"1100",
            height:"650",
			hide:{effect: "fadeOut", duration: 2000}
			//hide: "explode"
		});

$("#edit_hscnew_form").dialog({

			autoOpen: false,
            modal: true,
			show: "blind",
            width:"650",
            height:"700",
			hide:{effect: "fadeOut", duration: 2000}
			//hide: "explode"
		});

$("#edit_honsnew_form").dialog({

			autoOpen: false,
            modal: true,
			show: "blind",
            width:"650",
            height:"700",
			hide:{effect: "fadeOut", duration: 2000}
			//hide: "explode"
		});
$('.ui-dialog-titlebar-close').addClass('ui-icon ui-icon-closethick');
			
			
	});

