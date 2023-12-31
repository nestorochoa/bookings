﻿$(document).ready(function () {

    /* On load focus username input login */
    $('#username').focus();

    /* Keys pressed event handlers */
    $(document).keydown(function (key) {
        // "Enter" = 13
        if (key.keyCode == '13') {
            if ($('#form-signin').is(":visible")) {
                $('#btnLogOn').click();
            } else if ($('#form-coupon').is(":visible")) {
                $('#btnRegisterSend').click();
            } else if ($('#form-forgot').is(":visible")) {
                $('#btnForgotSend').click();
            }
        } else if (key.keyCode == '27') {
            if ($('#form-coupon').is(":visible")) {
                $('#btnRegisterCancel').click();
            } else if ($('#form-forgot').is(":visible")) {
                $('#btnForgotCancel').click();
            }
        }
    });

    $('#btnCoupon').click(function () {
        $('#form-signin').hide();
        $('#form-forgot').hide();
        $('#form-coupon').fadeIn(500);
        $('#r-username').focus();

        // clear inputs
        $('#username').val("");
        $('#password').val("");
    });

    $('#btnRegisterCancel').click(function () {
        $('#form-coupon').hide();
        $('#form-signin').fadeIn(500);
        $('#username').focus();

        // clear inputs
        $('#r-username').val("");
        $('#r-email').val("");
        $('#r-password').val("");
        $('#r-confirm').val("");
    });

    $('#btnForgot').click(function () {
        $('#form-signin').hide();
        $('#form-coupon').hide();
        $('#form-forgot').fadeIn(500);
        $('#rec-email').focus();

        // clear inputs
        $('#username').val("");
        $('#password').val("");
    });

    $('#btnForgotCancel').click(function () {
        $('#form-forgot').hide();
        $('#form-signin').fadeIn(500);
        $('#username').focus();

        // clear inputs
        $('#rec-email').val("");
    });

});