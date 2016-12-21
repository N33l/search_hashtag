<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search by #tag</title>
</head>
<body>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<div class="form-tweet-search" style=" width: 23%; margin: 0 auto;">
    <form action="tweets.php" method="GET" class="form-inline ">
        <i>#</i>
        <!--		<input type="text" name="q" placeholder="tag" class="sr-only">-->
        <input type="text" class="form-control" name="q" placeholder="tag" >
        <button type="submit" name="search" class="btn btn-default">Search</button>
        <!--		<input type="submit" name="search" value="Search">-->
    </form>
</div>
</body>
</html>