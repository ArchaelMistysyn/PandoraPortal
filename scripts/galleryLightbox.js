document.addEventListener("DOMContentLoaded", () => {
  const lightbox = document.getElementById("lightbox");
  const infoBox = document.getElementById("info-box"); 
  const lightboxImage = document.getElementById("lightbox-image");
  const closeLightbox = document.getElementById("lightbox-close");

  // Open lightbox when a gallery image is clicked
  document.addEventListener("click", (e) => {
    if (e.target.classList.contains("gallery-img")) {
      const imageName = e.target.alt || "Unknown";
      const artist = e.target.dataset.artist || "Unknown Artist";
      lightboxImage.src = e.target.src;
      lightboxImage.onload = () => {
        const dimensions = `${lightboxImage.naturalWidth}x${lightboxImage.naturalHeight}`;
        infoBox.innerHTML = `
          <div class="highlight-text">${imageName}</div>
          <div>${dimensions}</div>
          <div>Artist: ${artist}</div>
          <a href="${e.target.src}" download class="download-btn"></a>
        `;
      };
      lightbox.classList.remove("hidden");
    }
  });

  // Close lightbox when close button is clicked
  closeLightbox.addEventListener("click", () => {
    lightbox.classList.add("hidden");
    lightboxImage.src = "";
  });

  // Close lightbox when clicking outside the image
  lightbox.addEventListener("click", (e) => {
    if (e.target === lightbox) {
      lightbox.classList.add("hidden");
      lightboxImage.src = "";
    }
  });

  // Close lightbox with Escape key
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && !lightbox.classList.contains("hidden")) {
      lightbox.classList.add("hidden");
      lightboxImage.src = "";
    }
  });
});
