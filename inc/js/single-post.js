document.addEventListener('DOMContentLoaded', function() {
  function initLightbox() {
    console.log('funzione initLightbox() attivata');
    const parent = document.querySelector('#post-content');
    console.log('parent:', parent);
  
    parent.addEventListener('click', function(e) {
      const target = e.target;
      if (target.tagName === 'IMG' && window.innerWidth > 768) {
        openLightbox(target);
      }
    });
  }
  
  function openLightbox(img) {
    console.log('immagine cliccata:', img);
    const srcset = img.getAttribute('srcset');
    const sources = srcset.split(', ');
    const largeImageSrc = sources[sources.length - 1].split(' ')[0];
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
    
    console.log('evento di click dell\'immagine gestito correttamente');
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
  const section10 = document.getElementById('section-10');
  if (section10) {
      const content = section10.parentNode.innerHTML;
      const searchPattern = /(section-10.+?)(Riferimenti)/s;
      const replacement = '$1<p id="section-11">Riferimenti</p>';

      section10.parentNode.innerHTML = content.replace(searchPattern, replacement);
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