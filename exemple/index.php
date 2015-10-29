<?php
/**
 * @package library
 * @author Joseph LEMOINE <j.lemoine@ludi.cat>
 * @link https://ludi.cat
 */
require '../bootstrap.php';

$nodes = [
    10, 85, 15,
    70, 20, 60, 30,
    50, 65, 80, 90,
    40, 5, 55
];
$tree = new Jihel\Library\RBTree\Tree();
foreach ($nodes as $id) {
    $node = new Jihel\Library\RBTree\Node($id, $id);
    $tree->insert($node);
}

$removeNode = [
    30,
    70,
    60,
    15,
];

foreach ($removeNode as $id) {
    $tree->remove($tree->find($id));
}

$visualisation = new Jihel\Library\RBTree\Visualisation\Html();
$visualisation->render($tree);
