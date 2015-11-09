(function (factory) {
  if (typeof define === "function" && define.amd) {
    define(["jquery"], factory);
  } else {
    factory(jQuery);
  }
})(function ($) {

  "use strict";

  function CropAvatar($element) {
    this.$container = $element;

    this.$imageUpload = this.$container.find(".image-upload");
    this.$imageSrc = this.$container.find(".image-src");
    this.$imageData = this.$container.find(".image-data");
    this.$imageInput = $('#image-input');

    this.$imageWrapper = this.$container.find(".image-wrapper");
    this.$imagePreview = this.$container.find(".image-preview");

    this.init();
  }

  CropAvatar.prototype = {
    constructor: CropAvatar,

    support: {
      fileList: !!$("<input type=\"file\">").prop("files"),
      fileReader: !!window.FileReader,
      formData: !!window.FormData
    },

    init: function () {
      this.support.datauri = this.support.fileList && this.support.fileReader;

      if (!this.support.formData) {
        this.initIframe();
      }

      this.addListener();
    },

    addListener: function () {
      // this.$container.on("click", $.proxy(this.click, this));
      this.$imageInput.on("change", $.proxy(this.change, this));
    },

    initIframe: function () {
      var iframeName = "image-iframe-" + Math.random().toString().replace(".", ""),
          $iframe = $('<iframe name="' + iframeName + '" style="display:none;"></iframe>'),
          firstLoad = true,
          _this = this;

      this.$iframe = $iframe;
      this.$container.attr("target", iframeName).after($iframe);

      this.$iframe.on("load", function () {
        var data,
            win,
            doc;

        try {
          win = this.contentWindow;
          doc = this.contentDocument;

          doc = doc ? doc : win.document;
          data = doc ? doc.body.innerText : null;
        } catch (e) {}

        if (data) {
          _this.submitDone(data);
        } else {
          if (firstLoad) {
            firstLoad = false;
          } else {
            _this.submitFail("Image upload failed!");
          }
        }

        _this.submitEnd();
      });
    },

    change: function () {
      var files,
          file;

      if (this.support.datauri) {
        files = this.$imageInput.prop("files");

        if (files.length > 0) {
          file = files[0];

          if (this.isImageFile(file)) {
            this.read(file);
          }
        }
      } else {
        file = this.$avatarInput.val();

        if (this.isImageFile(file)) {
          this.syncUpload();
        }
      }
    },

    isImageFile: function (file) {
      if (file.type) {
        return /^image\/\w+$/.test(file.type);
      } else {
        return /\.(jpg|jpeg|png|gif)$/.test(file);
      }
    },

    read: function (file) {
      var _this = this,
          fileReader = new FileReader();

      fileReader.readAsDataURL(file);

      fileReader.onload = function () {
        _this.url = this.result
        _this.startCropper();
      };
    },

    startCropper: function () {
      var _this = this;

      if (this.active) {
        this.$img.cropper("setImgSrc", this.url);
      } else {
        this.$img = $('<img src="' + this.url + '">');
        this.$imageWrapper.empty().html(this.$img);
        
        this.$img.cropper({
          // aspectRatio: 1,
          resizable: false,
          preview: this.$imagePreview.selector,
          data: {
            width: intelli.config.portfolio_thumbnail_width,
            height: intelli.config.portfolio_thumbnail_height
          },
          done: function (data) {
            var naturalImageData = _this.$img.cropper('getImgInfo');
            var json = [
                  '{"x":' + data.x,
                  '"y":' + data.y,
                  '"naturalHeight":' + naturalImageData.naturalHeight,
                  '"naturalWidth":' + naturalImageData.naturalWidth,
                  '"height":' + data.height,
                  '"width":' + data.width + "}"
                ].join();

            _this.$imageData.val(json);
          }
        });

        this.active = true;
      }
    },

    stopCropper: function () {
      if (this.active) {
        this.$img.cropper("disable");
        this.$img.data("cropper", null).remove();
        this.active = false;
      }
    }
  };

  $(function () {
    var imageCrop = new CropAvatar($("#image-container"));
  });
});
