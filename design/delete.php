<?php
session_start();
require('dbconnect.php');

//ログイン状態で削除処理するif文
if(isset($_SESSION['id'])){
	$tweet_id = $_REQUEST['id'];

	//投稿を検査する　（指定されたつぶやきが、ログインしているユーザーのものかチェック
	$sql = sprintf('SELECT * FROM `tweets` WHERE `tweet_id`=%d',
		mysqli_real_escape_string($db, $tweet_id)
		);
	$record = mysqli_query($db, $sql) or die(mysqli_error($db));
	$table = mysqli_fetch_assoc($record);
	if($table['member_id'] == $_SESSION['id']){

		//一致したら削除
		$sql = sprintf('DELETE FROM `tweets` WHERE `tweet_id`=%d',
			mysqli_real_escape_string($db, $tweet_id)
		);
		mysqli_query($db, $sql) or die(mysqli_error($db));
	}
}
header('Location: index.php');
exit();

?>