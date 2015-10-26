<?php

require_once '../src/Node.php';
require_once '../src/Tree.php';
require_once '../src/CLIVisualisation.php';

use Jihel\Library\RBTree\Tree as RbTree;
use Jihel\Library\RBTree\Node as RbNode;
use Jihel\Library\RBTree\CLIVisualisation;

/**
 * Get user input from command line
 *
 * @access public
 * @return mixed
 */
function readUserInput() {
    do $input = trim(fgets(STDIN)); while($input === '');

    // Don't allow float inputs - cast the numeric input to int
    if(is_numeric($input))
        $input = (int) $input;

    return $input;
}

//------------------+
// {{{ start main() |
//------------------+

$newKey = $newKey2 = 0;
$option = 0;
$id = 1;
$tree = null;
$cliVisualisation = new CLIVisualisation();
while($option !== '8') {
    printf("choose one of the following:\n");
    printf("(1) add to tree\n(2) delete from tree\n(3) query\n");
    printf("(4) find predecessor\n(5) find sucessor\n(6) enumerate\n");
    printf("(7) print tree\n(8) quit\n");
    $option = readUserInput();
    switch($option) {
        case '1':
            printf("type key for new node\n");
            $newKey = readUserInput();
            $newNode = new RbNode($newKey, $newKey);
            $newNode->key = $newKey;
            if (null === $tree) {
                $tree = new RbTree($newNode);
            } else {
                $tree->insert($newNode);
            }
            break;

        case '2':
            printf("type key of node to remove\n");
            $newKey = readUserInput();
            if(($newNode = $tree->findId($newKey)) !== false) {
                $tree->remove($newNode);
            } else {
                printf("key not found in tree, no action taken\n");
            }
            break;

        case '3':
            printf("type key of node to query for\n");
            $newKey = readUserInput();
            if($tree->findId($newKey) !== false) {
                printf("data exists in tree with key {$newKey}\n");
            } else {
                printf("data not in tree\n");
            }
            break;

        case '4':
            printf("type key of node to find predecessor of\n");
            $newKey = readUserInput();
            if(($newNode = $tree->findId($newKey)) !== false) {
                $newNode = $tree->findRelative($newNode, RbNode::POSITION_LEFT);
                if($newNode->isLeaf()) {
                    printf("there is no predecessor for that node (it is a minimum)\n");
                } else {
                    printf("predecessor has key %d\n",$newNode->getId());
                }
            } else {
                printf("data not in tree\n");
            }
            break;

        case '5':
            printf("type key of node to find successor of\n");
            $newKey = readUserInput();
            if(($newNode = $tree->findId($newKey)) !== false) {
                $newNode = $tree->findRelative($newNode, RbNode::POSITION_RIGHT);
                if($newNode->isLeaf()) {
                    printf("there is no successor for that node (it is a maximum)\n");
                } else {
                    printf("successor has key %d\n", $newNode->getId());
                }
            } else {
                printf("data not in tree\n");
            }
            break;

        case '6':
            printf("type low and high keys to see all keys between them\n");
            printf("low:\n");
            $newKey = readUserInput();
            printf("high:\n");
            $newKey2 = readUserInput();
            printf("\n");
            $enumResult = $tree->enumerate($newKey, $newKey2);
            foreach($enumResult as $newNode) {
                printf("%s\n",$newNode->getId());
            }
            break;
        case 7:
            $cliVisualisation->render($tree);
            break;
        case 8:
            exit(0);

        default:
            printf("Invalid input; Please try again.\n");
    }
}

exit(0);
