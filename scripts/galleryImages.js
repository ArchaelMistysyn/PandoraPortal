const colorButtons = document.querySelectorAll('.color-button');
		const fullImageDisplay = document.getElementById('fullsize-image');
		const expandButton = document.getElementById('expand-button');
		let currentImageSrc = '';
		const preloadedImages = new Set();
        const ignoredFolders = ['Original'];
		const subfolders = {
			"Tarot": [
				{"label": "Paragon", "folder": "Tarot", "subfolders": ["Paragon"]}
			],
			"Displays": [
				{"label": "Banners", "folder": "Displays", "subfolders": ["Banners"]},
				{"label": "Locations", "folder": "Displays", "subfolders": ["Locations"]}
			],
			"Bosses": [
				{"label": "Fortress", "folder": "Bosses", "subfolders": ["Fortress"]},
				{"label": "Dragon", "folder": "Bosses", "subfolders": ["Dragon"]}
			],
			"Gear": [
				{"label": "Sovereign", "folder": "Gear", "subfolders": ["Sovereign"]},
				{"label": "Weapons", "folder": "Weapons", "subfolders": ["Knight", "Ranger", "Mage", "Assassin", "Weaver", "Rider", "Summoner"]},
				{"label": "Rings", "folder": "Rings", "subfolders": ["Signets", "Primordial", "Path", "Fabled", "Sovereign"]},
				{"label": "Equipment", "folder": "Equipment", "subfolders": ["Armour", "Amulet", "Wings", "Crest", "Gem"]},
				{"label": "Pact", "folder": "Misc", "subfolders": ["Pact"]},
			],
			"Items": [
				{"label": "Lotus", "folder": "Items", "subfolders": ["Lotus"]},
				{"label": "Gems", "folder": "Items", "subfolders": ["Gemstones", "Fragments"]},
				{"label": "Misc", "folder": "Items", "subfolders": ["Misc", "Skulls", "Hearts", "Fae Cores"]}
			],
			"Icons": [
				{"label": "Symbols", "folder": "Icons", "subfolders": ["Classes", "Elements", "BossTypes"]},
				{"label": "Misc", "folder": "Icons", "subfolders": ["Misc", "Stars", "Pearls"]}
			]
		};

		colorButtons.forEach(button => {
			button.addEventListener('click', function () {
				colorButtons.forEach(btn => btn.classList.remove('selected'));
				button.classList.add('selected');
				fullImageDisplay.style.backgroundColor = button.getAttribute('data-color');
			});
		});
		
		function highlightSelectedFolder(navItem) {
			const primaryNavItems = document.querySelectorAll('#primary-nav > ul > li > a');
			primaryNavItems.forEach(a => a.classList.remove('active'));
			navItem.classList.add('active');
		}
	
		async function preloadSegments() {
			const allPaths = Object.keys(subfolders).flatMap(folder => 
				subfolders[folder].flatMap(subfolder => {
                    if (typeof subfolder === 'string') {
                        return !ignoredFolders.includes(subfolder) ? `${folder}/${subfolder}` : null;
                    } else if (typeof subfolder === 'object' && subfolder !== null) {
                        return subfolder.subfolders
                            .filter(subSub => !ignoredFolders.includes(subSub))
                            .map(subSub => `${subfolder.folder}/${subSub}`)
                    }
                    return null;
                })
                .filter(Boolean) 
			);
			const maxConcurrentRequests = 5;
			for (let i = 0; i < allPaths.length; i += maxConcurrentRequests) {
				const batch = allPaths.slice(i, i + maxConcurrentRequests).map(path => 
					fetch(`./bot_php/gallery_data.php?action=getImages&path=${encodeURIComponent(path)}`)
						.then(response => {
							if (!response.ok) {
								throw new Error(`HTTP error! status: ${response.status}`);
							}
							return response.json();
						})
						.then(data => {
							const imagesArray = Array.isArray(data.images) ? data.images : Object.values(data.images);
							if (imagesArray.length) {
								imagesArray.forEach(image => {
									const imgSrc = `https://pandoraportal.ca/gallery/${path}/${encodeURIComponent(image)}`;
									preloadedImages.add(imgSrc);
									const img = new Image();
									img.src = imgSrc;
								});
							}
						})
						.catch(error => console.error(`Error loading images from path: ${path}`, error))
				);
				try {
					await Promise.all(batch);
				} catch (error) {
					console.error('Error preloading images:', error);
				}
			}
			console.log('All images preloaded successfully.');
		}

		function loadPrimaryFolders() {
			const primaryNavItems = document.querySelectorAll('#primary-nav > ul > li > a');
			primaryNavItems.forEach(navItem => {
				navItem.addEventListener('click', () => {
					highlightSelectedFolder(navItem);
					const folder = navItem.textContent;
					const firstSubfolder = subfolders[folder] ? subfolders[folder][0] : null;
					if (firstSubfolder) {
						loadImages(folder, firstSubfolder);
					}
				});
			});
			if (primaryNavItems.length > 0) {
				primaryNavItems[0].click();
			}
		}

		function createSubfolders() {
            const primaryNavItems = document.querySelectorAll('#primary-nav > ul > li');
            primaryNavItems.forEach(navItem => {
                const folder = navItem.querySelector('a').textContent;
                const subfolderList = navItem.querySelector('ul');
                if (subfolders[folder]) {
                    subfolders[folder].forEach(subfolder => {
                        if (typeof subfolder === 'object' && subfolder.label && subfolder.folder && subfolder.subfolders) {
                            if (subfolder.subfolders.some(subSub => !ignoredFolders.includes(subSub))) {
                                const validSubfolders = subfolder.subfolders.filter(
                                    subSub => !ignoredFolders.includes(subSub)
                                );
                                const li = document.createElement('li');
                                const a = document.createElement('a');
                                li.classList.add('sub-option');
                                a.classList.add('sub-a');
                                a.href = "#";
                                a.textContent = subfolder.label;
                                li.appendChild(a);
                                subfolderList.appendChild(li);
        
                                a.addEventListener('click', () => {
                                    loadImages(subfolder.folder, { ...subfolder, subfolders: validSubfolders });
                                    subfolderList.style.display = 'none';
                                    subfolderList.style.visibility = 'hidden';
                                    subfolderList.style.opacity = '0';
                                    subfolderList.style.transform = 'translateY(-10px)';
                                    highlightSelectedFolder(navItem.querySelector('a'));
                                });
                            }
                        }
                    });
        
                    navItem.addEventListener('mouseenter', () => {
                        subfolderList.style.display = 'block';
                        subfolderList.style.visibility = 'visible';
                        subfolderList.style.opacity = '1';
                        subfolderList.style.transform = 'translateY(0)';
                    });
                    navItem.addEventListener('mouseleave', () => {
                        subfolderList.style.visibility = 'hidden';
                        subfolderList.style.opacity = '0';
                        subfolderList.style.transform = 'translateY(-10px)';
                    });
                }
            });
        }        
		
		function flattenSubfolderPaths(folder, subfolder) {
            return subfolder.subfolders
                .filter(subSub => !ignoredFolders.includes(subSub))
                .map(subSub => `${folder}/${subSub}`);
        }        
		
		function loadImages(folder, subfolder) {
			const paths = flattenSubfolderPaths(folder, subfolder);
			const previewBox = document.getElementById('preview-box');
			previewBox.innerHTML = '';
			let firstImageSrc = null;
			paths.forEach((path, index) => {
				fetch(`./bot_php/gallery_data.php?action=getImages&path=${encodeURIComponent(path)}`)
					.then(response => response.json())
					.then(data => {
						const imagesArray = Object.values(data.images)
							.filter(image => {
								const isFolder = !image.includes('.');
								const isIgnored = ignoredFolders.some(ignored => image.includes(ignored));
								return !isFolder && !isIgnored;
							});
						imagesArray.forEach((image, imgIndex) => {
							const img = new Image();
							img.src = `./gallery/${path}/${encodeURIComponent(image)}`;
							img.dataset.imageName = image;
							appendImage(previewBox, img);
							if (!firstImageSrc && index === 0 && imgIndex === 0) {
								firstImageSrc = img.src;
							}
						});
						if (firstImageSrc) {
							displayFullSizeImage(firstImageSrc);
							const firstImageElement = previewBox.querySelector(`img[src="${firstImageSrc}"]`);
							if (firstImageElement) {
								firstImageElement.classList.add('selected-preview');
							}
						}
					})
					.catch(error => console.error(`Error loading images for path: ${path}`, error));
			});
		}
		
		

		function lazyLoadImage(img, src) {
			const observer = new IntersectionObserver((entries, observer) => {
				entries.forEach(entry => {
					if (entry.isIntersecting) {
						img.src = src;
						observer.unobserve(img);
					}
				});
			}, {
				rootMargin: "0px 0px 200px 0px"
			});
			observer.observe(img);
		}
		
		function appendImage(previewBox, image) {
			const img = new Image();
			const wrapper = document.createElement('div');
			wrapper.className = 'image-wrapper';
			img.src = image.src;
			if (preloadedImages.has(image.src)) {
				img.className = 'gallery-image';
			} else {
				const spinner = document.createElement('div');
				img.className = 'gallery-image lazy';
				img.alt = "Loading...";
				spinner.className = 'loading-spinner';
				wrapper.appendChild(spinner);
				lazyLoadImage(img, image.src);
				img.onload = () => {
					img.classList.remove('lazy');
					img.classList.add('lazy-loaded');
					spinner.remove();
				};
			}
			wrapper.appendChild(img);
			previewBox.appendChild(wrapper);
			img.onclick = () => displayFullSizeImage(img.src);
		}

		function displayFullSizeImage(src) {
			const fullImageDisplay = document.getElementById('fullsize-image');
			const infoBox = document.getElementById('info-box');
			const img = document.createElement('img');
			fullImageDisplay.innerHTML = '';
			img.src = src;
			currentImageSrc = src;
			fullImageDisplay.appendChild(img);
			const previewImages = document.querySelectorAll('.gallery-image');
			previewImages.forEach(image => {
				if (image.src === src) {
					image.classList.add('selected-preview');
				} else {
					image.classList.remove('selected-preview');
				}
			});
			img.onload = () => {
				updateInfoBox(img, src);
				infoBox.classList.remove('ease-load');
				void infoBox.offsetWidth;
				infoBox.classList.add('ease-load');
			};
		}
		
		function updateInfoBox(img, src) {
			const imageNameWithExtension = src.split('/').pop();
			let imageName = decodeURIComponent(imageNameWithExtension.replace(/\.[^/.]+$/, ""));
			const dimensions = `Original Image Size: ${img.naturalWidth}px, ${img.naturalHeight}px`;
			const shopLink = document.getElementById('shop-link');
			// Tier Naming
			if (src.includes('Weapons') || (src.includes('Equipment') && !src.includes('Sovereign'))) {
				const itemType = imageName.match(/[a-zA-Z]+/)[0];
				const tier = imageName.match(/\d+/)[0];
				imageName = `${itemType} (Tier ${tier})`;
			// Tarot Shop Links
			} else if (imageName.includes(' - ')) {
				let productName = imageName.split(' - ').pop();
				productName = productName.replace(/ /g, '-').replace(/,/g, '');
				shopLink.href = `https://archdragonstore.ca/products/framed-poster-${productName.toLowerCase()}`;
				shopLink.style.display = 'flex';
			} else if (imageName.includes('Dragon')) {
				let productName = imageName
				productName = productName.replace(/ /g, '-').replace(/,/g, '').toLowerCase();
				shopLink.href = `https://archdragonstore.ca/products/${productName}`;
				shopLink.style.display = 'flex';
			} else {
				shopLink.style.display = 'none';
			}
			document.getElementById('image-name').textContent = imageName;
			document.getElementById('image-dimensions').textContent = dimensions;
		}
		
		function openImageInNewTab(imageUrl) {
			const form = document.createElement('form');
			form.method = 'POST';
			form.action = './image_display/image_display.php';
			form.target = '_blank';
			const input = document.createElement('input');
			input.type = 'hidden';
			input.name = 'imgSrc';
			input.value = imageUrl;
			form.appendChild(input);
			document.body.appendChild(form);
			form.submit();
			document.body.removeChild(form);
		}
		
		fullImageDisplay.addEventListener('click', function() {
			openImageInNewTab(currentImageSrc);
		});

		expandButton.addEventListener('click', function() {
			openImageInNewTab(currentImageSrc);
		});
		
		preloadSegments();
		loadPrimaryFolders();
		createSubfolders();