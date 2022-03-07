<?php
  require_once('./conn.php');
  function generateToken() {
    $s = '';
    for ($i=0; $i<15; $i++) {
      // A ~ Z
      $s .= chr(rand(65,90));
    }
    return $s;
  }

  function get_user($username) {
    global $conn;
    // 從 token 找出對應的 user
    $sql = "SELECT * FROM users WHERE username='$username'";
    try {
      $result = $conn->query($sql);
      if ($result->num_rows > 0) {
        return $result->fetch_assoc();
      }
      return 'No match user';
    } catch (Exception $e) {
      return 'Failed' . $e;
    }
  }

  function escape($unsafe) {
    return htmlspecialchars($unsafe, ENT_QUOTES);
  }


  function get_role($username) {
    global $conn;
    // 檢查身分
    $sql = "SELECT role FROM users WHERE username=?";
    $stmt= $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['role'];
  }
?>