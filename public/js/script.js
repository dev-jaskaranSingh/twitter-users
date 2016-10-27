// google search api functions
function init() {
  var input = document.getElementById('locationTextField');
  var autocomplete = new google.maps.places.Autocomplete(input);
}

google.maps.event.addDomListener(window, 'load', init);

// internal functions
(function($, document, window){

  $('#searchUsers').on('submit', function(e){
    e.preventDefault();
    var url = '/twitter_user_api/functions.php?method=search',
      data = $(this).serialize(),
      tbody = $('.table').find('tbody'),
      loader = '<tr><td colspan="3" class="loader"></td>';

    tbody.html(loader);
    $.post(url, data)
      .done(function(response) {
        process_table(response)
      })
      .fail(function(response) {
        tbody.html('<tr><td colspan="3">No data found</td></tr>');
        alert('seems like something went wrong');
      })

    function process_table(users) {
      var html = '';
      users.forEach(function (user, index) {
        html += '<tr>' +
          '<td>' +
          '<span class="number">'+(index+1)+'</span>' +
          '<a target="_blank" href="https://twitter.com/'+user.screen_name+'" class="photo">' +
          '<img src="'+user.profile_image_url+'" alt="Image Description">' +
          '</a>' +
          '<a target="_blank" href="https://twitter.com/'+user.screen_name+'" class="name">'+user.name+'</a>' +
          '<a target="_blank" href="https://twitter.com/'+user.screen_name+'" class="user-name">@'+user.screen_name+'</a>' +
          '</td>' +
          '<td>'+user.followers_count+'</td>'+
          '<td>'+user.location+'</td>'+
          '</tr>';
      });
      tbody.html(html);
    }
  });

  $(function() {
    $('#searchUsers').submit();
  })
})(jQuery, document, window);