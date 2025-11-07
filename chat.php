<script>
async function sendMessage() {
  const message = document.getElementById('user-message').value.trim();
  if (!message) return;

  const chatBox = document.getElementById('chat-box');
  chatBox.innerHTML += `<p><strong>You:</strong> ${message}</p>`;

  document.getElementById('user-message').value = '';

  try {
    const response = await fetch('http://127.0.0.1:5001/chat', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ message })
    });

    const data = await response.json();
    chatBox.innerHTML += `<p><strong>Bot:</strong> ${data.reply}</p>`;

    // 🎧 Play voice if available
    if (data.audio) {
      const audio = new Audio("data:audio/mp3;base64," + data.audio);
      audio.play();
    }

    // 🖼 Show image if available
    if (data.image) {
      chatBox.innerHTML += `<img src="${data.image}" alt="Result Image" style="max-width:100%; border-radius:10px; margin-top:10px;">`;
    }

    chatBox.scrollTop = chatBox.scrollHeight;

  } catch (error) {
    chatBox.innerHTML += `<p style="color:red;"><strong>Error:</strong> Unable to connect to chatbot backend.</p>`;
  }
}
</script>
