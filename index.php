<?php
  session_start();
  require_once("./conn.php");
  require_once("./utils.php");
  $username = Null;
  $per_page = 10;
  $page = 1;
  $isAdmin = false;

  // 檢查分頁
  if (!empty($_GET['page'])) {
    $page = intval($_GET['page']);
  }

  // 檢查登入
  if (!empty($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $user = get_user($username);
    $nickname = $user['nickname'];
    $role = get_role($username);
    // 檢查身分
    if ($role === 2) {
      $isAdmin = true;
    }
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


  <main class="board">
    <div class="board__header">
      <div class="board__top">
        <h1 class="board__title">Comments</h1>
        <div class="board__buttons">
          <?php if ($isAdmin) { ?>
            <a class="button" href="./admin.php">管理權限</a>
          <?php } ?>
          <?php if (empty($username)) {?>
            <a class="button" href="./login.php">會員登入</a>
            <a class="button" href="./register.php">會員註冊</a>
          <?php } else { ?>
            <button class="button button-edit">編輯暱稱</button>
            <a class="button" href="./handle_logout.php">登出</a>
          <?php }?>
        </div>
      </div>

      <?php if (!empty($username)) {?>
        <div class="greeting">嗨～<span class="greeting__nickname"><?php echo escape($nickname); ?></span>，今天也來寫點東西吧 (ﾉ>ω<)ﾉ</div>
      <?php }?>

      <?php
          $msg = 'Error';
          if (!empty($_GET['errorCode'])) {
            if ($_GET['errorCode'] === '1') {
              $msg = '請輸入內容';
            }
            if ($_GET['errorCode'] === '2') {
              $msg = '拍謝，你已被禁止留言！';
            }
            echo '<div class="error-msg">錯誤：' . $msg . '</div>';
          }
        ?>

      <form class="nickname-form hide" method="POST" action="handle_update_nickname.php">
        <div class="input-block input-block--nickname">
          <input class="input-block__input" type="text" name="nickname">
          <button class="button">送出</button>
        </div>
      </form>

      <form class="board__form" method="POST" action="./handle_add_comments.php">
        <div class="input-block">
          <label class="input-block__label" for="content">內容：</label>
          <textarea class="input-block__textarea" name="content" id="content" placeholder="請輸入你的留言..."></textarea>
        </div>
        <?php if (empty($username)) {?>
          <div class="login-please">請先登入</div>
        <?php } else { ?>
          <button class="button">送出</button>
        <?php } ?>
      </form>
    </div>

    <div class="line-break"></div>


    <div class="board__body">

      <?php
        $sql = 
          "SELECT users.role, users.username, users.nickname, comments.content, comments.created_at, comments.id, comments.is_deleted
          FROM comments LEFT JOIN users ON comments.username=users.username
          WHERE comments.is_deleted IS null
          ORDER BY comments.id DESC LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($sql);
        $offset = ($page-1) * $per_page;
        $stmt->bind_param('ii', $per_page, $offset);
        $result = $stmt->execute();
        if (!$result) {
          die('執行失敗，' . $conn->error);
        }
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
      ?>
      <div class="card
        <?php 
          if ($row['role'] === 2) {
            echo 'card--admin';
          } else if ($row['role'] === 0) {
            echo 'card--suspended';
          } 
        ?>
      ">
        <div class="card__avatar"></div>
        <div class="card__content">
          <div class="card__info">
            <div class="card__author">
              <span class="card__icon"><i class="fas fa-crown"></i></span>
              <?php echo escape($row['nickname']); ?>
            </div>
            <div class="card__time"><?php echo escape($row['created_at']); ?></div>
          </div>
          <div class="card__username">@<?php echo escape($row['username']); ?></div>
          <div class="card__text"><?php echo escape($row['content']); ?></div>
        </div>
        <?php if ($row['username'] === $username || $isAdmin) {?>
        <div class="card__editor">
          <a class="card__editor-button" href="./update_comment.php?id=<?php echo $row['id']; ?>"><i class="fas fa-pen"></i></a>
          <a class="card__editor-button" href="./hande_delete_comment.php?id=<?php echo $row['id']; ?>"><i class="fas fa-trash-alt"></i></a>
        </div>
        <?php }?>
      </div>
      <?php }; ?>
    </div>

    <div class="line-break"></div>

    <div class="pagination">
      <?php
        $sql = 
          "SELECT COUNT(comments.id) AS total
          FROM comments
          WHERE comments.is_deleted IS NULL";
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute();
        if (!$result) {
          die('執行失敗，' . $conn->error);
        }
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
      
        $total_page = intval(ceil($row['total'] / $per_page));
      ?>
      <div class="pagination__status">
        目前在第 <?php echo $page; ?> 頁，總共有 <?php echo $total_page; ?> 頁。
      </div>
      <div class="pagination__buttons-wrap">
        <?php if ($page !== 1) {?>
          <a class="pagination__button" href="index.php?page=<?php echo 1; ?>">首頁</a>
          <a class="pagination__button" href="index.php?page=<?php echo $page - 1;?>">上一頁</a>
        <?php }?>
        <?php if ($page !== $total_page) {?>
          <a class="pagination__button" href="index.php?page=<?php echo $page + 1;?>">下一頁</a>
          <a class="pagination__button" href="index.php?page=<?php echo $total_page; ?>">最後一頁</a>
        <?php } ?>
      </div>
    </div>
  </main>

  <script>
    const editButton = document.querySelector('.button-edit')
    if (editButton) {
      editButton.addEventListener('click' ,() => {
        document.querySelector('.nickname-form').classList.toggle('hide');
      })
    }
  </script>
</body>
</html>