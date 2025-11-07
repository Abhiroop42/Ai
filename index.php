<?php
session_start();
$_SESSION['authenticated'] = true; // change true to simulate login
include __DIR__ . '/layout/header.php';
?>
<link rel="stylesheet" href="css/style.css">

<!-- Hero Section -->
<div class="container my-5">
  <div class="row align-items-center">
    <div class="col-md-6">
      <h1>Learn Anytime, Anywhere</h1>
      <p>Top courses from industry experts to boost your career.</p>
      <a href="#" class="btn btn-primary btn-lg">Explore Courses</a>
    </div>
    <div class="col-md-6">
      <img src="360_F_727272961_CK1r3YSfOwxIHctzKOi10C2TuKtZaVNF.jpg" class="img-fluid rounded" alt="Learning Image">
    </div>
  </div>
</div>

<!-- Courses Section -->
<div class="container my-5">
  <h2 class="mb-4">Popular Courses</h2>
  <div class="row">
    <div class="col-md-4 mb-3">
      <div class="card">
        <img src="aiii.jpg" class="card-img-top" alt="Course 1">
        <div class="card-body">
          <h5 class="card-title">Gen AI</h5>
          <p class="card-text">Generative AI, Responsible AI, ChatGPT.</p>
          <a href="#" class="btn btn-primary">Enroll Now</a>
        </div>
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <div class="card">
        <img src="Data-science.jpg" class="card-img-top" alt="Course 2">
        <div class="card-body">
          <h5 class="card-title">Data Science & Machine Learning</h5>
          <p class="card-text">Python, SQL, AI & analytics.</p>
          <a href="#" class="btn btn-primary">Enroll Now</a>
        </div>
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <div class="card">
        <img src="ai.jpg" class="card-img-top" alt="Course 3">
        <div class="card-body">
          <h5 class="card-title">AI</h5>
          <p class="card-text">Artificial Intelligence, Data Science, Development.</p>
          <a href="#" class="btn btn-primary">Enroll Now</a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Chatbot Section -->
<div class="container my-5">
  <h2 class="mb-4 text-center">Ask Our AI Chatbot 🤖</h2>
  <div class="card p-3 shadow-lg">
    <div id="chatbox" style="height:300px; overflow-y:auto; background:#f8f9fa; padding:10px; border-radius:10px;"></div>
    <div class="mt-3 d-flex">
      <input id="userInput" type="text" class="form-control me-2" placeholder="Ask about AI, ML, or Data Science...">
      <button onclick="sendMessage()" class="btn btn-primary">Send</button>
    </div>
  </div>
</div>

<script src="https://unpkg.com/vis-network/standalone/umd/vis-network.min.js"></script>
<script>
let nodes = new vis.DataSet([
  { id: 1, label: "Artificial Intelligence", color: "#ff6f61" }
]);
let edges = new vis.DataSet([]);
let nodeCount = 1;

const container = document.createElement("div");
container.id = "knowledgeGraph";
container.style = "height:400px; border:1px solid #ccc; border-radius:10px; margin-top:20px;";
document.querySelector(".container.my-5:last-of-type").appendChild(container);

const data = { nodes, edges };
const options = {
  nodes: { shape: "dot", size: 20, font: { size: 18 }, borderWidth: 2 },
  edges: { arrows: { to: { enabled: true } }, font: { size: 12, align: "middle" } },
  physics: { enabled: true, stabilization: { iterations: 150 } }
};
const network = new vis.Network(container, data, options);

async function sendMessage() {
  const message = document.getElementById("userInput").value.trim();
  if (!message) return;

  const chatbox = document.getElementById("chatbox");
  chatbox.innerHTML += `<div><b>You:</b> ${message}</div>`;
  document.getElementById("userInput").value = "";

  try {
    const response = await fetch("http://127.0.0.1:5001/chat", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ message: message, user_id: 1 })
    });

    const data = await response.json();

    // Bot text & info
    chatbox.innerHTML += `<div><b>Bot:</b> ${data.reply}</div>`;
    if (data.info) chatbox.innerHTML += `<div style="color:gray;">ℹ️ ${data.info}</div>`;

    // Image display
    if (data.image) {
      chatbox.innerHTML += `<img src="${data.image}" class="img-fluid rounded my-2" style="max-width:200px;">`;
    }

    // Audio playback
    if (data.audio) {
      const audio = new Audio("data:audio/mp3;base64," + data.audio);
      audio.play();
    }

    // Update Knowledge Graph dynamically
    if (data.related && data.related.length > 0) {
      const baseTopic = message.charAt(0).toUpperCase() + message.slice(1);
      let baseNode = nodes.get({ filter: n => n.label.toLowerCase() === baseTopic.toLowerCase() });
      let baseId = baseNode.length ? baseNode[0].id : ++nodeCount;
      if (!baseNode.length) nodes.add({ id: baseId, label: baseTopic, color: "#ffd166" });

      data.related.forEach((topic) => {
        const existing = nodes.get({ filter: n => n.label.toLowerCase() === topic.toLowerCase() });
        let topicId = existing.length ? existing[0].id : ++nodeCount;
        if (!existing.length) nodes.add({ id: topicId, label: topic, color: "#06d6a0" });
        if (!edges.get({ filter: e => e.from === baseId && e.to === topicId }).length) {
          edges.add({ from: baseId, to: topicId, label: "related to" });
        }
      });
    }

  } catch (err) {
    console.error(err);
    chatbox.innerHTML += `<div style='color:red;'>Error connecting to chatbot</div>`;
  }

  chatbox.scrollTop = chatbox.scrollHeight;
}
</script>


<?php include __DIR__ . '/layout/footer.php'; ?>

