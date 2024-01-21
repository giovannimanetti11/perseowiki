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
    prevButton.innerHTML = '<i class="fas fa-chevron-left"></i> &nbsp;<span>Precedente</span>';
    prevButton.classList.add("prev-button");
    prevButton.disabled = currentPage === 1;
    prevButton.addEventListener("click", function () {
      if (currentPage > 1) {
        currentPage--;
        searchBar.dispatchEvent(new Event("input"));
      }
    });
  
    var nextButton = document.createElement("button");
    nextButton.innerHTML = '<span>Successivo</span> &nbsp;<i class="fas fa-chevron-right"></i>';
    nextButton.classList.add("next-button");
    nextButton.addEventListener("click", function () {
      currentPage++;
      searchBar.dispatchEvent(new Event("input"));
    });
  
    pagination.appendChild(prevButton);
    pagination.appendChild(nextButton);
    searchResults.insertAdjacentElement('beforeend', pagination);
  }  

  searchBar.addEventListener("input", debounce(function() {
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
            var erbeIcon = document.createElement("i");
            erbeIcon.classList.add("fas", "fa-leaf");
            postsHeading.prepend(erbeIcon);
            searchResults.appendChild(postsHeading);

            posts.forEach((post) => {
              var postElement = document.createElement("li");
              postElement.classList.add("post-row");
              postElement.dataset.href = post.permalink;
              postElement.addEventListener("click", function () {
                window.location.href = this.dataset.href;
              });

              var imgElement = document.createElement("img");
              imgElement.classList.add("featured");
              imgElement.alt = post.title;
              if (post.featured_image) {
                imgElement.src = post.featured_image;
              } else {
                imgElement.style.display = "none";
              }

              var titleElement = document.createElement("h2");
              titleElement.classList.add("title");
              titleElement.innerHTML = post.title;

              if (post.meta_box_nome_scientifico) {
                var metaBoxElement = document.createElement("p");
                metaBoxElement.classList.add("meta-box-nome-scientifico");
                metaBoxElement.innerHTML = post.meta_box_nome_scientifico;
              }

              postElement.appendChild(imgElement);
              postElement.appendChild(titleElement);
              if (post.meta_box_nome_scientifico) {
                postElement.appendChild(metaBoxElement);
              }

              searchResults.appendChild(postElement);
            });
          }

          if (tags.length > 0) {
            var tagsHeading = document.createElement("h4");
            tagsHeading.textContent = "Proprietà";
            var proprietaIcon = document.createElement("i");
            proprietaIcon.classList.add("fas", "fa-cogs");
            tagsHeading.prepend(proprietaIcon);
            searchResults.appendChild(tagsHeading);

            tags.forEach((tag) => {
              var tagElement = document.createElement("li");
              tagElement.classList.add("tag-row");
              tagElement.dataset.href = tag.permalink;
              tagElement.addEventListener("click", function () {
                window.location.href = this.dataset.href;
              });

              var tagNameElement = document.createElement("span");
              tagNameElement.classList.add("tag-name");
              tagNameElement.innerHTML = tag.name;

              
              tagElement.appendChild(tagNameElement);

              searchResults.appendChild(tagElement);
            });
          }

          if (data.glossary_terms.length > 0) {
            var glossaryHeading = document.createElement("h4");
            glossaryHeading.textContent = "Glossario";
            var glossarioIcon = document.createElement("i");
            glossarioIcon.classList.add("fas", "fa-book");
            glossaryHeading.prepend(glossarioIcon);
            searchResults.appendChild(glossaryHeading);
          
            data.glossary_terms.forEach((glossary_term) => {
              var glossaryTermElement = document.createElement("li");
              glossaryTermElement.classList.add("post-row");
              glossaryTermElement.dataset.href = glossary_term.permalink;
              glossaryTermElement.addEventListener("click", function () {
                window.location.href = this.dataset.href;
              });
            
              var imgElement = document.createElement("img");
              imgElement.classList.add("featured");
              imgElement.alt = glossary_term.title;
              if (glossary_term.featured_image) {
                imgElement.src = glossary_term.featured_image;
              } else {
                imgElement.style.display = "none";
              }
            
              var titleElement = document.createElement("h2");
              titleElement.classList.add("title");
              titleElement.innerHTML = glossary_term.title;
            
              glossaryTermElement.appendChild(imgElement);
              glossaryTermElement.appendChild(titleElement);

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
  }, 200));

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

  function debounce(func, delay) {
    let debounceTimer;
    return function() {
      const context = this;
      const args = arguments;
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(() => func.apply(context, args), delay);
    };
  }

  if (posts.length === 0 && tags.length === 0 && glossary_terms.length === 0) {
    var noResultsMessage = document.createElement("p");
    noResultsMessage.textContent = "Nessun risultato trovato. Prova con parole chiave diverse.";
    searchResults.appendChild(noResultsMessage);
  }
  
});



