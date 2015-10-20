# Oliva Tree Utilities for PHP
Simple utility for handling tree structures in PHP.

Useful for handling **tree data stored in database** or for performing **search** and **filter** operations on arbitrary tree structures.


## Why use Oliva Tree?
It has been build for real-life usage and it is tested that way.

It provides many utilities for data manipulation, it will make building components like **menus**, **tree views**, **grids**, **data lists** and other a fun thing to do.

### What can Oliva Tree do?
* **build** tree structures **from arbitrary flat data** with support for
    *  materialized paths (position path variant)
    *  recursive trees (parent - id)
* enhance functionality of data already in tree structures
* build trees **from JSON** strings
* **find nodes**, breadth-first or depth-first
* **filter nodes** by any condition
* **compare trees**, nodes, sub-trees
* provide means for **easy** tree structure **manipulation**
* **transform** tree structures to flat data, in breadth-first or depth-first manner


## Installation
The easiest way to install is to use composer. Just add `"oliva/tree"` to the "require" section in your `composer.json` file.
```json
{
	"require": {
		"php": ">=5.6.0",
		"oliva/tree"
	}
}
```


## Usage
Each tree has a root node. Each node allows getting/setting of children and parent, thus creating a tree structure.

### Node
`NodeBase` is a basic abstract implementation of the `INode` interface. `Node` is a full-featured implementation allowing creation of trees with **arbitrary data** or objects.
```php
$node = new Node();
$node->title = 'item one';
$child = $node->addChild(new Node(['title' => 'first child of one']));
$node->addChildren([new Node(['title' => 'second child of one']), new Node(['title' => 'third child of one'])]);

$child->getParent() === $node; // TRUE
$siblings = $child->getSiblings();
$allChildren = $node->getChildren();
$child->isFirst(); // TRUE - first of the siblings
$child->isLast(); // FALSE - last of the siblings

$child->getLevel(); // 1 - since it only has one parent, the root (the root is level 0)

$child->detach(); // detach from the tree, becomes root
$child->isRoot(); // TRUE

// implements IteratorAggregate interface
foreach($node as $key => $node){...}

// can be created from arbitrary objects
$myObject = new MyObject();
$node = new Node($myObject);
$node->aMethodOfTheobject(); // the call is forwarded to the object
```


### Comparing nodes

To compare the data of nodes, use the `NodeComparator` class.
```php
// the comparator
$comparator = new NodeComparator();

// simple nodes
$node1 = new SimpleNode(1);
$node2 = new SimpleNode(1);
$node3 = new SimpleNode(2);

// basic comparison
$comparator->compare($node1, $node2); // TRUE
$comparator->compare($node1, $node3); // FALSE
```
`NodeComparator` can be configured to compare with various strictness (can be set up to compare to equality `==` or identity `===` for different data types), not to compare recursively, to compare child indices and much more.

A **custom comparison function** can also be provided:
```php
$comparator->callbackCompare($node1, $node3, function($nodeData1, $nodeData2) {
            if($nodeData1 > 10){
                return $nodeData1 > $nodeData2;
            }
			return $nodeData1 === $nodeData2;
		}); // FALSE
```
> **Note**: this is just a basic example, `NodeComparator` can of course compare whole branches with all their nodes  **recursively**.


### Tree
Trees are implementations of `ITree` interface. Class `Tree` holds the root and provides convenient methods for creating iterators easily.

Create a tree.
```php
$tree = new Tree($rootNode);
```
Iterators provide a way to **iterate through all the nodes**, **filter** them, **search** a specific node, **prune the tree** and so on.

### Iterations, search, filtering

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

### Building trees from linear data structures
You can build trees from linear data structures, such as database results, using `DataTree` class and tree builders. You can also build trees from data already in tree structure.

#### Recursive tree
A trivial data tree where each data-node has a pointer to its parent. This data model is called the Adjacency List Model and when stored in a database, it is often referred to as the self-joined table design, self-referencing or self-referenced tables and so on.

| ID        | parent    | title|
|:----------|:----------|:-------|
|1          | NULL| the root|
|2|1|first child of root
|3|1|second child of root
|4|6|fourth level - parent specified later - this will work
|5|2|third level
|6|2|third level, second child

