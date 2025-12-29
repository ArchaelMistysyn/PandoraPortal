const imageCycler = document.getElementById('image-cycler');
let filteredFiles = allFiles.filter(file => !file.toLowerCase().includes('cardback'));
let previousImage = null;

function setImageCycler() {
    let availableFiles = filteredFiles.filter(file => file !== previousImage);
    const randomImage = getRandomImage(availableFiles);
    imageCycler.style.backgroundImage = `url("${randomImage}")`;
    previousImage = randomImage;
    // Assign store links
    const imageNameWithExtension = randomImage.split('/').pop();
    let imageName = decodeURIComponent(imageNameWithExtension.replace(/\.[^/.]+$/, ""));
    if (imageName.toLowerCase().indexOf('dragon') !== -1) {
        imageCycler.classList.remove('portrait');
        let productName = imageName.replace(/ /g, '-').toLowerCase();
        const storelink = "https://archdragonstore.ca/products/" + productName;
        imageCycler.innerHTML = "<a id='tarot-link' href='" + storelink + "' target='_blank' style='display:block; width:100%; height:100%;'></a>";
    } else {
        imageCycler.classList.add('portrait');
        let productName = imageName.split(' - ').pop().replace(/ /g, '-').replace(/,/g, '').toLowerCase();
        const storelink = "https://archdragonstore.ca/products/framed-poster-" + productName;
        imageCycler.innerHTML = "<a id='tarot-link' href='" + storelink + "' target='_blank' style='display:block; width:100%; height:100%;'></a>";
    }
}

function getRandomImage(files) {
    return files[Math.floor(Math.random() * files.length)];
}

setImageCycler();
setInterval(setImageCycler, 5000);