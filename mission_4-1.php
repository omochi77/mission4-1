<?php
//データベース作成&接続
$dsn = 'データベース名';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn,$user,$password);

//データベース内にテーブルを作成
$sql = "CREATE TABLE tbtest"
."("
."id INT auto_increment primary key,"//数字はint
."name char(32),"
."comment TEXT,"//長文はtext
."time TIMESTAMP,"//日付はtimestamp
."pass1 TEXT"
.");";
$stmt = $pdo->query($sql);

?>

<?php
$name = $_POST['name'];
$comment = $_POST['comment'];
$time = date('Y-m-d H:i:s');
$pass1 = $_POST['pass1'];

$textbox = $_POST['textbox'];

$edit = $_POST['edit'];
$nm = $_POST['name'];
$kome = $_POST['comment'];

$delete = $_POST['delete'];

$pass2 = $_POST['pass2'];
$pass3 = $_POST['pass3'];

//編集対象番号に番号が入力されたら
if(!empty($_POST['edit'])){
	$sql = "SELECT * FROM tbtest where id = {$_POST['edit']}";
	$results = $pdo -> query($sql) -> fetchAll(PDO::FETCH_ASSOC);
	foreach((array)$results as $row){
		//既存のidと編集対象番号が一致したら＆pass3がpass1と一致したら
		if($row['id'] == $edit && $row['pass1'] == $pass3){
			$edit_name = $row['name'];
			$edit_comment = $row['comment'];
		}
		//既存のidと編集対象番号が一致したら＆pass3がpass1と一致しなかったら
		elseif($row['id'] == $edit && $row['pass1'] != $pass3){
			echo "パスワードが違います。";
		}
	}
}

//名前とコメントの処理
if(isset($_POST['name']) && isset($_POST['comment'])){
	//(編集機能)
	if(!empty($_POST['textbox']) && isset($_POST['pass1'])){
		$sql = "SELECT * FROM tbtest where id = $textbox";
		$results = $pdo -> query($sql) -> fetchAll(PDO::FETCH_ASSOC);
		foreach((array)$results as $row){
			if($row['id'] == $textbox){
				$sql = "update tbtest set name = '$nm', comment = '$kome' where id = {$textbox}";
				$result = $pdo->query($sql);
			}
			else{//投稿番号がテキストボックスの値と一致しなかったら
				echo "編集できません。";
			}
		}
	}
	//テキストボックスが空＆pass1がある＆pass2がpass1と一致しない
	elseif(empty($_POST['textbox']) && !empty($_POST['pass1']) && $row['pass1'] != $pass3){
		echo "パスワードが違います。";
	}
	//(新規投稿)
	else{//テキストボックスが空のとき
		$sql = $pdo -> prepare("INSERT INTO tbtest(name,comment,time,pass1) VALUES (:name,:comment,:time,:pass1)");
		$sql -> bindParam(':name',$name,PDO::PARAM_STR);
		$sql -> bindParam(':comment',$comment,PDO::PARAM_STR);
		$sql -> bindParam(':time',$time,PDO::PARAM_STR);
		$sql -> bindParam(':pass1',$pass1,PDO::PARAM_STR);
		$sql -> execute();//実行する
	}
}
//(削除機能)
if(!empty($_POST['delete'])){
	//削除番号のレコード取得
	$sql = "SELECT * FROM tbtest where id = {$_POST['delete']}";
	$results = $pdo -> query($sql)-> fetchAll(PDO::FETCH_ASSOC);
	//fetch関数はクエリ結果から1行分の投稿データ（1レコード）だけを取得する
	//foreachしたときの$rowには1レコードが入っている
	foreach((array)$results as $row){
		//パスワードが一致したら
		if($row['pass1'] == $pass2){//消す
			$sql = "delete from tbtest where id = {$delete}";
			$result = $pdo->query($sql);//foreachしたときの1番はじめの$rowが$result->fetch()にあたる
		}
		//パスワードが一致しなかったら
		else{
			echo "パスワードが違います。";
		}
	}
}
?>

<!DOCTYPE html>
<html lang = "ja">

<head>
<meta charset="UTF-8">
</head>

<body>
<form action = "mission_4-1.php" method = "post">
<input type = "text" name = "name" placeholder = "名前" value = "<?php echo $edit_name; ?>" size = "20"/><br>
<input type = "text" name = "comment" placeholder = "コメント" value = "<?php echo $edit_comment; ?>" size = "20"/><br>
<input type = "password" name = "pass1" placeholder = "パスワード" size = "20"/>
<input type = "submit" value = "送信" size = "20"/>
<input type = "hidden" name = "textbox" value = "<?php if(!empty($_POST['edit'])){ echo $_POST['edit'];} ?>"size = "20"/>
</form>
<br>
<form action = "mission_4-1.php" method = "post">
<input type = "text" name = "delete" placeholder = "削除対象番号" size = "20"/><br>
<input type = "password" name = "pass2" placeholder = "パスワード" size = "20"/>
<input type = "submit" value = "削除" size = "20"/>
</form>
<br>
<form action = "mission_4-1.php" method ="post">
<input type = "text" name = "edit" placeholder = "編集対象番号" size = "20"/><br>
<input type = "password" name = "pass3" placeholder = "パスワード" size = "20"/>
<input type = "submit" value = "編集" size = "20"/>
</form>
</body>

<?php
//(表示)
//入力したデータをselectによって取得
$sql = "SELECT * FROM tbtest";
$results = $pdo -> query($sql) -> fetchAll(PDO::FETCH_ASSOC);
foreach($results as $row){
 //$rowの中にはテーブルのカラム名が入る
 echo $row['id'].' ';
 echo $row['name'].' ';
 echo $row['comment'].' ';
 echo $row['time'].'<br>';
}
?>

</html>