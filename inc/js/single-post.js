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
  
      // Modifica questa riga per rimuovere il numero duplicato
      indexItem.innerHTML = '<a href="#section-' + indexCounter + '"><div class="romboid">' + indexCounter + '</div>' + h3.textContent.substring(1) + '</a>';
  
      indexItemsContainer.appendChild(indexItem);
  
      h3.id = 'section-' + indexCounter;
      indexCounter++;
    });


  });
  