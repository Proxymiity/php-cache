# php-cache
The ugliest file cache you've ever seen, written in PHP.  
This is not intended for a "public" usage.  
The original use case is to cache images from SEA servers which takes a while to reply.  
**There is no authentication included with the cache nor the API.**

### Cache usage
Get a file, or cache it if it's not in the cache:  
``https://your-cache-server.com/cache/cache.php?url=https://a-slow-server.com/image.png``

...using a custom referer:  
``https://your-cache-server.com/cache/cache.php?url=https://a-slow-server.com/image.png&referer=https://a-slow-server.com``

...using basic auth:  
``https://your-cache-server.com/cache/cache.php?url=https://a-slow-server.com/image.png&user=john&pass=doe``

⚠️ The cache server **does not** remove files **nor update** stored files automatically. You'll need to do that manually:  
``https://your-cache-server.com/cache/cache.php?url=https://a-slow-server.com/image.png&refresh=1``

You can view a live version of the file using the live argument:  
``https://your-cache-server.com/cache/cache.php?url=https://a-slow-server.com/image.png&live=1``

...or redirect the client to the file's URL for large files:  
``https://your-cache-server.com/cache/cache.php?url=https://a-slow-server.com/image.png&redirect=1``

*The live argument is prioritized over the refresh argument.  
You don't have to actually set their value to true or 1, the script only checks if they're set.*

You can also get URLs by their hash with the ``hash`` argument:  
*beware: if URLS aren't stored, you'll query the hash of an empty URL string*
``https://your-cache-server.com/cache/cache.php?hash=473b507ce2f6c162b92d15dcbc106e30``

### API Usage
Get the status of a file (its hash, filename, MIME type and cache status):  
``https://your-cache-server.com/cache/api.php?url=https://a-slow-server.com/image.png``
Reply:
````json
{
  "cached": true,
  "url": "https://your-cache-server.com/cache/data/473b507ce2f6c162b92d15dcbc106e30/image.png",
  "original_url": "https://a-slow-server.com/image.png",
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
  "url": "https://your-cache-server.com/cache/data/473b507ce2f6c162b92d15dcbc106e30/image.png",
  "original_url": "https://a-slow-server.com/image.png",
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
  "url": "https://your-cache-server.com/cache/data/473b507ce2f6c162b92d15dcbc106e30/image.png",
  "original_url": "https://a-slow-server.com/image.png",
  "url_hash": "473b507ce2f6c162b92d15dcbc106e30",
  "file": "image.png",
  "type": "image/png"
}
````

You can also get URLs by their hash with the ``hash`` argument:  
*beware: if URLS aren't stored, you'll query the hash of an empty URL string*
``https://your-cache-server.com/cache/api.php?hash=473b507ce2f6c162b92d15dcbc106e30``  
Reply:
````json
{
  "cached": true,
  "url": "https://your-cache-server.com/cache/data/473b507ce2f6c162b92d15dcbc106e30/image.png",
  "original_url": "https://a-slow-server.com/image.png",
  "url_hash": "473b507ce2f6c162b92d15dcbc106e30",
  "file": "image.png",
  "type": "image/png"
}
````

# Authentication
You can uncomment ``include 'auth.php';`` in each file and edit ``auth.php``'s array and append a ``token=`` parameter.
If you pass a token that's not in the array, you'll get a 403 error.