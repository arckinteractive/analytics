Analytics for Elgg
==================
![Elgg 2.3](https://img.shields.io/badge/Elgg-2.3-orange.svg?style=flat-square)

## Features

* Tracking of user sessions (duration, location, IP address etc)
* Tracking of page views
* Tracking of entity views
* Tracking of entity events
* Tracking of clicks and form submissions
* Tracking of benchmarks (total number of users on a given day etc)
* Detailed reporting tool
* Easy integration with custom metrics

## Installation

### IP Address to Location

After installing the plugin, download GeoLite2-City.mmdb from 
http://dev.maxmind.com/geoip/geoip2/geolite2/ and upload it to your server.
Update your `elgg-config/settings.php` with the path to the file, e.g.

```php
$CONFIG->geolite_db = dirname(dirname(__FILE__)) . '/data/GeoLite2-City.mmdb';
```

### Analytics Report

Report can be accessed at `/analytics` endpoint, or via `Admin > Statistics > Overview`

