define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
    'jquery-ui-modules/widget',
], function ($, modal, $t) {
    'use strict';

    $.widget('magepow.ajaxCompare',{

        options: {
            popupWrapperSelector: '#mgp-compare-popup-wrapper',
            popupBlankSelector: '#mgp-compare-blank',
            closePopupModal: '.action-close',
            processStart: 'processStart',
            processStop : 'processStop',
            addToCompareButtonSelector: '.tocompare',
            addToCompareButtonDisabledClass: 'disabled',
            addToCompareButtonTextWhileAdding: '',
            addToCompareButtonTextAdded: '',
            addToCompareButtonTextDefault: '',
            btnCloseSelector: '#ajaxcompare_btn_close_popup',
            showLoader: false
        },

         _create: function () {
            var self = this;
            self._init();
            $('body').on('contentUpdated', function () {
                self._init();
            });
        },

        autoClosePopup: function (wrapper) {
            var self = this;
            var autocloseCountdown = setInterval(function (wrapper) {
                    var leftTimeNode = $('body').find('#ajaxcompare_btn_close_popup .compare-autoclose-countdown');
                    var leftTime = parseInt(leftTimeNode.text()) - 1;                   
                    leftTimeNode.text(leftTime);
                    if (leftTime == 0) {
                        clearInterval(autocloseCountdown);
                        self.closePopup();
                    }
            }, 1000);
        },

        closePopup: function () {
            $(this.options.popupWrapperSelector).fadeOut('slow');
            $(this.options.popupBlankSelector).fadeOut('slow');
            $(this.options.closePopupModal).trigger('click');
        },

        _init: function () {
            var self = this;
            $('body').on('click', self.options.btnCloseSelector, function (e) {
                self.closePopup();
            });

            self.element.find(self.options.addToCompareButtonSelector).off('click').click(function(e) {
                e.preventDefault();
                e.stopPropagation();
                self.addCompare($(this));
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

        addCompare: function (el) {
            var self = this, 
                comparePopup = $(self.options.popupWrapperSelector),
                body   = $('body'),
                parent = el.parent(),
                post   = el.data('post');
            var params = post.data;
            if(parent.hasClass(self.options.addToCompareButtonDisabledClass)) return;
            $.ajax({
                url: post.action,
                data: params,
                type: 'POST',
                dataType: 'json',
                showLoader: self.options.showLoader,
                beforeSend: function () {
                    self.disableAddToCompareButton(parent);
                    if (self.options.showLoader) body.trigger(self.options.processStart);
                },
                success: function (res) {
                    if (self.options.showLoader) body.trigger(self.options.processStop);
                    if (res.popup) {
                        if (!comparePopup.length) {
                            body.append('<div class="mgp-compare-popup-wrapper" id="' + self.options.popupWrapperSelector.replace(/^#/, "") +'" >'+res.popup+'</div>');
                        }
                        self.showPopup();                     
                        self.autoClosePopup(self.options.popupWrapperSelector);                      
                    } else {
                        alert($t('No response from server'));
                    }
                }
            }).done(function(){
                 self.enableAddToCompareButton(parent);
            });
        },

        /**
         * @param {String} form
         */
        disableAddToCompareButton: function (form) {
            var addToCompareButtonTextWhileAdding = this.options.addToCompareButtonTextWhileAdding || $t('Adding...'),
                addToCompareButton = $(form).find(this.options.addToCompareButtonSelector);

            addToCompareButton.addClass(this.options.addToCompareButtonDisabledClass);
            addToCompareButton.find('span').text(addToCompareButtonTextWhileAdding);
            addToCompareButton.attr('title', addToCompareButtonTextWhileAdding);
        },

        /**
         * @param {String} form
         */
        enableAddToCompareButton: function (form) {
            var addToCompareButtonTextAdded = this.options.addToCompareButtonTextAdded || $t('Added'),
                self = this,
                addToCompareButton = $(form).find(this.options.addToCompareButtonSelector);

            addToCompareButton.find('span').text(addToCompareButtonTextAdded);
            addToCompareButton.attr('title', addToCompareButtonTextAdded);

            setTimeout(function () {
                var addToCompareButtonTextDefault = self.options.addToCompareButtonTextDefault || $t('Add to Compare');

                addToCompareButton.removeClass(self.options.addToCompareButtonDisabledClass);
                addToCompareButton.find('span').text(addToCompareButtonTextDefault);
                addToCompareButton.attr('title', addToCompareButtonTextDefault);
            }, 1000);
        }

    });

    return $.magepow.ajaxCompare;
});
