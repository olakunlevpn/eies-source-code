"use strict";

document.addEventListener("DOMContentLoaded", function () {
  var commentForm = document.getElementById("commentform");
  if (!commentForm) return;
  var fields = [{
    name: "author",
    message: commentValidationMessages.name_required
  }, {
    name: "email",
    validate: validateEmail,
    message: commentValidationMessages.email_required,
    invalidMessage: commentValidationMessages.invalid_email
  }, {
    name: "comment",
    message: commentValidationMessages.comment_required
  }];
  commentForm.addEventListener("submit", function (event) {
    var valid = true;
    fields.forEach(function (field) {
      var input = document.querySelector("[name=\"" + field.name + "\"]");
      if (!input) return;
      var error = input.parentNode.querySelector(".error-message");

      if (!error) {
        error = document.createElement("span");
        error.className = "error-message";
        error.style.color = "red";
        error.style.fontSize = "14px";
        error.style.marginTop = "5px";
        error.style.display = "block";
        input.parentNode.appendChild(error);
      }

      error.style.display = "none";
      error.textContent = "";

      if (input.value.trim() === "") {
        error.textContent = field.message;
        error.style.display = "block";
        valid = false;
      } else if (field.validate && !field.validate(input.value)) {
        error.textContent = field.invalidMessage;
        error.style.display = "block";
        valid = false;
      }
    });

    if (!valid) {
      event.preventDefault();
    }
  });

  function validateEmail(email) {
    var re = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    return re.test(String(email).toLowerCase());
  }
});