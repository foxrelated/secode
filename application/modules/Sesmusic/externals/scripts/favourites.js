
function sesmusicFavourite(resource_id, resource_type) {

  if ($(resource_type + '_favouritehidden_' + resource_id))
    var favourite_id = $(resource_type + '_favouritehidden_' + resource_id).value

  en4.core.request.send(new Request.JSON({
    url: en4.core.baseUrl + 'sesmusic/favourite/index',
    data: {
      format: 'json',
      'resource_type': resource_type,
      'resource_id': resource_id,
      'favourite_id': favourite_id
    },
    onSuccess: function(responseJSON) {
      if (responseJSON.favourite_id) {
        if ($(resource_type + '_unfavourite_' + resource_id))
          $(resource_type + '_unfavourite_' + resource_id).style.display = 'inline-block';
        if ($(resource_type + '_favouritehidden_' + resource_id))
          $(resource_type + '_favouritehidden_' + resource_id).value = responseJSON.favourite_id;
        if ($(resource_type + '_favourite_' + resource_id))
          $(resource_type + '_favourite_' + resource_id).style.display = 'none';
      } else {
        if ($(resource_type + '_favouritehidden_' + resource_id))
          $(resource_type + '_favouritehidden_' + resource_id).value = 0;
        if ($(resource_type + '_unfavourite_' + resource_id))
          $(resource_type + '_unfavourite_' + resource_id).style.display = 'none';
        if ($(resource_type + '_favourite_' + resource_id))
          $(resource_type + '_favourite_' + resource_id).style.display = 'inline-block';

      }
    }
  }));
}
