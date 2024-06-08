window.addEventListener('load', async function() {
  const scientificNameElements = document.querySelectorAll('.meta-box-nome-scientifico');
  scientificNameElements.forEach(element => {
    const scientificName = element.dataset.scientificName;
    
    if (scientificName) {
        fetchClassification(scientificName);
    }
  });

  async function fetchClassification(speciesName) {
    try {
      const response = await fetch(`https://api.gbif.org/v1/species/match?name=${speciesName}`);
      const data = await response.json();
      const speciesKey = data.usageKey;
      if (speciesKey) {
        await fetchDetails(speciesKey);
        await fetchSynonyms(speciesKey); 
      }
    } catch (error) {
      console.error('Si è verificato un errore:', error);
    }
  }

  async function fetchDetails(speciesKey) {
    try {
      const response = await fetch(`https://api.gbif.org/v1/species/${speciesKey}`);
      const data = await response.json();
      displayClassification(data);
      displayBasionym(data);
    } catch (error) {
      console.error('Si è verificato un errore:', error);
    }
  }

  async function fetchSynonyms(speciesKey) {
    try {
      const response = await fetch(`https://api.gbif.org/v1/species/${speciesKey}/synonyms`);
      const data = await response.json();
      displaySynonyms(data);
    } catch (error) {
      console.error('Si è verificato un errore:', error);
    }
  }

  function displayClassification(classification) {
    const classificationContainer = document.querySelector('#classification-container');
  
    const taxonomyMap = {
      kingdom: 'Regno',
      phylum: 'Phylum',
      class: 'Classe',
      order: 'Ordine',
      family: 'Famiglia',
      genus: 'Genere',
      species: 'Specie',
    };
  
    const htmlParts = [];
  
    for (const key in taxonomyMap) {
      if (classification.hasOwnProperty(key)) {
        const link = `https://it.wikipedia.org/wiki/${encodeURIComponent(classification[key])}`;
        const linkedName = key === 'species'
          ? classification[key]
          : `<a href="${link}" target="_blank">${classification[key]}</a>`;
        htmlParts.push(`<li>${taxonomyMap[key]}: ${linkedName}</li>`);
      }
    }
  
    const html = '<ul>' + htmlParts.join('') + '</ul>';
  
    classificationContainer.innerHTML = html;
  }

  function displayBasionym(data) {
    const sinonimiContainer = document.querySelector('#sinonimi');
    const basionym = data.basionym ? data.basionym : null;
    
    let html = '';

    if (basionym) {
        html += `<div class="basionimo"><p>Basionimo: <em>${basionym}</em></p></div>`;
    }

    sinonimiContainer.innerHTML = html;
  }

  function displaySynonyms(data) {
    const sinonimiContainer = document.querySelector('#sinonimi');
    const synonyms = data.results ? data.results : [];
    
    let html = sinonimiContainer.innerHTML;

    if (synonyms.length > 0) {
        const synonymsList = synonyms.map(syn => `<li><em>${syn.scientificName}</em></li>`).join('');
        html += `
          <div class="sinonimi">
            <div class="sinonimi-toggle"><span class="sinonimi-text">Sinonimi</span> <span class="toggle-synonyms">&#9660;</span></div>
            <ul class="synonyms-list hidden">${synonymsList}</ul>
          </div>`;
    } else {
        html += `<div class="sinonimi"></div>`;
    }

    sinonimiContainer.innerHTML = html;

    const toggleElement = sinonimiContainer.querySelector('.sinonimi-toggle');
    toggleElement.addEventListener('click', function() {
        const synonymsList = sinonimiContainer.querySelector('.synonyms-list');
        synonymsList.classList.toggle('hidden');
        synonymsList.style.maxHeight = synonymsList.classList.contains('hidden') ? '0' : `${synonymsList.scrollHeight}px`;
        const arrow = toggleElement.querySelector('.toggle-synonyms');
        arrow.innerHTML = synonymsList.classList.contains('hidden') ? '&#9660;' : '&#9650;';
    });
  }

  // Close the list when clicking outside
  document.addEventListener('click', function(event) {
    const sinonimiContainer = document.querySelector('#sinonimi');
    const synonymsList = sinonimiContainer.querySelector('.synonyms-list');
    const toggleButton = sinonimiContainer.querySelector('.toggle-synonyms');
    const sinonimiText = sinonimiContainer.querySelector('.sinonimi-text');
    
    if (synonymsList && !synonymsList.classList.contains('hidden') && !sinonimiContainer.contains(event.target)) {
      synonymsList.classList.add('hidden');
      synonymsList.style.maxHeight = '0';
      toggleButton.innerHTML = '&#9660;';
      sinonimiText.style.textDecoration = 'none';
    }
  });
});






