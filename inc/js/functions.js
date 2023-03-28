// Menu toggle

document.addEventListener("DOMContentLoaded", function() {
  var menuIcon = document.getElementById("menu-icon");
  var popupMenu = document.getElementById("popup-menu");
  var isClicked = false;


  menuIcon.addEventListener("click", function() {
    if (!isClicked) {
      menuIcon.classList.remove("fa-bars");
      menuIcon.classList.add("fa-xmark");
      menuIcon.classList.add("hidden");
      setTimeout(function() {
        menuIcon.classList.remove("hidden");
        menuIcon.classList.add("visible");
      }, 10);
      popupMenu.style.display = "block";
      isClicked = true;
    } else {
      menuIcon.classList.remove("fa-xmark");
      menuIcon.classList.add("fa-bars");
      menuIcon.classList.add("hidden");
      setTimeout(function() {
        menuIcon.classList.remove("hidden");
        menuIcon.classList.add("visible");
      }, 10);
      popupMenu.style.display = "none";
      isClicked = false;
    }
  });
});

// Mailing List Popup

document.addEventListener('DOMContentLoaded', () => {

  function toggleElementVisibility(element, show) {
    if (show) {
      element.classList.remove("hidden");
    } else {
      element.classList.add("hidden");
    }
  }

  const form = document.getElementById("subscribe-form");
  const successMessage = document.getElementById("mailingList-success-message");
  const errorMessage = document.getElementById("mailingList-error-message");

  form.addEventListener("submit", (event) => {
    const nome = document.getElementById("nome").value;
    const cognome = document.getElementById("cognome").value;
    const email = document.getElementById("email").value;

    const { nomeValid, cognomeValid, emailValid } = validateInputs(nome, cognome, email);

    if (!nomeValid || !cognomeValid || !emailValid) {
      toggleElementVisibility(errorMessage, true);
      toggleElementVisibility(successMessage, false);
      event.preventDefault();
    } else {
      const data = {
        email_address: email,
        status: "subscribed",
        merge_fields: {
          FNAME: nome,
          LNAME: cognome,
        },
      };

      fetch(
        "/wp-content/themes/perseowiki/inc/subscribe.php",
        {
          method: "POST",
          body: JSON.stringify(data),
        }
      )
      .then((response) => {
        if (response.status === 200) {
          toggleElementVisibility(successMessage, true);
          toggleElementVisibility(errorMessage, false);
        } else if (response.status === 400) {
          response.json().then((errorData) => {
            if (errorData.title === "Member Exists") {
              toggleElementVisibility(errorMessage, true);
              errorMessage.textContent = "L'email inserita è già presente nella lista. Per favore, inserisci un'altra email.";
            } else {
              toggleElementVisibility(errorMessage, true);
              errorMessage.textContent = "Si è verificato un errore durante l'iscrizione. Per favore, riprova più tardi.";
            }
          });
        } else {
          toggleElementVisibility(errorMessage, true);
          toggleElementVisibility(successMessage, false);
        }
      })
      .catch((error) => {
        toggleElementVisibility(errorMessage, true);
        toggleElementVisibility(successMessage, false);
      });
      event.preventDefault(); 
    }
  });

  const popupBtn = document.getElementById("mailingList-popup-btn");
  const popup = document.getElementById("mailingList-popup");
  const closeBtn = document.getElementById("mailingList-popup-close-btn");

  popupBtn.addEventListener("click", () => {
    loginPopup.style.display = "none";
    signupPopup.style.display = "none";
    popup.style.display = "block";
  });

  closeBtn.addEventListener("click", () => {
    popup.style.display = "none";
  });

  window.addEventListener("click", (event) => {
    if (event.target === popup) {
      popup.style.display = "none";
    }
  });

  function validateInputs(nome, cognome, email) {
    const nomeValid = nome.trim() !== "";
    const cognomeValid = cognome.trim() !== "";
    const emailValid = email.trim() !== "" && /\S+@\S+\.\S+/.test(email);
    return { nomeValid, cognomeValid, emailValid };
  }

  // Login Popup

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

// AJAX Search

document.addEventListener("DOMContentLoaded", function() {
  var searchBar = document.querySelector(".searchBar");
  var searchResults = document.querySelector("#searchResults");
  var clearSearch = document.querySelector("#clearSearch");

  searchBar.addEventListener("input", function() {
    var keywords = searchBar.value;

    if (keywords.length < 3) {
      searchResults.innerHTML = "";
      return;
    }

    fetch("search.php?keywords=" + keywords)
      .then(response => response.json())
      .then(data => {
        searchResults.innerHTML = "";
        if (data.length > 0) {
          data.forEach(post => {
            var postElement = document.createElement("li");
            postElement.classList.add("post-row");

            var postLink = document.createElement("a"); 
            postLink.href = post.permalink; 

            var imgElement = document.createElement("img");
            imgElement.classList.add("featured");
            imgElement.alt = post.title;
            if (post.featured_image) {
              imgElement.src = post.featured_image;
            } else {
              imgElement.style.display = "none"; 
            }
            postLink.appendChild(imgElement);

            postElement.appendChild(postLink); 

            var titleElement = document.createElement("h2");
            titleElement.classList.add("title");
            titleElement.innerHTML = post.title;
            postLink.appendChild(titleElement); 


            if (post.meta_box_nome_scientifico) {
              var metaBoxElement = document.createElement("p");
              metaBoxElement.classList.add("meta-box-nome-scientifico");
              metaBoxElement.innerHTML = post.meta_box_nome_scientifico;
              postLink.appendChild(metaBoxElement); // aggiungi il campo meta box al risultato
            }

            searchResults.appendChild(postElement);

          });
          searchBar.classList.add("noradius");
          searchResults.style.display = "block";
          clearSearch.style.display = "block";
        } else {
          searchResults.style.display = "none";
        }
      });
  });

  clearSearch.addEventListener("click", function() {
    searchBar.value = "";
    searchResults.style.display = "none";
    clearSearch.style.display = "none";
    searchBar.classList.remove("noradius");
  });

});


// Category menu scroll

document.addEventListener('DOMContentLoaded', function() {
  var menu = document.querySelector('#menu-categorie');
  var rightArrow = document.querySelector('#iconRightArrow');
  var leftArrow = document.querySelector('#iconLeftArrow');

  rightArrow.addEventListener('click', function() {
    menu.scrollBy({ left: 250, behavior: 'smooth' });
    leftArrow.style.display = 'block';
    if (menu.scrollLeft + menu.clientWidth >= menu.scrollWidth) {
      rightArrow.style.display = 'none';
    }
  });

  leftArrow.addEventListener('click', function() {
    menu.scrollBy({ left: -250, behavior: 'smooth' });
    rightArrow.style.display = 'block';
    if (menu.scrollLeft === 0) {
      leftArrow.style.display = 'none';
    }
  });

  // Hide the left arrow initially if the menu is at the beginning
  if (menu.scrollLeft === 0) {
    leftArrow.style.display = 'none';
  }

  // Hide the right arrow initially if the menu is at the end
  if (menu.scrollLeft + menu.clientWidth >= menu.scrollWidth) {
    rightArrow.style.display = 'none';
  }
});


// AJAX get posts alphabetically

document.addEventListener('DOMContentLoaded', () => {
  const alphabetLinks = document.querySelectorAll('.alphabet-link');
  const postsContainer = document.querySelector('#posts-container');
  const postsInfo = document.querySelector('#posts-info');

  alphabetLinks.forEach(link => {
    link.addEventListener('click', event => {
      event.preventDefault();
      alphabetLinks.forEach(link => link.classList.remove('active'));
      link.classList.add('active');
      const letter = link.dataset.letter;
      const ajaxUrl = myAjax.ajax_url;
      const urlParams = new URLSearchParams({
        action: 'get_posts_by_letter',
        letter: letter,
      });
      const xhr = new XMLHttpRequest();
      xhr.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
          const response = JSON.parse(xhr.responseText);
          postsContainer.innerHTML = response.data;
          const count = response.count;
          const noun = count === 1 ? "erba" : "erbe";
          const verb = count === 1 ? "inizia" : "iniziano";
          postsInfo.innerHTML = `<p>${count} ${noun} che ${verb} per ${letter}</p>`;
        }
      };
      xhr.open('GET', ajaxUrl + '?' + urlParams.toString());
      xhr.setRequestHeader('Content-Type', 'application/json');
      xhr.setRequestHeader('Accept', 'application/json');
      xhr.send();
    });
  });
  
  const defaultLink = document.querySelector('.alphabet-link[data-letter="A"]');
  defaultLink.classList.add('active');
  defaultLink.click();
});


