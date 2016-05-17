(function ($) {
  // Channel
  var Channel = function (methods) {
    methods = methods || {};

    var self = this;
    $.each(methods, function(methodName, method) {
      self[methodName] = method;
    });

    this.defaultOptions = {};
    this.initialize();
  };

  Channel.prototype = {
    message: function (type, title, message, options) {},
    notification: function (notification) {
      var options = $.extend(this.defaultOPtions, notification.options|{});
      this.message(notification.type, notification.title, notification.message, options);
    },
    success: function (title, message, options) {
      return this.message('success', title, message, options);
    },
    info: function (title, message, options) {
      return this.message('info', title, message, options);
    },
    warning: function (title, message, options) {
      return this.message('warning', title, message, options);
    },
    error: function (title, message, options) {
      return this.message('error', title, message, options);
    },
    initialize: function (defaultOptions) {
      this.defaultOptions = $.extend(this.defaultOptions, defaultOptions);
    }
  };

  var Notifier = function () {
    this.channels = {};
  };

  Notifier.prototype = {
    notify: function (channel, type, title, message, options) {
      if (!this.channels.hasOwnProperty(channel)) {
        throw 'Channel "' + channel + '" is not defined.';
      }
      this.channels[channel].message(type, title, message, options);
    },
    get: function (channel) {
      if (!this.channels.hasOwnProperty(channel)) {
        throw 'Channel "' + channel + '" is not defined.';
      }

      return this.channels[channel];
    }
  };

  Notifier.prototype.fn = Notifier.prototype;

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
          this.channels[channel].notification(notification);
        });
      });
      this.flashes = {};
    }
  };

  FlashBag.prototype.fn = FlashBag.prototype;

  SF.fn.notifier = new Notifier();
  SF.fn.notifier.fn.channels = {};
  SF.fn.flashes = new FlashBag();
  SF.classes.Channel = Channel;
  SF.classes.Notifier = Notifier;
  SF.classes.FlashBag = FlashBag;

  $(function() {
    $(document).on('ite-pre-ajax-complete', function(e, data) {
      if (!data.hasOwnProperty('notifications')) {
        return;
      }

      var notifications = data['notifications'];
      $.each(notifications, function(i, notifications) {
        $.each(notifications, function(j, notification) {
          SF.flashes.addObject(notification);
        });
      });

      SF.flashes.show();
    });
  });


})(jQuery);