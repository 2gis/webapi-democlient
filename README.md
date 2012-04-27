webapi-democlient
=================

This simple application, based on top of Yii PHP framework, shows the basic capabilities of DoubleGIS web API. See live demo at demo.api.2gis.ru.

Installation
------------

- git clone git@github.com:2gis/webapi-democlient.git ./webapi-democlient
- cd ./webapi-democlient
- git submodule init
- git submodule update
- Make sure the public directory is web-accessible
- Make sure the protected/runtime and public/assets directories are writable by the web server
- Add your API key to the configuration file (protected/config/main.php), if you don't have API key, register at http://partner.api.2gis.ru/
- Enjoy!
