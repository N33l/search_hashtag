# search_hashtag
prerequisites:


mysql,
curl   https://curl.haxx.se/docs/install.html ,
phinx https://robmorgan.id.au/posts/getting-started-with-phinx/ ,
crontab https://www.cyberciti.biz/faq/how-do-i-add-jobs-to-cron-under-linux-or-unix-oses/

1. create files DBConfig.php and TwitterConfig.php and make entries following pattern from DBConfigSample.php and TwitterConfigSample.php

2. cretae database testDb.

3. run migration. After following https://robmorgan.id.au/posts/getting-started-with-phinx/ to set up phinx change phinx.yml and make entries accordingly. Then run migration 
"php vendor/bin/phinx migrate" this will create tweet,hash_tag,hash_tag_tweet_mapper tables.

4. run cron present in folder cron_jobs "FetchHashtag.php" by command which takes argument hasgtag name by which you want to search.
php FetchHashtag.php --hash_tag london

here london is what we want to search for.

4. make entry in cron tab https://curl.haxx.se/docs/install.html .

* * * * *  /path/tp/php /full/path/to/file/FetchHashtag.php --hash_tag london >>/full/path/to/file/cron.log

for example
* * * * *  /usr/bin/php /var/www/html/search_hashtag/cron_jobs/FetchHashtag.php --hash_tag london >> /var/www/html/search_hashtag/cron_jobs/cron.log

5. after everything is set up start your server in project folder (inbuilt php server can be started by php -S localhost:8080)
visit localhost:8080



