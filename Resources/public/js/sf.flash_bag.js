(function ($) {
  // FlashBag
  var FlashBag = function() {
    this.flashes = {};
  };

  FlashBag.prototype = {
    add: function(title, message, type, pluginOptions) {
      this.flashes.push({
        title: title,
        message: message,
        type: type,
        pluginOptions: pluginOptions,
      });
    },
    all: function() {
      var _return = this.flashes;
      this.flashes = {};
      return _return;
    },
    show: function() {
      $.each(this.flashes, function(i, flash) {
        $(document).trigger('ite-show.notification', flash);
      });
    }
  };

  FlashBag.prototype.fn = FlashBag.prototype;

  SF.fn.flashes = new FlashBag();
  SF.classes.FlashBag = FlashBag;

  $(document).ajaxComplete(function (event, xhr, settings) {

    var notifications = xhr.getResponseHeader('X-SF-Notifications');

    if (notifications) {
      var n = $.parseJSON(notifications);
      var $this = this;

      $.each(n, function (i, notification) {
        $this.flashes.add(notification.title, notification.message, notification.type, notification.pluginOptions);
      });

      this.flashes.show();
    }
  });

})(jQuery);