<?php 
session_start();
  // db接続定義の読み込み
  require('dbconnect.php');
 var_dump($_SESSION);

  // ログインチェック //3600秒 = 1.0h 有効
  if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
      //ログインしている。
      $_SESSION['time'] = time();

var_dump($_SESSION);
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
 var_dump($member['nick_name']);

  //投稿を記録する(つぶやくボタンがクリックされた時)
if(!empty($_POST)){
  if($_POST['tweet'] !=''){
    $reply_tweet_id = 0;

    if(isset($_POST['reply_tweet_id'])){
      $reply_tweet_id = $_POST['reply_tweet_id'];
    }

    //INSERT文作成
    $sql = sprintf('INSERT INTO `tweets` SET `tweet`="%s", `member_id`=%d, `reply_tweet_id`=%d,`created`=NOW()',
        mysqli_real_escape_string($db, $_POST['tweet']),
        mysqli_real_escape_string($db, $member['member_id']),
        // mysqli_real_escape_string($db, $_POST['reply_tweet_id'])   
        $reply_tweet_id
      );

    
    mysqli_query($db, $sql) or die(mysqli_error($db));

    header('Location: index.php');
    exit();
  }
}

//ベージングを設置する
$page == '';

//GETパラメータで渡させるページ番号を取得
if(isset($_REQUEST['page'])){
$page = $_REQUEST['page'];
}

// pageパラメータがない場合は、ページ番号を１にする
if($page == ''){
  $page = 1;
}
//パラメータで出せるページ数以上の数字を打ち込んでもバグを発生させない
//pageパラメータがない場合はページ番号を１にする
$page = max($page, 1);
//max関数：()内に指定した複数のデータから、一番大きい値を返す
//page=-1　と指定された場合、マイナスの値のページ番号は存在しないので、１に強制変換する

//最終ページを取得する(必要なページ数を計算する)
$sql = 'SELECT COUNT(*) AS cnt FROM `tweets`';
$recordSet = mysqli_query($db, $sql);
$table = mysqli_fetch_assoc($recordSet);

//ceil()関数：切り上げする関数　割り切れない数の投稿数でも表示するため
$maxPage = ceil($table['cnt'] / 5);

//表示する正しいページの数値（max）を設定する page=100などページ数の数以上の存在しない数から、最大ページ数を強制変換する
//min()関数：引数で指定した複数のデータから、一番小さい値を返す

$page =min($page, $maxPage);

//ページに表示する件数だけ取得する
$start = ($page - 1)*5;
$start = max(0, $start);



//投稿を取得する
$sql = sprintf('SELECT m.`nick_name`, m.`picture_path`, t.*FROM `tweets` t,`members` m WHERE m.member_id=t.member_id ORDER BY t.`created` DESC LIMIT %d, 
    5',
    $start
);
$tweets = mysqli_query($db, $sql) or die(mysqli_error($db));

//返信の場合＠返信したいメッセージを書いてユーザー名返信元メッセージを初期表示するための情報取得
if(isset($_REQUEST['res'])){
  $sql = sprintf('SELECT m.`nick_name`, m.`picture_path`, t.* FROM `tweets` t,`members` m WHERE m.member_id=t.member_id AND t.tweet_id=%d ORDER BY t.`created` DESC',
      mysqli_real_escape_string($db, $_REQUEST['res'])
    );

  $record = mysqli_query($db, $sql) or die(mysqli_error($db));
  $table = mysqli_fetch_assoc($record);
  $tweet ='@' . $table['nick_name']. ' ' . $table['tweet'];
}

// if(isset($_GET['search_word'])){
// $sql = sprintf('SELECT m.`nick_name`, m.`picture_path`, t.*FROM `tweets` t,`members` m WHERE m.member_id=t.member_id AND Like %s ORDER BY t.`created` DESC',
//     mysqli_real_escape_string($db, $_GET['search_word'])
// );
// $search_word = mysqli_query($db, $sql) or die(mysqli_error($db));
// }
//htmlspecialcharsのショートカット
function h($value){
  return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}


