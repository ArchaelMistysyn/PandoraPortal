document.addEventListener("DOMContentLoaded", () => {
  const displayContent = document.getElementById("display-content__img");
  const displayExpandBtn = document.getElementById("display-content__expand");
  const tabMenu = document.getElementById("tabMenu");
  const carousel = document.querySelector(".carousel");
  const lightbox = document.getElementById("lightbox");
  const lightboxImage = document.getElementById("lightbox-image");
  const closeLightbox = document.getElementById("lightbox-close");
  const prevButton = document.getElementById("carousel-prev");
  const nextButton = document.getElementById("carousel-next");

  let firstImage = "";
  let galleryImages = [];

  // Handle Init Display Image + add carousel active class on load
  setTimeout(() => {
    firstImage = document.querySelector(".search-category img");
    if (firstImage) {
      firstImage.classList.toggle("active");
      const clonedFirstImage = firstImage.cloneNode(true);
      displayContent.append(clonedFirstImage);
    }
  }, 800);

  // Handle Display Img Click: Activate lightbox for display image
  displayExpandBtn.addEventListener("click", (e) => {
    if (displayContent.lastChild.classList.contains("gallery-img")) {
      lightboxImage.src = displayContent.lastChild.src;
      lightbox.classList.remove("hidden");
    }
  });

  // Handle Carousel Img Click: Display selected carousel img in display
  carousel.addEventListener("click", (e) => {
    if (e.target.classList.contains("gallery-img")) {
      // Build array of images in carousel
      galleryImages = Array.from(
        document.querySelectorAll(".search-category img")
      );
      activeIndex = galleryImages.indexOf(e.target);

      // Toggle 'active' class on selected image
      galleryImages.forEach((img) => {
        img.classList.toggle("active", false);
      });
      e.target.classList.toggle("active");

      // Replace display img with selected img
      const selectedImg = e.target.cloneNode(true);
      const displayImg = displayContent.lastChild;
      displayContent.replaceChild(selectedImg, displayImg);
    }
  });

  // Handle Carousel Navigation controls
  prevButton.addEventListener("click", () => {
    const scrollAmount = -carousel.clientWidth / 2;
    carousel.scrollBy({ left: scrollAmount, behavior: "instant" });
  });
  nextButton.addEventListener("click", () => {
    const scrollAmount = carousel.clientWidth / 2;
    carousel.scrollBy({ left: scrollAmount, behavior: "instant" });
  });

  // Handle mouse wheel scroll for carousel
  carousel.addEventListener("wheel", (e) => {
    e.preventDefault();
    const scrollAmount = carousel.clientWidth / 2;

    // Handle scrolling for each direction
    if (e.deltaY > 0) {
      carousel.scrollBy({ left: -scrollAmount, behavior: "instant" });
    } else {
      carousel.scrollBy({ left: scrollAmount, behavior: "instant" });
    }
  });

  // Handle reset for carousel position + display Img when changing category
  tabMenu.addEventListener("click", (e) => {
    if (e.target.tagName === "LI") {
      carousel.scrollTo({ left: 0, behavior: "instant" });
      setTimeout(() => {
        // Make first carousel imag active
        firstImage = document.querySelector(".search-category img");
        firstImage.classList.toggle("active");
        // Replace display img
        clonedFirstImage = firstImage.cloneNode(true);
        const displayImg = displayContent.lastChild;
        displayContent.replaceChild(clonedFirstImage, displayImg);
      }, 1000);
    }
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
