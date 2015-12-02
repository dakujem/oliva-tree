# Oliva Tree

**Oliva Tree** is a powerful yet simple to use utility for handling tree structures in PHP.

Useful for handling **tree data stored in database** or for performing **search** and **filter** operations on arbitrary tree structures.


## Why use Oliva Tree?
Because you only want one library for all your trees.
Because Oliva Tree has been build for real-life usage and it is also tested in real life, along with unit tests.
Because Oliva Tree provides many utilities for data manipulation, thus it makes writing components like **menus**, **tree views**, **grids**, **data lists** (and other) an easy and fun thing to do.
Because Oliva Tree is well documented (IMHO).


### What can you do with Oliva Tree?
* **build** tree structures **from arbitrary flat data** with support for
    *  materialized path data model
    *  recursive trees (parent - id) (adjacency list data model, self-joined tables)
	*  build trees **from JSON** strings
* build trees using a fluent interface
* seamlessly wrap objects with `Node` class and keep using them as before
* enhance functionality of data already in tree structures
* **find nodes**, breadth-first or depth-first
* **filter nodes** by any condition
* **compare trees**, nodes, sub-trees
* provide means for **easy** tree structure **manipulation**
* **transform** tree structures (back) to flat data, in breadth-first or depth-first manner


## Installation
The easiest way to install Oliva Tree is to use [Composer](https://getcomposer.org/). Just add `"oliva/tree"` to the "require" section in your `composer.json` file, like this:
```json
{
	"require": {
		"php": ">=5.4.0",
		"oliva/tree"
	}
}
```


## Docs

For more in-depth documentation see the sources or view the documents below:

* [Documentation](doc/docs.md)
	* Interfaces
	* Nodes
	* Building a tree from data stored in a database
	* Fluent tree building
	* Filtering, iterations
	* Creating menu
	* Creating a data list or a grid
	* Transformations
	* Recursive tree
	* Materialized path tree



## What's comming next

* prunning (condition- and depth-based)
* node moving mechanism / helpers
* tree writers - alter the node's data to reflect current tree structure (prepare for storage) - a counterpart to tree builders
* unite and document exception codes
* support for nested sets data model


## Notes

* great thank's to folks in **Via Aurea, s.r.o.** for providing valuable support, motivation and real-life testing

----

> **Warning**: This library is provided **as-is** with absolutely **no warranty** nor any liability from its creators for anything it's usage, manipulation or distribution may cause.
