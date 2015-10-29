<?php
/**
 * @package library
 * @author Joseph LEMOINE <j.lemoine@ludi.cat>
 * @link https://ludi.cat
 */
namespace Jihel\Library\RBTree\Tests\Model;

use Jihel\Library\RBTree\Model\NodeInterface;
use Jihel\Library\RBTree\Node;
use Jihel\Library\RBTree\Tree;

class TreeTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $tree = new Tree();

        $this->assertEquals($tree->getRoot(), null);
        $this->assertEquals($tree->infixeList(), [0 => null]);
    }

    public function testInsertAndInfixe()
    {
        $tree = new Tree();

        $list = $ordered = [];
        foreach([
            10, 85, 15,
            70, 20, 60, 30,
            50, 65, 80, 90,
            40, 5, 55
        ] as $id) {
            $list[] = new Node($id, $id);
        }

        $order = function(Node $a, Node $b) {
            if ($a->getId() == $b->getId()) {
                return 0;
            }
            return $a > $b;
        };

        foreach ($list as $node) {
            $tree->insert($node);
            $ordered[] = $node;
            usort($ordered, $order);

            foreach ($tree->infixeList() as $row => $item) {
                $this->assertEquals($item->getId(), $ordered[$row]->getId());
                $this->assertEquals($item->getParent(), $ordered[$row]->getParent());
            }
        }
    }

    public function testAfterRemove()
    {
        $nodes = [
            10, 85, 15,
            70, 20, 60, 30,
            50, 65, 80, 90,
            40, 5, 55
        ];
        $tree = new Tree();
        foreach ($nodes as $id) {
            $node = new Node($id, $id);
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

        $expect = [
            [
                'i' => 5,
                'l' => null,
                'r' => null,
                'p' => 10,
                'c' => NodeInterface::COLOR_RED
            ], [
                'i' => 10,
                'l' => 5,
                'r' => null,
                'p' => 40,
                'c' => NodeInterface::COLOR_BLACK
            ], [
                'i' => 40,
                'l' => 10,
                'r' => 50,
                'p' => 55,
                'c' => NodeInterface::COLOR_BLACK
            ], [
                'i' => 50,
                'l' => null,
                'r' => null,
                'p' => 40,
                'c' => NodeInterface::COLOR_BLACK
            ], [
                'i' => 55,
                'l' => 40,
                'r' => 80,
                'p' => null,
                'c' => NodeInterface::COLOR_BLACK
            ], [
                'i' => 65,
                'l' => null,
                'r' => null,
                'p' => 80,
                'c' => NodeInterface::COLOR_BLACK
            ], [
                'i' => 80,
                'l' => 65,
                'r' => 85,
                'p' => 55,
                'c' => NodeInterface::COLOR_BLACK
            ], [
                'i' => 85,
                'l' => null,
                'r' => 90,
                'p' => 80,
                'c' => NodeInterface::COLOR_BLACK
            ], [
                'i' => 90,
                'l' => null,
                'r' => null,
                'p' => 85,
                'c' => NodeInterface::COLOR_RED
            ],
        ];

        foreach ($tree->infixeList() as $row => $node) {
            $this->assertEquals($node->getId(), $expect[$row]['i']);
            $this->assertEquals($node->getChild(NodeInterface::POSITION_LEFT) ? $node->getChild(NodeInterface::POSITION_LEFT)->getId() : null, $expect[$row]['l']);
            $this->assertEquals($node->getChild(NodeInterface::POSITION_RIGHT) ? $node->getChild(NodeInterface::POSITION_RIGHT)->getId() : null, $expect[$row]['r']);
            $this->assertEquals($node->getParent() ? $node->getParent()->getId() : null, $expect[$row]['p']);
            $this->assertEquals($node->getColor(), $expect[$row]['c']);
        }
    }
}
