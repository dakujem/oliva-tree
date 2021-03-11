# Oliva Tree

[![PHP from Packagist](https://img.shields.io/packagist/php-v/oliva/tree)](https://packagist.org/packages/oliva/tree)
[![Build Status](https://travis-ci.com/dakujem/oliva-tree.svg?branch=master)](https://travis-ci.com/dakujem/oliva-tree)
[![Coverage Status](https://coveralls.io/repos/github/dakujem/oliva-tree/badge.svg?branch=master)](https://coveralls.io/github/dakujem/oliva-tree?branch=master)


**Oliva Tree** is a powerful yet simple to use utility for handling tree structures in PHP.

Useful for handling **tree data stored in database** or for performing **search** and **filter** operations on arbitrary tree structures.


## Why use Oliva Tree?

Because you only want one library for all your trees.

Because Oliva Tree has been build for real-life usage and it is also tested in real life, along with unit tests.

Because Oliva Tree provides many utilities for data manipulation, thus it makes writing components like **menus**, **tree views**, **grids**, **data lists** (and other) an easy and fun thing to do.

Because Oliva Tree is [well documented](doc/docs.md) (IMHO).


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


## Documentation

For more in-depth information, use cases and other examples, see the **[documentation section](doc/docs.md)**.


## Installation
The easiest way to install Oliva Tree is to use [Composer](https://getcomposer.org/). Just add `"oliva/tree"` to the "require" section in your `composer.json` file, like this:
```json
{
	"require": {
		"php": "^5.4 || ^7.0",
		"oliva/tree": "*"
	}
}
```

Oliva Tree runs on PHP 5.4 and up and also PHP 7.0 and up.


## What's comming next

* prunning (condition- and depth-based)
* node moving mechanism / helpers
* tree writers - alter the node's data to reflect current tree structure (prepare for storage) - a counterpart to tree builders
* unite and document exception codes
* support for nested sets data model


## Notable Changes

**1.3**
- PHP 8 support added
- PHP >= 7.4 required

**1.2.3**
- updated licensing to enable multi-licensing, i.e. one can now use any of GPL, MIT or BSD license, see [license.md](license.md) for more information


## Notes

* great thank's to folks in **Via Aurea, s.r.o.** for providing valuable support, motivation and real-life testing

