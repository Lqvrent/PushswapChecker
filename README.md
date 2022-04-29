## Pushswap Checker
PushswapChecker is a simple program that checks if your Pushswap algorithm is working correctly.

### Configuration
If you're in the Web@cademy cursus, you must use the following configuration:
```php
$config = [
    'list_sizes' => [3, 4, 5, 10, 20],
    'timeout' => 5, // Limit the runtime of the Pushswap (in seconds)
    'pushswap_filename' => 'push_swap.php', // Web@cadémie : "push_swap.php" // PGE1 : "pushswap"
    'is_php_script' => true, // Web@cadémie : true // PGE1 : false
];
```
If you're in the PGE cursus, you must use the following configuration:
```php
$config = [
    'list_sizes' => [3, 4, 5, 10, 20],
    'timeout' => 5, // Limit the runtime of the Pushswap (in seconds)
    'pushswap_filename' => 'pushswap', // Web@cadémie : "push_swap.php" // PGE1 : "pushswap"
    'is_php_script' => false, // Web@cadémie : true // PGE1 : false
];
```
The parameters `list_sizes` and `timeout` can be changed to your needs.
The first one is the list of sizes you want to test.
The second one is the limit of the runtime of the Pushswap.

### Execution
To run the program, you need php.
Run the program with :
```bash
$ php PushswapChecker.php
```
