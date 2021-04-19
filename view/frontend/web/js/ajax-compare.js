define([
    'jquery',
    'mage/translate',
    'jquery/ui',
    'mage/validation/validation',
], function ($, $t) {
    'use strict';

    $.widget('magepow.ajaxCompare',{

        options: {
            initConfig: {},
            popupWrapper: null,
            popup: null,
            close: null,
            popupBlank: null,
            popupWrapperSelector: '#mgp-compare-popup-wrapper',
            popupSelector: '#mgp-compare-popup',
            popupBlankSelector: '#mgp-compare-blank',
            closePopupButtonSelector: '#mgp-compare-close',
            ajaxCompare: {
                processStart: 'processStart',
                processStop: 'processStop',
                enabled: null,
                ajaxCompareUrl: null,
                compareBtnSelector: 'a.action.tocompare',
                compareWrapperSelector: '#mgp-compare-popup-wrapper',
                btnCloseSelector: '#ajaxcompare_btn_close_popup',
                popupTTL: null,
                
            }
        },

         _create: function () {
            this._bind();
        },


        _bind: function () {
            if (this.options.ajaxCompare.enabled == true) {
                this.initElements();
                this.initEvents();
            }
            this.createElements();
            this.initEvents();
        },

       initElements: function () {
            this.options.popupWrapper = $(this.options.popupWrapperSelector);
            this.options.popup = $(this.options.popupSelector);
            this.options.popupBlank = $(this.options.popupBlankSelector);
            this.options.close = $(this.options.btnCloseSelector);
            if (!this.options.compareWrapper) {
                this.options.compareWrapper = $('<div />', {
                    'id': 'mgp-compare-popup-wrapper'
                }).appendTo(this.options.popup);
            }
        },
       createElements: function () {
            this.options.popupWrapper = $(this.options.popupWrapperSelector);
            this.options.popupBlank = $(this.options.popupBlankSelector);
            this.options.close = $(this.options.closePopupButtonSelector);
            this.options.popup = $(this.options.popupSelector);
           
        },
      
        isLoaderEnabled: function () {
            return (this.options.ajaxCompare.processStart && this.options.ajaxCompare.processStop);
        },

        showElement: function (elmSelector) {
            this.options.popup.children().hide();
            this.options.popup.children(elmSelector).show();
        },

        autoClosePopup: function (wrapper) {
            var self = this;
             var ajaxcompare_autoclose_countdown = setInterval(function (wrapper) {
                    var leftTimeNode = $('body').find('#ajaxcompare_btn_close_popup .compare-autoclose-countdown');
                    var leftTime = parseInt(leftTimeNode.text()) - 1;                   
                    leftTimeNode.text(leftTime);
                    if (leftTime <= 0) {
                        clearInterval(ajaxcompare_autoclose_countdown);
                        $(self.options.popupWrapperSelector).fadeOut('slow');
                        $(self.options.popupWrapperSelector).removeClass("_show"); 

                    }
            }, 1000);
        },

        closePopup: function () {
            this.options.popupWrapper.fadeOut('slow');
            this.options.popupBlank.fadeOut('slow');
        },

        initEvents: function () {
            var self = this;
            $('body').on('click', self.options.ajaxCompare.btnCloseSelector, function (e) {
                self.closePopup();
            });

            $('body').on('click', this.options.ajaxCompare.compareBtnSelector, function (e) {
                e.preventDefault();
                e.stopPropagation();
                var params = $(this).data('post').data;
                params['isCompare'] = true;
                self.addCompare(params);
            });

            this.options.popupWrapper = $(this.options.popupWrapperSelector);
            this.options.popup = $(this.options.popupSelector);
            this.options.popupBlank = $(this.options.popupBlankSelector);
            this.options.close = $(this.options.btnCloseSelector);
            if (!this.options.compareWrapper) {
                this.options.compareWrapper = $('<div />', {
                    'id': 'mgp-compare-popup-wrapper'
                }).appendTo(this.options.popup);
            }
        },

        addCompare: function (params) {
            var self = this;
            $.ajax({
                url: self.options.ajaxCompare.ajaxCompareUrl,
                data: params,
                type: 'POST',
                dataType: 'json',
                beforeSend: function () {
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.ajaxCompare.processStart);
                    }
                },
                success: function (res) {
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.ajaxCompare.processStop);
                    }

                    if (res.html_popup) {
                       $(self.options.popupWrapperSelector).html(res.html_popup);
                       $(self.options.popupWrapperSelector).show();                       
                       $(self.options.popupWrapperSelector).addClass("_show");                       
                        setTimeout(function () {
                            self.showElement(self.options.ajaxCompare.compareWrapperSelector);
                        }, 1000);
                        self.autoClosePopup(self.options.compareWrapper);
                       
                    } else {
                        alert($t('No response from server'));
                    }
                }
            });
        },
    });

    return $.magepow.ajaxCompare;
});