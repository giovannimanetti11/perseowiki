// Funzione per aprire il media uploader e gestire la selezione delle immagini
function openMediaUploader(event) {
    event.preventDefault();
    console.log("Media uploader opened."); // Verifica che la funzione sia chiamata correttamente
  
    const mediaUploader = new wp.media.view.MediaFrame.Select({
      title: "Scegli le immagini aggiuntive",
      multiple: true,
      library: {
        type: "image"
      },
      button: {
        text: "Inserisci immagini"
      }
    });
  
    mediaUploader.on("select", function () {
      console.log("Images selected."); // Verifica che la selezione delle immagini funzioni correttamente
  
      const attachments = mediaUploader.state().get("selection").toJSON();
      const existingAttachmentIds = document
        .getElementById("additional_images_data")
        .value.split(",")
        .filter((id) => id.length > 0)
        .map((id) => parseInt(id));
      const newAttachmentIds = attachments.map((attachment) => attachment.id);
  
      const allAttachmentIds = existingAttachmentIds.concat(newAttachmentIds);
      document.getElementById("additional_images_data").value = allAttachmentIds.join(
        ","
      );
  
      const additionalImagesContainer = document.getElementById(
        "additional_images_container"
      );
  
      attachments.forEach((attachment) => {
        const img = document.createElement("img");
        img.src = attachment.url;
        img.dataset.id = attachment.id;
        img.style.width = "100px";
        img.style.height = "auto";
        img.style.margin = "5px";
  
        const button = document.createElement("button");
        button.classList.add("remove-image-button");
        button.style.position = "absolute";
        button.style.top = "0";
        button.style.right = "0";
        button.textContent = "X";
  
        const wrapper = document.createElement("div");
        wrapper.style.display = "inline-block";
        wrapper.style.position = "relative";
        wrapper.appendChild(img);
        wrapper.appendChild(button);
  
        additionalImagesContainer.appendChild(wrapper);
        button.addEventListener("click", removeImage);
      });
    });
  
    mediaUploader.open();
  }
  

function removeImage(event) {
    event.preventDefault();

    const button = event.target;
    const wrapper = button.parentNode;
    const img = wrapper.querySelector('img');
    const attachmentId = img.dataset.id;

    // Remove the image wrapper from the DOM
    wrapper.remove();

    // Remove the attachment ID from the hidden input
    const additionalImagesDataInput = document.getElementById("additional_images_data");
    const attachmentIds = additionalImagesDataInput.value.split(',').filter(id => id !== attachmentId);
    additionalImagesDataInput.value = attachmentIds.join(',');
}

document.addEventListener("DOMContentLoaded", function () {
    const uploadButton = document.getElementById("upload_additional_images_button");
    const removeButtons = document.querySelectorAll(".remove-image-button");
    removeButtons.forEach(button => button.addEventListener('click', removeImage));
});


// Funzione per gestire l'aggiunta e la rimozione dei revisori
document.addEventListener('DOMContentLoaded', function() {
  const dropdown = document.getElementById('add-revisore-dropdown');
  const revisoriList = document.getElementById('revisori-list');
  const dateInput = document.querySelector('input[name="date_revisioni"]');

  // Aggiungi stili CSS per la "x" e il cursore
  const style = document.createElement('style');
  style.innerHTML = `
    .remove-revisore {
      color: red;
      cursor: pointer;
      margin-left: 5px;
    }
  `;
  document.head.appendChild(style);


  dropdown.addEventListener('change', function() {
      if (this.value) {
          const revisoreName = this.options[this.selectedIndex].text;
          const selectedDate = dateInput.value;
          const div = document.createElement('div');
          div.className = 'revisore-item';
          div.innerHTML = revisoreName + ' (' + selectedDate + ') <span class="remove-revisore">x</span>';
          revisoriList.appendChild(div);

          // Add hidden input for the selected revisore
          const hiddenInput = document.createElement('input');
          hiddenInput.type = 'hidden';
          hiddenInput.name = 'revisori[]';
          hiddenInput.value = this.value;
          div.appendChild(hiddenInput);  
      }
  });

  revisoriList.addEventListener('click', function(e) {
      if (e.target && e.target.classList.contains('remove-revisore')) {
          e.target.parentNode.remove();
      }
  });
});
