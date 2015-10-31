# Oliva Tree Utilities for PHP
Simple utility for handling tree structures in PHP.

Useful for handling **tree data stored in database** or for performing **search** and **filter** operations on arbitrary tree structures.


## Why use Oliva Tree?
Because you want one library for all your trees.
Because Oliva Tree has been build for real-life usage and it is also tested that way.
Because Oliva Tree provides many utilities for data manipulation, thus it makes building components like **menus**, **tree views**, **grids**, **data lists** (and other) an easy and fun thing to do.

### What can you do with Oliva Tree?
* **build** tree structures **from arbitrary flat data** with support for
    *  materialized path data model
    *  recursive trees (parent - id) (adjacency list data model, self-joined tables)
* build trees using fluent interface
* seamlessly wrap any data (Node class)
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
`NodeBase` is an abstract implementation of the `INode` interface and the base for `Node` and `SimpleNode` classes. It provides a rich set of convenience methods.

`Node` is an implementation allowing creation of trees with **arbitrary data** or objects with seamless access to the original data.
```php
$node = new Node(['title' => NULL]);
$node->title = 'item one';
$child = $node->addChild(new Node(['title' => 'first child of one']));
$node->addChildren([new Node(['title' => 'second child of one']), new Node(['title' => 'third child of one'])]);

$child->getParent() === $node; // TRUE
$siblings = $child->getSiblings();
$allChildren = $node->getChildren();
$child->isFirst(); // TRUE - first of the siblings
$child->isLast(); // FALSE - last of the siblings

$child->getDepth(); // 1 - since it only has one parent, the root (the root has depth 0)

$child->detach(); // detach from the tree, becomes root
$child->isRoot(); // TRUE

// implements IteratorAggregate interface
foreach($node as $key => $node){...}

// can be created from arbitrary objects
$myObject = new MyObject();
$node = new Node($myObject);
$node->aMethodOfTheobject(); // the call is forwarded to the object
```
`SimpleNode` only has a value accessible by `getValue()` and `setValue()` methods. It can be any value.

#### Fluent tree building
Another cool feature. Both `Node` and `SimpleNode` descend from `NodeBase` that provides methods for fluent tree building and many more.
```
$root = new SimpleNode('0');
$root->addNode('1')
       ->addNode('1.1')
           ->addLeaf('1.1.1')
           ->addLeaf('1.1.2')
           ->getParent()
       ->addLeaf('1.2')
       ->addNode('1.3')
           ->addLeaf('1.3.1')
           ->addLeaf('1.3.2')
           ->getParent()
       ->getParent()
  ->addLeaf('2');
  ```
  The same works with any `NodeBase` descendant.

#### Own nodes
You can create your own nodes by implementing the `INode` inteface to work with the rest of the library. By implementing the `IDataNode` interface, you can compare nodes using node comparator.

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

More [here](doc/trees.md).

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

#### Materialized path tree
`MaterializedPathTreeBuilder` builds trees from data that follow the Materialized Paths data model.
The position of a node in a tree is represented by a hierarchy string.

There are more variants of the Path trees, some use fixed length strings for each depth level, some use character delimited strings. The hierarchy may be represented by ID's or by sibling numbers (numerators). In many cases, the IDs or sibling numbers are encoded to reduce the path length.

And the good new is `MaterializedPathTreeBuilder` can handle them all.

Path trees with sibling numbers are useful for storing menu hierarchy or similar structures where position of a node within a sub-tree matters. In large data sets with big IDs this approach may also reduce data size (the paths string is considerably shorter).

In this example, each depth level is represented by a fixed-length string representing the position of the node in the current sub-tree (3 characters per depth level) and sibling numbers are used. This allows ordering of leafs and is useful for creating for example menu components.

> **Note**: this is a special case of Materialized Path data model, where fixed number of characters (3) are used for each level. No encoding is used.

| ID        | hierarchy    | title|
|:----------|:----------|:-------|
|1         | `001`| the first level|
|2|`001001`|first child of `001`
|3|`001002`|second child of `001`
|4|`001001001`|third level
|5|`001001002`|third level, second child
|6|`002001`|a child of a node not yet specified - this will work
|7|`002`|second first-level item
|100|`NULL`|the root - this needs not be specified, an empty root will be created automatically

Get this structure from a database as an array.
Tell the tree that you are using 'position' as the hierarchy column and there are 3 characters for each level.
Create the tree.
```php
$tree = new DataTree($data, new MaterializedPathTreeBuilder('hierarchy', 3));
```
Unless specified with NULL position (or position that is shorter than number of characters needed for each level),
an empty root node will be created to wrap the data tree. You can provide a data item with NULL position to specify your own root.

> Note: if you have more than one root, the later will override the previous one.

And now an example of a delimited hierarchy string with node position in `position` member.

| ID        | position    | title|
|:----------|:----------|:-------|
|`1`         | `1` | the first level|
|`2`|`1.1`|first child of ID `1`
|`3`|`1.2`|second child of ID `1`
|`4`|`1.1.1`|third level, a direct child of ID `2`
|`5`|`1.1.2`|third level, second child, also a direct child of ID `2`
|`6`|`2.1`|a child of a node not yet specified - this will work
|`7`|`2`|second first-level item

```php
$tree = new DataTree($data, new MaterializedPathTreeBuilder('position', '.'));
```

> **Note**: for now, when using IDs in hierarchy and only specify the "parents" path, you need to provide your own processing routines to the builder. See code for more details.

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
* fetching nodes using a vector (1D array of node indices)
* prunning (condition- and depth-based)
* node moving mechanism / helpers
* tree writers - alter the node's data to reflect current tree structure (prepare for storage) - a counterpart to tree builders
* unite and document exception codes
* nested sets


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
