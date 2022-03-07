<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>留言板</title>
  <link rel="stylesheet" href="./css/reset.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.6.0/css/all.min.css" integrity="sha512-ykRBEJhyZ+B/BIJcBuOyUoIxh0OfdICfHPnPfBy7eIiyJv536ojTCsgX8aqrLQ9VJZHGz4tvYyzOM0lkgmQZGw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="./css/style.css">
</head>
<body>

  <header class="warning">注意！本站為練習用網站，因教學用途刻意忽略資安的實作，註冊時請勿使用任何真實的帳號或密碼。</header>


  <main class="board">
    <div class="board__header">
      <div class="board__top">
        <h1 class="board__title">Register</h1>
        <div class="board__buttons">
          <a class="button" href="./index.php">回留言板</a>
        </div>
      </div>

      <form class="board__form" method="POST" action="./handle_register.php">
        <?php
          $msg = 'Error';
          if (!empty($_GET['errorCode'])) {
            if ($_GET['errorCode'] === '1') {
              $msg = '資料有缺';
            } else if ($_GET['errorCode'] === '2') {
              $msg = '這個帳號已經被註冊過囉！';
            }
            echo '<div class="error-msg">錯誤：' . $msg . '</div>';
          }
        ?>
        <div class="input-block">
          <label class="input-block__label" for="nickname">暱稱：</label>
          <input class="input-block__input" type="text" name="nickname" id="nickname">
        </div>
         <div class="input-block">
          <label class="input-block__label" for="username">帳號：</label>
          <input class="input-block__input" type="text" name="username" id="username">
        </div>
        <div class="input-block">
          <label class="input-block__label" for="password">密碼：</label>
          <input class="input-block__input" type="password" name="password" id="password">
        </div>
        <button class="button">送出</button>
      </form>
    </div>

  </main>

  
</body>
</html>