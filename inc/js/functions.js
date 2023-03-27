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
    .then(response => response.json())
    .then(data => {
      // Ottieni l'URL corrente
      const currentURL = window.location.href;

      // Esegui la funzione linkifyContent per tutti gli elementi del DOM in cui vuoi aggiungere i link
      const contentElements = document.querySelectorAll('#post-content, .content-area, .home-content');
      contentElements.forEach(element => {
        linkifyContent(element, data, currentURL);
      });
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
  const linksWithExcerpt = element.querySelectorAll('.link-with-excerpt');
  linksWithExcerpt.forEach(link => {
    const excerpt = link.getAttribute('data-excerpt');
    link.addEventListener('mouseover', (event) => {
      showTooltip(excerpt, event);
    });
    link.addEventListener('mouseout', () => {
      hideTooltip();
    });
  });
} // Aggiungi la chiusura della funzione qui

let tooltip = null;

function showTooltip(excerpt, event) {
  tooltip = document.createElement('div');
  tooltip.classList.add('tooltip-term-excerpt');
  tooltip.innerHTML = excerpt;

  const linkRect = event.target.getBoundingClientRect();
  const scrollX = window.pageXOffset;
  const scrollY = window.pageYOffset;
  const tooltipX = linkRect.left + scrollX + (linkRect.width / 2) - 150; // Sposta il tooltip 150px a sinistra
  const tooltipY = linkRect.top + scrollY - 10 - 175; // 10 px sopra il link e spostato in alto di 175px (altezza del tooltip)

  tooltip.style.left = `${tooltipX}px`;
  tooltip.style.top = `${tooltipY}px`;

  document.body.appendChild(tooltip);
}



function hideTooltip() {
  if (tooltip) {
    document.body.removeChild(tooltip);
    tooltip = null;
  }
}