Get this structure from a database as an array.
Tell the tree that "parent" is the member where the parent's "id" is found.
Create the tree.
```php
$tree = new DataTree($data, new RecursiveTreeBuilder('parent', 'id'));
```
> Note: if you have more than one root, the builder's behaviour is undefined, the trees will overwrite one another.

#### Path tree
A data tree where the position of a node in a tree is represented by a position (hierarchy) string. This allows ordering of leafs. Each level is represented by 3 (in this case) characters of the string representing the position of the node in the current sub-tree.

Path trees are useful for storing menu hierarchy or similar structures where position of a node within a sub-tree matters. In large data sets with big IDs this approach may also reduce data size.

> **Note**: this is a special case of Materialized Path data model, where fixed number of characters are used for each level and no encoding of position is used.

| ID        | position    | title|
|:----------|:----------|:-------|
|1          | 001| the first level|
|2|001001|first child of 001
|3|001002|second child of 001
|4|001001001|third level
|5|001001002|third level, second child
|6|002001|a child of a node not yet specified - this will work
|7|002|second first-level item
|100|NULL|the root - this needs not be specified, an empty root will be created automatically

Get this structure from a database as an array.
Tell the tree that you are using 'position' as the hierarchy column and there are 3 characters for each level.
Create the tree.
```php
$tree = new DataTree($data, new PathTreeBuilder('position', 3));
```
Unless specified with NULL position (or position that is shorter than number of characters needed for each level),
an empty root node will be created to wrap the data tree. You can provide a data item with NULL position to specify your own root.

> Note: if you have more than one root, the later will override the previous one.

> Note: this builder uses by default an auto-sorting feature that ensures the children are always in the correct order assumed by the hierarchy member. This behaviour can be turned off.

#### Simple tree
This tree only converts data already in a tree structure using `Tree` and `Node` classes for further convenience.
It does not matter whether the data is stored in **array matrix** or as **objects**. The children need to be held in a common attribute ("children" by default).
```php
$tree = new DataTree($data, new SimpleTreeBuilder('children'));
```

#### JSON tree
Accepts string containing JSON, which is decoded upon construction.
```php
$tree = new DataTree($data, new JsonTreeBuilder($jsonEncodedString));
```

### Transformations
Allows transformation of trees to 1D arrays. This can be useful for printing the tree data in linear fasion, for example to display them in tables or grids.
```php
$tree = new Tree($rootNode);
$linear = $tree->getBreadthFirst();
$linearWithDeepestFirst = $tree->getDepthFirst();
```


## What's comming next

* extended documentation
* prunning (condition- and depth-based)
* node moving
* tree writers - alter the node's data to reflect current tree structure (prepare for storage) - a counterpart to tree builders
* nested sets
* fluent tree construction
* improvement: in recursive tree builder - allow users to choose which member is used as a node key (currently id is forced)
* unite and document exception codes


## Caveats
The following will not work with `Node` implementation:
```
$data = (object) ['array' => []];
$node = new Node($data);
$node->array[4] = 'foobar'; // indirect modification of overloaded property
```
The assignment to `$node->array`'s element will trigger `E_NOTICE` and **will not have** the expected **effect**. To overcome this problem, use this approach:
```
$data = (object) ['array' => []];
$node = new Node($data);
$array = $node->array;
$array[4] = 'foobar';
$data->array = $array;
```

Furthermore, when using Node data of type `array`, using `NULL` index on `$node` does not produce the same results as indexing the array directly. These work just like an array would:
```
// data of type array
$array = [1, 2, 3];
$node = new Node($array);

// these work just fine
$node[] = 5;
$node[100] = 'foo';
$node[''] = 6;
```
However, the assignment to `NULL` index does not write to the `''` (empty string) index of the array, but appends an element as `$node[] = 7` would!
```
$array = [1, 2, 3];
$node = new Node($array);

// these fail
$node[NULL] = 7;  // $node->getObject() === [1, 2, 3, 7]
$array[NULL] = 7; // $array             === [1, 2, 3, '' => 7]
```


## Notes

* great thank's to folks in **Via Aurea, s.r.o.** for providing valuable support, motivation and real-life testing

----

> **Warning**: This library is provided **as-is** with absolutely **no warranty** nor any liability from its creators for anything it's usage, manipulation or distribution may cause.
