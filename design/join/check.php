<?php
// $dsn = 'mysql:dbname=seed_sns;host=localhost';
// $user = 'root';
// $password = 'mysql';
  
// $dbh = new PDO($dsn, $user, $password);
// $dbh->query('SET NAMES utf8');


session_start();

//タイムゾーンのエラーが出た場合
date_default_timezone_set("Asia/Manila");

//dbconnect.phpを読み込む
//joinと同じ階層に行くため　上に行くために 「../ 」が必要
//require：エラーが発生したら処理をやめる
//DB接続など、途中でエラーが出たら処理を止めたいファイルを読み込む場合はrequire

require('../dbconnect.php');



//セッションにデータがなかった時にindex.phpへ遷移する
//ブックマークなどで直接チェック画面に行かないようにする。

if(empty($_SESSION['join'])){
  header('Location:index.php');
  exit();
}

//DB登録処理をする（データがPOST送信されたら）
if(!empty($_POST)){
  
  //SQL文を作成
  //sprintif('今日は％ｓ'、'シード君')；→　今日はシード君
  //$name=シード君　sprintif('今日は％ｓ'、'$name')；→　今日はシード君
  //sprintf()関数
  //指定した文の書式を整えることができる関数
  //%dは整数、%sは文字列を代入することができる

  $sql = sprintf('INSERT INTO members SET nick_name="%s", email="%s", password="%s", picture_path="%s", created="%s"',
    //created=now()', date('Y-m-d H:i:s')が必要なくなる。

    //mysqli_real_escape_string SQL用のサニタイジング
    //PHPからMySQLにデータを登録するときに、MySQLで使用する特殊文字をエスケープする方法
// 今まではSQL文に「.」を使って受け取った値をそのまま使ってた
// SQLインジェクション対策として、サニタイズする必要がある
// この関数はシングルクォーテーションなどの前にバックスラッシュを付けてくれる
// 例：フォームのパスワードに「’ or ‘A’ = ‘A」などと入れると、ログインできてしまう
// SELECT * FROM テーブル名 WHERE パスワード = 'password'
// SELECT * FROM テーブル名 WHERE パスワード = '' OR 'A' = 'A’

    mysqli_real_escape_string($db, $_SESSION['join']['nick_name']),
    mysqli_real_escape_string($db, $_SESSION['join']['email']),

    // sha1(シャーワン)暗号化する関数　16進数の４０byteの文字列を取得する
    mysqli_real_escape_string($db, sha1($_SESSION['join']['password'])),
    mysqli_real_escape_string($db, $_SESSION['join']['picture_path']),date('Y-m-d H:i:s')
    );

  //mysqli_query()SQL文実行
    mysqli_query($db, $sql) or die(mysqli_error($db));

    // unset ここから使わないから存在しないよと表す。指定された変数の割当を解除する。ここではセッション情報を破棄している。
    unset($_SESSION['join']);

    header('Location: thanks.php');
    exit();
    
}
//５　DB切断
  // $dbh = null;

 ?>






<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>SeedSNS</title>

    <!-- Bootstrap -->
    <link href="../../assets/css/bootstrap.css" rel="stylesheet">
    <link href="../../assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="../../assets/css/form.css" rel="stylesheet">
    <link href="../../assets/css/timeline.css" rel="stylesheet">
    <link href="../../assets/css/main.css" rel="stylesheet">
    <!--
      designフォルダ内では2つパスの位置を戻ってからcssにアクセスしていることに注意！
     -->


    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
  <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
          <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header page-scroll">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="index.php"><span class="strong-title"><i class="fa fa-twitter-square"></i> Seed SNS</span></a>
          </div>
          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav navbar-right">
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </nav>

  <div class="container">
    <div class="row">
      <div class="col-md-4 col-md-offset-4 content-margin-top">
        <form method="post" action="" class="form-horizontal" role="form">
          <input type="hidden" name="action" value="submit">
          <div class="well">ご登録内容をご確認ください。</div>
            <table class="table table-striped table-condensed">
              <tbody>
                <!-- 登録内容を表示 -->
                <tr>
                  <td><div class="text-center">ニックネーム</div></td>
                  <td><div class="text-center"><?php echo htmlspecialchars($_SESSION['join']['nick_name'],ENT_QUOTES,'UTF-8');?></div></td>
                </tr>
                <tr>
                  <td><div class="text-center">メールアドレス</div></td>
                  <td><div class="text-center"><?php echo htmlspecialchars($_SESSION['join']['email'],ENT_QUOTES,'UTF-8');?></div></td>
                </tr>
                <tr>
                  <td><div class="text-center">パスワード</div></td>
                  <td><div class="text-center"></div></td>
                </tr>
                <tr>
                  <td><div class="text-center">プロフィール画像</div></td>
                  <td><div class="text-center">
<!--                     <img src="http://c85c7a.medialib.glogster.com/taniaarca/media/71/71c8671f98761a43f6f50a282e20f0b82bdb1f8c/blog-images-1349202732-fondo-steve-jobs-ipad.jpg" width="100" height="100">
 -->               
                 <img src="../member_picture/<?php echo htmlspecialchars($_SESSION['join']['picture_path'],ENT_QUOTES,'UTF-8');?>" width="100" height="100" alt="" />
 
                </tr>
              </tbody>
            </table>
<!-- index.phpに戻る　GET送信のactionで書いたまでの時点　rewriteで書き直せるようにする -->
            <a href="index.php?action=rewrite">&laquo;&nbsp;書き直す</a> | 
            <input type="submit" class="btn btn-default" value="会員登録">
         <!-- <input type="hidden" name="action" value="submit"/> -->

          </div>
        </form>
      </div>
    </div>
  </div>

  <!--   jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>