// HOME filter
document.addEventListener('DOMContentLoaded', () => {
  // Select the buttons and containers
  const alphabeticOrderBtn = document.getElementById("alphabeticOrder");
  const propertiesListBtn = document.getElementById("propertiesList");
  const alphabeticContainer = document.querySelector(".alphabetic-container");
  const propertiesContainer = document.querySelector(".properties-container");

  // Function to handle the switch of containers and update button classes
  function switchContainers(showAlphabetic) {
      if (showAlphabetic) {
          alphabeticContainer.style.display = "block";
          propertiesContainer.style.display = "none";
          alphabeticOrderBtn.classList.add("btn-active");
          propertiesListBtn.classList.remove("btn-active");
          // Ensure the default alphabet link is active and clicked
          if (!document.querySelector('.alphabet-link.active')) {
              const defaultLink = document.querySelector('.alphabet-link[data-letter="A"]');
              defaultLink.classList.add('active');
              defaultLink.click();
          }
      } else {
          alphabeticContainer.style.display = "none";
          propertiesContainer.style.display = "flex";
          alphabeticOrderBtn.classList.remove("btn-active");
          propertiesListBtn.classList.add("btn-active");
      }
  }

  // Add an event listener to buttons
  alphabeticOrderBtn.addEventListener("click", function () {
      switchContainers(true);
  });

  propertiesListBtn.addEventListener("click", function () {
      switchContainers(false);
  });

  // Edit event listener of propertiesList button to load data when it's clicked
  function onClickPropertiesListBtn() {
      switchContainers(false);
      loadTherapeuticPropertiesAndHerbs();
      propertiesListBtn.removeEventListener('click', onClickPropertiesListBtn);
  }
  propertiesListBtn.addEventListener("click", onClickPropertiesListBtn);
});

// AJAX to get posts alphabetically
document.addEventListener('DOMContentLoaded', () => {
  const alphabetLinks = document.querySelectorAll('.alphabet-link');
  const postsContainers = document.querySelectorAll('.posts-container');
  const postsInfo = document.querySelector('#posts-info');

  alphabetLinks.forEach(link => {
      link.addEventListener('click', event => {
          // Prevent scrolling to the top when clicking an alphabet link
          event.preventDefault();

          const letter = link.dataset.letter;
          const targetContainer = document.querySelector('#posts-container-' + letter);
          const count = targetContainer.getElementsByClassName('card-link').length;

          alphabetLinks.forEach(link => link.classList.remove('active'));
          link.classList.add('active');

          postsContainers.forEach(container => {
              container.style.display = container.id === 'posts-container-' + letter ? 'block' : 'none';
          });

          const noun = count === 1 ? "erba" : "erbe";
          const verb = count === 1 ? "inizia" : "iniziano";
          postsInfo.innerHTML = `<p>${count} ${noun} che ${verb} per ${letter}</p>`;
      });
  });

  // Automatically click the default alphabet link 'A'
  const defaultLink = document.querySelector('.alphabet-link[data-letter="A"]');
  defaultLink.classList.add('active');
  defaultLink.click();
});



