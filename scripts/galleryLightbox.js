document.addEventListener("DOMContentLoaded", () => {
  const gallery = document.getElementById("gallery");
  const carousel = document.getElementById("carousel");
  const lightbox = document.getElementById("lightbox");
  const lightboxImage = document.getElementById("lightbox-image");
  const closeLightbox = document.getElementById("lightbox-close");
  const prevButton = document.getElementById("carousel-prev");
  const nextButton = document.getElementById("carousel-next");
  let galleryImages = [];
  let activeIndex = 0;

  // Listeners to open lightbox via image selection
  gallery.addEventListener("click", (e) => {
    if (e.target.classList.contains("gallery-img")) {
      galleryImages = Array.from(document.querySelectorAll(".gallery-img"));
      activeIndex = galleryImages.indexOf(e.target);

      carousel.innerHTML = galleryImages
        .map(
          (img, index) =>
            `<img src="${img.src}" data-index="${index}" class="${
              index === activeIndex ? "active" : ""
            }">`
        )
        .join("");

      updateLightboxImage(activeIndex);
      lightbox.classList.remove("hidden");
    }
  });

  // Update lightbox preview image and active thumbnail(s)
  function updateLightboxImage(index) {
    activeIndex = index;
    lightboxImage.src = galleryImages[index].src;

    // Highlight the active thumbnail
    const thumbnails = carousel.querySelectorAll("img");
    thumbnails.forEach((thumb, idx) => {
      thumb.classList.toggle("active", idx === activeIndex);
    });
  }

  // Handle thumbnail clicks
  carousel.addEventListener("click", (e) => {
    if (e.target.tagName === "IMG") {
      const index = parseInt(e.target.dataset.index, 10);
      updateLightboxImage(index);
    }
  });

  // Carousel Navigation controls
  prevButton.addEventListener("click", () => {
    const scrollAmount = -carousel.clientWidth / 2;
    carousel.scrollBy({ left: scrollAmount, behavior: "smooth" });
  });

  nextButton.addEventListener("click", () => {
    const scrollAmount = carousel.clientWidth / 2;
    carousel.scrollBy({ left: scrollAmount, behavior: "smooth" });
  });

  // Close lightbox
  closeLightbox.addEventListener("click", () => {
    lightbox.classList.add("hidden");
    lightboxImage.src = "";
  });

  // Close lightbox on overlay click
  lightbox.addEventListener("click", (e) => {
    if (e.target === lightbox) {
      lightbox.classList.add("hidden");
      lightboxImage.src = "";
    }
  });

  // Close lightbox with escape key
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && !lightbox.classList.contains("hidden")) {
      lightbox.classList.add("hidden");
      lightboxImage.src = "";
    }
  });
});
