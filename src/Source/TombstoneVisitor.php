<?php

declare(strict_types=1);

namespace Scheb\Tombstone\Analyzer\Source;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PhpParser\NodeVisitor\NameResolver;
use Scheb\Tombstone\Analyzer\TombstoneIndex;
use Scheb\Tombstone\Tombstone;

class TombstoneVisitor extends NameResolver
{
    /**
     * @var string|null
     */
    private $currentClass = null;

    /**
     * @var string[]
     */
    private $currentMethod = [];

    /**
     * @var string
     */
    private $file;

    /**
     * @var TombstoneIndex
     */
    private $tombstoneIndex;

    /**
     * @param TombstoneIndex $tombstoneIndex
     */
    public function __construct(TombstoneIndex $tombstoneIndex)
    {
        parent::__construct();
        $this->tombstoneIndex = $tombstoneIndex;
    }

    public function setCurrentFile(string $file): void
    {
        $this->file = $file;
    }

    public function enterNode(Node $node)
    {
        parent::enterNode($node);
        if ($node instanceof Class_) {
            $this->visitClassNode($node);
        } elseif ($node instanceof ClassMethod) {
            $this->visitMethodNode($node);
        } elseif ($node instanceof Function_) {
            $this->visitFunctionNode($node);
        } elseif ($node instanceof FuncCall) {
            $this->visitFunctionCallNode($node);
        }
    }

    private function visitClassNode(Class_ $node): void
    {
        $this->currentClass = (string) $node->namespacedName;
    }

    private function visitMethodNode(ClassMethod $node): void
    {
        $methodName = $this->currentClass.($node->isStatic() ? '::' : '->').$node->name;
        $this->currentMethod[] = $methodName;
    }

    private function visitFunctionNode(Function_ $node): void
    {
        $this->currentMethod[] = (string) $node->namespacedName;
    }

    private function visitFunctionCallNode(FuncCall $node): void
    {
        if ($this->isTombstoneFunction($node)) {
            $line = $node->getLine();
            $methodName = $this->getCurrentMethodName();
            $params = $this->extractParameters($node);
            $tombstone = new Tombstone($params, $this->file, $line, $methodName);
            $this->tombstoneIndex->addTombstone($tombstone);
        }
    }

    public function leaveNode(Node $node)
    {
        parent::leaveNode($node);
        if ($node instanceof ClassMethod || $node instanceof Function_) {
            array_pop($this->currentMethod);
        }
    }

    private function isTombstoneFunction(FuncCall $node): bool
    {
        if (isset($node->name->parts)) {
            $nameParts = $node->name->parts;

            return 1 === count($nameParts) && 'tombstone' === $nameParts[0];
        }

        return false;
    }

    private function getCurrentMethodName(): ?string
    {
        end($this->currentMethod);

        return $this->currentMethod ? $this->currentMethod[key($this->currentMethod)] : null;
    }

    public function getTombstones(): TombstoneIndex
    {
        return $this->tombstoneIndex;
    }

    /**
     * @param FuncCall $node
     *
     * @return string[]
     */
    private function extractParameters(FuncCall $node): array
    {
        $params = [];
        foreach ($node->args as $arg) {
            if ($arg->value instanceof String_) {
                $params[] = $arg->value->value;
            } else {
                $params[] = null;
            }
        }

        return $params;
    }
}
