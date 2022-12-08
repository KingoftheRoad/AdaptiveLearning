$(function() {
    Validation.init();
    OnClickEvent.init();
});

OnClickEvent = {
    init: function() {
        //For a Login Button Click open Login Type Buttons
        // $(document).on('click', '#enter-btn', function() {
        //     $('#enter-login-btn').attr('style','display:none !important');
        //     $('#main-login-btn').attr('style','display:block !important');
        // });

        // $(document).on('click', '.loginType', function() {
        //     $('#login-type').val($(this).attr('data-loginType'));
        // });
    }
};


Validation = {
    init: function() {
        Validation.validforms();
    },
    validforms: function() {
        $('#loginform').validate({
            rules: {
                email: {
                    required: true,
                    email: true
                },
                password: {
                    required: true,
                },
            },
            messages: {
                email: {
                    required: "Please enter email address.",
                    email: "Please enter valid email address.",
                    remote: "Email not register"
                },
                password: {
                    required: "Please enter password.",
                },
            },
            submitHandler: function(form) {
                $("#cover-spin").show();
                $.ajax({
                    url: BASE_URL + '/loginCheck',
                    type: 'POST',
                    data: $('#loginform').serialize(),
                    success: function(response) {
                        $("#cover-spin").hide();
                        var data = JSON.parse(JSON.stringify(response));
                        if (data.status === 'success') {
                            toastr.success(data.message);
                            if(data.data.user_role==5){
                                window.location = data.data.redirectUrl+'?isLoggedIn=true';
                            }else{
                                window.location = data.data.redirectUrl;
                            }
                            
                        }else{
                            toastr.error(data.message);
                        }
                    },
                    error: function(response){
                        ErrorHandlingMessage(response);
                    }
                });
            }
        });

        /**
         * USE : Form Validations for forget password send email link in email
         */
        $("#forget-password").validate({
            rules: {
                email: {
                    required: true
                }
            },
            messages: {
                email: {
                    required: 'Please enter email address'
                }
            },
            submitHandler: function (form) {
                $("#cover-spin").show();
                $.ajax({
                    url: BASE_URL + "/forget-password",
                    type: "POST",
                    data: $("#forget-password").serialize(),
                    success: function (response) {
                        $("#cover-spin").hide();
                        var data = JSON.parse(JSON.stringify(response));
                        if (data.status === "success") {
                            toastr.success(data.message);
                        } else {
                            toastr.error(data.message);
                        }
                    },
                    error: function (response) {
                        ErrorHandlingMessage(response);
                    },
                });
            }
        });

        /**
         * USE : Form Validations for forget passsword send email link in email
         */
         $("#reset_password").validate({
            rules: {
                password: {
                    required: true,
                    minlength: 6
                },
                password_confirmation: {
                    required: true,
                    minlength: 6,
                    equalTo: "#password"
                }
            },
            messages: {
                password: {
                    required: 'Please enter password',
                    minlength: 'Minium 6 character required'
                },
                password_confirmation: {
                    required: 'please enter confirm password',
                    minlength: 'Minium 6 character required',
                    equalTo: 'Password and confirm password does not match'
                }
            },
            submitHandler: function (form) {
                $("#cover-spin").show();
                $.ajax({
                    url: BASE_URL + "/reset-password",
                    type: "POST",
                    data: $("#reset_password").serialize(),
                    success: function (response) {
                        $("#cover-spin").hide();
                        var data = JSON.parse(JSON.stringify(response));
                        if (data.status === "success") {
                            toastr.success(data.message);
                            window.setTimeout(function(){
                                window.location.href = data.data.redirectUrl;
                            }, 2000);
                            //window.location = data.data.redirectUrl;
                        } else {
                            toastr.error(data.message);
                        }
                    },
                    error: function (response) {
                        ErrorHandlingMessage(response);
                    },
                });
            }
        });
    }
};

// Error Handling Display Message
function ErrorHandlingMessage(response) {
    $("#cover-spin").hide();
    var data = JSON.parse(JSON.stringify(response));
    var errorResponse = data.responseJSON;
    if (errorResponse.status === 'failed') {
        toastr.error(errorResponse.message);
    }
}