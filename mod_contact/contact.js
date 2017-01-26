$(document).ready(function() {
    // Display original dates older than 5 day
    jQuery.timeago.settings.cutoff = 1000 * 60 * 60 * 24 * 5;
    $("time.timeago").timeago();

    $('input').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%',
        activeClass: 'active',
    });

    //Enable iCheck plugin for checkboxes
    //iCheck for checkbox and radio inputs
    $('.mailbox-messages input[type="checkbox"]').iCheck({
        checkboxClass: 'icheckbox_flat-blue',
        radioClass: 'iradio_flat-blue'
    });

    //Enable check and uncheck all functionality
    $(".checkbox-toggle").on("click", function(e) {
        var clicks = $(this).data('clicks');
        if (clicks) {
            //Uncheck all checkboxes
            $(".mailbox-messages input[type='checkbox']").iCheck("uncheck");
            $(".fa", this).removeClass("fa-check-square-o").addClass('fa-square-o');
        } else {
            //Check all checkboxes
            $(".mailbox-messages input[type='checkbox']").iCheck("check");
            $(".fa", this).removeClass("fa-square-o").addClass('fa-check-square-o');
        }
        $(this).data("clicks", !clicks);
    });

    //Handle starring for glyphicon and font awesome
    $(".mailbox-star").on("click", function(e) {
        e.preventDefault();
        //detect type
        var $this = $(this).find("a > i");
        var glyph = $this.hasClass("glyphicon");
        var fa = $this.hasClass("fa");

        //Switch states
        if (glyph) {
            $this.toggleClass("glyphicon-star");
            $this.toggleClass("glyphicon-star-empty");
        }

        if (fa) {
            $this.toggleClass("fa-star");
            $this.toggleClass("fa-star-o");
        }
    });
    
    
    $( ".btn-swapPage:enabled" ).on("click", function(e) {
	   var page = $(this).attr('data-page'); 
	   var url = window.location.href ;
	   var res = url.split("&pg=");
	   window.location.href = res[0] + '&pg='+ page;
	});  
	
	$( ".refresh_btn" ).on("click", function(e) {
		window.location.reload();
	});  
	
	$( ".delete_btn" ).on("click", function(e) {
		var numberOfChecked = $('input:checkbox:checked').length;
		if(numberOfChecked == 0){
            var res = warning_text2.replace('|:NUM:|', 1);
            swal(warning_text1, res, "error");
        }else{
			$('input:checkbox:checked').each(function () {
			    var ThisVal = $(this).val();

				$.post( "mod_contact/contact-action.php", { id: ThisVal, type: "delete" })
				  .done(function( data ) {
	    
				    $('.mailbox-messages tr[data-id="'+ThisVal+'"]').remove();
					var numberOfCheckbox = $('.mailbox-messages tr').length;
					if(numberOfCheckbox == 0){
						window.location.reload();
					}
  				});	
			});	        
        }
	});  	
});