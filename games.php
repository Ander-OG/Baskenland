<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Popular Games</title>
  <link rel="stylesheet" href="style.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<style>
  body {
    overflow: hidden;
  }
</style>

<body>

  <header>
    <div class="header-inner">
      <h1>Euskal Herria &amp; Netherlands — Cultural Guide</h1>
      <nav>
        <a href="index.php">Sarrera</a>
        <a href="cultures.php">Cultures &amp; Languages</a>
        <a href="food.php">Traditional Food</a>
        <a href="sports.php">Sports</a>
        <a href="games.php" class="active">Popular Games</a>
      </nav>
    </div>
  </header>

  <div id="main">
    <!-- snake game -->
    <div>
      <h2>snake game</h2>

      <div class="wrapper">

        <div class="game" id="game"></div>

      </div>
    </div>
    <div>
      <h2>Score: <span id="score">0</span></h2>
      <h3>High Score: <span id="highscore">0</span></h3>
      <p class="groter">Eat pringles</p>
      <p class="groter">Avoid bombs</p>
      <p class="groter">Press SPACE to pause</p>
    </div>

    <div></div>
    <div></div>
    <!-- chat bot -->
    <div>
      <h2>AI Chatbot</h2>

      <div id="chat-container">
        <h4 id="respond">Ask me something...</h4>
      </div>

      <form id="chatForm">
        <input placeholder="Type something..." name="prompt" type="text" id="userInput" required>
        <input type="submit" value="Send">
      </form>
    </div>
  </div>

</body>

<script>
  const size = 20
  const game = document.getElementById("game")
  const scoreEl = document.getElementById("score")
  const highScoreEl = document.getElementById("highscore")

  const cells = []

  let snake = []
  let direction = {
    x: 0,
    y: 0
  }
  let food = null
  let score = 0
  let obstacles = []

  let speed = 120
  let gameInterval = null
  let isPaused = false

  let highScore = localStorage.getItem("snakeHighScore") || 0
  highScoreEl.textContent = highScore

  for (let i = 0; i < size * size; i++) {
    const cell = document.createElement("div")
    cell.className = "cell"
    game.appendChild(cell)
    cells.push(cell)
  }

  const index = (x, y) => y * size + x

  function resetGame() {
    snake = [{
      x: 10,
      y: 10
    }]
    direction = {
      x: 0,
      y: 0
    }
    score = 0
    obstacles = []
    food = randomEmptyCell()

    scoreEl.textContent = score

    clearInterval(gameInterval)
    gameInterval = setInterval(gameLoop, speed)

    update()
  }

  function randomEmptyCell() {
    let p
    do {
      p = {
        x: Math.floor(Math.random() * size),
        y: Math.floor(Math.random() * size)
      }
    } while (
      snake.some(s => s.x === p.x && s.y === p.y) ||
      obstacles.some(o => o.x === p.x && o.y === p.y)
    )
    return p
  }

  function spawnObstacles(n) {
    for (let i = 0; i < n; i++) {
      obstacles.push(randomEmptyCell())
    }
  }

  function update() {
    cells.forEach(c => c.className = "cell")

    snake.forEach((s, i) => {
      const c = cells[index(s.x, s.y)]
      c.classList.add("snake")
      if (i === 0) c.classList.add("head")
    })

    cells[index(food.x, food.y)].classList.add("food")

    obstacles.forEach(o => {
      cells[index(o.x, o.y)].classList.add("obstacle")
    })

    scoreEl.textContent = score
  }

  function gameLoop() {
    if (isPaused) return
    if (!direction.x && !direction.y) return

    const head = {
      x: snake[0].x + direction.x,
      y: snake[0].y + direction.y
    }

    if (
      head.x < 0 || head.y < 0 ||
      head.x >= size || head.y >= size ||
      snake.some(s => s.x === head.x && s.y === head.y) ||
      obstacles.some(o => o.x === head.x && o.y === head.y)
    ) {
      return gameOver()
    }

    snake.unshift(head)

    if (head.x === food.x && head.y === food.y) {
      score++

      if (score > highScore) {
        highScore = score
        localStorage.setItem("snakeHighScore", highScore)
        highScoreEl.textContent = highScore
      }

      food = randomEmptyCell()

      if (score === 10) spawnObstacles(6)
      if (score === 20) spawnObstacles(4)

      if (score % 10 === 0) {
        speed -= 10
        if (speed < 60) speed = 60

        clearInterval(gameInterval)
        gameInterval = setInterval(gameLoop, speed)
      }
    } else {
      snake.pop()
    }

    update()
  }

  function gameOver() {
    alert("Game Over! Score: " + score);
    speed = 120
    resetGame()
  }

  // ✅ FIXED KEYBOARD HANDLING
  const inputField = document.getElementById("userInput")

  document.addEventListener("keydown", e => {

    // 🚫 Ignore keys if typing in chat input
    if (document.activeElement === inputField) return

    const k = e.key.toLowerCase()

    if (k === " ") {
      isPaused = !isPaused
    }

    if ((k === "arrowup" || k === "w") && direction.y !== 1)
      direction = {
        x: 0,
        y: -1
      }

    if ((k === "arrowdown" || k === "s") && direction.y !== -1)
      direction = {
        x: 0,
        y: 1
      }

    if ((k === "arrowleft" || k === "a") && direction.x !== 1)
      direction = {
        x: -1,
        y: 0
      }

    if ((k === "arrowright" || k === "d") && direction.x !== -1)
      direction = {
        x: 1,
        y: 0
      }
  })

  resetGame()

  // mini chatgpt
  const pageContext = `You are a helpful AI assistant. that tries to help the user about the information about baskenland and the netherlands`;

  let chatHistory = [{
    role: "system",
    content: pageContext
  }];

  const chatForm = document.getElementById("chatForm");
  const chatContainer = document.getElementById("chat-container");
  const respondElement = document.getElementById("respond");

  chatForm.addEventListener("submit", async (e) => {

    e.preventDefault();

    const inputField = document.getElementById("userInput");
    const userPrompt = inputField.value.trim();

    if (!userPrompt) return;

    const userDiv = document.createElement("div");
    userDiv.className = "user-msg";
    userDiv.textContent = "You: " + userPrompt;
    chatContainer.appendChild(userDiv);

    chatHistory.push({
      role: "user",
      content: userPrompt
    });

    inputField.value = "";

    respondElement.textContent = "AI is thinking...";
    respondElement.className = "thinking";

    const url = "https://router.huggingface.co/v1/chat/completions";
    const apiKey = "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";

    try {

      const response = await fetch(url, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "Authorization": `Bearer ${apiKey}`
        },
        body: JSON.stringify({
          model: "deepseek-ai/DeepSeek-V3",
          messages: chatHistory,
          max_tokens: 300
        })
      });

      if (!response.ok) {
        const errorText = await response.text();
        throw new Error(`Server ${response.status}: ${errorText}`);
      }

      const result = await response.json();
      const aiMessage = result.choices[0].message.content;

      chatHistory.push({
        role: "assistant",
        content: aiMessage
      });

      const aiDiv = document.createElement("div");
      aiDiv.className = "ai-msg";
      aiDiv.textContent = "AI: " + aiMessage;

      chatContainer.appendChild(aiDiv);

      respondElement.textContent = "";
      chatContainer.scrollTop = chatContainer.scrollHeight;

    } catch (error) {
      console.error(error);
      respondElement.textContent = "Error: " + error.message;
      respondElement.className = "";
    }
  });
</script>

</html>