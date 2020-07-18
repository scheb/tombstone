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
use Scheb\Tombstone\Model\Tombstone;

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
     * @var string|null
     */
    private $file;

    /**
     * @var TombstoneIndex
     */
    private $tombstoneIndex;

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
        $this->currentClass = $this->getNamespacedName($node);
    }

    private function visitMethodNode(ClassMethod $node): void
    {
        $methodName = $this->currentClass.($node->isStatic() ? '::' : '->').$node->name;
        $this->currentMethod[] = $methodName;
    }

    private function visitFunctionNode(Function_ $node): void
    {
        $this->currentMethod[] = $this->getNamespacedName($node);
    }

    /**
     * @param Class_|Function_ $node
     */
    private function getNamespacedName($node): string
    {
        if (!isset($node->namespacedName)) {
            $nodeName = isset($node->name) ? (string) $node->name : '<anonymous>';
            throw new \RuntimeException(sprintf('Node %s of type %s did not provide attribute namespacedName', $nodeName, \get_class($node)));
        }

        return (string) $node->namespacedName;
    }

    private function visitFunctionCallNode(FuncCall $node): void
    {
        if ($this->isTombstoneFunction($node)) {
            $line = $node->getLine();
            $methodName = $this->getCurrentMethodName();
            $params = $this->extractParameters($node);
            /** @psalm-suppress PossiblyNullArgument */
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

            return 1 === \count($nameParts) && 'tombstone' === $nameParts[0];
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
     * @return list<string|null>
     */
    private function extractParameters(FuncCall $node): array
    {
        $params = [];
        foreach ($node->args as $arg) {
            if ($arg->value instanceof String_) {
                $params[] = (string) $arg->value->value;
            } else {
                $params[] = null;
            }
        }

        return $params;
    }
}
