$(document).ready(function() {
  var $body = $('body'),
   events = {
    init: function() {
      $body.on('click', 'button#create', this.addClient);
      $body.on('click', 'button.add', this.addProject);
      $body.on('click', '#remove-project', this.removeProject.bind(this));
    },
    addClient: function() {
      var data = {project: {}};
      $('#add-client :input').not(':button').each(function() {
        var inputName = $(this).attr('id'),
          inputValue = $(this).val(),
          projectCount = $(this).data('project');

          if (typeof projectCount != 'undefined') {
            if (typeof data['project'][projectCount] == 'undefined') {
              data['project'][projectCount] = {};
            }
            data['project'][projectCount][inputName] = inputValue;
          }
          else {
            data[inputName] = inputValue;
          }
      });

      $.get('../service/add_client.php', {ax: 'add-client', data: data}, function(result) {
        if (result.success) {
          window.location = '/';
        }
        else {
          events.showMessage(result.message);
        }
      }, 'json');
    },
    addProject: function() {
      var count = $body.find('.project').length + 1;

      $.get('../service/add_client.php', {ax: 'add-option', data: { count: count }}, function(result) {
        if (result) {
          $body.find('.project:last').after(result.result);
        }
      }, 'json');
    },
    removeProject: function(e) {
      $(e.target).closest('.project').remove();
    },
    showMessage: function(message) {
      $('body').prepend('<div class="notice">' + message + '</div>');
      window.scrollTo(0, 0);
    }
  };
  events.init();
});