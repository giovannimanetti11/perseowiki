  // Login Popup

  document.addEventListener("DOMContentLoaded", () => {

  const loginPopupBtn = document.getElementById("login-popup-btn");
  const loginCloseBtn = document.getElementById("login-popup-close-btn");
  const signupPopup = document.getElementById("signup-popup");
  const loginPopup = document.getElementById("login-popup");


  loginPopupBtn.addEventListener("click", () => {
    popup.style.display = "none";
    signupPopup.style.display = "none";
    loginPopup.style.display = "block";
  });

  loginCloseBtn.addEventListener("click", () => {
    loginPopup.style.display = "none";
  });

  const signupBtnFromLogin = document.getElementById("signup-btn-from-login");
  signupBtnFromLogin.addEventListener("click", (event) => {
    event.preventDefault(); 
    loginPopup.style.display = "none";
    signupPopup.style.display = "block";
  });

  window.addEventListener("click", (event) => {
    if (event.target === loginPopup) {
      loginPopup.style.display = "none";
    }
  });

  // Signup Popup

  const signupCloseBtn = document.getElementById("signup-popup-close-btn");
  const signupPopupBtn = document.getElementById("signup-btn-from-login");
  const loginBtnFromSignup = document.getElementById("login-btn-from-signup");

  signupPopupBtn.addEventListener("click", () => {
    signupPopup.style.display = "block";
  });

  signupCloseBtn.addEventListener("click", () => {
    signupPopup.style.display = "none";
  });

  loginBtnFromSignup.addEventListener("click", (event) => {
    event.preventDefault();
    signupPopup.style.display = "none";
    loginPopup.style.display = "block";
  });

  window.addEventListener("click", (event) => {
    if (event.target === signupPopup) {
      signupPopup.style.display = "none";
    }
  });

});

// Manage AJAX for signup
document.addEventListener("DOMContentLoaded", () => {

  const signupForm = document.getElementById("signup-form");
  const signupSuccessMessage = document.getElementById("signup-success-message");
  const signupErrorMessage = document.getElementById("signup-error-message");

  signupForm.addEventListener("submit", (event) => {
    event.preventDefault();
    const email = document.getElementById("signup-email").value;
    const password = document.getElementById("signup-password").value;

    const data = new FormData();
    data.append("action", "register_user");
    data.append("email", email);
    data.append("password", password);

    fetch(myAjax.ajax_url, {
      method: "POST",
      body: data,
    })
    .then((response) => {
      return response.json();
    })
    .then((result) => {
      if (result.success) {
        signupSuccessMessage.textContent = result.message;
        signupSuccessMessage.classList.remove("hidden");
        signupErrorMessage.classList.add("hidden");
      } else {
        signupErrorMessage.textContent = result.message;
        signupErrorMessage.classList.remove("hidden");
        signupSuccessMessage.classList.add("hidden");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
    });
  });

});