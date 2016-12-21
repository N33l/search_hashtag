<html lang="en">
<head>

    <title>Search tweets</title>
</head>
<body>

<div class="search-tweets">hi
<div id="auto_load_div">

    hi
</div>
</div>
<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
<script>
    function auto_load(){
        $.ajax({
            url: "fetchTweet.php",
            cache: false,
            success: function(data){
                $("#auto_load_div").html(data);
            }
        });
    }

    $(document).ready(function(){
        auto_load(); //Call auto_load() function when DOM is Ready
    });
    //Refresh auto_load() function after 1000 milliseconds
    setInterval(auto_load,1000);
</script>
</body>
</html>