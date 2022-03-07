<?php
  session_start();
  require_once('./conn.php');
  require_once('./utils.php');
  $username = $_SESSION['username'];
  $content = $_POST['content'];

  // 檢查身分
  $role = get_role($username);

  // 被 ban 的使用者
  if ($role === 0) {
    header('Location: index.php?errorCode=2');
    die();
  }

  // 檢查留言內容
  if (empty($content)) {
    header('Location: index.php?errorCode=1');
    die();
  }

  // 加到資料庫
  $sql = "INSERT INTO comments(`username`, `content`) VALUES (?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('ss', $username, $content);
  

  try {
    $result = $stmt->execute();
    header('Location: index.php');
  } catch (Exception $e) {
    echo '執行失敗：' . $e->getMessage() . '<br>';
    echo '錯誤代碼：' . $conn->errno;
  }
?>