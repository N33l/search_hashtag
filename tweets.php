<?php
$hash = $_GET['q'] ?? false;
if ( empty($hash) ) {
    header('Location: search.php');
} else {
    require_once('DBConfig.php');
    require_once('DBOperations.php');
    require_once('DBConnection.php');

    $hash = trim($hash, '# ');
    $page = $_GET['page'] ?? 0;

    $dbObject = new DBOperations(new DBConnection(HOST_NAME, DB_NAME, USER_NAME, PASSWORD));

    if ( $page > 0 ) {
        $lastId = $_GET['lastId'] ?? 0;
        $tweets = $dbObject->getTweetsByHash($hash, $lastId, false);
    } else {
        $tweets = $dbObject->getTweetsByHash($hash, 0, true);
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tweets for <?= '#' . $hash ?></title>
    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    <script>
        var tweets;
        $(document).ready(function () {
            console.log('here');
            tweets = <?= json_encode($tweets); ?>;
            var hashTag="<?= $hash ?>";
            var lastId=tweets[0]['tweet_id'];

            generateHtml(tweets);
            function auto_load(){
                $.ajax({
                    url: "newTweets.php",
                    type:"get",
                    data: {
                        q: hashTag,
                        lastId: lastId
                    },
                    cache: false,
                    success: function(dataArr){
                    dataArr = JSON.parse(dataArr);
                    if(dataArr.length >0){
                        for(var index in dataArr) {
                            tweets.unshift(dataArr[index]);
                            tweets.splice(9, 1);
                        }
                        generateHtml(tweets);
                    }

                    }
                });
            }
            //Refresh auto_load() function after 1000 milliseconds
            setInterval(auto_load,3000);
        });
        function generateHtml(tweets){
            var tweetHtml = '';
            for(var i=0;i<tweets.length;i++){
                tweetHtml+=" <tr> <td> " + (i+1) + " </td>\
                    <td> " + tweets[i]['tweet'] + " </td> \
                    <td> " + tweets[i]['tweet_id'] + "</td> \
                    </tr>";
            }
            $('#div_id_tweet').html(tweetHtml);
        }

    </script>


    <div class="form-tweet-search" style=" width: 23%; margin: 0 auto;">
        <form action="tweets.php" method="GET" class="form-inline ">
            <i>#</i>
            <input type="text" class="form-control" name="q" value= <?= $hash ?> >
            <button type="submit" name="search" class="btn btn-default">Search</button>
        </form>
    </div>

    <br>
    <br>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <div id='new_cd_offers'></div>
    <div class='row'>
        <div class='col-md-10 col-sm-6 col-xs-12'>
            <div class='box'>
                <div class='box-header bg-green'>
                    <h3 class='box-title '>Tweets for search <?= $hash ?></h3>
                    <div class='box-tools'>

                    </div>
                </div><!-- /.box-header -->
                <div class='box-body table-responsive no-padding '>
                    <table class='table table-striped'>
                        <thead>
                            <tr>
                                <th>Serial No.</th>
                                <th>Tweet</th>
                                <th>Tweet_id</th>
                            </tr>
                        </thead>
                        <tbody id="div_id_tweet">

                        </tbody>
                    </table>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div>
    </div>

    <br>
    <br>
</head>
<body>

</body>
</html>