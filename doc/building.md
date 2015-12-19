[Oliva Tree](docs.md) > Building a tree

## Building a tree

A tree can be built by adding child nodes to their parent recursively.

```php
$node = new SimpleNode('the root');
$child = $node->addChild(new SimpleNode('first child of one'));
$child->addChild(...);
```
However, this approach is inconvenient for building trees from data that already exists in some structure or a storage.

Oliva Tree library provides "builders" for this purpose. A "builder" is any object implementing the `ITreeBuilder` interface.

The `DataTree` class is a tree that encapsulates a builder and so povides a reusable way to create a tree from data.



### Building trees from linear data structures
With Oliva Tree library, you can build trees from linear data structures, such as **results of a database data fetch operations**.
This is one of the key features of the library.

The building is done using tree builders, as simply as this:
```php
$data = MyDatabase::gimmeData();
$rootNode = (new MyBuilder)->build($data);
```

The `DataTree` class is used to encapsulate the builder, so that you can comfortably rebuild the tree later.
```php
$tree = new DataTree($data, $builder);
$tree->rebuild($anotherData);
$rootNode = $tree->getRoot(); // built from $anotherData
```


There are many ways trees can be stored in database tables. Oliva Tree builders can (so far) handle:
* [trivial recursive approach](recursive.md)
* [materialized paths data model](materialized.md)





### Building trees from structured data

You can also build trees from data already in tree structure, like a parsed configuration file or a JSON result of an API call.

For structured data, use `SimpleTreeBuilder` class.
```php
$data = [
	'key1' => 'val1',
	'key2' => 'val2',
	'children' => [
		['child1key'=>'val'],
		['child2'=>'val'],
		'child3',
	]
];

$root = (new SimpleTreeBuilder('children'))->build($data); // "children" is the default value for the constructor
// the 'children' key will be unset and the root node will contain the remaining content of the input,
// it's three children will contain their respective data

dump($root->getContents());
dump($root->getChild(0)->getContents());
dump($root->getChild(1)->getContents());
dump($root->getChild(2)->getContents());

/* the result of the previous calls:
array (2)
key1 => "val1" (4)
key2 => "val2" (4)

array (1)
child1key => "val" (3)

array (1)
child2 => "val" (3)

"child3" (6)
*/
```
> Note: the *children* member can be set to be any key.

It does not matter whether the data is stored in an **array matrix** or as **objects**.
The children need to be held in a common attribute ("children" by default).

For data in a JSON string, use `JsonTreeBuilder` class. It first decodes the string to an object,
then continues exactly as `SimpleTreeBuilder` (which is it's parent).
```php
$jsonRoot = (new JsonTreeBuilder('children'))->build($json); // again, you can specify your "children" key
```



----
|Reference|Full class name|File|Docs|
|:---|:---|:---|:---|
|`ITreeBuilder` | `Oliva\Utils\Tree\Builder\ITreeBuilder` | [ITreeBuilder.php](../src/Builder/ITreeBuilder.php) ||
|`SimpleTreeBuilder` | `Oliva\Utils\Tree\Builder\SimpleTreeBuilder` | [SimpleTreeBuilder.php](../src/Builder/SimpleTreeBuilder.php) ||
|`JsonTreeBuilder` | `Oliva\Utils\Tree\Builder\JsonTreeBuilder` | [JsonTreeBuilder.php](../src/Builder/JsonTreeBuilder.php) ||
|`SimpleNode` | `Oliva\Utils\Tree\Node\SimpleNode` | [SimpleNode.php](../src/Node/SimpleNode.php) |[Nodes](nodes.md)|
|`DataTree` | `Oliva\Utils\Tree\DataTree` | [DataTree.php](../src/DataTree.php) |[Trees](trees.md)|

