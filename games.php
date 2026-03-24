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
  main {
    margin: 0;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #FAF7F2;
    gap: 30px;
  }

  .game {
    display: grid;
    grid-template-columns: repeat(20, 22px);
    grid-template-rows: repeat(20, 22px);
    gap: 2px;
    background: #0b0f14;
    padding: 8px;
    border-radius: 10px;
  }

  .cell {
    width: 22px;
    height: 22px;
    background: #003F91;
    border-radius: 6px;
    position: relative;
  }

  /* Snake */
  .snake {
    background: linear-gradient(145deg, #00ff99, #00cc77);
  }

  .snake.head {
    background: #66ffd1;
  }

  /* Food (Pringle) */
  .food::before {
    content: "";
    position: absolute;
    width: 18px;
    height: 10px;
    left: 2px;
    top: 6px;
    background: linear-gradient(145deg, #ffe066, #ffcc00);
    border-radius: 50%;
    transform: rotate(-25deg);
  }

  /* Bomb Image */
  .obstacle {
    background: url("boemm.png") center/contain no-repeat;
    animation: pulse 1s infinite alternate;
  }

  .groter {
    font-size: 20px;
  }

  @keyframes pulse {
    from {
      transform: scale(1);
    }

    to {
      transform: scale(1.1);
    }
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

  <main>
    <div>
      <h2>Popular Games</h2>

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
  </main>
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
      }
      while (
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

    document.addEventListener("keydown", e => {
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
  </script>

</body>

</html>