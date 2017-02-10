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

    function CropAvatar($element) {
        isModal = $element.hasClass('is-modal'); ///[isModal]
        isInputWidget = $element.hasClass('is-input-widget');   ///[InputWidget]

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
            this.initPreview();
        },

        addListener: function () {
            this.$avatarView.on('click', $.proxy(this.click, this));
            this.$avatarInput.on('change', $.proxy(this.change, this));
            !isInputWidget && this.$avatarForm.on('submit', $.proxy(this.submit, this));
            this.$avatarBtns.on('click', $.proxy(this.rotate, this));
            isInputWidget && this.$avatarSave.on('click', $.proxy(this.submit, this));   ///[InputWidget]
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
            var url = this.$avatar.attr('src');

            this.$avatarPreview.html('<img src="' + url + '">');
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
            ///??????????fix:裁剪完图片关闭裁剪窗口后，再次点击打开，选择文件后选中的图片不出现在裁剪窗口！
            ///原因是avatar-input已经被修改为了ajaxFileUpload临时复制的avatar-input对象，需要再次绑定嫦娥事件
            this.$avatarInput = this.$avatarUpload.find('.avatar-input');
            this.$avatarInput.on('change', $.proxy(this.change, this));
            ///[http://www.brainbook.cc]
            
            isModal &&  this.$avatarModal.modal('show');  ///[isModal]
            this.initPreview();
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
            var csrfToken = $('meta[name="csrf-token"]').attr("content");   ///[csrf]
            var data = {'UploadForm[avatarSrc]':this.$avatarSrc.val(), 'UploadForm[avatarData]':this.$avatarData.val(),  '_csrf-frontend' : csrfToken};
            var _this = this;

            $.ajaxFileUpload({url: url, 
                secureuri: false,
                fileElementId:'avatarInput',
                type: 'post',
                data: data,
                dataType: 'json',

                global: false,  ///@see http://www.lai18.com/content/9621987.html

                beforeSend: function () {
                    _this.submitStart();
                },

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
        },

        submitFail: function (msg) {
            this.alert(msg);
        },

        submitEnd: function () {
            this.$loading.fadeOut();
        },

        cropDone: function () {
            // this.$avatarForm.get(0).reset();    ///?????改为Profile upddate form
            // $("#w0").reset();

            $("#profile-avatar").val(this.url);   ///???????profile-avatar变量化！！！/1_user/frontend/web/uploads/avatar/123/20170210020855.png只取文件名！
            this.$avatar.attr('src', this.url);
            this.stopCropper();
            isModal && this.$avatarModal.modal('hide'); ///[isModal]
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