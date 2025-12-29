bannerScreen = document.getElementById("banner-screen");

function setActiveButton(buttonClass, selectedValue) {
    document.querySelectorAll(buttonClass).forEach(button => {
        if (button.getAttribute("data-value") === selectedValue) {
            button.classList.add("active-menu");
        } else {
            button.classList.remove("active-menu");
        }
    });
}

function numberConversion(inputNumber) {
    const labels = ['', 'K', 'M', 'B', 'T', 'Q', 'Qt', 'Z', 'Z+', 'Z++', 'Z+++', 
                    'ZZ', 'ZZ+', 'ZZ++', 'ZZ+++', 'ZZZ', 'ZZZ+', 'ZZZ++', 'ZZZ+++'];

    const input = BigInt(inputNumber);
    if (input < 1000n) return input.toString();
    let idx = 0;
    let threshold = 1000n;
    while (input >= threshold * 1000n) {
        threshold *= 1000n;
        idx++;
    }
    const scaled = (input * 100n) / threshold;
    const whole = scaled / 100n;
    const frac = scaled % 100n;

    let result = whole.toString();
    if (frac !== 0n) {
        let fracStr = frac.toString().padStart(2, '0');
        fracStr = fracStr.replace(/0+$/, '');
        result += '.' + fracStr;
    }

    const label = labels[idx + 1] || '';
    return `${result} ${label}`.trim();
}

function buildAchievementBanner(banner_type, message, title, image_url = null) {
    const bannerUrls = {
        "achievement": "https://pandoraportal.ca/botimages/banners/achievement_banner.png",
        "blank1": "https://pandoraportal.ca/botimages/banners/blank_banner_1.png",
        "blank2": "https://pandoraportal.ca/botimages/banners/blank_banner_2.png",
        "blank3": "https://pandoraportal.ca/botimages/banners/blank_banner_3.png"
    };
    let banner = document.createElement("div");
    let titleContent = document.createElement("div");
    let rowContainer = document.createElement("div");
    let imgContent = document.createElement("div");
    let msgContent = document.createElement("div");
    banner.style.backgroundImage = `url(${bannerUrls[banner_type] || bannerUrls['achievement']})`;
    banner.classList.add("hideItem");
    rowContainer.classList.add("row-container");
    titleContent.classList.add("highlight-text");
    if (banner_type == "achievement") {
        titleContent.classList.add("achievement-title-shifted");
        msgContent.classList.add("achievement-text-shifted");
    } else {
        titleContent.classList.add("achievement-title");
        msgContent.classList.add("achievement-text");
    }
    imgContent.classList.add("achievement-image");
    titleContent.innerText = title;
    msgContent.innerText = message;
    if (image_url) {
        imgContent.style.backgroundImage = `url(${image_url})`;
    }
    rowContainer.appendChild(imgContent);
    rowContainer.appendChild(msgContent);
    banner.appendChild(titleContent);
    banner.appendChild(rowContainer);
    return banner;
}

function showAchievements(achievement_data) {
    if (achievement_data.length == 0) {
        return;
    }
    bannerScreen.innerHTML = "";
    achievement_data.forEach((data, index) => {
        const { banner_type, message, title, image_url } = data;
        const banner = buildAchievementBanner(banner_type, message, title, image_url);
        bannerScreen.appendChild(banner);
        setTimeout(() => {
            banner.classList.add("achievement-banner");
            banner.classList.remove("hideItem");
        }, index * 1500);
        setTimeout(() => {
            banner.classList.add("fade-out");
            setTimeout(() => banner.remove(), 1000);
        }, 8000 + index * 1500);
    });
}

