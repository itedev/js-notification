(function ($) {
  // FlashBag
  var FlashBag = function () {
    this.flashes = {};
  };

  FlashBag.prototype = {
    add: function (channel, title, message, type, pluginOptions) {
      if (typeof this.flashes[channel] == 'undefined') {
        this.flashes[channel] = [];
      }
      this.flashes[channel].push({
        title: title,
        message: message,
        type: type,
        pluginOptions: pluginOptions
      });
    },
    addObject: function (notification) {
      if (typeof this.flashes[notification.channel] == 'undefined') {
        this.flashes[notification.channel] = [];
      }
      this.flashes[notification.channel].push(notification);
    },
    all: function () {
      var _return = this.flashes;
      this.flashes = {};
      return _return;
    },
    show: function () {
      $.each(this.flashes, function (i, flashes) {
        var index = i;
        $.each(flashes, function (k, flash) {
          $(document).trigger('ite-show.' + index + '.notification', flash);
        });
      });
      this.flashes = {};
    }
  };

  FlashBag.prototype.fn = FlashBag.prototype;

  SF.fn.flashes = new FlashBag();
  SF.classes.FlashBag = FlashBag;

  $(document).ajaxComplete(function (event, xhr, settings) {

    var notifications = xhr.getResponseHeader('X-SF-Notifications');

    if (notifications) {
      var n = $.parseJSON(notifications);

      $.each(n, function (i, notifications) {
        $.each(notifications, function (i, n) {
          SF.flashes.addObject(n);
        });

      });

      SF.flashes.show();
    }
  });

})(jQuery);