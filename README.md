Sendy Webhooks
==============

#About
This project will allow you to setup webhooks for when using services other than Amazon SES with Sendy.

Copy these files into a **webhooks** folder in your Sendy root (or wherever you would like them to reside) and then edit
the **webhooks/includes/config.php** file to update how to access the regular Sendy **includes/config.php** file. Obviously,
if you are not putting it in the **webhooks** folder under the root, you'll need to access the webhooks config file from
wherever you install it.

Inside of that config file, there are some values you can customize; if you are going to get updates directly from here,
you may want to place those variables into your standard Sendy configuration file so that you don't have to worry about
overwriting them. It will work just fine either way.

Once you have done that, then you can just point your mailing service's webhooks API call to the appropriate service.

#Supported Services
Currently, I have implemented the following services:

* [Mandrill](http://www.mandrill.com) - no further customization required
* [CritSend](http://www.critsend.com) - you will need to add your CritSend API key to config.php

#Future Services
If you have any future services that you'd like to include, just let me know and point me to the documentation, or else
use any of the existing files as a template and push the update to this repository. I will be adding more.

#Supported Webhooks
I have been adding in debugging for each of the supported features provided by individual APIs, and all of them will be
logged into the debuglog (if debugging is turned on). However, only the following functions will actually interact with
your Sendy data:

* hard bounce
* soft bounce
* spam complaint

Basically, I reproduced what is available through Amazon SNS.