// Manage hover and clicks on additional images
let currentIndex = 0;
let images = [];

document.addEventListener('DOMContentLoaded', function () {
  // Delay the execution of the code to give time for images to load
  setTimeout(function () {
    images = Array.from(document.querySelectorAll('.additional-images-thumbnails img'));
    const featuredImage = document.getElementById("featured-image");

    if (featuredImage) {
      images.unshift(featuredImage);
      let originalFeaturedImageSrc = featuredImage.src;

      // Create an additional thumbnail for the featured image
      const additionalImages = document.querySelectorAll(".additional-images-thumbnails img[src]:not(#featured-image)");
      if (additionalImages.length > 0) {
        const featuredImageThumbnail = document.createElement("img");
        featuredImageThumbnail.src = featuredImage.src;
        featuredImageThumbnail.dataset.fullImageUrl = featuredImage.dataset.fullImageUrl;
        featuredImageThumbnail.alt = featuredImage.alt;
        featuredImageThumbnail.classList.add("additional-image-thumbnail", "selected");
        document.querySelector(".additional-images-thumbnails").prepend(featuredImageThumbnail);
      }

      const updatedThumbnails = document.querySelectorAll(".additional-image-thumbnail");

      // Attach mouseenter, mouseleave, and click events for thumbnail
      updatedThumbnails.forEach((thumbnail, index) => {
        thumbnail.addEventListener("mouseenter", function () {
          const fullImageUrl = thumbnail.dataset.fullImageUrl;
          featuredImage.src = fullImageUrl;
          featuredImage.setAttribute('srcset', fullImageUrl + '');
        });

        thumbnail.addEventListener("mouseleave", function () {
          featuredImage.src = originalFeaturedImageSrc;
          featuredImage.setAttribute('srcset', originalFeaturedImageSrc + '');
        });

        thumbnail.addEventListener("click", function () {
          const fullImageUrl = thumbnail.dataset.fullImageUrl;
          featuredImage.src = fullImageUrl;
          originalFeaturedImageSrc = fullImageUrl;

          document.querySelectorAll(".additional-image-thumbnail.selected").forEach((selectedThumbnail) => {
            selectedThumbnail.classList.remove("selected");
          });

          thumbnail.classList.add("selected");

          currentIndex = images.findIndex(image => image.dataset.fullImageUrl === fullImageUrl);

        });
      });
    }

  }, 600);
  

  // Initialize lightbox
  function initLightbox() {
    const parent = document.querySelector('#post-content');

    if (!parent) {
      return;
    }

    // Attach click event for opening lightbox
    parent.addEventListener('click', function (e) {
      const target = e.target;
    
      if (target.closest('.additional-images-thumbnails')) {
        e.stopPropagation();
        return;
      }
    
      if (target.tagName === 'IMG' && window.innerWidth > 768) {
        if (target.id === 'featured-image') {
         
          openLightbox(images[currentIndex]);
          
        } else {
         
          let index = images.findIndex(image => image.dataset.fullImageUrl === target.dataset.fullImageUrl);
    
          if (index !== -1) {
            currentIndex = index;
            openLightbox(images[currentIndex]);
          } else {
            console.error('Image not found in "images" array');
          }
        }
      }
    });
  }

  function nextImage() {
    currentIndex = (currentIndex + 1) % images.length;
    updateLightboxImage();
  }

  function previousImage() {
    currentIndex = (currentIndex - 1 + images.length) % images.length;
    updateLightboxImage();
  }

  function updateLightboxImage() {
    const newImage = images[currentIndex];
    const lightboxImage = document.querySelector('.lightbox-image');

    const highResImageUrl = newImage.dataset.fullImageUrl || newImage.src;

    lightboxImage.src = highResImageUrl;
    lightboxImage.alt = newImage.alt;
  }

  // Open lightbox and display image
function openLightbox(img) {
  let largeImageSrc = img.dataset.fullImageUrl || img.src; 

  // Create lightbox
  const lightbox = document.createElement('div');
  lightbox.classList.add('custom-lightbox', 'active');

  lightbox.innerHTML = `
    <div class="image-wrapper">
      <img src="${largeImageSrc}" alt="${img.alt}" class="lightbox-image" />
    </div>`;
  if (images.length > 1) {
    lightbox.innerHTML += `
      <div class="lightbox-arrow left-arrow"></div>
      <div class="lightbox-arrow right-arrow"></div>`;

    // Attach event listeners for left and right arrows
    lightbox.querySelector('.left-arrow').addEventListener('click', previousImage);
    lightbox.querySelector('.right-arrow').addEventListener('click', nextImage);
  }

  // Close lightbox on click outside of image
  lightbox.addEventListener('click', (e) => {
    if (!e.target.classList.contains('lightbox-arrow') && e.target !== lightbox.querySelector('img')) {
      lightbox.remove();
    }
  });

  // Close lightbox on pressing 'Escape' key
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' || e.keyCode === 27) {
      lightbox.remove();
    }
  });

  document.body.appendChild(lightbox);
}

  // Initialize the lightbox
  initLightbox();
});


