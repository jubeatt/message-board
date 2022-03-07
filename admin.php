<?php
  session_start();
  require_once("./conn.php");
  require_once("./utils.php");
  
  if (empty($_SESSION['username'])) {
    die('請先登入');
  }

  // 取得使用者資訊
  $username = strtolower($_SESSION['username']);
  $role = get_role($username);

  if (($role !== 2)) {
    die('你不具管理員身分');
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>留言板</title>
  <link rel="stylesheet" href="./css/reset.css">
  <!-- font-awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.6.0/css/all.min.css" integrity="sha512-ykRBEJhyZ+B/BIJcBuOyUoIxh0OfdICfHPnPfBy7eIiyJv536ojTCsgX8aqrLQ9VJZHGz4tvYyzOM0lkgmQZGw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="./css/style.css">
</head>
<body>

  <header class="warning">注意！本站為練習用網站，因教學用途刻意忽略資安的實作，註冊時請勿使用任何真實的帳號或密碼。</header>

  <div class="top">
    <h1 class="top__title">管理權限</h1>
    <a class="button" href="./index.php">回留言板</a>
  </div>

  <main class="board">
    <div class="board__body">
      <?php
        $sql = "SELECT * FROM users ORDER BY id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $template = '
        <div class="%s">
          <div class="card__avatar"></div>
          <div class="card__content">
            <div class="card__info">
              <div class="card__author">
                %s
                <span class="card__icon"><i class="fas fa-crown"></i></span>
              </div>
              <div class="card__time">%s</div>
            </div>
            <div class="card__username">@%s</div>
          </div>
          <div class="card__editor">
            <button class="card__editor-button fas fa-pen"  data-role="%s" data-username="%s"></button>
          </div>
        </div>';
        while ($row = $result->fetch_assoc()) {
          // 預設 card 樣式
          $card_type = 'card';
          //  略過本人
          if ($row['username'] === $username) {
            continue;
          }
          // 檢查權限
          if ($row['role'] === 2) {
            $card_type = 'card card--admin';
          } else if ($row['role'] === 0) {
            $card_type = 'card card--suspended';
          }
          // 印出 HTML
          echo sprintf(
            $template,
            $card_type, 
            escape($row['nickname']),
            escape($row['created_at']),
            escape($row['username']),
            escape($row['role']),
            escape($row['username'])
          );
        }
      ?>
  </main>

  
  <script>

    // 動態產生 modal 
    const board = document.querySelector('.board');
    board.addEventListener('click', function (e) {
      if (e.target.classList.contains('card__editor-button')) {
        const userId= e.target.getAttribute('data-role');
        const username = e.target.getAttribute('data-username');
        const div = document.createElement('div');
        document.body.appendChild(div);

        div.outerHTML = `
        <div class="modal">
          <div class="modal__inner">
            <button class="modal__close-button">x</button>
            <form method="POST" action="./handle_update_role.php">
              <select class="select-box" name="role">
                <option value="0" ${userId === '0' ? 'selected' : ''}>停權用戶</option>
                <option value="1" ${userId === '1' ? 'selected' : ''}>一般用戶</option>
                <option value="2" ${userId === '2' ? 'selected' : ''}>管理員</option>
              </select>
              <input type="hidden" name="username" value="${username}">
              <button class="button">送出</button>
            </form>
          </div>
        </div>
        `;
      }
    })
    window.addEventListener('click', function (e) {
      if (
        e.target.classList.contains('modal__close-button') ||
        e.target.classList.contains('modal')
      ) {
        const modal = document.querySelector('.modal');
        document.body.removeChild(modal);
      }
    });
  </script>
</body>
</html>