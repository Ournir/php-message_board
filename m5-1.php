<?php
  // DB接続設定
	$dsn = 'データベース名';
	$user = 'ユーザー名';
	$password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
  
  // テーブルの作成
  $sql = 'CREATE TABLE IF NOT EXISTS message_board'
	    ." ("
	    . "id INT AUTO_INCREMENT PRIMARY KEY,"
	    . "name char(32),"
        . "comment TEXT,"
        ."date datetime DEFAULT CURRENT_TIMESTAMP,"
        ."password char(32)"
	    .");";
  $stmt = $pdo->query($sql);
  
//   $name = $_POST["name"];
//   $comment = $_POST["comment"];
//   $password = $_POST["password"];
//   $delPass = $_POST["delPass"];
//   $delNum = $_POST["deleteNum"];
//   $edit_flag = $_POST["edit_flag"];
//   $editNum = $_POST["editNum"];
//   $editPass = $_POST["editPass"];
//   $alert[] = "";

  // 編集情報初期化
  $editNumber = "";
  $editName = "";
  $editComment = "";
  
    //   パスワード入手処理
  if(!empty($_POST["password"])){
        $password = $_POST["password"];
    }

    if(!empty($_POST["deleteNum"])){
        $id = $_POST["deleteNum"];
        $sql = 'SELECT * FROM message_board';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            if($row['id'] == $id){
                $pass = $row['password'];
            }
        }
    }elseif(!empty($_POST["editNum"])){
        $id = $_POST["editNum"];
        $sql = 'SELECT * FROM message_board';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            if($row['id'] == $id){
                $pass = $row['password'];
            }
        }
    }

  if(!empty($_POST["name"]) && !empty($_POST["comment"])){
    // 編集対象番号があるか
    if(!empty($_POST["edit_flag"])){
      // 編集（更新）処理
      $name = $_POST["name"];
      $comment = $_POST["comment"];
      $edit_flag = $_POST["edit_flag"];
      $sql = 'UPDATE message_board SET name = :name,comment =:comment WHERE id = :id;';
      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(':name',$name,PDO::PARAM_STR);
      $stmt->bindParam(':comment',$comment,PDO::PARAM_STR);
      $stmt->bindParam(':id',$edit_flag,PDO::PARAM_INT);
      $stmt->execute();
    // 新規投稿モード
    }else{
      // 挿入処理
      if(!empty($_POST["password"])){
          $name = $_POST["name"];
          $comment = $_POST["comment"];
          $sql = $pdo -> prepare("INSERT INTO message_board (name, comment,password) 
                    VALUES (:name, :comment,:password)");
          $sql -> bindParam(':name', $name, PDO::PARAM_STR);
          $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
          $sql -> bindParam(':password', $password, PDO::PARAM_STR);
          $sql -> execute();
      }else{
          echo "パスワードが入力されていません。<br>";
      }
    }
  // 削除モード
  }elseif(!empty($_POST["deleteNum"]) && !empty($_POST["delPass"])){
    if($_POST["delPass"] == $pass){
        $id = $_POST["deleteNum"];
        $sql = 'DELETE FROM message_board WHERE id = :id;';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id',$id,PDO::PARAM_INT);
        $stmt->execute();
    }else{
        echo "パスワードが違います。<br>";
    }
  // 編集モード
  }elseif(!empty($_POST["editNum"]) && !empty($_POST["editPass"]) 
    && $pass == $_POST["editPass"]){
    if($_POST["editPass"] == $pass){
        $id = $_POST["editNum"];
        $sql = 'SELECT * FROM message_board';
        $stmt = $pdo->query($sql);
        $editData = $stmt->fetchAll();
        // formのvalueに値をセット
        foreach($editData as $Data){
            if($Data['id'] == $id){
                $editNumber = $Data['id'];
                $editName = $Data['name'];
                $editComment = $Data['comment'];
                break;
            }
        }
    }else{
        echo "パスワードが違います。<br>";
    }
  }
?>
<!DOCTYPE html>
    <head>
        <meta charset="UTF-8">
        <title>mission_5-1</title>
        <style>
            span{
                display: inline-block;
                width: 7em;
            }
        </style>
    </head>
    <body>
        <h1>コメントお願いします！</h1>
        <form action="" method="post">
            <input type="hidden" name="edit_flag" value="<?php echo $editNumber; ?>">
            <span>名前</span><input type="text" name="name" value= "<?php echo  $editName; ?>" >
            <span>コメント</span><input type="text" name="comment" value = "<?php echo $editComment; ?>">
            <span>パスワード</span><input type="text" name="password">
            <input type="submit" name="submit" value="送信"><br>
        </form><br>
        <form action="" method="POST">
            <span>削除対象番号</span><input type="number" name="deleteNum">
            <span>パスワード</span><input type="text" name="delPass">
            <input type="submit" name="delete" value="送信"><br>
        </form><br>
        <form action="" method="POST">
            <span>編集対象番号</span><input type="number" name="editNum">
            <span>パスワード</span><input type="text" name="editPass">
            <input type="submit" name="edit" value="送信"><br>
        </form><br>
    </body>

    
</html>
<!--ブラウザ表示-->
<?php
//   if($alert != ""){
//       foreach($alert as $a){
//           echo $a."<br>";
//       }
//   }
  
//   $sql = 'SHOW TABLES';
//     $result = $pdo -> query($sql);
//     foreach($result as $table){
//         echo $table[0];
//         echo "<br>";
//     }
//     echo "<hr>";
    
//     // 作成したテーブルの構成確認
//     $sql = 'SHOW CREATE TABLE message_board';
//     $conponent = $pdo -> query($sql);
//     foreach($conponent as $con){
//         echo $con[1];
//     }
//     echo "<hr>";

  echo "コメント一覧<br>";
  $sql2 = 'SELECT * FROM message_board';
  $stmt2 = $pdo->query($sql2);
  $results = $stmt2->fetchAll();
  foreach($results as $row){
      //$rowの中にはテーブルのカラム名が入る
  echo $row['id'].',';
  echo $row['name'].',';
  echo $row['comment'].',';
  echo $row['date']."<br>";
//   echo $row['date'].',';
//   echo $row['password']."<br>";
  
echo "<hr>";
  }
?>