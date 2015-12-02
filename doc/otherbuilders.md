[Oliva Tree](docs.md) > Other tree builders

## Other tree builders

### Simple tree
This tree only converts data already in a tree structure using `Tree` and `Node` classes for further convenience.
It does not matter whether the data is stored in **array matrix** or as **objects**. The children need to be held in a common attribute ("children" by default).
```php
$tree = new DataTree($data, new SimpleTreeBuilder('children'));
```

### JSON tree
Accepts string containing JSON, which is decoded upon construction.
```php
$tree = new DataTree($data, new JsonTreeBuilder($jsonEncodedString));
```