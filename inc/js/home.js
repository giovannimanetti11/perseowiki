// AJAX Search

document.addEventListener("DOMContentLoaded", function () {
  var searchBar = document.querySelector(".searchBar");
  var searchResults = document.querySelector("#searchResults");
  var clearSearch = document.querySelector("#clearSearch");
  var currentPage = 1;

  function createPagination(totalResults) {
    var maxResultsPerPage = 10;
    var totalPages = Math.ceil(totalResults / maxResultsPerPage);
  
    if (totalPages <= 1) {
      return;
    }
  
    var pagination = document.createElement("div");
    pagination.classList.add("pagination");
  
    var prevButton = document.createElement("button");
    prevButton.innerHTML = '<i class="fas fa-chevron-left"></i> <span>Precedente</span>';
    prevButton.classList.add("prev-button");
    prevButton.disabled = currentPage === 1;
    prevButton.addEventListener("click", function () {
      if (currentPage > 1) {
        currentPage--;
        searchBar.dispatchEvent(new Event("input"));
      }
    });
  
    var nextButton = document.createElement("button");
    nextButton.innerHTML = '<span>Successivo</span> <i class="fas fa-chevron-right"></i>';
    nextButton.classList.add("next-button");
    nextButton.addEventListener("click", function () {
      currentPage++;
      searchBar.dispatchEvent(new Event("input"));
    });
  
    pagination.appendChild(prevButton);
    pagination.appendChild(nextButton);
    searchResults.insertAdjacentElement('beforeend', pagination);
  }  

  searchBar.addEventListener("input", function () {
    var keywords = searchBar.value;

    if (keywords.length < 3) {
      searchResults.innerHTML = "";
      return;
    }

    fetch(
      "search.php?keywords=" +
        encodeURIComponent(keywords) +
        "&page=" +
        currentPage +
        "&_=" +
        new Date().getTime()
    )
    
      

      .then((response) => response.json())
      .then((data) => {
        const { posts, tags, glossary_terms, total_results } = data;
        var totalResults = total_results;
        
        searchResults.innerHTML = "";
        createPagination(totalResults);
        

        if (posts.length > 0 || tags.length > 0 || data.glossary_terms.length > 0) {
          if (posts.length > 0) {
            var postsHeading = document.createElement("h4");
            postsHeading.textContent = "Erbe";
            searchResults.appendChild(postsHeading);

            posts.forEach((post) => {
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
                postLink.appendChild(metaBoxElement);
              }

              searchResults.appendChild(postElement);
            });
          }

          if (tags.length > 0) {
            var tagsHeading = document.createElement("h4");
            tagsHeading.textContent = "Proprietà";
            searchResults.appendChild(tagsHeading);

            tags.forEach((tag) => {
              var tagElement = document.createElement("li");
              tagElement.classList.add("tag-row");

              var tagLink = document.createElement("a");
              tagLink.href = tag.permalink;

              var tagNameElement = document.createElement("span");
              tagNameElement.classList.add("tag-name");
              tagNameElement.innerHTML = tag.name;
              tagLink.appendChild(tagNameElement);

              tagElement.appendChild(tagLink);
              searchResults.appendChild(tagElement);
            });
          }

          if (data.glossary_terms.length > 0) {
            var glossaryHeading = document.createElement("h4");
            glossaryHeading.textContent = "Glossario";
            searchResults.appendChild(glossaryHeading);
          
            data.glossary_terms.forEach((glossary_term) => {
              var glossaryTermElement = document.createElement("li");
              glossaryTermElement.classList.add("post-row");
            
              var glossaryTermLink = document.createElement("a");
              glossaryTermLink.href = glossary_term.permalink;
            
              var imgElement = document.createElement("img");
              imgElement.classList.add("featured");
              imgElement.alt = glossary_term.title;
              if (glossary_term.featured_image) {
                imgElement.src = glossary_term.featured_image;
              } else {
                imgElement.style.display = "none";
              }
              glossaryTermLink.appendChild(imgElement);
            
              glossaryTermElement.appendChild(glossaryTermLink);
            
              var titleElement = document.createElement("h2");
              titleElement.classList.add("title");
              titleElement.innerHTML = glossary_term.title;
              glossaryTermLink.appendChild(titleElement);
            
              searchResults.appendChild(glossaryTermElement);
            });
            
          }

          searchBar.classList.add("noradius");
          searchResults.style.display = "flex";
          clearSearch.style.display = "block";
        } else {
          searchResults.style.display = "none";
        }
      });
  });

  clearSearch.addEventListener("click", function () {
    searchBar.value = "";
    searchResults.style.display = "none";
    clearSearch.style.display = "none";
    searchBar.classList.remove("noradius");
  });

  document.addEventListener("click", function (event) {
    if (
      !searchBar.contains(event.target) &&
      !searchResults.contains(event.target)
    ) {
      searchResults.style.display = "none";
      clearSearch.style.display = "none";
      searchBar.classList.remove("noradius");
    }
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


// HOME filter
document.addEventListener('DOMContentLoaded', () => {
    // Seleziona i pulsanti e i contenitori
    const alphabeticOrderBtn = document.getElementById("alphabeticOrder");
    const propertiesListBtn = document.getElementById("propertiesList");
    const alphabeticContainer = document.querySelector(".alphabetic-container");
    const propertiesContainer = document.querySelector(".properties-container");

    // Funzione per gestire il cambio dei contenitori e l'aggiornamento delle classi dei pulsanti
    function switchContainers(showAlphabetic) {
        if (showAlphabetic) {
            alphabeticContainer.style.display = "block";
            propertiesContainer.style.display = "none";
            alphabeticOrderBtn.classList.add("btn-active");
            propertiesListBtn.classList.remove("btn-active");
        } else {
            alphabeticContainer.style.display = "none";
            propertiesContainer.style.display = "flex";
            alphabeticOrderBtn.classList.remove("btn-active");
            propertiesListBtn.classList.add("btn-active");
        }
    }

    // Aggiungi event listener ai pulsanti
    alphabeticOrderBtn.addEventListener("click", function () {
        switchContainers(true);
    });

    propertiesListBtn.addEventListener("click", function () {
        switchContainers(false);
    });


  // AJAX get therapeutic properties and herbs

  function loadTherapeuticPropertiesAndHerbs() {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "/wp-admin/admin-ajax.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
            const data = JSON.parse(this.responseText);
            const propertiesColumn = document.querySelector(".properties-column");
            const herbsColumn = document.querySelector(".herbs-column");

            let counter = 0;
            data.forEach((item) => {
              const propertyHerbsRow = document.createElement("div");
              propertyHerbsRow.classList.add("property-herbs-row");
              if (counter % 2 === 0) {
                propertyHerbsRow.classList.add("even-row");
              } else {
                propertyHerbsRow.classList.add("odd-row");
              }
              counter++;

              const propertyName = document.createElement("a");
              propertyName.classList.add("property-name");
              propertyName.href = `/tag/${item.property}`; 
              propertyName.textContent = item.property;
              propertyHerbsRow.appendChild(propertyName);
            
              const herbsList = document.createElement("div");
              herbsList.classList.add("herbs-list");
            
              const herbsArray = item.herbs.split(", ");
              herbsArray.forEach((herb, index) => {
                const herbLink = document.createElement("a");
                herbLink.href = `${herb}`;
                herbLink.textContent = herb.trim();

                const herbSpan = document.createElement("span");
                herbSpan.appendChild(herbLink);

                if (index < herbsArray.length - 1) {
                  const comma = document.createTextNode(", ");
                  herbSpan.appendChild(comma);
                }

                herbsList.appendChild(herbSpan);
              });

            
              propertyHerbsRow.appendChild(herbsList);
              propertiesContainer.appendChild(propertyHerbsRow);
            });
        }
    };
    xhr.send("action=get_properties_and_herbs");
  }

  // Modifica l'event listener del pulsante propertiesList per caricare i dati quando viene premuto
  function onClickPropertiesListBtn() {
    switchContainers(false);
    loadTherapeuticPropertiesAndHerbs();
    propertiesListBtn.removeEventListener('click', onClickPropertiesListBtn); // rimuove l'event listener dopo il primo clic
  }
  propertiesListBtn.addEventListener("click", onClickPropertiesListBtn);

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