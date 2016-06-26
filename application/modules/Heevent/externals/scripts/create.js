function _initUploader() {
  if (window.URL && window.URL.createObjectURL) {
    var handleFileSelect = function (evt) {
      var files = evt.target.files; // FileList object
      var file = files[0];
//      console.log(files);
//      console.log(window.URL.createObjectURL(files[0]));
      var coverPhotoImg = document.getElementById('heevent-create-cover');
      var path = window.URL.createObjectURL(file);
      var photo_id = $('photo_id');
      if (photo_id.value) {
        coverPhotoImg.setAttribute('selected-bg', coverPhotoImg.style.backgroundImage);
        photo_id.oldValue = photo_id.value;
        photo_id.value = '';
        $$('.heevent-create-cover-nav').hide();
      } else if (!coverPhotoImg.getAttribute('selected'))
        coverPhotoImg.setAttribute('default-bg', coverPhotoImg.style.backgroundImage);
      coverPhotoImg.style.backgroundImage = ['url("', path, '")'].join('');
      checkPhotoRatio(path);
      coverPhotoImg.setAttribute('selected', true);
      $('heevent-create-delete-cover').show();
      if($('all_photos-wrapper'))$('all_photos-wrapper').hide();
    };
    var photoEl = document.getElementById('photo');
    if (photoEl)
      photoEl.addEvent('change', handleFileSelect, false);
  }
}
function checkPhotoRatio(path) {
  _hem.getImageDimensions(path, function (size) {
    if (Math.round((size.width * 8) / (size.height * 29) * 5) != 5) {
      $('cover_params-wrapper').show();
    }
  });
}
function deletePhoto() {
  var coverPhotoImg = document.getElementById('heevent-create-cover');
  var photo_id = $('photo_id');
  if (photo_id.oldValue) {
    photo_id.value = photo_id.oldValue;
    coverPhotoImg.style.backgroundImage = coverPhotoImg.getAttribute('selected-bg');
    $$('.heevent-create-cover-nav').show();
  } else {
    coverPhotoImg.style.backgroundImage = coverPhotoImg.getAttribute('default-bg');
    photo_id.value = '';
  }
  coverPhotoImg.setAttribute('selected', '');
  $('heevent-create-delete-cover').hide();
  var photoEl = $('photo-element');
  photoEl.innerHTML = photoEl.innerHTML;
  $('cover_params-wrapper').hide();
  //$('all_photos-wrapper').show();
  _initUploader();
}
function getLocation() {
  _hem.getCurrentLocation(function (results) {
    var loc = results[0].formatted_address;
    $('location').value = loc;
    drawMap();
  });
}
function zoomIn() {
  var zoom = $('map_zoom');
  zoom.value = 1 + parseInt(zoom.value);
  drawMap();
}
function zoomOut() {
  var zoom = $('map_zoom');
  zoom.value = parseInt(zoom.value) - 1;
  drawMap();
}
function drawMap() {
  var loc = $('location').value;
  var mapWEl = $('map-wrapper');
  if (!loc) {
    if (mapWEl) {
      mapWEl.hide();
      $('map_zoom-wrapper').hide();
    }
    return;
  }
  if (mapWEl) {
    mapWEl.show();
    $('map_zoom-wrapper').show();
  }
  var zoom = $('map_zoom').value;
  var smapEl = $('heevent-create-map-img');
  if (smapEl) {
    var smapDiv = $('coverphoto-element');
    var w = 400;
    var h = 300;
    smapEl.set('src', 'http://maps.googleapis.com/maps/api/staticmap?center=' + encodeURIComponent(loc) + '&zoom=' + zoom + '&size=' + w + 'x' + h + '&markers=color:red|' + encodeURIComponent(loc) + '&scale=1&sensor=false');
  }
}