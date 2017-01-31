<?php
require ("../inc/config.inc.php");
?>
    
var api_list = [];
var OAUTH2_CLIENT_ID = '<?php echo _YOUTUBE_KEY_?>';
var FACEBOOK_CLIENT_ID = '<?php echo _FB_APP_ID_?>';
var pageAccessToken = '<?php echo _FB_PAGE_TOKEN_?>;';

function callDataYoutube(vid , pid , pos) {
    var url = "https://www.googleapis.com/youtube/v3/videos?id=" + vid + "&key=" + OAUTH2_CLIENT_ID + "&part=snippet";
    $.ajax({
        url: url,
        dataType: "jsonp",
        success: function(data) {
            var dataCallBack = [];
            dataCallBack[0] = data.items[0].snippet.title;
            dataCallBack[1] = data.items[0].snippet.thumbnails.default.url;
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

    var url = "/"+vid;
    
    //FB.api(url, 'GET', { access_token: pageAccessToken, "fields":"title" }, function(response) {
            var dataCallBack = [];
            //dataCallBack[0] = response.title;
            dataCallBack[0] = 'Facebook Video'
            dataCallBack[1] = 'https://graph.facebook.com/'+vid+'/picture';
            displayVideoCover(dataCallBack, pid , pos);
    //});
}

function openvideo(path,type){

    var iframe_url = '';    
        if (type == 'youtube') {
            var videopath = youtube_parser(path);
            iframe_url = '<iframe width="640" height="480" src="https://www.youtube.com/embed/'+videopath+'" frameborder="0" allowfullscreen></iframe>';
        } else if (type == 'viemo') {
            var videopath = Prepending_http('https://', path);
            iframe_url = vimeo_embed(videopath);
        } else if (type == 'facebook') {
             var videopath = encodeURI(path);
             videopath = videopath.replace(/\/$/, "");
            iframe_url = '<iframe src="https://www.facebook.com/plugins/video.php?href='+videopath+'%2F&show_text=0&width=640&height=480" width="640" height="480" scrolling="no" frameborder="0" allowTransparency="true" allowFullScreen="true"></iframe>';
        } else{
            var header = getVideoHeader(path);
            iframe_url += '<video width="640" height="480" autoplay>';
            iframe_url += '<source src="'+path+'" type="'+header+'">';
            iframe_url += '</video>';
        }

        $('#inline_video').html(iframe_url);
        $.colorbox({
            href:'#inline_video',
            inline:true,
			onClosed:function(){
                $('#inline_video').html('');
            }
        });

}

  window.fbAsyncInit = function() {
    FB.init({
      appId      : FACEBOOK_CLIENT_ID,
      xfbml      : true,
      version    : 'v2.8'
    });
    FB.AppEvents.logPageView();
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));