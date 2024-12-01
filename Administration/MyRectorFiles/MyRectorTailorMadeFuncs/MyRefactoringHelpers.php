<?php

declare(strict_types=1);

namespace Administration\MyRectorFiles\MyRectorTailorMadeFuncs;

use PhpParser\Comment\Doc;
use PhpParser\Node;

trait MyRefactoringHelpers
{
    private function getFilePath(Node $node): ?string
    {
        $fileName = $node->getAttribute('fileName');

        if ($fileName !== null) {
            return $fileName;
        } else {
            // Log en advarsel kun for rodnoder (Namespace_ eller Class_)
            if ($node instanceof Node\Stmt\Namespace_ || $node instanceof Node\Stmt\Class_) {
                $this->logger->log("Noden mangler filstien.", 'WARNING');
            }
            return null;
        }
    }

    private function getExpectedNamespace(string $filePath): ?string
    {
        foreach ($this->projectPaths as $projectPath) {
            $normalizedProjectPath = str_replace('\\', '/', realpath($projectPath));
            $normalizedFilePath = str_replace('\\', '/', $filePath);

            if (strpos($normalizedFilePath, $normalizedProjectPath) === 0) {
                $relativePath = substr($normalizedFilePath, strlen($normalizedProjectPath));
                $namespaceParts = array_filter(explode('/', dirname($relativePath)));
                $namespace = 'Administration' . '\\' . implode('\\', $namespaceParts);
                return trim($namespace, '\\');
            }
        }

        $this->logger->log("Kunne ikke bestemme forventet namespace for fil: {$filePath}", 'WARNING');
        return null;
    }

    private function getExpectedClassName(string $filePath): ?string
    {
        return basename($filePath, '.php');
    }

    private function updateClassReferences(Node $node, array $classRenames): ?Node
    {
        if ($node instanceof Node\Name) {
            $name = $node->toString();
            if (isset($classRenames[$name])) {
                $newName = $classRenames[$name];
                $this->logger->log("Opdaterer klassereference fra {$name} til {$newName}");

                // Opret en ny Node\Name\FullyQualified instans
                return new Node\Name\FullyQualified($newName);
            }
        }

        // Rekursivt opdatere børnenoder
        foreach ($node->getSubNodeNames() as $subNodeName) {
            $subNode = $node->$subNodeName;

            if (is_array($subNode)) {
                foreach ($subNode as $key => $subSubNode) {
                    if ($subSubNode instanceof Node) {
                        $newSubNode = $this->updateClassReferences($subSubNode, $classRenames);
                        if ($newSubNode !== null) {
                            $node->{$subNodeName}[$key] = $newSubNode;
                        }
                    }
                }
            } elseif ($subNode instanceof Node) {
                $newSubNode = $this->updateClassReferences($subNode, $classRenames);
                if ($newSubNode !== null) {
                    $node->$subNodeName = $newSubNode;
                }
            }
        }

        return null;
    }

    private function updatePhpDoc(Node $node, array $classRenames): void
    {
        $docComment = $node->getDocComment();

        if ($docComment instanceof Doc) {
            $text = $docComment->getText();

            foreach ($classRenames as $oldName => $newName) {
                $pattern = '/\b' . preg_quote($oldName, '/') . '\b/';
                $text = preg_replace($pattern, $newName, $text);
            }

            $node->setDocComment(new Doc($text));
            $this->logger->log("Opdateret PHPDoc-kommentarer i node.");
        }
    }
}