// Print article

function printArticle() {
  var content = document.getElementById("post-content").innerHTML;
  var mywindow = window.open('', 'Print', 'height=600,width=800');
  mywindow.document.write('<html><head><title>&nbsp;</title>');
  mywindow.document.write('<link rel="stylesheet" type="text/css" href="' + window.location.origin + '/wp-content/themes/perseowiki/print.css" media="print">');
  mywindow.document.write('</head><body>');
  mywindow.document.write(content);
  mywindow.document.write('</body></html>');
  mywindow.print();
  mywindow.close();
  return true;
}



// Share article

function openSharePopup() {
  var popup = document.getElementById("share-popup");
  popup.style.display = "block";
}


function closeSharePopup() {
  var popup = document.getElementById("share-popup");
  popup.style.display = "none";
}


function copyToClipboard() {
  var urlInput = document.getElementById("article-url");
  urlInput.select();
  document.execCommand("copy");
  urlInput.classList.add("copied");
  setTimeout(function() {
    urlInput.classList.remove("copied");
  }, 1000);
}

function shareUrl(url) {
  window.open(url, "", "menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600");
  return false;
}


// Replace terms with hyperlink


document.addEventListener('DOMContentLoaded', function() {

  // Esegui la richiesta AJAX per ottenere i titoli e i permalink
  fetch('/wp-admin/admin-ajax.php?action=get_all_posts_titles_and_links')
    .then(response => {
        return response.json();
    })
    .then(data => {
      const currentURL = window.location.href;

      // Esegui la funzione linkifyContent per tutti gli elementi del DOM in cui vuoi aggiungere i link
      const contentElements = document.querySelectorAll('#post-content, .content-area, .home-content');
      contentElements.forEach(element => {
        linkifyContent(element, data, currentURL);
      });
    });


  function escapeRegExp(string) {
    return string.replace(/[.*+\-?^${}()|[\]\\]/g, '\\$&');
  }

  function linkifyContent(element, titlesAndLinks, currentURL) {
    const originalHTML = element.innerHTML;
    let newHTML = originalHTML;

    // Cerca corrispondenze nel contenuto e sostituisci con i link corrispondenti
    titlesAndLinks.forEach(({ title, link, excerpt }) => {
      if (link !== currentURL) {
        const escapedTitle = escapeRegExp(title);
        const regex = new RegExp(`\\b(${escapedTitle})\\b(?!([^<]+)?>)`, 'gi');
        const linkHTML = `<a href="${link}" data-excerpt="${excerpt}" class="link-with-excerpt">$1</a>`;
        newHTML = newHTML.replace(regex, linkHTML);
      }
    });

    element.innerHTML = newHTML;



    // Inizializza i tooltip per tutti i link
    
    const linksWithExcerpt = document.querySelectorAll('.link-with-excerpt');
    linksWithExcerpt.forEach(link => {
      const excerpt = link.getAttribute('data-excerpt');
      link.addEventListener('mouseover', (event) => {
        showTooltip(excerpt, event);
      });
      link.addEventListener('mouseout', () => {
        hideTooltip();
      });
    });
  } 

  function isTouchDevice() {
    try {
      document.createEvent("TouchEvent");
      return true;
    } catch (e) {
      return false;
    }
  }
  

  let tooltip = null;

  function showTooltip(excerpt, event) {
    if (tooltip) {
      tooltip.remove();
      tooltip = null;
    }

    if (!isTouchDevice()) {
      tooltip = document.createElement('div');
      tooltip.classList.add('tooltip-term-excerpt');
      tooltip.innerHTML = excerpt;

      document.body.appendChild(tooltip);

      const tooltipRect = tooltip.getBoundingClientRect();
      const tooltipHeight = tooltipRect.height;

      const linkRect = event.target.getBoundingClientRect();
      const scrollX = window.pageXOffset;
      const scrollY = window.pageYOffset;
      const tooltipX = linkRect.left + scrollX + (linkRect.width / 2) - 150; // Sposta il tooltip 150px a sinistra
      const tooltipY = linkRect.top + scrollY - 10 - tooltipHeight; // 10 px sopra il link e spostato in alto in base all'altezza del tooltip

      tooltip.style.left = `${tooltipX}px`;
      tooltip.style.top = `${tooltipY}px`;
    }
  }

  function hideTooltip() {
    if (tooltip) {
      tooltip.remove();
      tooltip = null;
    }
  }
});





