define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
    'jquery/ui',
    'mage/validation/validation',
], function ($, modal, $t) {
    'use strict';

    $.widget('magepow.ajaxCompare',{

        options: {
            popupWrapperSelector: '#mgp-compare-popup-wrapper',
            popupBlankSelector: '#mgp-compare-blank',
            closePopupModal: '.action-close',
            ajaxCompare: {
                processStart: 'processStart',
                processStop: 'processStop',
                enabled: null,
                ajaxCompareUrl: null,
                compareBtnSelector: 'a.tocompare',
                btnCloseSelector: '#ajaxcompare_btn_close_popup',
                showLoader: null               
            }
        },

         _create: function () {
            this._bind();
        },


        _bind: function () {
            if (this.options.ajaxCompare.enabled == true) this.initEvents();
            
        },

        isLoaderEnabled: function () {
            return (this.options.ajaxCompare.processStart && this.options.ajaxCompare.processStop);
        },
        autoClosePopup: function (wrapper) {
            var self = this;
             var ajaxcompare_autoclose_countdown = setInterval(function (wrapper) {
                    var leftTimeNode = $('body').find('#ajaxcompare_btn_close_popup .compare-autoclose-countdown');
                    var leftTime = parseInt(leftTimeNode.text()) - 1;                   
                    leftTimeNode.text(leftTime);
                    if (leftTime == 0) {
                        clearInterval(ajaxcompare_autoclose_countdown);
                        self.closePopup();
                    }
            }, 1000);
        },

        closePopup: function () {
            $(this.options.popupWrapperSelector).fadeOut('slow');
            $(this.options.popupBlankSelector).fadeOut('slow');
            $(this.options.closePopupModal).trigger('click');
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

        },

        showPopup: function() {
            var self = this,
                comparePopup = $(self.options.popupWrapperSelector);
            var modaloption = {
                type: 'popup',
                modalClass: 'modal-popup_ajaxcompare_magepow',
                responsive: true,
                innerScroll: true,
                clickableOverlay: true,
                closed: function(){
                   $('.modal-popup_ajaxcompare_magepow').remove();
                }
            };
            modal(modaloption, comparePopup);
            comparePopup.modal('openModal');
        },

        addCompare: function (params) {
            var self = this, 
                comparePopup = $(self.options.popupWrapperSelector),
                body = $('body');
            $.ajax({
                url: self.options.ajaxCompare.ajaxCompareUrl,
                data: params,
                type: 'POST',
                dataType: 'json',
                showLoader: self.options.ajaxCompare.showLoader,
                beforeSend: function () {
                    if (self.options.ajaxCompare.showLoader && self.isLoaderEnabled()) {
                        body.trigger(self.options.ajaxCompare.processStart);
                    }
                },
                success: function (res) {
                    if (self.options.ajaxCompare.showLoader && self.isLoaderEnabled()) {
                        body.trigger(self.options.ajaxCompare.processStop);
                    }
                    if (res.html_popup) {
                        if (!comparePopup.length) {
                            body.append('<div class="mgp-compare-popup-wrapper" id="mgp-compare-popup-wrapper">'+res.html_popup+'</div>');
                        }
                        self.showPopup();                     
                        self.autoClosePopup(self.options.popupWrapperSelector);                      
                    } else {
                        alert($t('No response from server'));
                    }
                }
            });
        },
    });

    return $.magepow.ajaxCompare;
});
