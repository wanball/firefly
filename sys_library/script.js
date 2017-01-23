$( document ).ready(function() {


	var page_Height = $('html').height();
	$('.box-body').css({'min-height':(page_Height-115)+'px'});
	if($('.preview-image').length > 0){
		$('.box-body').css({'height':(page_Height-115)+'px'});
	}

	
	$('#refresh_btn').on( "click", function(event) {
		location.reload();
	});	
	
	$('#new_dir_btn').on( "click", function(event) {
		$( "#block_display" ).prepend( '<div class="col-sm-2 col-xs-3 img-wrap" id="new_dir"> <a><img src="images/folder.svg" alt=" "></a><span><input type="text" onkeypress="if (event.keyCode==13){ createDir($(this).val()) };" onblur="createDir($(this).val())"></span></div>' );
		$('#new_dir input').focus();
	});	

	$('#new_file_btn').on( "click", function(event) {
		window.location.href = $(this).attr('data-url');
	});	

	$('#home_btn').on( "click", function(event) {
		window.location.href = 'library.php';
	});	
	
	$('#show_gallery').on( "click", function(event) {
			$('#show_gallery').addClass('disabled');
			$('#show_list').removeClass('disabled');
			$('#block_list_title , #block_display').removeClass('active');
			
			localStorage.setItem('library_view', 'gallery');
	});		
	
	$('#show_list').on( "click", function(event) {
			$('#show_gallery').removeClass('disabled');
			$('#show_list').addClass('disabled');
			$('#block_list_title , #block_display').addClass('active');
			
			localStorage.setItem('library_view', 'list'); 
	});	

    if(localStorage.getItem('library_view') != null){
        if(localStorage.getItem('library_view') == 'list'){
			$('#show_gallery').removeClass('disabled');
			$('#show_list').addClass('disabled');
			$('#block_list_title , #block_display').addClass('active');	        
        }
    }
    	
	$('#back_btn').on( "click", function(event) {
		var id = $(this).attr('data-id');
		var path = '';
		if(id != 0){
			path = '?l='+id;
		}
		window.location.href = 'library.php'+path;
	});	


	$('#edit_btn ,#cancel_btn').on( "click", function() {
		$('.dirname , .filename').toggle();
		$('.dirnameBtn div').toggleClass('hideBtn');
		$('input.dirname , input.filename').focus();
	});	
	$('#save_btn').on( "click", function() {
		$("#edit_dirname , #edit_imagename").submit();
	});	
	
	$("#edit_dirname").on( "submit",function(e) {
		
		var level = $('body').attr('data-level');
		var old_name = $('h4.dirname').text();
		var new_name = $('input.dirname').val();
		
		$.post( "sys_library/library_ajax.php", { name: "rename_dir", level: level, old_name:old_name , new_name:new_name })
		  .done(function( data ) {
				$('h4.dirname').text(new_name);
				$('.dirname').toggle();
				$('.dirnameBtn div').toggleClass('hideBtn');
				
				swal(warning_text4, warning_text5, "success");
		  });
		  
	    e.preventDefault(); // avoid to execute the actual submit of the form.
	});	
		
	$('#del_btn').on( "click", function() {
		
		var dir_name = $('h4.dirname').text();
		var res = warning_text6.replace('|:NAME:|', dir_name);
		
		swal({
		  title: res,
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonColor: "#DD6B55",
		  confirmButtonText: "Yes",
		  cancelButtonText: "No",
		  closeOnConfirm: false,
		  closeOnCancel: false
		},
		function(isConfirm){
			if (isConfirm) {
		
				var level = $('body').attr('data-level');
				
				$.post( "sys_library/library_ajax.php", { name: "del_dir", level: level, dir_name:dir_name })
				  .done(function( data ) {
						window.location.href = 'library.php?l='+data;
				  });				
			}else{
				swal.close();
			}
		});	
	});	

	$("#edit_imagename").on( "submit",function(e) {
		
		var level = $('#preview_id').val();
		var old_name = $('h4.filename').text();
		var new_name = $('input.filename').val();

	
		$.post( "sys_library/library_ajax.php", { name: "rename_file", level: level, old_name:old_name , new_name:new_name })
		  .done(function( data ) {
				$('h4.filename').text(new_name);
				$('.filename').toggle();
				$('.dirnameBtn div').toggleClass('hideBtn');
				
				swal(warning_text4, warning_text5, "success");
		  });

	    e.preventDefault(); // avoid to execute the actual submit of the form.
	});	
		
	$('#remove_btn').on( "click", function() {
		
		var dir_name = $('h4.filename').text();
		var res = warning_text6.replace('|:NAME:|', dir_name);
		
		swal({
		  title: res,
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonColor: "#DD6B55",
		  confirmButtonText: "Yes",
		  cancelButtonText: "No",
		  closeOnConfirm: false,
		  closeOnCancel: false
		},
		function(isConfirm){
			if (isConfirm) {
		
				var level = $('#preview_id').val();
				$.post( "sys_library/library_ajax.php", { name: "del_file", level: level, dir_name:dir_name })
				  .done(function( data ) {
						window.location.href = 'library.php?l='+data;
				  });			
			}else{
				swal.close();
			}
		});	
	});	
	
	$('.dir_remove_btn').on( "click", function() {
		
		var level = $(this).attr('data-id');
		var dir_name = $('#row_'+level+' span > a').text();
		var res = warning_text7.replace('|:NAME:|', dir_name);
		

		swal({
		  title: res,
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonColor: "#DD6B55",
		  confirmButtonText: "Yes",
		  cancelButtonText: "No",
		  closeOnConfirm: false,
		  closeOnCancel: false
		},
		function(isConfirm){
			if (isConfirm) {
		
				$.post( "sys_library/library_ajax.php", { name: "del_file", level: level, dir_name:dir_name })
				  .done(function( data ) {
					  
						$('#row_'+level).hide( "blind", function() {
						    $('#row_'+level).remove();
						});
						swal.close();
				  });		
			}else{
				swal.close();
			}
		});	
	
	});	
		
	try {
		var clipboard = new Clipboard('.btn');
	
	    clipboard.on('success', function(e) {
	        //console.log(e);
			toastr.options = {
			  "closeButton": false,
			  "debug": false,
			  "newestOnTop": false,
			  "progressBar": false,
			  "positionClass": "toast-bottom-center",
			  "preventDuplicates": false,
			  "onclick": null,
			  "showDuration": "300",
			  "hideDuration": "1000",
			  "timeOut": "1000",
			  "extendedTimeOut": "1000",
			  "showEasing": "swing",
			  "hideEasing": "linear",
			  "showMethod": "fadeIn",
			  "hideMethod": "fadeOut"
			}
	        toastr["success"]("Copies", "");
    	        
	    });
		/*
	    clipboard.on('error', function(e) {
	        console.log(e);
	    });
	    */
	}catch(err) {
		console.log(err);
	}	
	
	$('#orderControl').on( "change", function() {
		var my_value = $(this).val();
		Cookies.set('order_by', my_value, { expires: (7/24) });
		window.location.reload();
	});
	
});


function createDir(txt){
	if(txt == ''){
		$('#new_dir').remove();
	}else{
		var level = $('body').attr('data-level');
		$.post( "sys_library/library_ajax.php", { name: txt, level: level })
		  .done(function( data ) {
		    if(data == 0){
			    $('#new_dir').remove();
		    }else if(data == 2){
				var res = warning_text1.replace('|:NAME:|', txt);
				 
				swal(warning_text2, res, "error");
			    $('#new_dir').remove();
		    }else{
				swal(warning_text4, warning_text3, "success");
			    location.reload();
		    }
		  });		
	}
}	