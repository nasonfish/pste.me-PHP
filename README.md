## pste.me-PHP

*pste.me-PHP* is a small and simple library for accessing [pste.me](http://beta.pste.me/)'s API.

### How do I use it?

Simply copy the PsteMe.php file somewhere and include/require it.

```php
include 'pste.me-PHP/PsteMe.php'
```

Then, to use the API:
```php
$pste = new PsteMe('your_username', 'MyAwesomeAPIKey');
```

And use your new PsteMe object to call functions!

This API has two functions currently for allowing access to the current api.

 - `$pste->create($paste, $name="New Paste", $access=Access::_PUBLIC, $expires="+1 month", $mode=Language::Plain)` - Create a new paste
  - **$paste**: The information you are making a paste of. "Hey there!\r\nWhat's up?"
  - **$name**: The title of your new paste. "Hello World!"
  - **$access**: The level of access you allow for your paste. You can use the constants in `Access` for this. (`Access::_PUBLIC, Access::_PRIVATE`)
  - **$expires**: A string that represents the amount of time this paste is expiring in. This should be compatible with the PHP `strtotime()` function. "+1 week", "+1 month"
  - **$mode**: The syntax highlighting language you are using for this paste. Use the constants in `Language` for this. (Language::PHP, Language::Plain)
 - `$pste->view($slug)` - View the information about a certain page by its slug.

Both of these functions return a `Paste` object. The methods in this class give you access to all the data, plus access to the raw JSON.

### Examples

```php
include('PsteMe.php')
$pste = new PsteMe;
$p1 = $pste->create("I'm the coolest person ever.\r\n\\o/"); // With default info
echo $p1->getSlug(); // Echo the new slug
$p2 = $pste->create('<?php echo "Hey there!"', "Hello World!", Access::_PRIVATE, '+1 minute', Language::PHP);
echo $p1->getSlug(); // Echo the new slug
$p3 = $pste->get('asfdg'); // Get the information of the paste with the name
echo $p3->getTitle(); // Echo the title
print_r($p3->getRaw()); // Print the raw information on this paste
// TODO celebrate
```
