<?php
require ("../inc/config.inc.php");
?>
    
var api_list = [];
var OAUTH2_CLIENT_ID = '<?php echo _YOUTUBE_KEY_?>';
var FACEBOOK_CLIENT_ID = <?php echo _FB_APP_ID_?>;

function callDataYoutube(vid , pid , pos) {
    var url = "https://www.googleapis.com/youtube/v3/videos?id=" + vid + "&key=" + OAUTH2_CLIENT_ID + "&part=snippet";
    $.ajax({
        url: url,
        dataType: "jsonp",
        success: function(data) {
            var dataCallBack = [];
            dataCallBack[0] = data.items[0].snippet.title;
            dataCallBack[1] = data.items[0].snippet.thumbnails.standard.url;
            displayVideoCover(dataCallBack, pid, pos);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert(textStatus, +' | ' + errorThrown);
        }
    });
}
function callDataVimeo(vid , pid , pos) {
    var url = "https://vimeo.com/api/oembed.json?url="+vid;
    $.ajax({
        url: url,
        dataType: "jsonp",
        success: function(data) {
            var dataCallBack = [];
            dataCallBack[0] = data.title;
            dataCallBack[1] = data.thumbnail_url;
            displayVideoCover(dataCallBack, pid , pos);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert(textStatus, +' | ' + errorThrown);
        }
    });
}

function callDataFacebook(vid , pid ,pos) {
alert(vid);
    var url = "/"+vid;
    FB.api(url, function(response) {
            var dataCallBack = [];
            //dataCallBack[0] = response.title;
            //dataCallBack[1] = 'https://graph.facebook.com/'+response.id+'/picture';
            //displayVideoCover(dataCallBack, pid);
    });
}

$( document ).ready(function() {

    (function(d, s, id){
       var js, fjs = d.getElementsByTagName(s)[0];
       if (d.getElementById(id)) {return;}
       js = d.createElement(s); js.id = id;
       js.src = "//connect.facebook.net/en_US/sdk.js";
       fjs.parentNode.insertBefore(js, fjs);
     }(document, 'script', 'facebook-jssdk'));



window.fbAsyncInit = function() {
    FB.init({
        appId: FACEBOOK_CLIENT_ID,
        cookie: true, // enable cookies to allow the server to access
        xfbml      : true,
        version    : 'v2.8'
    });
};
 

 function fbEnsureInit(callback) {
        if(!window.fbApiInit) {
            setTimeout(function() {fbEnsureInit(callback);}, 50);
        } else {
            if(callback) {
                callback();
            }
        }
    }

});

