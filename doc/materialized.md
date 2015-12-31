[Oliva Tree](docs.md) > Materialized path tree

## Materialized path tree

`MaterializedPathTreeBuilder` builds trees from data that follow the Materialized Paths data model.
The position of a node in a tree is represented by a hierarchy string.

There are more variants of the Path trees, some use fixed length strings for each depth level, some use delimited strings.
The hierarchy may be represented by parent nodes' IDs (parent references) or by sibling numbers (sequence numerators).
In many cases, the references and sibling numbers are encoded to reduce the path string length.

And the good news is `MaterializedPathTreeBuilder` can handle them all.


> **Note**: you do not have to specify the root in the data, it will be added automatically.
> This is useful for components like menus.


### Path trees with sibling numbers

Path trees with sibling numbers are useful for storing menu hierarchy or similar structures where position of a node within a sub-tree matters.
A *sibling number* (I saw it somewhere under the name *sequence numerator*) is simply a number of an ordered sequence representing the position,
for example when a node is second within its siblings, it's number may be 2, depending on implementation.
In large data sets with long IDs this approach may also reduce data size (the path string is considerably shorter).



#### Fixed-length

In this example, each depth level is represented by a fixed-length string representing
the position of the node in the current sub-tree (3 characters per depth level)
and sibling numbers are used (starting with 1).
This allows ordering of leafs and is useful for creating for example menu components.

```
[100] (root)
 |
 +-- [1] position: 001
 |    |
 |    +-- [2] position: 001 001
 |    |    |
 |    |    +-- [4] position: 001 001 001
 |    |    |
 |    |    +-- [5] position: 001 001 002
 |    |
 |    +-- [3] position: 001 002
 |
 +-- [7] position: 002
      |
      +-- [6] position: 002 001
```

| ID        | hierarchy    | title|
|:----------|:----------|:-------|
|1         | `001`| the first level|
|2|`001001`|first child of `001`
|3|`001002`|second child of `001`
|4|`001001001`|third level
|5|`001001002`|third level, second child
|6|`002001`|a child of a node not yet specified - this will work
|7|`002`|second first-level item
|100|`NULL`|the root - this needs not be specified, an empty root would be created automatically

Get this structure from a database as an array.
Tell the tree that you are using 'position' as the hierarchy column and there are 3 characters for each level.
Create the tree.
```php
$root = (new MaterializedPathTreeBuilder('hierarchy', 3))->build(MyDatabase::fetchAll());
```
Unless specified with NULL position (or position that is shorter than number of characters needed for each level),
an empty root node will be created to wrap the data tree. You can provide a data item with NULL position to specify your own root.

> **Note**: if you have more than one root, the later will override the previous one.


#### Delimited strings

And now an example of a delimited hierarchy string with node position in `position` member.

```
[NULL] (an empty root)
 |
 +-- [1] position: 1
 |    |
 |    +-- [2] position: 1.1
 |    |    |
 |    |    +-- [4] position: 1.1.1
 |    |    |
 |    |    +-- [5] position: 1.1.2
 |    |
 |    +-- [3] position: 1.2
 |
 +-- [7] position: 2
      |
      +-- [6] position: 2.1
```


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
$root = (new MaterializedPathTreeBuilder('position', '.'))->build(MyDatabase::fetchAll());
```

In cases where the hierarchy member is not well formed because it contains unnecessary delimiters,
there is a factory that creates a *robust* builder that can handle these cases:
```php
$builder = MaterializedPathTreeBuilderFactory::createRobustDelimitedVariant('position', '.');
```
This builder can handle strings like `.1.2.43`, `1.2.43.` or `1...2..43` that all have the meaning of `1.2.43` but got polluted.


> **Note**: encoding of the references or sibling numbers won't matter for the builder when using sibling numbers.


### Path trees with parent references

The hierarchy member in these trees carries a *referenced path* from the node itself through all its predecessors, to the root.

A *parent reference* is a unique identifier (*ID*) that directly identifies the parent node, this is usually the primary key of the record in a table or some other unique identifier.


The example below shows a tree structure where nodes carry information on their parents.

```
[root] (root) no parents
 |
 +-- [1] parents: root
 |    |
 |    +-- [2] parents: root, 1
 |    |    |
 |    |    +-- [4] parents: root, 1, 2
 |    |    |
 |    |    +-- [5] parents: root, 1, 2
 |    |
 |    +-- [3] parents: root, 1
 |
 +-- [7] parents: root
      |
      +-- [6] parents: root, 7
```

An example of such a tree structure in a table with hierarchy in the `parents` column / member may look like:

| ID        | parents    | title|
|:----------|:----------|:-------|
|`root`|`NULL`| the root
|`1`         | `root` | the first level|
|`2`|`root.1`|first child of `1`
|`3`|`root.1`|second child of `1`
|`4`|`root.1.2`|third level, a direct child of `2`
|`5`|`root.1.2`|third level, second child, also a direct child of `2`
|`6`|`root.7`|a child of a node with ID 7 not yet specified - this will work
|`7`|`root`|second first-level item

Note that the hierarchy column does not contain a reference of the node itself, but for the builder to work the whole path from the node to the root has to be known. This is why `MaterializedPathTreeBuilderFactory` is used below. It configures the builder precisely for this case, like this:
```php
$builder = MaterializedPathTreeBuilderFactory::createDelimitedReferenceVariant('parents', '.', 'id');
$root = $builder->build(MyDatabase::fetchAll());
```
Another method is used to create a builder for fixed-length references:
```php
$builder = MaterializedPathTreeBuilderFactory::createFixedLengthReferenceVariant('parents', 3, 'id');
```

> **Note**: See code for even more details.


### Gap bridging

`MaterializedPathTreeBuilder` has one useful ability - it can fill the gaps when there are references to non-existent nodes in the hierarchy strings.
This means that:
* the order of the nodes in the data does not matter
* you will always get a consistent tree structure

Like in this case:

| ID        | position    | title|
|:----------|:----------|:-------|
|`2`|`1.1`|first child of ID `1`

The resulting tree will have an empty root, its empty child, and a leaf node with ID 2.
```
(empty root)
 |
 +-- (empty)
      |
      +-- [node ID 2]
```

Or in this case:

| ID        | position    | title|
|:----------|:----------|:-------|
|`2`|`1`|a node
|`1`|`NULL`|the root is specified after a node

```
[root node ID 1]
 |
 +-- [node ID 2]
```

### Flexibility

`MaterializedPathTreeBuilder` is very flexible, it allows one to set callbacks for fetching the reference,
parsing the hierarchy string and more. This is actually the way `MaterializedPathTreeBuilderFactory`
works - it configures the builder with specific callbacks.


----
|Reference|Full class name|File|
|:---|:---|:---|
|`MaterializedPathTreeBuilder` | `Oliva\Utils\Tree\Builder\MaterializedPathTreeBuilder` | [Mate...Builder.php](../src/Builder/MaterializedPathTreeBuilder.php) |
|`MaterializedPathTreeBuilderFactory` | `Oliva\Utils\Tree\Builder\MaterializedPathTreeBuilderFactory` | [Mate...Factory.php](../src/Builder/MaterializedPathTreeBuilderFactory.php) |
