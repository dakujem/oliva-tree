# A refactor

... after 8+ years

---

tree generics
- node visitor (base for possible future filter, comparator, etc.)

builder:
- only native callables for tree building
- use strategy instead of traits

omit:
- tree classes (not needed)
  - replace with a tree manipulator
- comparator (too complex for now)

refactor
- traits into classes
- ditch all PHP 5 constructs
- fully typed
