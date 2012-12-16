--------------------
HybridAuth
--------------------
Author: Vasiliy Naumkin <bezumkin@yandex.ru>
--------------------
An integration of open source social sign on php library - http://hybridauth.sourceforge.net/ into MODX Revolution.

The main goal of HybridAuth library is to act as an abstract api between your application and various social apis and identities providers such as Facebook, Twitter, MySpace, LinkedIn, Google and Yahoo.

HybridAuth enable developers to easily build social applications to engage websites vistors and customers on a social level by implementing social signin, social sharing, users profiles, friends list, activities stream, status updates and more.

Feel free to suggest ideas/improvements/bugs on GitHub:
http://github.com/bezumkin/modx-hybridauth/issues

--------------------
Installation
--------------------
Download and install it with MODX package manager

Then:
1. Register and get api keys from needed services. For example, create twitter application - https://dev.twitter.com/apps/new
2. Open system settings in manager, switch to hybridauth and make\update ha.keys.Servicename. In our wxample it will be ha.keys.Twitter
3. You need to set your keys as json sting with array. {"key":"you key from twitter","secret":"secret from twitter"}. It needed for proper initialization of the library (http://hybridauth.sourceforge.net/userguide/Configuration.html).
4. Now you can run snippet [[!HybriAuth?providers=`Twitter`]] on any page.

If there will be any errors on library initialization - it will be logged in in system log.

I recorded simple video with Twitter login http://www.youtube.com/watch?v=ron_VTaQeWE for you.