bravecollective-recruit
================

Website: http://recruiting.braveineve.com

see *demo.png*

## Frontend
* Run *./composer* *install* in *webroot/auth*
* Copy *webroot/config.php.dist* to *webroot/config.php*
* Adapt settings in *webroot/config.php*
* Point webserver to *webroot*

## Backend
* Create database from *schema.sql*
* Configure database access in *gen_data/alliance.py* and *gen_data/recruiting.py*
* Setup crontab as outlined in *crontab*
