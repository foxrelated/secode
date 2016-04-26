/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if (typeof(ynvideo_types) == 'undefined') {
    ynvideo_types = [];
}


if (typeof(ynvideo_extract_code) == 'undefined') {
    ynvideo_extract_code = {};
}

ynvideo_extract_code.youtube = function (url) {
    var myURI = new URI(url);
    var youtube_code = myURI.get('data')['v'];
    if( youtube_code === undefined ) {
        youtube_code = myURI.get('file');
    }
    return youtube_code;
}

ynvideo_extract_code.vimeo = function (url) {
    var myURI = new URI(url);
    var vimeo_code = myURI.get('file');
    return vimeo_code;
}

ynvideo_extract_code.dailymotion = function (url) {
    var myURI = new URI(url);
    var dailymotion_file = myURI.get('file');
    var dailymotion_code = dailymotion_file.split('_')[0];
    return dailymotion_code;
}

ynvideo_extract_code.videoURL = function (url) {
    var myURI = new URI(url);
    file = myURI.get('file');
    return file;
}