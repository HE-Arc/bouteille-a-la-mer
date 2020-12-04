"use strict";
/*
function onSignup() {
    if ($('#password').val() === $('#password2').val()) {
        $('#btnSubmit').attr("disabled", true);
        $.post("trySignup", $('#signupForm').serialize(), (result) => {
            console.log(result);
            if (result.success === true) {
                window.location.replace("/");
            } else {
                $('#btnSubmit').attr("disabled", false);
                if (result.error === "alreadyExist") {
                    displayError("This user already exist");
                } else {
                    displayError("Something went wrong");
                }
            }
        });
    } else {
        displayError("Warning ! the 2 passwords are not the same");
    }
    return false;
}

function displayError(error) {
    $('#error').html(error);
}
*/

const app = new Vue({
    el: '#app',
    data() {
        return {
            errors: [],
            username: null,
            password: null,
            password2: null
        }
    },
    methods:{
      checkForm: function (e) {
        
        e.preventDefault();  

        if (this.username && this.password && this.password2 && this.password == this.password2) {

            var form = new FormData(document.getElementById('signupForm'));

            fetch("/trySignup", {
                method: "POST",
                body: form
            }).then(async res => {
                
                if (res.status === 204) {
                    this.errors = [];
                } else if (res.status === 400) {
                    this.errors = [];

                    let errorResponse = await res.json();
                    this.errors.push(errorResponse.error);
                }
                else if (res.status === 200) {

                    res.json().then(async res => {
                        this.errors = [];

                        if(res.success) {
                            window.location.replace("/");
                        }
                        else {
                            if (res.error === "alreadyExist") {
                                this.errors.push("This user already exist");
                            }
                            else {
                                this.errors.push("Something went wrong");
                            }
                        }
                    });
                }
            });
        }

        this.errors = [];

        if (!this.username) {
          this.errors.push('Username required.');
        }
        if (!this.password) {
          this.errors.push('Password required.');
        }
        if(this.password != this.password2) {
            this.errors.push('Confirmation password is not identical.')
        }
      }
    }
  });
