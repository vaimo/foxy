# Changelog

_This file has been auto-generated from the contents of changelog.json_

## v1.0.6.6 (2019-09-24)

### Fix

* allow the plugin to be installed as dependency to globally installed package (as part of dependency of some global package); previously caused every composer call to crash with class declaration conflict
* removed class reference to dependency that is not listed (and should not be) as dependency


## v1.0.6.5 (2019-07-29)

### Fix

* node_modules not cleaned up after the plugin gets uninstalled (unless FOXY_NO_CLEANUP or root package config extra/foxy/cleanup:false used