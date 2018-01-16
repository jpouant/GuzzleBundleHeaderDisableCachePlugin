# Guzzle Bundle Disable Gregurco Cache Plugin

This plugin integrates a way to disable the [cache plugin][1] using a header.


## Requirements
 - PHP 7.0 or above
 - [Guzzle Bundle][2]

 
### Installation
Using [composer][3]:

##### composer.json
``` json
{
    "require": {
        "neirda24/guzzle-bundle-header-disable-cache-plugin": "^1.0"
    }
}
```

##### command line
``` bash
$ composer require neirda24/guzzle-bundle-header-disable-cache-plugin
```

## Usage
### Enable bundle
``` php
# app/AppKernel.php

new EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundle([
    new Neirda24\Bundle\GuzzleBundleHeaderDisableCachePlugin\GuzzleBundleHeaderDisableCachePlugin(),
])
```

### Basic configuration
``` yaml
# app/config/config.yml

eight_points_guzzle:
    clients:
        api_payment:
            base_url: "http://api.domain.tld"

            # define headers, options

            # plugin settings
            plugin:
                header_disable_cache:
                    enabled: true
                    header: 'X-Guzzle-Skip-Cache' # Optional
```

[1]: https://github.com/gregurco/GuzzleBundleCachePlugin
[2]: https://github.com/8p/EightPointsGuzzleBundle
[3]: https://getcomposer.org/