//本文内のURLにリンクを設定します。
function makeLink($value){
return mb_ereg_replace("(https?)(://[[:alnum:]\+\$\;\?\.%,!#~*/:@&=_-]+)", '<a href="\1\2">\1\2</a>' , $value);
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
        <legend>ようこそ<?php echo h($member['nick_name']);?>さん！</legend>
        <form method="post" action="" class="form-horizontal" role="form">
            <!-- つぶやき -->
            <div class="form-group">
              <label class="col-sm-4 control-label">つぶやき</label>
              <div class="col-sm-8">
                <!-- textareaに改行を入れると空欄とみなすので注意 -->
                <textarea name="tweet" cols="50" rows="5" class="form-control" placeholder="例：Hello World!"><?php echo h($tweet);?></textarea>


                  <input type="hidden" name="reply_tweet_id" value="<?php echo h($_REQUEST['res']); ?>"/>
 
              </div>
            </div>


  
          <ul class="paging">

         <input type="submit" class="btn btn-info" value="つぶやく">
         
           &nbsp;&nbsp;&nbsp;&nbsp;
           <?php 
            if ($page > 1){
         ?>
           
                <li><a href="index.php?page=<?php print($page - 1); ?>" class="btn btn-default">前</a></li>
        <?php
        } else {
            ?>
                <li>前</li>
        <?php 
        }
        ?>

        <?php 
            if ($page < $maxPage){
        ?>
            &nbsp;&nbsp;|&nbsp;&nbsp;
                <li><a href="index.php?page=<?php print($page + 1); ?>" class="btn btn-default">次</a></li>
        <?php
        } else {
        ?>
                <li>次</li>
        <?php 
        }
        ?>
          </ul>
          
        </form>
        <!-- <form method="get" action="" class="form-horizontal" role="form">
        <p>検索：
               <input type="text" name="search_word"/>
               <input type="submit" value="送信"/> 
               <?php echo h($search_word); ?>  
              </p> -->


      </div>



      <div class="col-md-8 content-margin-top">

<!-- つぶやいた内容を繰り返し表示させる -->
 <?php
 while ($tweet = mysqli_fetch_assoc($tweets)):
  ?>
        <div class="msg">
          <!-- 同じ階層にある member_picture/から画像を取る。 テーブル結合しているので、tweetの写真とニックネームが同じものを一件取得-->
          <img src="member_picture/<?php echo h($tweet['picture_path']);?>" width="48" height="48"

          alt ="<?php echo h($tweet['nick_name']);?>"/>
          <p>
            <?php echo makeLink(h($tweet['tweet']));?><span class="name"> <?php echo h($tweet['nick_name']);?> </span>
          
          
            <!-- ツイート返信でツイートを選択するのでtweet_idで取得 -->
            [<a href="index.php?res=<?php echo h($tweet['tweet_id']);?>">Re</a>]
          </p>
          <p class="day">
            <!-- GET送信し、判別して飛ぶため view.php?tweet_id=時だけ飛ぶ、他人にURLで入らないようにする-->
            <a href="view.php?id=<?php echo h($tweet['tweet_id']); ?>">
              <?php echo h($tweet['created']);?>
            </a>

             <?php 
             if ($tweet['reply_tweet_id'] > O):
              ?> 
            <a href="view.php?id=<?php echo h($tweet['reply_tweet_id']); ?>">
              返信元のメッセージ</a>
              <?php
              endif;
              ?>

            [<a href="#" style="color: #00994C;">編集</a>]

            <?php if ($_SESSION['id'] = $tweet['member_id']):
            ?>
            [<a href="delete.php?tweet_id=<?php echo h($tweet['tweet_id'])?>" style="color: #F33;">削除</a>]
          <?php endif; ?>
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