// Create index and rhomboids

document.addEventListener('DOMContentLoaded', function() {
  var indexDiv = document.querySelector('.index');
  var h3Elements = document.querySelectorAll('.post-content-text h3');
  var indexCounter = 1;

  // Create a container for the index title
  var indexTitleContainer = document.createElement('div');
  indexTitleContainer.classList.add('index-title');
  indexTitleContainer.innerHTML = '<i class="fa-solid fa-list"></i> Index';
  indexDiv.appendChild(indexTitleContainer);

  // Create a container for the index items
  var indexItemsContainer = document.createElement('div');
  indexItemsContainer.classList.add('index-items');
  indexDiv.appendChild(indexItemsContainer);

  h3Elements.forEach(function(h3) {
      // Create a rhomboid shape for visual effect
      var romboid = document.createElement('div');
      romboid.classList.add('romboid');
      romboid.innerText = indexCounter;
      h3.prepend(romboid);
  
      // Create and append an index item for each header
      var indexItem = document.createElement('div');
      indexItem.classList.add('index-item');
  
      indexItem.innerHTML = '<a href="#section-' + indexCounter + '"><div class="romboid">' + indexCounter + '</div>' + h3.textContent.replace(/^\d+\s*/, '') + '</a>';
  
      indexItemsContainer.appendChild(indexItem);
  
      // Set an ID for each header for navigation
      h3.id = 'section-' + indexCounter;
      indexCounter++;
  });



// Adds a ID to Riferimenti
  const section11 = document.getElementById('section-11');
  if (section11) {
      let currentNode = section11.nextSibling; 
      while (currentNode !== null) {
         
          if (currentNode.nodeType === Node.ELEMENT_NODE && currentNode.textContent.includes('Riferimenti')) {
              currentNode.id = 'section-12'; 
              break;
          }
          currentNode = currentNode.nextSibling;
      }
  }
});


// Print article

document.addEventListener('DOMContentLoaded', () => {
  window.printArticle = function() {
    var content = document.getElementById("post-content").innerHTML;
    var title = document.querySelector(".post-content h1").textContent;
    var mywindow = window.open('', 'Print', 'height=600,width=800');
    mywindow.document.title = title;
    mywindow.document.write('<html><head><title>&nbsp;</title>');
    mywindow.document.write('<link rel="stylesheet" id="print-styles" type="text/css" href="' + window.location.origin + '/wp-content/themes/perseowiki/print.css" media="print">');
    mywindow.document.write('</head><body>');
    mywindow.document.write(content);
    mywindow.document.write('</body></html>');

    mywindow.document.getElementById("print-styles").onload = function() {
      mywindow.print();
      mywindow.close();
    };
    
    return true;
  }
});

// Share article
document.addEventListener('DOMContentLoaded', () => {
  window.openSharePopup = function() {
    var popup = document.getElementById("share-popup");
    popup.style.display = "block";
  }

  window.closeSharePopup = function() {
    var popup = document.getElementById("share-popup");
    popup.style.display = "none";
  }

  window.copyToClipboard = function() {
    var urlInput = document.getElementById("hidden-url");
    urlInput.select();
    document.execCommand("copy");
  
    var copyMessage = document.getElementById("copy-message");
    copyMessage.style.display = "block";
    copyMessage.textContent = "URL copiato con successo!";
  
    setTimeout(function() {
      copyMessage.style.display = "none";
    }, 2000);
  }
  
  window.shareUrl = function(url) {
    window.open(url, "", "menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600");
    return false;
  }

  window.shareUrlWhatsApp = function(element) {
    var url = element.getAttribute('data-url');
    var whatsappUrl = "https://api.whatsapp.com/send?text=" + url;
    window.open(whatsappUrl, '_blank').focus();
  }

  // Close popup when you click outside of the popup div
  document.addEventListener('click', function(event) {
    var popup = document.getElementById('share-popup');
    var popupContent = document.querySelector('.popup-content');

    if (event.target === popup) {
      closeSharePopup();
    }
  });
});


