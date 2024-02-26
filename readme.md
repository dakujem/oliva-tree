>
> 📢
>
> It's been a long time since the dawn of this library.
> It still runs on modern PHP, but it's far from optimal.
>
> See a modern reimplementation of this library here: [👉 `dakujem/oliva`](https://github.com/dakujem/oliva)
>


# Oliva Tree

[![PHP from Packagist](https://img.shields.io/packagist/php-v/oliva/tree)](https://packagist.org/packages/oliva/tree)
[![Tests](https://github.com/dakujem/oliva-tree/actions/workflows/php-test.yml/badge.svg)](https://github.com/dakujem/oliva-tree/actions/workflows/php-test.yml)
[![Coverage Status](https://coveralls.io/repos/github/dakujem/oliva-tree/badge.svg?branch=trunk)](https://coveralls.io/github/dakujem/oliva-tree?branch=trunk)

Utility for handling tree data structures.

>
> 💿 `composer require oliva/tree`
>
> 📖 **[documentation](doc/docs.md)**
>

**Oliva Tree** is a powerful yet simple to use utility for handling tree data structures in PHP.

Useful for handling **tree data stored in database** or for performing **search** and **filter** operations on arbitrary tree structures.


## Why use Oliva Tree?

- data manipulation
- write components like **menus**, **tree views**, **grids**, **data lists**, etc. with ease.
- [it's documented](doc/docs.md)
- battle and unit tested


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

`composer require oliva/tree`

Older versions of Oliva Tree run on PHP 5.4 and up and also PHP 7.0 and up.
Versions since `1.3` support PHP 7.4+ and PHP 8+ (including PHP 8.2 and PHP 8.3).


## Changelog

> Notable changes only.

**1.4**
- PHP 8.1+ supported
- some return type-hints added

**1.3**
- PHP 8.0 support added
- PHP >= 7.4 required
- updated to a simple permissive license (Unlicense)

**1.2.3**
- updated licensing to enable multi-licensing, i.e. one can now use any of GPL, MIT or BSD license, see [license.md](license.md) for more information


## Missing features

I never added these...

* prunning (condition- and depth-based)
* node moving mechanism / helpers
* tree writers - alter the node's data to reflect current tree structure (prepare for storage) - a counterpart to tree builders
* unite and document exception codes
* support for nested sets data model

> On the second thought, most of these things are outside the scope of this library anyway. 


## Notes

Great thank's to folks in [**Via Aurea**](https://github.com/viaaurea) for providing valuable support, motivation and real-world testing.

> 
> 🎉
>
> I finally found a reason to reimplement the idea of this tool for building and managing trees.
> 
> See the modern reimplementation here: [👉 `dakujem/oliva`](https://github.com/dakujem/oliva)
> 


