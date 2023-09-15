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



// Gestisce le revisioni nel backend di WP

document.addEventListener('DOMContentLoaded', function() {
  const addRevisionButton = document.getElementById('add-revision');
  const revisionsList = document.getElementById('revisions-list');
  const memberList = document.getElementById('member-list');
  const revisionDate = document.getElementById('revision-date');

  addRevisionButton.addEventListener('click', function() {
      const selectedMemberId = memberList.value;
      const selectedMemberText = memberList.options[memberList.selectedIndex].text;
      const selectedDate = revisionDate.value;

      if (selectedMemberId && selectedDate) {
          const revisionItem = document.createElement('div');
          revisionItem.className = 'revision-item';
          revisionItem.dataset.memberId = selectedMemberId;
          revisionItem.dataset.date = selectedDate;

          const revisionText = document.createElement('span');
          revisionText.textContent = selectedMemberText + ' ' + selectedDate;
          revisionItem.appendChild(revisionText);

          const removeButton = document.createElement('button');
          removeButton.textContent = 'X';
          removeButton.className = 'remove-revision';
          removeButton.addEventListener('click', function() {
              revisionsList.removeChild(revisionItem);
          });
          revisionItem.appendChild(removeButton);

          revisionsList.appendChild(revisionItem);
      }
  });

  const postForm = document.getElementById('post');
  postForm.addEventListener('submit', function(event) {
      const revisions = [];
      const revisionItems = document.querySelectorAll('.revision-item');
      revisionItems.forEach(function(revisionItem) {
          const memberId = revisionItem.dataset.memberId;
          const date = revisionItem.dataset.date;
          revisions.push({memberId, date});
      });

      const revisionsInput = document.createElement('input');
      revisionsInput.type = 'hidden';
      revisionsInput.name = 'revisions';
      revisionsInput.value = JSON.stringify(revisions);
      postForm.appendChild(revisionsInput);
  });

  const removeButtons = document.querySelectorAll('.remove-revision');
  removeButtons.forEach(function(removeButton) {
      removeButton.addEventListener('click', function() {
          const revisionItem = removeButton.parentElement;
          revisionsList.removeChild(revisionItem);
      });
  });

});
