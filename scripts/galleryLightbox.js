document.addEventListener("DOMContentLoaded", () => {
  const displayContent = document.getElementById("display-content__img");
  const displayExpandBtn = document.getElementById("display-content__expand");
  const tabMenu = document.getElementById("tabMenu");
  const lightbox = document.getElementById("lightbox");
  const lightboxImage = document.getElementById("lightbox-image");
  const closeLightbox = document.getElementById("lightbox-close");
  let firstImage = "";
  let galleryImages = [];

  // Handle Display Img Click: Activate lightbox for display image
  displayExpandBtn.addEventListener("click", (e) => {
    if (displayContent.lastChild.classList.contains("gallery-img")) {
      lightboxImage.src = displayContent.lastChild.src;
      lightbox.classList.remove("hidden");
    }
  });

  // Close lightbox
  closeLightbox.addEventListener("click", () => {
    lightbox.classList.add("hidden");
    lightboxImage.src = "";
  });
  lightbox.addEventListener("click", (e) => {
    if (e.target === lightbox) {
      lightbox.classList.add("hidden");
      lightboxImage.src = "";
    }
  });
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && !lightbox.classList.contains("hidden")) {
      lightbox.classList.add("hidden");
      lightboxImage.src = "";
    }
  });
});
