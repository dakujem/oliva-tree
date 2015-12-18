[Oliva Tree](docs.md) > Caveats

## Caveats

The `Node` class serves as a seamless data wrapper providing tree functions, however, it is best fitted for using it with PHP objects, like
```php
$myObject = new MyObject();
$node = new Node($myObject); // this is what Node has been designed for
```


The following will not work with `Node` implementation:
```php
$data = (object) ['array' => []];
$node = new Node($data);
$node->array[4] = 'foobar'; // indirect modification of overloaded property
```
The assignment to `$node->array`'s element will trigger `E_NOTICE` (**indirect modification of overloaded property**) and **will not have** the expected **effect**. To overcome this problem, use this approach:
```php
$data = (object) ['array' => []];
$node = new Node($data);
$array = $node->array;
$array[4] = 'foobar';
$data->array = $array;
```

Furthermore, when using data of type `array` with a `Node` instance, **using `NULL` index** on the `$node` does not produce the same results as indexing the array directly, as shown below.

These work just like an array would:
```php
// data of type array
$array = [1, 2, 3];
$node = new Node($array);

// these work just fine
$node[] = 5;
$node[100] = 'foo';
$node[''] = 6;
```
However, the **assignment to `NULL` index** does not write to the `''` (empty string) index of the array, but appends an element as `$node[] = 7` would!
```php
$array = [1, 2, 3];
$node = new Node($array);

// these fail
$node[NULL] = 7;  // $node->getObject() === [1, 2, 3, 7]
$array[NULL] = 7; // $array             === [1, 2, 3, '' => 7]
```

Finally, **iterating over a `Node` instance actually iterates over its child nodes**, not over the array (or any other data) contained within!
To iterate over the data, use:
```php
foreach($node->getContents() as $item){
	...
}
