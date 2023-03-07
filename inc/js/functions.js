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

      var xhr = new XMLHttpRequest();
      xhr.open("GET", "search.php?keywords=" + keywords);
      xhr.onload = function() {
        if (xhr.status === 200) {
          searchResults.innerHTML = xhr.responseText;
        } else {
          searchResults.innerHTML = "Errore " + xhr.status;
        }
      };
      xhr.send();
    });
  });


