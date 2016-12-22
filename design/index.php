<?php 
session_start();
  // db接続定義の読み込み
  require('dbconnect.php');
 

  // ログインチェック //3600秒 = 1.0h 有効
  if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
      //ログインしている。
      $_SESSION['time'] = time();

//ユーザーデータを取得
    $sql = sprintf('SELECT * FROM members WHERE member_id=%d',
      mysqli_real_escape_string($db,$_SESSION['id'])
    );

    
    $record = mysqli_query($db, $sql) or die(mysqli_error($db));
    $member = mysqli_fetch_assoc($record);

} else{
    //ログインしていない
    header('Location: login.php'); 
    exit();
  }
 

  //投稿を記録する(つぶやくボタンがクリックされた時)
if(!empty($_POST)){
  if($_POST['tweet'] !=''){

    //INSERT文作成
    $sql = sprintf('INSERT INTO `tweets` SET `tweet`="%s", `member_id`=%d, `reply_tweet_id`=%d,`created`=NOW()',
        mysqli_real_escape_string($db, $_POST['tweet']),
        mysqli_real_escape_string($db, $member['member_id']),
        mysqli_real_escape_string($db, $_POST['reply_tweet_id'])   
      );

    
    mysqli_query($db, $sql) or die(mysqli_error($db));

    header('Location: index.php');
    exit();
  }
}

//投稿を取得する
$sql = sprintf('SELECT m.`nick_name`, m.`picture_path`, t.*FROM `tweets` t,`members` m WHERE m.member_id=t.member_id ORDER BY t.`created` DESC');
$tweets = mysqli_query($db, $sql) or die(mysqli_error($db));

//返信の場合
if(isset($_REQUEST['res'])){
  $sql = sprintf('SELECT m.`nick_name`, m.`picture_path`, t.* FROM `tweets` t,`members` m WHERE m.member_id=t.member_id AND t.member_id=%d ORDER BY t.`created` DESC',
      mysqli_real_escape_string($db, $_REQUEST['res'])
    );

  $record = mysqli_query($db, $sql) or die(mysqli_error($db));
  $table = mysqli_fetch_assoc($record);
  $tweet ='@' . $table['nick_name']. ' ' . $table['tweet'];
}




?>







<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>SeedSNS</title>

    <!-- Bootstrap -->
    <link href="../assets/css/bootstrap.css" rel="stylesheet">
    <link href="../assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="../assets/css/form.css" rel="stylesheet">
    <link href="../assets/css/timeline.css" rel="stylesheet">
    <link href="../assets/css/main.css" rel="stylesheet">


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
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1" style = "text-align: right">
              <ul class="nav navbar-nav navbar-right">
                <li><a href="logout.php">ログアウト</a></li>
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </nav>

  <div class="container">
    <div class="row">
      <div class="col-md-4 content-margin-top">
        <legend>ようこそ<?php echo htmlspecialchars($member['nick_name']);?>さん！</legend>
        <form method="post" action="" class="form-horizontal" role="form">
            <!-- つぶやき -->
            <div class="form-group">
              <label class="col-sm-4 control-label">つぶやき</label>
              <div class="col-sm-8">
                <textarea name="tweet" cols="50" rows="5" class="form-control" placeholder="例：Hello World!"><?php echo htmlspecialchars($tweet, ENT_QUOTES, 'UTF-8');?></textarea>
                  <input type="hidden" name="reply_tweet_id" value="<?php echo htmlspecialchars($_REQUEST['res'], ENT_QUOTES, 'UTF-8'); ?>"/>
 
              </div>
            </div>
          <ul class="paging">
          
         <input type="submit" class="btn btn-info" value="つぶやく">

           &nbsp;&nbsp;&nbsp;&nbsp;
                <li><a href="index.php" class="btn btn-default">前</a></li>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <li><a href="index.php" class="btn btn-default">次</a></li>
          </ul>
        </form>

      </div>



      <div class="col-md-8 content-margin-top">

       <?php
while ($tweet = mysqli_fetch_assoc($tweets)): 
?>
        <div class="msg">
          <img src="member_picture/<?php echo htmlspecialchars($tweet['picture_path'], ENT_QUOTES, 'UTF-8');?>" width="48" height="48"
          alt ="<?php echo htmlspecialchars($tweet['nick_name'], ENT_QUOTES, 'UTF-8');?>"/>
          <p>
            <?php echo htmlspecialchars($tweet['tweet'], ENT_QUOTES, 'UTF-8');?><span class="name"> <?php echo htmlspecialchars($tweet['nick_name'], ENT_QUOTES, 'UTF-8');?> </span>
            [<a href="index.php?res=<?php echo htmlspecialchars($tweet['member_id'], ENT_QUOTES, 'UTF-8');?>">Re</a>]
          </p>
          <p class="day">
            <?php echo htmlspecialchars($tweet['created'], ENT_QUOTES, 'UTF-8');?>
            <a href="view.php">
              2016-01-28 18:04
            </a>
            [<a href="#" style="color: #00994C;">編集</a>]
            [<a href="#" style="color: #F33;">削除</a>]
          </p>
        </div>
<?php endwhile;?>
       <!--  <div class="msg">
          <img src="http://c85c7a.medialib.glogster.com/taniaarca/media/71/71c8671f98761a43f6f50a282e20f0b82bdb1f8c/blog-images-1349202732-fondo-steve-jobs-ipad.jpg" width="48" height="48">
          <p>
            つぶやき３<span class="name"> (Seed kun) </span>
            [<a href="#">Re</a>]
          </p>
          <p class="day">
            <a href="view.php">
              2016-01-28 18:03
            </a>
            [<a href="#" style="color: #00994C;">編集</a>]
            [<a href="#" style="color: #F33;">削除</a>]
          </p>
        </div>
        <div class="msg">
          <img src="http://c85c7a.medialib.glogster.com/taniaarca/media/71/71c8671f98761a43f6f50a282e20f0b82bdb1f8c/blog-images-1349202732-fondo-steve-jobs-ipad.jpg" width="48" height="48">
          <p>
            つぶやき２<span class="name"> (Seed kun) </span>
            [<a href="#">Re</a>]
          </p>
          <p class="day">
            <a href="view.php">
              2016-01-28 18:02
            </a>
            [<a href="#" style="color: #00994C;">編集</a>]
            [<a href="#" style="color: #F33;">削除</a>]
          </p>
        </div>
        <div class="msg">
          <img src="http://c85c7a.medialib.glogster.com/taniaarca/media/71/71c8671f98761a43f6f50a282e20f0b82bdb1f8c/blog-images-1349202732-fondo-steve-jobs-ipad.jpg" width="48" height="48">
          <p>
            つぶやき１<span class="name"> (Seed kun) </span>
            [<a href="#">Re</a>]
          </p>
          <p class="day">
            <a href="view.php">
              2016-01-28 18:01
            </a>
            [<a href="#" style="color: #00994C;">編集</a>]
            [<a href="#" style="color: #F33;">削除</a>]
          </p>
        </div> -->
      </div>

    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>
