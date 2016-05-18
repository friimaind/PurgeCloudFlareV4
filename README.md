PurgeCloudFlareV4
=================

##What is PurgeCloudFlareV4?
This is a fork of PurgeCloudFlare created by Jason Carney: https://github.com/DashMedia/PurgeCloudFlare

##Why a fork?
Because the good original PurgeCloudFlare uses an **outdated version** of CloudFlare API which will be **discontinued on Nov 2016**.

The original project seems to be not maintained anymore and I would like to share my code with other MODX developers who would like to use CloudFlare CDN.

##So, what are the differences between PurgeCloudFlare and PurgeCloudFlareV4?
PurgeCloudFlareV4 uses the new CloudFlare API V4.

The plugin is not on MODX marketplace so you need to install it manually.

##Ok, great! How can I install it on my MODX?
* Uninstall the previous PurgeCloudFlare
* Create, if not exists, a new system setting "cloudflare.api_key" and put your **CloudFlare API Key**
* Create, if not exists, a new system setting "cloudflare.email_address" and put your **CloudFlare Email Address**
* Create a new plugin [PurgeCache](https://github.com/friimaind/PurgeCloudFlareV4/blob/master/elements/plugins/PurgeCache.php), copy and paste the code, assign to the event "OnBeforeCacheUpdate"
* Create a new plugin [PurgeSingleFile](https://github.com/friimaind/PurgeCloudFlareV4/blob/master/elements/plugins/PurgeSingleFile.php), copy and paste the code, assign to the event "OnDocFormSave"

**Optional context or system settings**
* "cloudflare.skip" = 1; ignore this context when clearing global cache
* "cloudflare.use_dev" = 1; enable CloudFlare Development mode when clearing the global cache
