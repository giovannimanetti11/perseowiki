// Manage hover and clics on additional images

document.addEventListener("DOMContentLoaded", function () {
  const featuredImage = document.getElementById("featured-image");
  const additionalImageThumbnails = document.querySelectorAll(".additional-image-thumbnail");

  console.log("featured image:", featuredImage);
  console.log("additional image:", additionalImageThumbnails);

  let originalFeaturedImageSrc = featuredImage.src;

  additionalImageThumbnails.forEach((thumbnail) => {
    console.log("thumbnails:", thumbnail);
    thumbnail.addEventListener("mouseenter", function () {
      console.log("mouseenter");
      const fullImageUrl = thumbnail.dataset.fullImageUrl;
      featuredImage.src = fullImageUrl;
    });
    
    thumbnail.addEventListener("mouseleave", function () {
      console.log("mouseleave");
      featuredImage.src = originalFeaturedImageSrc;
    });
    
    thumbnail.addEventListener("click", function () {
      console.log("click");
      const fullImageUrl = thumbnail.dataset.fullImageUrl;
      featuredImage.src = fullImageUrl;
      originalFeaturedImageSrc = fullImageUrl;
    });
  });
});

// Create custom lightbox

document.addEventListener('DOMContentLoaded', function() {
  function initLightbox() {
    const parent = document.querySelector('#post-content');
  
    parent.addEventListener('click', function(e) {
      const target = e.target;
      if (target.closest('.additional-images-thumbnails')) {
        // Prevent lightbox for images inside additional-images-thumbnails
        e.stopPropagation();
        return;
      }
      if (target.tagName === 'IMG' && window.innerWidth > 768) {
        openLightbox(target);
      }
    });
  }
  
  
  function openLightbox(img) {
    const srcset = img.getAttribute('srcset');
    let largeImageSrc;
    if (srcset) {
        const sources = srcset.split(', ');
        largeImageSrc = sources[sources.length - 1].split(' ')[0];
    } else {
        largeImageSrc = img.getAttribute('src');
    }
    const lightbox = document.createElement('div');
    lightbox.classList.add('custom-lightbox', 'active');
    lightbox.innerHTML = `<div class="image-wrapper"><img src="${largeImageSrc}" alt="${img.alt}" /></div>`;
    
    lightbox.addEventListener('click', (e) => {
      if (e.target !== lightbox.querySelector('img')) {
        lightbox.remove();
      }
    });

    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' || e.keyCode === 27) {
        lightbox.remove();
      }
    });    
    
    document.body.appendChild(lightbox);
}

  initLightbox();
});

// Create index and romboids

document.addEventListener('DOMContentLoaded', function() {
  var indexDiv = document.querySelector('.index');
  var h3Elements = document.querySelectorAll('.post-content-text h3');
  var indexCounter = 1;

  // Crea un contenitore per il titolo dell'indice
  var indexTitleContainer = document.createElement('div');
  indexTitleContainer.classList.add('index-title');
  indexTitleContainer.innerHTML = '<i class="fa-solid fa-list"></i> Indice';
  indexDiv.appendChild(indexTitleContainer);

  // Crea un contenitore per gli elementi dell'indice
  var indexItemsContainer = document.createElement('div');
  indexItemsContainer.classList.add('index-items');
  indexDiv.appendChild(indexItemsContainer);

  h3Elements.forEach(function(h3) {
      var romboid = document.createElement('div');
      romboid.classList.add('romboid');
      romboid.innerText = indexCounter;
      h3.prepend(romboid);
  
      var indexItem = document.createElement('div');
      indexItem.classList.add('index-item');
  
      indexItem.innerHTML = '<a href="#section-' + indexCounter + '"><div class="romboid">' + indexCounter + '</div>' + h3.textContent.replace(/^\d+\s*/, '') + '</a>';
  
      indexItemsContainer.appendChild(indexItem);
  
      h3.id = 'section-' + indexCounter;
      indexCounter++;
  });

  // Aggiunge un ID a Riferimenti
  const section11 = document.getElementById('section-11');
  if (section11) {
      const content = section11.parentNode.innerHTML;
      const searchPattern = /(section-11.+?)(Riferimenti)/s;
      const replacement = '$1<p id="section-12">Riferimenti</p>';

      section11.parentNode.innerHTML = content.replace(searchPattern, replacement);
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

  // Chiude il popup quando si fa clic al di fuori del contenuto del popup
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
      citation = author + ". (" + publicationDate + "). <em>" + title + "</em>. " + siteName + ". " + url;
    } else if (citationStyle === "MLA") {
      citation = author + ". \"" + title + ".\" <i>" + siteName + "</i>. " + formattedPublicationDate + ". Web. Accessed " + formattedAccessDate + ". " + url + ".";
    } else if (citationStyle === "Wikipedia") {
      citation = "&lt;ref&gt;{{Cita web |url=" + url + " |titolo=" + title + " |accesso=" + formattedAccessDate + "}}&lt;/ref&gt;";
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

  // Chiude il popup quando si fa clic al di fuori del contenuto del popup
  document.addEventListener('click', function(event) {
    var popup = document.getElementById('citation-popup');
    var popupContent = document.querySelector('.popup-content');

    if (event.target === popup) {
      closeCitationPopup();
    }
  });


});

