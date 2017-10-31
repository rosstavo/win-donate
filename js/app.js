(function($) {

    $(document).ready(function() {

        // Checks if page is being accessed via back button, if so refreshes the page

        if ($('#refresh')[0].checked) {
            window.location.reload();
        }
        $('#refresh')[0].checked = true;

    });



    // Hide the disclaimer and form validation notification
    $('#validation').hide();
    var formLink = $('#donate-form .submit').attr("name");

    // Selects whether to display the One-off or Monthly donation forms

    $('.radio-button').click(function() {

        if ($(this).attr('id') == 'one-off') {

            formLink = formLink.replace("gocardless", "paypal");
            $('#donate-form .submit').attr("name", formLink);

        } else {

            formLink = formLink.replace("paypal", "gocardless");
            $('#donate-form .submit').attr("name", formLink);

        }
    });

    $('input[type="radio"] + label').children('input[type="text"]').click(function() {
        $(this).parent('label').siblings('input[type="radio"]').prop('checked', true);
    });

    $('label[for="giftaid"]').click(function() {
        $('.checkbox').toggleClass("checked");
    });

    $('.submit').prop('disabled', true);

    $('.submit').on( 'click', function() {
        if ( $( this ).prop( 'disabled', false ) ) {
            $( this ).html( '<img src="' + win.plugin_url + '/img/ring.gif" />' );
        }
    } );

    // Regex strings for form validation

    var nameReg = /^[A-Za-z]+$/;
    var numberReg = /^[0-9]+$/;
    var emailReg = /^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/;
    var postCodeReg = /^[A-Z]{1,2}[0-9]{1,2} ?[0-9][A-Z]{2}$/i;
    var priceReg = /^\d+(\.\d{2})?$/;

    // Create an array for all required fields

    var valid = [];

    $(".win-sponsor :required").each(function() {
        valid.push($(this).attr('id'));
    });

    // requiredFields function deals with the validation of required fields.

    function requiredFields() {
        $(".win-sponsor :required").each(function(index) {

            var field = $(this).val();

            if ($(this).data('validation') === 'name') {

                valid[index] = nameReg.test(field);

            } else if ($(this).data('validation') === 'phone') {

                valid[index] = numberReg.test(field);

            } else if ($(this).data('validation') === 'email') {

                valid[index] = emailReg.test(field);

            } else if ($(this).data('validation') === 'post-code') {

                valid[index] = postCodeReg.test(field);

            } else {

                if (field.length === 0) {
                    valid[index] = false;
                } else {
                    valid[index] = true;
                }

            }

            if (valid) {
                $(this).removeClass("error");
            } else {
                $(this).addClass("error");
            }
        });

        return valid.every(Boolean);
    }

    // validateAmount function deals with the validation of a price

    function validateAmount(field) {

        if (priceReg.test(field) && field >= 1) {
            $("#validation").hide(100);
            return true;
        } else if (field < 1) {
            $("#validation").show(100).html("<p>Please enter a number above £1.</p>")
            return false;
        } else {
            $("#validation").hide(100);
            return false;
        }

    }

    // When an input is changed, check if all required fields are valid, and if amount is valid.

    $('.win-sponsor input').each(function() {

        $(this).bind("change keyup input", function() {

            if ($(this).is('.user-input') && $(this).prop('checked') === true) {

                if (validateAmount($('.amount').val())) {
                    $('.amount').removeClass("error");
                } else {
                    $('.amount').addClass("error");
                }

                $(this).val($('.amount').val());

            } else if ($(this).is('.amount')) {

                if (validateAmount($(this).val())) {
                    $('.amount').removeClass("error");
                    $("#validation").hide(100);
                } else {
                    $('.amount').addClass("error");
                }

                $('.user-input').val($(this).val());

            } else if ($(this).not('user-input') && $(this).prop('checked') === true) {
                $('.amount').removeClass("error");
                $("#validation").hide(100);
            }

            var validForm = requiredFields();

            if (validForm && $('.error').length == 0) {
                $('.submit').prop('disabled', false);
            } else {
                $('.submit').prop('disabled', true);
            }
        });

    });

    $(".win-donate .amount").bind("change keyup input", function() {

        var field = $(this).val();

        if (priceReg.test(field) && field >= 1) {
            $(this).removeClass("error");
            $("#validation").hide(100);
        } else if (field < 1) {
            $(this).addClass("error");
            $("#validation").show(100).html("<p>Please enter a number above £1.</p>")
        } else {
            $(this).addClass("error");
            $("#validation").hide(100);
        }

        if ($('.error').length == 0) {
            $('.submit').prop('disabled', false);
        } else {
            $('.submit').prop('disabled', true);
        }

    });


})(jQuery);
