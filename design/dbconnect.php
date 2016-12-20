<?php
//データベース接続処理をするため
//パスワードなど変更などを一箇所のみで行うため

//mysqlしか使えない方法である

//接続したいDBを選択する
//localhostは自分のPC内にサーバがあるから
$db = mysqli_connect('localhost', 'root', 'mysql', 'seed_sns')
//接続がうまくいかない時はここで終了する
or　die(mysqli_connect_error());

//utf8 で　ーはいらない
mysqli_set_charset($db, 'utf8');
?>