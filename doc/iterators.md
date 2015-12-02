[Oliva Tree](docs.md) > Iterations, search, filtering

## Iterations, search, filtering

Iterate through all the nodes, with nodes on the current level first (**breadth-first** iteration).
```php
$it = $tree->getIterator(TreeIterator::BREADTH_FIRST_RECURSION); // the default
foreach($it as $node) {...}
// or also this
foreach($tree as $node) {...}
```

Iterate through all the nodes, in **depth-first** manner.
```php
$it = $tree->getIterator(TreeIterator::DEPTH_FIRST_RECURSION);
foreach($it as $node) {...}
```

Iterate through the nodes on the **current level only**.
```php
$it = $tree->getIterator(TreeIterator::NON_RECURSIVE);
foreach($it as $node) {...}
```

**Search** for a node with a specific key-value pair.
```php
// find a red node
$redNode = $tree->find('color', 'red');
```

**Filter** specific nodes. Multiple `key => value` pairs are implicitly treated using `AND` operation. If an array is present as `value` for a given `key`, the values of the array are treated using `OR` operation. The behaviour can be altered by parameters.
```php
// filter all oranges that are big and ripe
$oranges = $orangeTree->getFilterIterator(['status' => 'ripe', 'size' => 'big'], TreeFilterIterator::MODE_AND, TreeFilterIterator::MODE_OR); // the default mode

// filter apples, pears and all citruses
$filtered = $fruitTree->getFilterIterator(['name' => ['apple', 'pear'], 'category' => 'citrus'], TreeFilterIterator::MODE_OR);
```
A **filtering callback** can be used for *advanced filtering options*.
```php
// filter very specific nodes
$it = $tree->getFilteringCallbackIterator(function(\Oliva\Tree\NodeBase $node, $index) {
    try {
    	return $index === '002' || $node->id === 4;
    } catch (MemberAccessException $e) {
    	return FALSE;
    }
});
```
