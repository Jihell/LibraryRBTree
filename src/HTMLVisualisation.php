<?php
/**
 * @package v4
 * @author Joseph LEMOINE <j.lemoine@ludi.cat>
 * @link https://ludi.cat
 */
namespace Jihel\Library\RBTree;

/**
 * Class CLIVisualisation
 */
class HTMLVisualisation
{
    /**
     * Simple recursive visualisation.
     *
     * @param Tree $tree
     * @return string
     */
    public function render(Tree $tree)
    {
        echo '<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
        <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
        <style type="text/css">
        .node {
            border: 1px solid #0f0;
            border-radius: 50px;
            height: 90px;
            width: 90px;
            padding: 10px;
            text-align: center;
        }
        .spacer {
            height: 100px;
        }
        .black {
            border-color: #000;
        }
        .red {
            border-color: #F00;
        }
        .leaf {
            background: #eee2c1;
        }
        .top, .bottom {
        height: 20px;
        }
        .left, .right {
        display: inline-block;
        }
        .parent, .root {
            border: 1px solid;
            height: 24px;
            width: 24px;
            padding: 2px;
            border-radius: 12px;
            text-align: center;
            display: inline-block;
        }
        .parent, .root, .glyphicon-remove {
        color: #999;
        }
        .parent {
            border-color: #ccc;
            background: #eee;
        }
        .root {
            border-color: #aad;
            background: #ccf;
            color: #aad;
        }
        </style>
    </head>
    <body>
        <table class="table">
            <tr>
        ';

        $this->infixeRender($tree->getRoot());

        echo '
            </tr>
        </table>
    </body>
</html>
';
    }

    /**
     * Simple recursive visualisation.
     * The first node should be the root
     *
     * @param Node $node
     * @return string The max indentation
     */
    protected function infixeRender(Node $node)
    {
        if ($node->haveChild(Node::POSITION_LEFT)) {
            echo '<td><div class="spacer"></div><table class="table"><tr>';
            $this->infixeRender($node->getChild(Node::POSITION_LEFT));
            echo '</tr></table></td>';
        }

        echo '<td>';
        $this->nodeRender($node);
        echo '</td>';

        if ($node->haveChild(Node::POSITION_RIGHT)) {
            echo '<td><div class="spacer"></div><table class="table"><tr>';
            $this->infixeRender($node->getChild(Node::POSITION_RIGHT));
            echo '</tr></table></td>';
        }
    }

    /**
     * @param Node $node
     */
    protected function nodeRender(Node $node)
    {

        echo '<div class="node'
            .(Node::COLOR_RED === $node->getColor() ? ' red' : ' black')
            .($node->isLeaf() ? ' leaf' : '')
            .'">';
        if (null !== $node->getParent()) {
            echo '<div class="parent">'.$node->getParent()->getId().'</div>';
        } else {
            echo '<div class="root">
<span class="glyphicon glyphicon-home"></span>
</div>';
        }

        echo '<div class="top">#'.$node->getId().' ('.$node->__toString().')</div>
            <div class="bottom">';

        if (!$node->isLeaf()) {
            echo '<div class="left">'.
                ($node->haveChild(Node::POSITION_LEFT) ?
                    $node->getChild(Node::POSITION_LEFT)->getId() : '<span class="glyphicon glyphicon-remove"></span>').
            '<</div>
            <div class="right">>'.
                ($node->haveChild(Node::POSITION_RIGHT) ?
                    $node->getChild(Node::POSITION_RIGHT)->getId() : '<span class="glyphicon glyphicon-remove"></span>').
            '</div>';
        } else {
            echo '<span class="glyphicon glyphicon-leaf"></span>';
        }

        echo '</div></div>';
    }
}
