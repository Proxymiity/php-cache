# php-cache
The ugliest file cacher you've ever seen, written in PHP.  
This is not intended for a "public" usage.  
The original use case is to cache images from SEA servers which takes a while to reply.  
**There is no authentication included with the cache nor the API.**

###Cache usage
Get a file, or cache it if it's not in the cache:  
``https://your-cache-server.com/cache/cacher.php?url=https://a-slow-server.com/image.png``

...using a custom referer:  
``https://your-cache-server.com/cache/cacher.php?url=https://a-slow-server.com/image.png&referer=https://a-slow-server.com``

...using basic auth:  
``https://your-cache-server.com/cache/cacher.php?url=https://a-slow-server.com/image.png&user=john&pass=doe``

⚠️ The cache server **does not** remove files **nor update** stored files automatically. You'll need to do that manually:  
``https://your-cache-server.com/cache/cacher.php?url=https://a-slow-server.com/image.png&refresh=1``

You can view a live version of the file using the live argument:  
``https://your-cache-server.com/cache/cacher.php?url=https://a-slow-server.com/image.png&live=1``

*The live argument is prioritized over the refresh argument.  
You don't have to actually set their value to true or 1, the script only checks if they're set.*

###API Usage
Get the status of a file (its hash, filename, MIME type and cache status):  
``https://your-cache-server.com/cache/api.php?url=https://a-slow-server.com/image.png``
Reply:
````json
{
  "cached": true,
  "url": "https://your-cache-server.com/cacher/data/473b507ce2f6c162b92d15dcbc106e30/image.png",
  "url_hash": "473b507ce2f6c162b92d15dcbc106e30",
  "file": "image.png",
  "type": "image/png"
}
````

You can invalidate a cache by supplying the ``delete`` argument:  
``https://your-cache-server.com/cache/api.php?url=https://a-slow-server.com/image.png?delete=1``  
Reply:
````json
{
  "cached": false,
  "url": "https://your-cache-server.com/cacher/data/473b507ce2f6c162b92d15dcbc106e30/image.png",
  "url_hash": "473b507ce2f6c162b92d15dcbc106e30",
  "file": "image.png",
  "type": "image/png"
}
````

You can force the creation of a cache by supplying the ``create`` argument:  
*(you can also use arguments listed above in the "Cache Usage" section)*  
``https://your-cache-server.com/cache/api.php?url=https://a-slow-server.com/image.png?create=1``  
Reply:
````json
{
  "cached": true,
  "url": "https://your-cache-server.com/cacher/data/473b507ce2f6c162b92d15dcbc106e30/image.png",
  "url_hash": "473b507ce2f6c162b92d15dcbc106e30",
  "file": "image.png",
  "type": "image/png"
}
````