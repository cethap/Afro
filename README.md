# Afro
Expressive routing framework for PHP.

# Working with Afro
Afro includes five procedural functions which allow you to handle requests with:

- `get`
- `post`
- `put`
- `delete`
- `ajax`

All of them take the same parameters but will only activate on the given request types.

## Named parameters
**NEW!**

Named parameters give you much more flexibility in how you handle data, whilst still giving you the power of regular expressions, plus you're still able to use the `param` function at the same time if you'd like.

```php
<?
	get('/blog/(\w+)/(\d+)', function($Afro, $catID, $pageID) {
		echo "Retrieving from Blog: Category: {$catID} and Page ID: {$pageID}";
	});
?>
```

Here we've been able to retrieve what we'd expect to be the category and page id. It's important to note that the `blog` chunk of the route is **not** returned as a parameter, as you should already know what route is being handled. If this is something you'd need, then you'd want to replace blog with `(\w+)` and then add a parameter to the callback of `$route` or whatever, then a condition to handle the value returned.

## Handling a simple `GET` request with no parameters.

```php
<?
	get('/', function($Afro) {
		echo "HELLO";
	});
?>
```

## Handling a `GET` request for getting a users name.

```php
<?
	get('/hello/(.*?)', function($Afro) {
		echo 'Hello ' . $Afro->param(1) . ', I hope today is full of Unicorns.'
	});
?>
```

## Handling a `GET` request for getting a users name in different formats.

One of the beautiful things about Afro is that you can use the same request handler, but output different data depending on the format the request is called as.

Let's take the example above and use add a JSON output.

```php
<?
	get('/hello/(.*?)', function($Afro) {
		$Afro->format('json', function($Afro) {
			echo json_encode(array('name', $Afro->param(1)));
		});

		if(!$Afro->format)
			echo 'Hello ' . $Afro->param(1) . ', I hope today is full of Unicorns.'
	});
?>
```

Now, if the request ends is `http://localhost/afro/hello/jbrooksuk.json` the output will be returned as a valid JSON string.

## Handling a simple `POST` request with a username.

POST requests are just handled with the `post` function.

```php
<?
	post('/connect/(.*?)', function($Afro) {
		if(!$Afro->format) {
			// Insert the user into a database? The format will always be the same in whichever function you use.
		}
	});
?>
```

# Payload
Afro is also aware of requests which aren't handled by one of the five *router* functions. If you're request is in the format of `?hello=James&day=Sunday` for example, then Afro will create an array of this in the `payload` array for you.

This can be accessed with `$Afro->payload` just in case you need it.

# Under the hood
You can expose any part of Afro by taking a look at the instance created by the class on the `$Afro` variable. Calling `var_dump($Afro->getInstance())` will display the current pages requested data along with several other useful elements.

- `URI` the entire URI being requested.
- `params` an array of params, accessible with the `$Instance->param($index)` function.
- `method` a string representation of the requested method used.
- `format` if a format such as `.json` or `.csv` is appended to the end of the URI this will be populated.
- `paramCount` the amount of parameters being requested in the `params` array above.
- `payload` as explained in the section above.

# License
MIT - [http://jbrooksuk.mit-license.org](http://jbrooksuk.mit-license.org)