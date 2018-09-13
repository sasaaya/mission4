<?php
//DB接続
$dsn='データベース名';
$user='ユーザー名';
$password='パスワード';
$pdo=new PDO($dsn,$user,$password);
//テーブル作成
$sql="CREATE TABLE mission41_bbs"
."("
."id INT,"
."name char(32),"
."comment TEXT,"
."posttime DateTime,"
."pass TEXT"
.");";
$stmt=$pdo->query($sql);



$name = $_POST['name']; //名前

	$comment = $_POST['comment']; //コメント

	$password_set = $_POST['password_set']; //設定したパスワード

	$id_delete = $_POST['id_delete']; //削除する投稿番号

	$id_edit = $_POST['id_edit_post']; //編集する投稿番号

	$id_edit_update = $_POST['id_edit_update']; //編集する投稿番号(id_edit_postと同一)??

	$password = $_POST['password']; //編集・削除時のパスワード



    if($_POST['submit_send']){ //送信ボタンが押された時



        //投稿番号取得

        $sql_count = "SELECT id FROM mission41_bbs ORDER BY id DESC LIMIT 1"; //最新の投稿(一番大きいid)を表示

        $stmt = $pdo -> query($sql_count);

        $id = ($stmt->fetchColumn() + 1); //投稿番号(ID)


        if(ctype_digit($id_edit_update) and strlen($name) and strlen($comment) ){ //編集フォームに記入済みの場合

            $sql = "UPDATE mission41_bbs SET name='$name', comment='$comment' WHERE id = $id_edit_update";

            $result = $pdo -> query($sql);

            echo "投稿番号[" .$id_edit_update. "]を編集しました" . '<br>';



        } else if(strlen($name) and strlen($comment) and strlen($password_set)){  //通常書き込み

            //SQLの書き込みの準備

            $sql = $pdo -> prepare("INSERT INTO mission41_bbs(id, name, comment, posttime, pass) VALUES(:id, :name, :comment, now(), :pass)");

            //各種パラメータを入力

            $sql -> bindParam(':id', $id, PDO::PARAM_INT);

            $sql -> bindParam(':name', $name, PDO::PARAM_STR);

            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);

            $sql -> bindParam(':pass', $password_set, PDO::PARAM_STR);

            //実行

            $sql -> execute();

            echo "投稿されました" . '<br>';



        } else { //未入力パラメータがある場合のエラーメッセージ

            echo "名前・コメント・パスワードを入力しなければ投稿出来ません" . "<br>";

        }

    }



    if($_POST['submit_delete']){ //削除ボタンが押された時

        //idが$id_delete(削除対象番号)かつパスワードが一致したものを削除

        $sql = "SELECT * FROM mission41_bbs WHERE id=$id_delete" ;

        $stmt = $pdo -> query($sql);

        if(strcmp($stmt->fetchColumn(4) , $password) == 0){

            $sql = "DELETE FROM mission41_bbs WHERE id=$id_delete";

            $pdo -> query($sql);

            echo "投稿番号[" .$id_delete. "]は削除されました" . '<br>';

        } else {

            echo "パスワードが一致しません" . '<br>';

        }

    }



    if($_POST['submit_edit']){ //編集ボタンが押された時

        //DBを検索

        $sql = "SELECT * FROM mission41_bbs WHERE id=$id_edit";

        $stmt = $pdo -> query($sql);

        if(strcmp($stmt->fetchColumn(4) , $password) == 0){ //パスワードが一致する場合

            $sql = "SELECT * FROM mission41_bbs WHERE id=$id_edit"; //$stmtを再取得？

            $stmt = $pdo -> query($sql);

            foreach($stmt as $row){

                $eName = $row['name'];

                $eComment = $row['comment'];

                $eID = $id_edit;

            }

            echo "編集後、送信ボタンを押してください" . '<br>';

        } else {

            echo "パスワードが一致しません" . '<br>';

        }

    }



?>



<!DOCTYPE html>

<html lang="ja">

<head> <meta charset="utf-8"></head>

<body>

	<form action="" method="post">

<br>





		<input type="text" name="name" placeholder="名前" value="<?php echo $eName; ?>">

<br>


        <input type="text" name="comment" placeholder="コメント" value="<?php echo $eComment; ?>">

<br>

		<input type="text" name="password_set" placeholder="パスワード">

		<input type="submit" name="submit_send" value="送信">
	</form>
<br>




	<form action="" method="post">


		<input type="text" name="id_delete" placeholder="削除対象番号">
<br>

		<input type="text" name="password" placeholder="パスワード">

		<input type="submit" name="submit_delete" value="削除">

	</form>
<br>


	<form action="" method="post">


		<input type="text" name="id_edit_post" placeholder="編集対象番号">
<br>

		<input type="text" name="password" placeholder="パスワード">

		<input type="submit" name="submit_edit" value="編集">

	</form>




<br>

<hr>
    <?php

    //掲示板の表示

    $sql = 'SELECT * FROM mission41_bbs';

    $result = $pdo -> query($sql);



    foreach ($result as $row){

    //$rowの中にはカラムが入る??

        echo "ID[".$row['id'] . '] ';

        echo "Name[" . $row['name'] . '] ';

        echo "Time[" . $row['posttime'] . '] ';

        echo "PASS(Debug)[" . $row['pass'] . ']<br>';

        echo 'Comment：' . $row['comment'] .  '<br><br>';



    }

    ?>
</body>
</html>