# MovistarTV open node finder
PRESENTATION
This is a simple project developed *just for fun*. It is a web panel developed in PHP that looks for IP addresses of networks that have Spanish Movistar's Imagenio, and they are using Kodi's (https://kodi.tv/) MovistarTV plugin (https://sourceforge.net/projects/movistartv/files/) to play it's contents in a non-official device, but they failed to properly secure their instalations.
To do so, it gets some data from https://shodan.io/, scans the nodes, checks for the response, and it fixes a DNS record to point to a valid IP.
So you can have your Raspberry, Android, Linux or Windows Kodi pointing to this DNS record... And as far as there is somebody with Imagenio and Kodi sharing it, your Imagenio will (almost) always work for you hopefully!

This project was developed and tested under Ubuntu 17.10, but almost any mayor Linux (Debian, Raspbian, OpenWRT...) should do.
So if you haven't already, install MySQL, PHP and Apache.

INSTALLATION
In this sample we created a MySQL database called MovistarTV, with a user movistartv with password MySecretPass2018
We created a free user at shodan.io, and we got it's free API key.
We got our free Google Geo API key at https://developers.google.com/maps/documentation/geocoding/intro
Copy the files to your web server root, for example /var/www/html/MovistarTV/
Copy install/config-sample.php to <parent-dir>/config.php and edit it: MySQL info, API keys...

Import MovistarTV.sql to MySQL. The fastest way should be to create the database, user and password, check the privileges and finally run:
mysql -u movistartv -pMySecretPass2018 MovistarTV < install/MovistarTV.sql
But I would recommend to install phpMyAdmin to manage MySQL.

I have a custom PHP script that gets a host name (under my registered domain), IP address and my protection password and sets it. For example:

This should be enought to start working!


Don't forget to add a cron job (crontab -e) to update the database. I added this job to run every 15 minutes:
*/15 * * * * /usr/bin/php /var/www/html/MovistarTV/tvtimer.php

I guess MySQL could be replaced by MariaDB, and Apache by Nginx. I didn's try it.

TO-DO
Make the system fully automatic (now it's a manual web panel), choosing a valid node and pointing the DNS record to it.
Scan the responding nodes to list their channel list (paid channels should be interesting...), and store them
Load the panel faster, loading the status later with Ajax

JokinAU 2018/02
