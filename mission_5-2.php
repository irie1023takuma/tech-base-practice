<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-2</title>
</head>

<body>

<?php
   // DB接続設定
    $dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

        //TABLE作成
    $sql = "CREATE TABLE IF NOT EXISTS  Bulletinboard"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY, "//加算されていく
    . "name char(32),"
    . "comment TEXT, "
    . "day TEXT, "
    . "pass TEXT"
    .");";
    $stmt = $pdo->query($sql);




    //投稿
    if(!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["pass"]) &&
    empty($_POST["delete"]) && empty($_POST["edit"]) && empty($_POST["pass2"]) && empty($_POST["pass3"])) {

    $sql = $pdo -> prepare("INSERT INTO Bulletinboard (name, comment, day, pass)
    VALUES (:name, :comment, :day, :pass)");
    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
    $sql->bindParam(':day', $day, PDO::PARAM_STR);
    $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    $day = date("Y年m月d日 H:i:s");
    $pass = $_POST["pass"];
    $sql -> execute();

    }

   //削除
    if(!empty($_POST["delete"]) && !empty($_POST["pass2"]) &&
    empty($name) && empty($comment) && empty($pass) && empty($_POST["edit"])) {
        $id = $_POST["delete"];
        $pass = $_POST["pass2"];
        $sql = 'delete from Bulletinboard where id=:id AND pass=:pass';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':pass',$pass,PDO::PARAM_STR);
        $stmt->execute();


        $sql = 'SELECT * FROM Bulletinboard';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach($results as $deleterow) {
            if($deleterow['id'] == $_POST['delete'] && $deleterow['pass'] != $_POST['pass2']) {
                echo "パスワードが違います";
            }
        }

   }

   //編集番号選択
   if(!empty($_POST["edit"]) && !empty($_POST["pass3"])
    && empty($_POST["delete"]) && empty($_POST["pass2"])
    && empty($_POST["name"]) && empty($_POST["comment"]) && empty($_POST["pass"])) {

        $id = $_POST["edit"];
        $pass = $_POST["pass3"];
        $sql = 'SELECT * FROM Bulletinboard WHERE id=:id AND pass=:pass';
        $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
        $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
        $stmt->execute();   // ←SQLを実行する。

        $sql = 'SELECT * FROM Bulletinboard';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach($results as $editrow) {
            if($editrow['id'] == $_POST['edit'] && $editrow['pass'] == $_POST['pass3']) {
                $editname = $editrow['name'];
                $editcomment = $editrow['comment'];
                $editpass = $editrow['pass'];
                $editid = $editrow['id'];
            }elseif($editrow['id'] == $_POST['edit'] && $editrow['pass'] != $_POST['pass3']) {
                echo "パスワードが違います";
            }
        }

    }

    //内容編集
    if(!empty($_POST["editnum"]) && !empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["pass"])
    && empty($_POST["delete"]) && empty($_POST["edit"]) && empty($_POST["pass2"])) {
        $name = $_POST["name"];
        $comment = $_POST["comment"];
        $id = $_POST["editnum"];
        $pass = $_POST["pass"];

        $sql = 'UPDATE Bulletinboard SET name=:name,comment=:comment, pass=:pass WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
        $stmt->execute();


        $results = $stmt->fetchAll();

    }

    ?>

<form action ="" method="post">

        <input type="name" name="name"  placeholder="名前"
        value="<?php if(!empty($_POST["edit"]) && !empty($_POST["pass3"])) {
            echo $editname;}?>"><br>


        <input type="text" name="comment"  placeholder="コメント"
        value="<?php if(!empty($_POST["edit"]) && !empty($_POST["pass3"])) {
            echo $editcomment;}?>"><br>

        <input type="text" name="pass"  placeholder="パスワード"
        value="<?php if(!empty($_POST["edit"]) && !empty($_POST["pass3"])) {
            echo $editpass;}?>"><br>
        <input type="submit"><br>

    <!--編集番号がポストされた場合以下の削除フォームと編集フォームは不要になるのでその場合type属性をhiddenにして隠すための処理-->

        <?php if(!empty($_POST["edit"]) && !empty($_POST["pass3"])
        && $editid == $_POST['edit'] && $editpass == $_POST['pass3']) {
            echo "投稿番号:";}
        ?>
        <input type="<?php if(!empty($_POST["edit"]) && !empty($_POST["pass3"])
        && $editid == $_POST['edit'] && $editpass == $_POST['pass3']) {
            echo "text";}
        else{
            echo "hidden";
            }
        ?>" name="editnum" value="<?php if(!empty($_POST["edit"]) && !empty($_POST["pass3"])) {
            echo $editid;}?>"><br><br>


        <!--削除フォーム-->
        <input type="<?php if(!empty($_POST["edit"]) && !empty($_POST["pass3"])
        && $editid == $_POST['edit'] && $editpass == $_POST['pass3']) {
            echo "hidden";}
        else{
            echo "number";
            }
        ?>" name ="delete" placeholder ="削除対象番号"><br>


        <input type="<?php if(!empty($_POST["edit"]) && !empty($_POST["pass3"])
        && $editid == $_POST['edit'] && $editpass == $_POST['pass3']) {
            echo "hidden";}
        else{
            echo "text";
            }
        ?>" name="pass2" placeholder="パスワード"><br>


        <input type="<?php if(!empty($_POST["edit"]) && !empty($_POST["pass3"])
        && $editid == $_POST['edit'] && $editpass == $_POST['pass3']) {
            echo "hidden";}
        else{
            echo "submit";
            }
        ?>" value="削除"><br><br>


        <!--編集フォーム-->
        <input type="<?php if(!empty($_POST["edit"]) && !empty($_POST["pass3"])
        && $editid == $_POST['edit'] && $editpass == $_POST['pass3']) {
            echo "hidden";}
        else{
            echo "number";
            }
        ?>" name="edit" placeholder="編集対象番号"><br>

        <input type="<?php if(!empty($_POST["edit"]) && !empty($_POST["pass3"])
        && $editid == $_POST['edit'] && $editpass == $_POST['pass3']) {
            echo "hidden";}
        else{
            echo "text";
            }
        ?>" name="pass3" placeholder="パスワード"><br>

        <input type="<?php if(!empty($_POST["edit"]) && !empty($_POST["pass3"])
        && $editid == $_POST['edit'] && $editpass == $_POST['pass3']) {
            echo "hidden";}
        else{
            echo "submit";
            }
        ?>" value="編集"><br><br>


    </form>

    <span style="font-size:50px">                        掲示板                          </span><br><br>
<?php
    //書き込み処理
    $sql = 'SELECT * FROM Bulletinboard';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
    //$rowの中にはテーブルのカラム名が入る
        echo $row['id'].' . ';
        echo $row['name'].' . ';
        echo $row['comment'].' . ';
        echo $row['day'].'<br>';
        echo "<hr>";
        }
    ?>