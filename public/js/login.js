"use strict";
/*
function onLogin() {
    $('#btnSubmit').attr("disabled", true);
    $.post("tryLogin", $('#loginForm').serialize(), (result) => {
        console.log(result);
        if (result.success === true) {
            window.location.replace("/");
        } else {
            $('#btnSubmit').attr("disabled", false);
            if (result.error === "wrongPassword") {
                displayError("Wrong password. Try again.");
            } else if (result.error === "wrongUsername") {
                displayError("This username doesn't exist. Try again or <a href='signup'>sign up</a>");
            } else {
                displayError("Something went wrong");
            }
        }
    });
    return false;
}

function displayError(error) {
    console.log(error);
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
        }
    },
    methods:{
      checkForm: function (e) {
        
        e.preventDefault();  

        if (this.username && this.password) {

            var form = new FormData(document.getElementById('loginForm'));

            fetch("/tryLogin", {
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
                            if (res.error === "wrongUsername") {
                                this.errors.push("Wrong username.");
                            }
                            else if(res.error === "wrongPassword") {
                                this.errors.push("Wrong password.")
                            }
                            else {
                                this.errors.push("Something went wrong.");
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
      }
    }
  });
