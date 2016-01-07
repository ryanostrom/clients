$(document).ready(function() {
  var $body = $('body'),
   events = {
    init: function() {
      $body.on('click', '.client', this.showClient.bind(this));
      $body.on('click', '.add', this.goToAdd);
    },
    showClient: function(e) {
      var client = $(e.target).closest('.client'),
        hideCurrent = client.hasClass('show-projects');

      $body.find('.client')
        .removeClass('show-projects')
        .addClass('hide-projects')
        .find('.projects').addClass('hide');

      if (!hideCurrent) {
        client
          .removeClass('hide-projects')
          .addClass('show-projects')
          .find('.projects').removeClass('hide');
      }
    },
    goToAdd: function() {
      window.location = '/add_client.php';
    }
  };

  events.init();
});