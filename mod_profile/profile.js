$(function() {

	function avatarUpload() {
		var $uploadCrop;
		

		function readFile(input) {
 			if (input.files && input.files[0]) {
	            var reader = new FileReader();
	            
	            reader.onload = function (e) {
	            	$uploadCrop.croppie('bind', {
	            		url: e.target.result
	            	});
	            	$('#avatar').addClass('ready');
	            }
	            
	            reader.readAsDataURL(input.files[0]);
	        }
	        else {
		        swal("Sorry - you're browser doesn't support the FileReader API");
		    }

		}

		$uploadCrop = $('#upload-demo').croppie({
			enableExif: true,
			viewport: {
				width: 250,
				height: 250,
				type: 'circle'
			},
			boundary: {
				width: 280,
				height: 280
			}
		});

		$('#upload').on('change', function () { readFile(this); });
		$('.upload-result').on('click', function (ev) {
			$uploadCrop.croppie('result', {
				type: 'canvas',
				size: 'viewport',
				format: 'jpeg',
				circle: false
			}).then(function (resp) {
				$('.actions').addClass('actions-loading');
				$.post( "mod_profile/avatar_save.php", { name: resp })
				  .done(function( data ) {
				  		window.location.reload();
				  });
			});
		});
	}
	avatarUpload();
	jQuery("time.timeago").timeago();
	
	$('#password1 input').on('blur', function() {
		$.post( "mod_profile/profile-action.php", { action:'validate_pass' , pass : $(this).val() })
			.done(function( data ) {
				$('#pass_staus').val(data);
				if(data == '0'){
					$('#password1').removeClass('has-success').addClass('has-error').find('.help-block').text(warning_text1);
				}else{
					$('#password1').removeClass('has-error').addClass('has-success').find('.help-block').text('');
				}
		});
	});

	$('#Profile_Email').on("dblclick", function(e) {
		if(confirm(warning_text4)){
    		$(this).prop('readonly', false).focus();
    	}
	}).on("blur", function(e) {
	    $(this).prop('readonly', true);
	});
});

function checkForm(form){
	var password1 = $('#password1 input').val();
	var password2 = $('#password2 input').val();
	var password3 = $('#password3 input').val();
	
	$('#password1 , #password2 , #password3').removeClass('has-error').find('.help-block').text('');
    
    if(password1 == password2){
      $('#password2').addClass('has-error').find('.help-block').text(warning_text2).focus();
      return false;
    }else if(password2 != password3){
      $('#password3').addClass('has-error').find('.help-block').text(warning_text3).focus();
      return false;
    }
    return true;
    		
}