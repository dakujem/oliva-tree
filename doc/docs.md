# Oliva Tree

For the **root readme** file, see [readme.md](../README.md).

* [Installation](installation.md)
* [Trees](trees.md)
* [Nodes](nodes.md)
	* [Comparing nodes](comparing.md)
* [Building a tree](building.md)
	* [fluently - fluent tree building](fluent.md)
	* from data stored in a database
		* [Recursive trees](recursive.md)
		* [Materialized path trees](materialized.md)
* [Iterators](iterators.md)
* [Interfaces](interfaces.md)
* [Transformations](transformations.md)
* [Caveats](caveats.md)


## The very basics on a tree structure

Each tree has a root node.
Each node can have *any number of child nodes*, but *only one parent* node. A *root* node has no parent, a *leaf* node has no children.
Each node allows getting/setting of children and parent, thus creating a tree structure.


## Hints

Most of the source code is commented, so if you can't find sufficient information in this docs section,
you most probably will find your answers looking at the sources.

There are some useful use cases in the **test sources**,
however, I admit that I did not write the tests to be human readable.


## Oliva Tree documentation

This documentation covers most aspects of Oliva Tree.
I will continue to update it with more examples, use cases and class descriptions.


## Comming soon (docs):

- transformations
