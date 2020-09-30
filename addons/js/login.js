$().ready(function() {
    $('#btnLogin').click(function() {
        var username = $("#login-username").val();
        var password = $("#login-password").val();
        var dataString = 'username=' + username + '&password=' + password;

        if ($.trim(username).length > 0 && $.trim(password).length > 0) {
            $.ajax({
                type: "POST",
                url: "cek-login",
                data: dataString,
                cache: false,
                beforeSend: function() { $("#btnLogin").html('Loading...'); },
                success: function(data) {
                    if (data == "ok") {
                        window.location.href = "home";
                    } else {
                        $("#btnLogin").html('Login');
                        $("#error").html("<div class='alert alert-danger'>Invalid username and password.</div>")
                    }
                }
            });

        }
        return false;
    });
});