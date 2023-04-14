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
          response.text()
            .then((text) => {
              try {
                return JSON.parse(text);
              } catch (error) {
                console.error("Errore nel parsing della risposta JSON:", text);
                throw error;
              }
            })
            .then((errorData) => {
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
  const mobilePopupBtn = document.getElementById("mailingList-popup-btn-mobile");
  const popupBtn = document.getElementById("mailingList-popup-btn");
  const popup = document.getElementById("mailingList-popup");
  const closeBtn = document.getElementById("mailingList-popup-close-btn");

  popupBtn.addEventListener("click", () => {
    popup.style.display = "block";
  });

  mobilePopupBtn.addEventListener("click", () => {
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

});



// Replace terms with hyperlink


document.addEventListener('DOMContentLoaded', function() {
  const currentPostID = parseInt(document.body.getAttribute('data-post-id'), 10);

  fetch('/wp-admin/admin-ajax.php?action=get_all_posts_titles_and_links')
    .then(response => {
      return response.json();
    })
    .then(data => {
      const titlesAndLinks = data;
      const currentURL = window.location.href;

      const contentElements = document.querySelectorAll('#post-content, .content-area, .home-content');
      contentElements.forEach(element => {
        linkifyContent(element, titlesAndLinks, currentURL, currentPostID);
      });
    });

  function escapeRegExp(string) {
    return string.replace(/[.*+\-?^${}()|[\]\\]/g, '\\$&');
  }

  function linkifyContent(element, titlesAndLinks, currentURL, currentPostID) {
    const originalHTML = element.innerHTML;
    let newHTML = originalHTML;
  
    titlesAndLinks.forEach(({ title, link, excerpt, plurale, id }) => {
      if (link !== currentURL && id !== currentPostID) {
        const escapedTitle = escapeRegExp(title);
        const regexTitle = new RegExp(`\\b(${escapedTitle})\\b(?!([^<]+)?>)`, 'gi');
        let matchTitle = regexTitle.exec(newHTML);
        
        if (matchTitle) {
          const linkHTMLTitle = `<a href="${link}" data-excerpt="${excerpt}" class="link-with-excerpt">${matchTitle[0]}</a>`;
          newHTML = newHTML.slice(0, matchTitle.index) + linkHTMLTitle + newHTML.slice(matchTitle.index + matchTitle[0].length);
        }
        
        if (plurale && plurale !== '') {
          const escapedPlurale = escapeRegExp(plurale);
          const regexPlurale = new RegExp(`\\b(${escapedPlurale})\\b(?!([^<]+)?>)`, 'gi');
          let matchPlurale = regexPlurale.exec(newHTML);
          
          if (matchPlurale) {
            const linkHTMLPlurale = `<a href="${link}" data-excerpt="${excerpt}" class="link-with-excerpt">${matchPlurale[0]}</a>`;
            newHTML = newHTML.slice(0, matchPlurale.index) + linkHTMLPlurale + newHTML.slice(matchPlurale.index + matchPlurale[0].length);
          }
        }
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





