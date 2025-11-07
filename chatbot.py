from flask import Flask, request, jsonify, send_file
from flask_cors import CORS
from gtts import gTTS
from io import BytesIO
from base64 import b64encode
import mysql.connector

app = Flask(__name__)
CORS(app)

# --- Database Connection ---
conn = mysql.connector.connect(
    host="localhost",
    user="root",
    password="Abhiroop@7767",
    database="ai_learningdb"
)
cursor = conn.cursor(dictionary=True)

# --- Save Chat Function ---
def save_chat(user_msg, bot_reply, user_id=None, image_url=None, audio_base64=None):
    cursor.execute(
        """
        INSERT INTO chat_history (user_message, bot_reply, user_id, image_url, audio_base64)
        VALUES (%s, %s, %s, %s, %s)
        """,
        (user_msg, bot_reply, user_id, image_url, audio_base64)
    )
    conn.commit()


# --- Knowledge Graph Data ---
knowledge_graph = {
    "ai": ["machine learning", "neural networks", "computer vision"],
    "machine learning": ["supervised learning", "unsupervised learning", "deep learning"],
    "data science": ["statistics", "data visualization", "big data"],
    "deep learning": ["cnn", "rnn", "neural networks"],
    "neural networks": ["activation function", "backpropagation", "perceptron"],
    "supervised learning": ["classification", "regression"],
    "unsupervised learning": ["clustering", "dimensionality reduction"]
}


# --- Chatbot Logic with Knowledge Graph ---
def chatbot_response(user_input):
    text = user_input.lower()
    topic = None
    reply = ""
    info = ""
    image = ""
    related = []

    if "hello" in text or "hi" in text:
        reply = "Hello! How can I help you today?"
        info = "You can ask about AI, Machine Learning, or Data Science!"
        image = "https://cdn-icons-png.flaticon.com/512/4712/4712108.png"
        return {"reply": reply, "info": info, "image": image, "related": []}

    # Find matching topic
    for key in knowledge_graph.keys():
        if key in text:
            topic = key
            break

    if topic:
        reply = f"Let's talk about {topic.title()}!"
        info = f"{topic.title()} is related to: {', '.join(knowledge_graph[topic])}."
        image = f"https://via.placeholder.com/400x200.png?text={topic.replace(' ', '+')}"
        related = knowledge_graph[topic]
    else:
        reply = "I’ll have to look that up — can you specify your topic?"
        info = "Try asking about AI, Machine Learning, or Data Science!"
        image = "https://cdn-icons-png.flaticon.com/512/4712/4712108.png"
        related = []

    return {"reply": reply, "info": info, "image": image, "related": related}


# --- Chat Route ---
@app.route("/chat", methods=["POST"])
def chat():
    data = request.json
    user_msg = data.get("message")
    user_id = data.get("user_id")

    # Generate chatbot reply
    bot_data = chatbot_response(user_msg)
    bot_reply = bot_data["reply"]
    image_url = bot_data["image"]
    info_text = bot_data["info"]
    related_topics = bot_data["related"]

    # 🎧 Generate Voice (Base64)
    tts = gTTS(bot_reply)
    mp3_fp = BytesIO()
    tts.write_to_fp(mp3_fp)
    mp3_fp.seek(0)
    audio_base64 = b64encode(mp3_fp.read()).decode('utf-8')

    # 💾 Save chat to DB
    save_chat(user_msg, bot_reply, user_id, image_url, audio_base64)

    return jsonify({
        "reply": bot_reply,
        "info": info_text,
        "image": image_url,
        "related": related_topics,
        "audio": audio_base64
    })


# --- Chat History Route ---
@app.route("/history", methods=["GET"])
def get_history():
    user_id = request.args.get("user_id")

    if user_id:
        cursor.execute("SELECT * FROM chat_history WHERE user_id = %s ORDER BY id ASC", (user_id,))
    else:
        cursor.execute("SELECT * FROM chat_history ORDER BY id ASC")

    rows = cursor.fetchall()

    html = """
    <html>
    <head>
        <title>Chat History</title>
        <style>
            body { font-family: Arial; background: #f9f9f9; color: #333; padding: 20px; }
            .chat-entry {
                background: #fff;
                border-radius: 10px;
                box-shadow: 0 0 5px rgba(0,0,0,0.1);
                margin-bottom: 15px;
                padding: 15px;
            }
            .user { color: #007bff; font-weight: bold; }
            .bot { color: #28a745; font-weight: bold; }
            img { max-width: 100%; border-radius: 10px; margin-top: 10px; }
            button { margin-top: 8px; background: #007bff; color: #fff; border: none; padding: 6px 12px; border-radius: 5px; cursor: pointer; }
            button:hover { background: #0056b3; }
        </style>
    </head>
    <body>
        <h2>🧠 Chat History</h2>
    """

    for row in rows:
        html += f"""
        <div class="chat-entry">
            <p><span class="user">You:</span> {row['user_message']}</p>
            <p><span class="bot">Bot:</span> {row['bot_reply']}</p>
        """
        if row['image_url']:
            html += f'<img src="{row["image_url"]}" alt="Chat Image">'
        if row['audio_base64']:
            html += f"""
            <br>
            <button onclick="playAudio('{row['audio_base64']}')">🔊 Play Voice</button>
            """
        html += "</div>"

    html += """
        <script>
        function playAudio(base64) {
            const audio = new Audio("data:audio/mp3;base64," + base64);
            audio.play();
        }
        </script>
    </body>
    </html>
    """

    return html


# --- Voice Route ---
@app.route("/voice")
def voice():
    text = request.args.get("text", "Hello")
    tts = gTTS(text)
    mp3_fp = BytesIO()
    tts.write_to_fp(mp3_fp)
    mp3_fp.seek(0)
    return send_file(mp3_fp, mimetype="audio/mpeg")


# --- Run Flask App ---
if __name__ == "__main__":
    app.run(host="127.0.0.1", port=5001, debug=True)
