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

            var postLink = document.createElement("a"); // crea il link
            postLink.href = post.permalink; // imposta l'URL del post

            var imgElement = document.createElement("img");
            imgElement.classList.add("featured");
            imgElement.alt = post.title;
            if (post.featured_image) {
              imgElement.src = post.featured_image;
            } else {
              imgElement.style.display = "none"; // nasconde l'immagine se non è presente
            }
            postLink.appendChild(imgElement); // aggiungi l'immagine al link

            postElement.appendChild(postLink); // aggiungi il link al risultato

            var titleElement = document.createElement("h2");
            titleElement.classList.add("title");
            titleElement.innerHTML = post.title;
            postLink.appendChild(titleElement); // aggiungi il titolo al link

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
          postsInfo.innerHTML = `<p>${count} erbe che iniziano per ${letter}</p>`;
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