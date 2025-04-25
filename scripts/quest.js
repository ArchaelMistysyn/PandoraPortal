const questBox = document.getElementById("quest-box");
const questCharacterImage = document.getElementById("quest-character-image");
const actionBtn1 = document.getElementById("quest-action-btn-1");
const actionBtn2 = document.getElementById("quest-action-btn-2");
const actionBtn3 = document.getElementById("quest-action-btn-3");

function onQuest() {
    clearScreens();
    fetch('./fetch_handler.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "getQuest" })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            questBox.innerHTML = data.quest['html'];
            questCharacterImage.src = data.quest['image'];
            questContainer.style.display = "flex";
            // Handle Buttons
            actionBtn2.classList.add("hideItem");
            actionBtn3.classList.add("hideItem");
            if(data.quest['quest_num'] >= 55) {
                actionBtn1.classList.add("hideItem");
                return;
            }
            const btns = [actionBtn1, actionBtn2, actionBtn3];
            if (data.quest['quest_options'].length > 0) { 
                data.quest['quest_options'].forEach((opt, index) => {
                    const btn = btns[index];
                    btn.innerText = opt[0];
                    if (opt[3]){
                        btn.onclick = () => completeQuest(index + 1);
                        btn.className = "lightbox-button-green";
                    } else {
                        btn.className = "lightbox-button-gray";
                    }
                });
            } else {
                actionBtn1.innerText = "Hand In";
                if (data.ready_status) {
                    actionBtn1.className = "lightbox-button-green";
                    actionBtn1.onclick = () => completeQuest();
                } else {
                    actionBtn1.className = "lightbox-button-gray";
                }
            }
        } else {
            alert(data.message || "Failed to load quest.");
        }
    })
    .catch(error => console.error('Error loading quest:', error));
}

function completeQuest(button_number = 0) {
    blockingScreen.style.display = "flex";
    fetch('./fetch_handler.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "completeQuest", quest_choice: button_number })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            questBox.innerHTML = data.reward_html;
            actionBtn1.className = "lightbox-button-green";
            actionBtn1.innerText = "Claim";
            actionBtn1.onclick = null;
            actionBtn2.style.display = "none";
            actionBtn3.style.display = "none";
            actionBtn2.className = "lightbox-button-gray";
            actionBtn3.className = "lightbox-button-gray";
            actionBtn1.onclick = () => onQuest();
            showAchievements(data.achievement_data);           
        } else {
            alert(data.message || "Failed to complete quest.");
        }
    })
    .catch(error => console.error('Error completing quest:', error))
    .finally(() => {
        blockingScreen.style.display = "none";
    });
}
