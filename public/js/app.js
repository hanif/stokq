"use strict";

function ajaxForm(elem, o) {
    var options = o || {};
    elem.on('submit', function (e) {
        e.preventDefault();
        e.stopPropagation();
        elem.trigger('clearMessages');

        var data = new FormData(e.target),
            oData = options.data;

        if (oData instanceof Object) {
            for (var prop in oData) {
                if (oData.hasOwnProperty(prop)) {
                    data.append(prop, oData[prop]);
                }
            }
        }

        if (options['before']) {
            var next = options['before'].bind(elem)();
            if (!next) {
                return;
            }
        }

        $.ajax({
            data: data,
            url: elem.attr('action'),
            method: elem.attr('method'),
            processData: false,
            contentType: false,
            cache: false,
            beforeSend: function () {
                elem.trigger('processing');
                elem.trigger('busy');
                if (options['busy']) {
                    options['busy'].bind(elem)();
                }
            }
        })
            .always(function (data, status, xhr) {
                if (status == 'error') {
                    elem.trigger('error', data['responseJSON']);
                    if (options['error']) {
                        options['error'].bind(elem)(data, status, xhr);
                    }
                } else if (status == 'success') {
                    if (options['ok']) {
                        options['ok'].bind(elem)(data, status, xhr);
                    }
                }

                var handler = elem.data('handler');
                if (window[handler]) {
                    window[handler](data, status, xhr, elem);
                }

                elem.trigger('finished');
                elem.trigger('done');
                if (options['done']) {
                    options['done'].bind(elem)(data, status, xhr);
                }

            });
    });

    elem.on('clearMessages', function () {
        $('.form-group', elem).removeClass('has-error');
        $('.error-messages', elem).remove();
    });

    elem.on('error', function (e, messages) {
        if (typeof messages === 'object') {
            $.each(messages, function (name, val) {
                var fieldMessages;

                if (typeof val === 'object') {
                    fieldMessages = $.map(val, function (e) {
                        return e;
                    });
                } else {
                    fieldMessages = [val];
                }

                var message = $('<ul class="error-messages"><ul>').html('<li>' + fieldMessages.join('</li><li>') + '</li>');
                var field = $('[name=' + name + ']', elem);

                if (field.length < 1) {
                    field = $('[data-error-for=' + name + ']', elem);
                }

                message.insertAfter(field);
                field.closest('.form-group').addClass('has-error');
            });
        }
    });
}

function toggleCommand(selector) {
    $(selector).on('click', function() {
        var toggleElem = $(this).data('toggle');
        $(toggleElem).fadeToggle("fast");
        $(this).toggleClass("active");
    });
}

function toggleLayoutPane(sender, targetToHide, targetToExpand, origColSize) {
    $(sender).on('click', function(e) {
        e.preventDefault();

        var t1 = $(targetToHide),
            t2 = $(targetToExpand);

        t1.toggle();

        if (t1.is(':hidden')) {
            t2.attr('class', 'col-xs-12');
        } else {
            t2.attr('class', 'col-xs-' + (origColSize ? origColSize : 8));
        }
    });
}

function formBusy(scope) {
    return function(form) {
        $('button[type=submit]', scope ? scope : $(this)).attr('disabled', 'disabled');
    };
}

function formFinish(scope) {
    return function() {
        $('button[type=submit]', scope ? scope : $(this)).removeAttr('disabled');
    };
}

$(document).ready(function () {
    $('.init-hidden').hide();

    if ($.fn['select2']) {
        $('select[data-ui=select2]').select2();
    }

    if ($.fn['datepicker']) {
        var uiDatePicker = $('[data-ui=datepicker]');
        uiDatePicker.each(function(key, el) {
            var format = $(el).data('format');
            $(el).datepicker({
                dateFormat: format ? format : 'yy-mm-dd',
                changeMonth: true,
                changeYear: true
            });
        });
    }

    toggleCommand('[data-toggle^="."], [data-toggle^="#"]');
    toggleLayoutPane('.toggle-sidebar', '#sidebar-pane', '#main-pane', 8);
});