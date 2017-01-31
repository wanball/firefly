$(document).ready(function() {
    //menu active
    try {
        $('li#menu' + menu_id).addClass('active');
    } catch (err) {
        console.log(err.message);
    }
    $("button#btn_back").on("click", function() {
        window.history.back();
    });

    $('#galleryBtn').on("click", function(event) {

        var path = $(this).attr('href');
        if (!mobile) {
            galleryOpen(path);
        } else {
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

function galleryOpen(url) {
    var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
    var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

    var newwidth = width / 2;
    var left = newwidth;
    var top = 0;

    var newWindow = window.open(url, 'gallery', 'scrollbars=yes, width=' + newwidth + ', height=' + height + ', top=' + top + ', left=' + left);

    // Puts focus on the newWindow
    if (window.focus) {
        newWindow.focus();
    }

}

function signout_confirm(txt) {
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
        function(isConfirm) {
            if (isConfirm) {
                window.location.href = 'signout.php';
            } else {
                swal.close();
            }
        });
}

function getMimes(ext) {
    var text;
    switch (ext) {
        case 'htm':
        case 'asp':
        case 'aspx':
        case 'php':
        case 'js':
        case 'css':
        case 'xml':
        case 'html':
            text = "fa-internet-explorer";
            break;
        case 'ods':
        case 'otf':
        case 'xls':
        case 'xlt':
        case 'csv':
        case 'xlsx':
        case 'xlsm':
        case 'numbers':
            text = "fa-file-excel-o";
            break;
        case 'swf':
        case 'fla':
            text = "fa-video-camera";
            break;
        case 'odp':
        case 'otp':
        case 'ppt':
        case 'pps':
        case 'pot':
        case 'pptx':
        case 'potm':
        case 'ppsx':
        case 'key':
            text = "fa-file-powerpoint-o";
            break;
        case 'aac':
        case 'ac3':
        case 'aiff':
        case 'flac':
        case 'm4a':
        case 'mid':
        case 'mp3':
        case 'oga':
        case 'ogg':
        case 'wav':
        case 'wma':
            text = "fa-file-audio-o";
            break;
        case 'pdf':
            text = "fa-file-pdf-o";
            break;
        case 'bmp':
        case 'cr2':
        case 'gif':
        case 'ico':
        case 'jpg':
        case 'jpeg':
        case 'odg':
        case 'png':
        case 'psd':
        case 'svg':
        case 'tiff':
        case 'wbmp':
        case 'webp':
            text = "fa-file-image-o";
            break;
        case 'txt':
            text = "fa-file-text-o";
            break;
        case '264':
        case '3ga':
        case '3gp':
        case 'avi':
        case 'f4v':
        case 'flv':
        case 'm2ts':
        case 'm4v':
        case 'mkv':
        case 'mov':
        case 'mp4':
        case 'mpg':
        case 'mpeg':
        case 'mts':
        case 'rmvb':
        case 'vob':
        case 'webm':
        case 'wmv':
        case 'ts':
            text = "fa-file-video-o";
            break;
        case 'odt':
        case 'ott':
        case 'doc':
        case 'dot':
        case 'docx':
        case 'page':
            text = "fa-file-word-o";
            break;
        case '7z':
        case 'tar.bz2':
        case 'cab':
        case 'lzh':
        case 'tar':
        case 'tar.gz':
        case 'yz1':
        case 'zip':
            text = "fa-file-archive-o";
            break;
        default:
            text = "fa-file-file-o";
    }
    return text;
}

function getFileExtension(filename) {
    var re = /(?:\.([^.]+))?$/;

    return re.exec(filename)[1];
}

function getVideoHeader(path) {
    var ext = getFileExtension(path);
    ext = ext.toLowerCase();
    var text;
    switch (ext) {
        case 'mp4':
            text = "video/mp4";
            break;
        case 'ogg':
            text = "video/ogg";
            break;
        case 'webm':
            text = "video/webm";
            break;
    }
    return text;
}

function valid_url(url, type) {
    if (type == 'youtube') {
        var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=|\?v=)([^#\&\?]*).*/;
    } else if (type == 'facebook') {
        var regExp = /^(https?:\/\/)?((w{3}\.)?)facebook.com\/.*/;
    } else if (type == 'twitter') {
        var regExp = /^(https?:\/\/)?((w{3}\.)?)twitter\.com\/(#!\/)?[a-z0-9_]+$/;
    } else if (type == 'viemo') {
        var regExp = /^(https?:\/\/)?((w{3}\.)?)vimeo.com\/(\d+)($|\/)/;
    } else if (type == 'link') {
        var regExp = /^(https?:\/\/)?((w{3}\.)?).*(\.mp4|\.ogv|\.ogg|\.webm)$/;
    }

    var match = url.match(regExp);


    return match;

}

function youtube_parser(url) {
    var regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/;
    var match = url.match(regExp);
    return (match && match[7].length == 11) ? match[7] : false;
}

function Prepending_http(prefix, s) {
    if (s.substr(0, prefix.length) !== prefix) {
        return prefix + s;
    } else {
        return s;
    }
}

function facebook_parser(frame) {
    var res = frame.split("videos");
    var last_element = res[res.length - 1];
    return last_element.replace(/\//g, "");

}

function getFileName(fullPath) {
    return fullPath.replace(/^.*[\\\/]/, '');
}

function vimeo_embed(vid) {
    var vimeo_Reg = /https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)/;
    var match = vid.match(vimeo_Reg);

    if (match) {
        return '<iframe src="https://player.vimeo.com/video/' + match[3] + '" width="640" height="480" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
    }
}