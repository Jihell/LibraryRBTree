<?php

require_once '../src/Node.php';
require_once '../src/Tree.php';
require_once '../src/CLIVisualisation.php';

use Jihel\Library\RBTree\Tree as RbTree;
use Jihel\Library\RBTree\Node as RbNode;
use Jihel\Library\RBTree\HTMLVisualisation;

$nodes = [10, 85, 15, 70, 20, 60, 30, 50, 65, 80, 90, 40, 5, 55];
$tree = new RbTree();
foreach ($nodes as $id) {
    $node = new RbNode($id, $id);
    $tree->insert($node);
}

$visualisation = new HTMLVisualisation();
$visualisation->render($tree);
