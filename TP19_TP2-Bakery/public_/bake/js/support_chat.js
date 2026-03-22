function toggleChat() {
    document.getElementById("support-chat").classList.toggle("open");
}

function sendMessage(event) {
    if (event) event.preventDefault();

    const input = document.getElementById("chat-input");
    const message = input.value.trim();
    if (!message) return;

    addMessage(message, "user");
    input.value = "";

    fetch("/public_/bake/faq_response.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "message=" + encodeURIComponent(message)
    })
    .then(response => response.json())
    .then(data => {
        if (data.reply) {
            addMessage(data.reply, "bot");
        }

        if (data.buttons && data.buttons.length > 0) {
            addButtons(data.buttons);
        }
    })
    .catch(() => {
        addMessage("There was a temporary issue. Please try again.", "bot");
    });
}

function addMessage(text, type) {
    const box = document.getElementById("chat-messages");

    const div = document.createElement("div");
    div.className = type === "user" ? "chat-user" : "chat-bot";
    div.innerHTML = text;

    box.appendChild(div);
    box.scrollTop = box.scrollHeight;
}

function addButtons(buttons) {
    const box = document.getElementById("chat-messages");

    const container = document.createElement("div");
    container.className = "chat-buttons";

    const links = {
        "View Products": "/public_/bake/bakes.php",
        "View Basket": "/public_/bake/basket.php",
        "Login": "/public_/bake/loginpage.php",
        "Register": "/public_/bake/register.php",
        "Track an Order": "/public_/bake/helppage.php",
        "Place an Order": "/public_/bake/bakes.php",
        "Delivery Times": null,
        "Account Help": null
    };

    buttons.forEach(text => {
        const btn = document.createElement("button");
        btn.textContent = text;

        btn.onclick = () => {
            if (links[text]) {
                window.location.href = links[text];
            } else {
                document.getElementById("chat-input").value = text;
                sendMessage();
            }
        };

        container.appendChild(btn);
    });

    box.appendChild(container);
    box.scrollTop = box.scrollHeight;
}