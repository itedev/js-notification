(function ($) {
  // FlashBag
  var FlashBag = function () {
    this.flashes = {};
  };

  FlashBag.prototype = {
    add: function (channel, title, message, type, options) {
      if ('undefined' === typeof this.flashes[channel]) {
        this.flashes[channel] = [];
      }
      this.flashes[channel].push({
        title: title,
        message: message,
        type: type,
        options: options
      });
    },
    addObject: function (notification) {
      if ('undefined' === typeof this.flashes[notification.channel]) {
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
      $.each(this.flashes, function(channel, notifications) {
        $.each(notifications, function(i, notification) {
          $(document).trigger('ite-show.' + channel + '.notification', notification);
        });
      });
      this.flashes = {};
    }
  };

  FlashBag.prototype.fn = FlashBag.prototype;

  SF.fn.flashes = new FlashBag();
  SF.classes.FlashBag = FlashBag;

  $(document).ajaxComplete(function(event, xhr, settings) {
    var notificationsHeader = xhr.getResponseHeader('X-SF-Notifications');
    if (notificationsHeader) {
      var notifications = $.parseJSON(notificationsHeader);

      $.each(notifications, function(i, notifications) {
        $.each(notifications, function(j, notification) {
          SF.flashes.addObject(notification);
        });

      });

      SF.flashes.show();
    }
  });

})(jQuery);