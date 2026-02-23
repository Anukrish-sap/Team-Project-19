function toggleChat() {
    document.getElementById("support-chat").classList.toggle("open");
}

function sendMessage(event) {
    event.preventDefault();

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
    .then(response => response.text())
    .then(reply => addMessage(reply, "bot"))
    .catch(() => addMessage("Support service unavailable.", "bot"));
}

function addMessage(text, type) {
    const box = document.getElementById("chat-messages");
    const div = document.createElement("div");

    div.className = type === "user" ? "chat-user" : "chat-bot";
    div.textContent = text;

    box.appendChild(div);
    box.scrollTop = box.scrollHeight;
}
