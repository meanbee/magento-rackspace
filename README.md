Meanbee Rackspace Cloud Files for Downloadable Products Extension
=====================
When you specify a URL for downloadable products in your Magento administration area, the product download will still be processed through your server, using your bandwidth. This tends to be relatively expensive and can slow down your Magento store.

This module allows you to offload the hosting of your downloadable products onto Rackspace Cloud Files, using cheaper bandwidth and saving your server for handling your customers. Your downloadable products will still record the number of downloads the customer uses when going through the “My Account” area of your website.

All generated URLs are time-sensitive, the length of time you’d like the URL to be valid for is configurable in your administration area. The URL can be valid for 5 seconds, or indefinitely. It’s up to you.

All you need to do is:
 1. Install the module
 2. Enter your Rackspace Cloud Files Username and API key in the administration area (for making the secure URLs)
 3. Upload your downloadable products to Rackspace Cloud files
 4. Use the Rackspace Cloud Files CDN URL to your uploaded files to create your downloadable products
 5. That’s it! The secure URL will be automatically generated for the customer when they try and download from the “My Downloadable Products” section

##### Compatibility
 Compatibility: 1.6, 1.7, 1.8, 1.9

Support
-------
You are welcome to log any issues you find for community support but the functionality is provided *as is* and we will not be providing support. We will however review pull requests if you provide one.

Contribution
------------
Any contribution is highly appreciated. The best way to contribute code is to open a [pull request on GitHub](https://help.github.com/articles/using-pull-requests).


Licence
-------
[OSL - Open Software Licence 3.0](http://opensource.org/licenses/osl-3.0.php)

Copyright
---------
(c) 2017 Meanbee
