
$( document ).ready(function() {
	//menu active
	try{
		$('li#menu'+menu_id).addClass('active');
	}catch(err) {
		console.log(err.message);	
	}
	$( "button#btn_back" ).on( "click", function() {
		window.history.back();
	});	

	$('#galleryBtn').on( "click", function(event) {
		
		   var path = $(this).attr('href');	
		   if(!mobile){
		   		galleryOpen(path);
		   }else{
			    window.open(path, 'gallery'); 
		   }	
	       event.preventDefault();
	       event.stopPropagation();
	});	
		
});
//PopupCenter
function PopupCenter(url, title, w, h) {
    // Fixes dual-screen position                         Most browsers      Firefox
    var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
    var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

    var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
    var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

    var left = ((width / 2) - (w / 2)) + dualScreenLeft;
    var top = ((height / 2) - (h / 2)) + dualScreenTop;
    var newWindow = window.open(url, title, 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);

    // Puts focus on the newWindow
    if (window.focus) {
        newWindow.focus();
    }
}

function galleryOpen(url){
    var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
    var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;	
	
	var newwidth = width/2;
    var left = newwidth;
    var top = 0;
        
    var newWindow = window.open(url, 'gallery', 'scrollbars=yes, width=' + newwidth + ', height=' + height + ', top=' + top + ', left=' + left);

    // Puts focus on the newWindow
    if (window.focus) {
        newWindow.focus();
    }
        
}

function signout_confirm(txt){
	swal({
	  title: txt,
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
	    	window.location.href = 'signout.php';
		}else{
			swal.close();
		}
	});	
}