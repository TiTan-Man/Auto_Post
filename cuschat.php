<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot TMĐT</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        #chatbox { width: 400px; height: 400px; border: 1px solid #ccc; overflow-y: auto; margin: auto; padding: 10px; }
        #userInput { width: 300px; padding: 5px; }
        button { padding: 5px 10px; }
    </style>
</head>
<body>
    <h2>Chatbot Hỗ Trợ Khách Hàng</h2>
    <div id="chatbox"></div>
    <input type="text" id="userInput" placeholder="Nhập tin nhắn...">
    <button onclick="sendMessage()">Gửi</button>

    <script>
        function sendMessage() {
            let userInput = document.getElementById("userInput").value;
            if (userInput.trim() === "") return;
          
            let chatbox = document.getElementById("chatbox");
            chatbox.innerHTML += "<p><b>Bạn:</b> " + userInput + "</p>";
            document.getElementById("userInput").value = "";
            fetch("chatbot.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ message: userInput })
            })
            .then(response => response.json())
            .then(data => { 
                chatbox.innerHTML += "<p><b>Bot:</b> " + data.reply + "</p>";
                chatbox.scrollTop = chatbox.scrollHeight;
            }); alert(data);
        }
    </script>
</body>
</html>