// Citation popup

document.addEventListener('DOMContentLoaded', () => {
  window.openCitationPopup = function() {
    document.getElementById("citation-popup").style.display = "block";
    generateCitation();
  }

  window.closeCitationPopup = function() {
    document.getElementById("citation-popup").style.display = "none";
  }

  window.formatDate = function(dateString, citationStyle) {
    var date = new Date(dateString);
    var day = date.getDate();
    var month = new Intl.DateTimeFormat('en-US', { month: 'long' }).format(date);
    var year = date.getFullYear();
    var monthNumber = date.getMonth() + 1;
  
    if (citationStyle === "APA") {
      return `${year}, ${month} ${day}`;
    } else if (citationStyle === "MLA") {
      return `${day} ${month} ${year}`;
    } else if (citationStyle === "Wikipedia") {
      return `${year}-${monthNumber.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
    }
  }
  
  // Formats author names according to APA 7th edition

  window.formatAPAAuthors = function(authors) {
    if (authors === "Editors of WikiHerbalist" || authors === "WHAdmin") {
      return authors;
    }
    else {
      const authorsArray = authors.split(', ').map((author) => {
        const parts = author.split(' ');
        const lastName = parts.pop(); 
        const initials = parts.map(name => `${name.charAt(0)}.`).join(' ');
        return `${lastName}, ${initials}`;
      });

      if (authorsArray.length > 1) {
        const lastAuthor = authorsArray.pop();
        return `${authorsArray.join(', ')}, & ${lastAuthor}`;
      } else {
        return authorsArray[0]; // No need for '&' for a single author
      }
    }
  }

  window.generateCitation = function() {
    var author = articleData.author;
    var title = articleData.title;
    var siteName = "WikiHerbalist";
    var publicationDate = articleData.publishedDate;
    var updatedDate = articleData.lastModifiedDate;
    var accessDate = articleData.accessDate;
    var url = articleData.url;
    var citationStyle = document.getElementById("citation-style").value;
  
    var formattedPublicationDate = formatDate(publicationDate, citationStyle);
    var formattedUpdatedDate = formatDate(updatedDate, citationStyle);
    var formattedAccessDate = formatDate(accessDate, citationStyle);
  
    var citation = "";
  
    if (citationStyle === "APA") {
      const formattedAuthors = formatAPAAuthors(author); 
      citation = `${formattedAuthors} (${formattedPublicationDate}). <em>${title}</em>. ${siteName}. ${url}`;
    } else if (citationStyle === "MLA") {
      citation = `${author}. "${title}." <i>${siteName}</i>, ${formattedPublicationDate}. Web. Accessed ${formattedAccessDate}. ${url}.`;
    } else if (citationStyle === "Wikipedia") {
      citation = `&lt;ref&gt;{{Cita web |url=${url} |titolo=${title} |accesso=${formattedAccessDate}}}&lt;/ref&gt;`;
    }
  
    document.getElementById("citation-text").innerHTML = citation;
  }

  window.copyCitationToClipboard = function() {
    var citation = document.getElementById("citation-text").textContent;
    var textarea = document.createElement("textarea");
    textarea.textContent = citation;
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand("copy");
    document.body.removeChild(textarea);

    var copyMessage = document.getElementById("citation-copy-message");
    copyMessage.style.display = "block";
    copyMessage.textContent = "Citazione copiata negli appunti!";
    setTimeout(function () {
        copyMessage.style.display = "none";
    }, 2000);
  }

  document.querySelector('.citation-button').addEventListener('click', openCitationPopup);


  // Close popup when you click outside of it
  document.addEventListener('click', function(event) {
    var popup = document.getElementById('citation-popup');
    var popupContent = document.querySelector('.popup-content');

    if (event.target === popup) {
      closeCitationPopup();
    }
  });


});