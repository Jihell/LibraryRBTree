Jihel's RB Tree PHP implementation
==================================

PHP OOP implementation of Red Black Tree.
You can found information about this kind of binary search tree on
[wikipedia](https://en.wikipedia.org/wiki/Red%E2%80%93black_tree).


1- Install
----------

Add library to your composer.json require:

    {
        "require": {
            "jihel/library-rbtree": "dev-master",
        }
    }

or

    php composer.phar require jihel/library-rbtree


2- Usage
--------

### a) Create a tree:

    use Jihel\Library\RBTree\Tree as Tree;
    $tree = new Tree();

### b) Create a node:

    $node = new Jihel\Library\RBTree\Node(1, 'My value');
    $tree->insert($node);

### c) Delete a node:

    $tree->remove($node);
    
Please note that the var $node is not deleted, the object still exist but is detached from the tree.

### d) Find a node:

    $node = $tree->find(1);

### e) Find a node relative to another:

    $next = $tree->findRelative($node, Jihel\Library\RBTree\Model\NodeInterface::POSITION_RIGHT);
    // or
    $next = $tree->findSuccessor($node);

### f) Enumerate nodes between a min and max

    $list = $tree->enumerate(1, 12);


You can look at exemples for a simple implementation with integer nodes :

- [cli.php](exemple/cli.php)
- [html.php](exemple/index.php)


4- Thanks
---------

I inspired from the work of Gokce Toykuyu from the MIT.
See his [implementation](http://web.mit.edu/~emin/Desktop/ref_to_emin/www.old/source_code/red_black_tree/index.html)

There is also some youtube video of a RB Tree at work who might interest you :

- [https://www.youtube.com/watch?v=rcDF8IqTnyI](https://www.youtube.com/watch?v=rcDF8IqTnyI)
- [https://www.youtube.com/watch?v=vDHFF4wjWYU](https://www.youtube.com/watch?v=vDHFF4wjWYU)


Thanks to me for giving my free time doing class for lazy developers.

You can access read CV [here](http://www.joseph-lemoine.fr)
