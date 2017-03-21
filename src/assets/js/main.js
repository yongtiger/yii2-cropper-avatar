(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as anonymous module.
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        // Node / CommonJS
        factory(require('jquery'));
    } else {
        // Browser globals.
        factory(jQuery);
    }
})(function ($) {

    'use strict';

    var console = window.console || { log: function () {} };

    var isModal; ///[isModal]
    var isInputWidget; ///[InputWidget]
    var isRounded; ///[rounded avatar:image base64]

    function CropAvatar($element) {
        ///Fetching parameters from view
        isModal = $element.hasClass('is-modal'); ///[isModal]
        isInputWidget = $element.hasClass('is-input-widget');   ///[InputWidget]
        isRounded = $element.hasClass('is-rounded');   ///[rounded avatar:image base64]

        this.$container = $element;

        this.$avatarView = this.$container.find('.avatar-view');
        this.$avatar = this.$avatarView.find('img');
        this.$avatarModal = this.$container.find('#avatar-modal');
        this.$loading = this.$container.find('.loading');

        this.$avatarForm = this.$avatarModal.find('.avatar-form');
        this.$avatarUpload = this.$avatarModal.find('.avatar-upload');
        this.$avatarSrc = this.$avatarUpload.find('.avatar-src');
        this.$avatarData = this.$avatarUpload.find('.avatar-data');
        this.$avatarInput = this.$avatarUpload.find('.avatar-input');
        this.$avatarSave = this.$avatarModal.find('.avatar-save');
        this.$avatarBtns = this.$avatarModal.find('.avatar-btns .btn-group');    ///[fix:avatar-btns]

        this.$avatarWrapper = this.$avatarModal.find('.avatar-wrapper');
        this.$avatarPreview = this.$avatarModal.find('.avatar-preview');

        this.init();

    }

    CropAvatar.prototype = {
        constructor: CropAvatar,

        support: {
            fileList: !!$('<input type="file">').prop('files'),
            blobURLs: !!window.URL && URL.createObjectURL,
            formData: !!window.FormData
        },

        init: function () {
            this.support.datauri = this.support.fileList && this.support.blobURLs;
            // this.support.formData = false;   ///test for [fix:main.js:this.support.formData = false]
            if (!this.support.formData) {
                this.initIframe();
            }

            this.initTooltip();
            isModal && this.initModal();  ///[isModal]
            this.addListener();

            ///[v0.10.4 (FIX# main.js:automatically start cropper when init or submitEnd)]
            // this.initPreview();
            this.$avatarPreview.html('<img src="' + this.$avatar.attr('src') + '">');
            if (!isModal) {
                this.url = this.$avatar.attr('src');
                this.$avatarSrc.val(getAbsoluteUrl(this.url));
                this.startCropper();
            }

        },

        addListener: function () {
            isModal && this.$avatarView.on('click', $.proxy(this.click, this));
            this.$avatarInput.on('change', $.proxy(this.change, this));
            !isInputWidget && this.$avatarForm.on('submit', $.proxy(this.submit, this));
            this.$avatarBtns.on('click', $.proxy(this.rotate, this));
            isInputWidget && this.$avatarSave.on('click', $.proxy(this.submit, this));   ///[InputWidget]

            ///[v0.10.4 (FIX# main.js:automatically start cropper when init or submitEnd)]
            isModal && this.$avatarModal.on('shown.bs.modal', $.proxy(this.initPreview, this));

        },

        initTooltip: function () {
            this.$avatarView.tooltip({
                placement: 'bottom'
            });
        },

        initModal: function () {
            this.$avatarModal.modal({
                show: false
            });
        },

        initPreview: function () {

            ///[v0.10.4 (FIX# main.js:automatically start cropper when init or submitEnd)]
            this.url = this.$avatar.attr('src');
            this.$avatarPreview.html('<img src="' + this.url + '">');
            this.$avatarSrc.val(getAbsoluteUrl(this.url));
            this.startCropper();

        },

        initIframe: function () {
            var target = 'upload-iframe-' + (new Date()).getTime();
            var $iframe = $('<iframe>').attr({
                name: target,
                src: ''
            });
            var _this = this;

            // Ready ifrmae
            $iframe.one('load', function () {

                // respond response
                $iframe.on('load', function () {
                    var data;

                    try {
                        data = $(this).contents().find('body').text();
                    } catch (e) {
                        console.log(e.message);
                    }

                    if (data) {
                        try {
                            data = $.parseJSON(data);
                        } catch (e) {
                            console.log(e.message);
                        }

                        _this.submitDone(data);
                    } else {
                        _this.submitFail('Image upload failed!');
                    }

                    _this.submitEnd();

                });
            });

            this.$iframe = $iframe;
            this.$avatarForm.attr('target', target).after($iframe.hide());
        },

        click: function () {
            ///fix:Crop off the picture Close the crop window, click again to open, select the file after the selected image does not appear in the crop window!
            ///Because `avatar-input` has been modified to ajaxFileUpload temporary copy of the `avatar-input` object, need to re-bind the change event
            this.$avatarInput = this.$avatarUpload.find('.avatar-input');
            this.$avatarInput.on('change', $.proxy(this.change, this));
            ///[http://www.brainbook.cc]
            
            ///[v0.10.4 (FIX# main.js:automatically start cropper when init or submitEnd)]
            if (isModal) {
                this.$avatarModal.modal('show');  ///[isModal]
            } else {
                this.initPreview();
            }

        },

        change: function () {
            var files;
            var file;

            if (this.support.datauri) {
                files = this.$avatarInput.prop('files');

                if (files.length > 0) {
                    file = files[0];

                    if (this.isImageFile(file)) {
                        if (this.url) {
                            URL.revokeObjectURL(this.url); // Revoke the old one
                        }

                        this.url = URL.createObjectURL(file);
                        this.startCropper();
                    }
                }
            } else {
                file = this.$avatarInput.val();

                if (this.isImageFile(file)) {
                  this.syncUpload();
                }
            }
        },

        submit: function () {
            if (!this.$avatarSrc.val() && !this.$avatarInput.val()) {
                return false;
            }

            ///[rounded avatar:image base64]
            if(isRounded) {
                var croppedCanvas;
                var roundedCanvas;
                croppedCanvas = this.$img.cropper('getCroppedCanvas');
                roundedCanvas = getRoundedCanvas(croppedCanvas);
                this.$avatarSrc.val(roundedCanvas.toDataURL());
            }
            ///[http://www.brainbook.cc]

            if (this.support.formData) {
                if (isInputWidget) {
                    this.ajaxFileUpload();  ///[InputWidget]
                } else {
                    this.ajaxUpload();
                }
                return false;
            }
        },

        rotate: function (e) {
            var data;

            if (this.active) {
                data = $(e.target).data();

                ///[fix:conflict data-method]
                if (data.trixMethod) {
                    this.$img.cropper(data.trixMethod, data.option);
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

        startCropper: function () {
            var _this = this;

            if (this.active) {
                this.$img.cropper('replace', this.url);
            } else {
                this.$img = $('<img src="' + this.url + '">');
                this.$avatarWrapper.empty().html(this.$img);
                this.$img.cropper({
                    aspectRatio: 1,
                    preview: this.$avatarPreview.selector,
                    crop: function (e) {
                        var json = [
                            '{"x":' + e.x,
                            '"y":' + e.y,
                            '"height":' + e.height,
                            '"width":' + e.width,
                            '"rotate":' + e.rotate + '}'
                        ].join();

                        _this.$avatarData.val(json);
                    }
                });

                this.active = true;
            }

            isModal && this.$avatarModal.one('hidden.bs.modal', function () { ///[isModal]
                _this.$avatarPreview.empty();
                _this.stopCropper();
            });
        },

        stopCropper: function () {
            if (this.active) {
                this.$img.cropper('destroy');
                this.$img.remove();
                this.active = false;
            }
        },

        ajaxUpload: function () {
            var url = this.$avatarForm.attr('action');
            var data = new FormData(this.$avatarForm[0]);
            var _this = this;

            $.ajax(url, {
                type: 'post',
                data: data,
                dataType: 'json',

                ///NOTE: THIS MUST BE DONE FOR FILE UPLOADING 
                ///send ajax request like you submit regular form with enctype="multipart/form-data"
                ///@see http://stackoverflow.com/questions/21044798/how-to-use-formdata-for-ajax-file-upload
                ///@see http://stackoverflow.com/questions/5392344/sending-multipart-formdata-with-jquery-ajax
                processData: false,
                contentType: false,
                ///[http://www.brainbook.cc]

                beforeSend: function () {
                    _this.submitStart();
                },

                success: function (data) {
                    _this.submitDone(data);
                },

                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    _this.submitFail(textStatus || errorThrown);
                },

                complete: function () {
                    _this.submitEnd();
                }
            });
        },

        ///[ajaxfileupload]
        ajaxFileUpload: function () {
            var url = this.$avatarForm.attr('action');
            var csrfToken = $('meta[name="csrf-token"]').attr("content");   ///[csrf]@see http://stackoverflow.com/questions/32147040/csrf-issue-when-using-ajax-submit-via-link-in-yii2/32339079
            var data = {'UploadForm[avatarSrc]':this.$avatarSrc.val(), 'UploadForm[avatarData]':this.$avatarData.val(),  '_csrf-frontend' : csrfToken};
            var _this = this;

            $.ajaxFileUpload({url: url, 
                secureuri: false,
                fileElementId:'avatarInput',
                type: 'post',
                data: data,
                dataType: 'json',

                // global: false,  ///[bug:confict with yii.js(350)]@see http://www.lai18.com/content/9621987.html

                ///[fix:beforeSend]@see http://www.developwebapp.com/5538869/
                beforeSend: function () {
                    _this.submitStart();
                },
                ///[http://www.brainbook.cc]

                success: function (data, status) {
                    _this.submitDone(data);
                },

                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    _this.submitFail(textStatus || errorThrown);
                },

                complete: function () {
                    _this.submitEnd();
                }
            });
        },

        syncUpload: function () {
            this.$avatarSave.click();
        },

        submitStart: function () {
            this.$loading.fadeIn();
        },

        submitDone: function (data) {
            console.log(data);

            if ($.isPlainObject(data) && data.state === 200) {
                if (data.result) {
                    this.url = data.result;

                    if (this.support.datauri || this.uploaded) {
                        this.uploaded = false;
                        this.cropDone();
                    } else {
                        this.uploaded = true;
                        this.$avatarSrc.val(this.url);
                        this.startCropper();
                    }

                    this.$avatarInput.val('');
                } else if (data.message) {
                    this.alert(data.message);
                }
            } else {
                this.alert('Failed to response');
            }

            ///fix:Crop off the picture Close the crop window, click again to open, select the file after the selected image does not appear in the crop window!
            ///Because `avatar-input` has been modified to ajaxFileUpload temporary copy of the `avatar-input` object, need to re-bind the change event
            this.$avatarInput = this.$avatarUpload.find('.avatar-input');
            this.$avatarInput.on('change', $.proxy(this.change, this));
            ///[http://www.brainbook.cc]

        },

        submitFail: function (msg) {
            this.alert(msg);
        },

        submitEnd: function () {
            this.$loading.fadeOut();
        },

        cropDone: function () {
            !isInputWidget && this.$avatarForm.get(0).reset();

            var fn = this.url.substring(this.url.lastIndexOf('/')+1);   ///[InputWidget]only return the filename of url
            $("#input-widget-avatar-field input").val(fn);   ///[InputWidget]input-widget-avatar-field

            this.$avatar.attr('src', this.url);
            this.stopCropper();

            ///[v0.10.4 (FIX# main.js:automatically start cropper when init or submitEnd)]
            if (isModal) {
                this.$avatarModal.modal('hide');  ///[isModal]
            } else {
                this.initPreview();
            }

        },

        alert: function (msg) {
            var $alert = [
                '<div class="alert alert-danger avatar-alert alert-dismissable">',
                    '<button type="button" class="close" data-dismiss="alert">&times;</button>',
                    msg,
                '</div>'
            ].join('');

            this.$avatarUpload.after($alert);
        }
    };

    $(function () {
        return new CropAvatar($('#crop-avatar'));
    });
});

function getRoundedCanvas(sourceCanvas) {
    var canvas = document.createElement('canvas');
    var context = canvas.getContext('2d');
    var width = sourceCanvas.width;
    var height = sourceCanvas.height;

    canvas.width = width;
    canvas.height = height;
    context.beginPath();
    context.arc(width / 2, height / 2, Math.min(width, height) / 2, 0, 2 * Math.PI);
    context.strokeStyle = 'rgba(0,0,0,0)';
    context.stroke();
    context.clip();
    context.drawImage(sourceCanvas, 0, 0, width, height);

    return canvas;
}

///[v0.10.4 (FIX# main.js:automatically start cropper when init or submitEnd)]
///@see http://code.askmein.com/get-absolute-url-using-javascript/
///@see https://davidwalsh.name/get-absolute-url
var getAbsoluteUrl = (function() {
    var a;
    return function(url) {
        if(!a) a = document.createElement('a');
        a.href = url;
        return a.href;
    };
})();