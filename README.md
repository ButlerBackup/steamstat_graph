SteamStat
=========

Simple tool for fetching and parsing users logged in in Steam from http://store.steampowered.com/stats/ (Can be used for archival purposes)

+ Language : PHP

+ Output : Stored in database.

+ Sample : https://github.com/shaunidiot/steamstat_graph/img/example.png

##Setup
1. Clone this repo or just download `steamstat_graph.php` and `dump.sql`
2. Create a new database and dump the `dump.sql` file.
3. Run file.

##Cron
Alow cron to run this file every half an hour or so. Stats will continue from the previous ending point.

`0 */1 * * * php /home/root/steamstat_graph.php`

###steamstat_graph.php
What does this file do exactly?

1. Grab and parse data from http://store.steampowered.com/stats/ (users logged in part)
2. Store values in database

##Graph
This project comes with a PHP graph library that renders a graph after retrieving the last 10 rows of data from the database. (Look at the sample above)

##Credit
[PHPlot](http://www.phplot.com/)

[Roboto font](https://developer.android.com/design/style/typography.html)
