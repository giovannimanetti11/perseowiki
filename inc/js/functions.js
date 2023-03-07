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
    
    searchBar.addEventListener("input", function() {
    var keywords = searchBar.value;
    
    fetch("search.php?keywords=" + keywords)
      .then(response => response.json())
      .then(data => {
        searchResults.innerHTML = "";
        if (data.length > 0) {
          data.forEach(post => {
            var postElement = document.createElement("div");
            postElement.classList.add("post");
    
            var titleElement = document.createElement("h2");
            titleElement.textContent = post.title;
            postElement.appendChild(titleElement);
    
            var contentElement = document.createElement("p");
            contentElement.textContent = post.content;
            postElement.appendChild(contentElement);
    
            var imgElement = document.createElement("img");
            imgElement.alt = post.title; // aggiunge un testo alternativo all'immagine
            if (post.featured_image) {
            imgElement.src = post.featured_image;
            } else {
            imgElement.style.display = "none"; // nasconde l'immagine se non è presente
            }
            postElement.appendChild(imgElement);
    
            searchResults.appendChild(postElement);
          });
        } else {
          searchResults.innerHTML = "Nessun risultato trovato.";
        }
      })
      .catch(error => console.error(error));
    
    });
    });


