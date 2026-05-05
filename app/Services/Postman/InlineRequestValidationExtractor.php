<?php

namespace App\Services\Postman;

use ReflectionMethod;

class InlineRequestValidationExtractor
{
    /**
     * @return array<string, array{rules: array<int, string>, source: string}>
     */
    public function forAction(string $controllerClass, string $methodName): array
    {
        if (! class_exists($controllerClass) || ! method_exists($controllerClass, $methodName)) {
            return [];
        }

        $reflection = new ReflectionMethod($controllerClass, $methodName);
        $fileName = $reflection->getFileName();

        if (! is_string($fileName) || $fileName === '' || ! is_file($fileName)) {
            return [];
        }

        $source = $this->readMethodSource($reflection, $fileName);
        $validationArray = $this->extractValidationArray($source);

        if ($validationArray === null) {
            return [];
        }

        $fields = [];

        foreach ($this->parseTopLevelEntries($validationArray) as $field => $rulesSource) {
            $fields[$field] = [
                'rules' => $this->extractQuotedStrings($rulesSource),
                'source' => $rulesSource,
            ];
        }

        return $fields;
    }

    private function readMethodSource(ReflectionMethod $reflection, string $fileName): string
    {
        $lines = file($fileName);

        if (! is_array($lines)) {
            return '';
        }

        return implode('', array_slice(
            $lines,
            $reflection->getStartLine() - 1,
            $reflection->getEndLine() - $reflection->getStartLine() + 1
        ));
    }

    private function extractValidationArray(string $source): ?string
    {
        $offset = strpos($source, '->validate(');

        if ($offset === false) {
            return null;
        }

        $start = strpos($source, '[', $offset);

        if ($start === false) {
            return null;
        }

        $end = $this->findMatchingDelimiter($source, $start, '[', ']');

        if ($end === null) {
            return null;
        }

        return substr($source, $start, $end - $start + 1);
    }

    private function findMatchingDelimiter(string $source, int $start, string $open, string $close): ?int
    {
        $depth = 0;
        $length = strlen($source);
        $quote = null;

        for ($index = $start; $index < $length; $index++) {
            $char = $source[$index];

            if ($quote !== null) {
                if ($char === '\\') {
                    $index++;

                    continue;
                }

                if ($char === $quote) {
                    $quote = null;
                }

                continue;
            }

            if ($char === '\'' || $char === '"') {
                $quote = $char;

                continue;
            }

            if ($char === $open) {
                $depth++;

                continue;
            }

            if ($char === $close) {
                $depth--;

                if ($depth === 0) {
                    return $index;
                }
            }
        }

        return null;
    }

    /**
     * @return array<string, string>
     */
    private function parseTopLevelEntries(string $arraySource): array
    {
        $inner = trim(substr(trim($arraySource), 1, -1));
        $entries = [];
        $length = strlen($inner);
        $offset = 0;

        while ($offset < $length) {
            $offset = $this->skipIgnorableCharacters($inner, $offset);

            if ($offset >= $length) {
                break;
            }

            $key = $this->readQuotedString($inner, $offset);

            if ($key === null) {
                break;
            }

            $offset = $this->skipIgnorableCharacters($inner, $offset);

            if (substr($inner, $offset, 2) !== '=>') {
                break;
            }

            $offset += 2;
            $offset = $this->skipIgnorableCharacters($inner, $offset);

            [$value, $offset] = $this->readValueExpression($inner, $offset);
            $entries[$key] = trim($value);

            $offset = $this->skipIgnorableCharacters($inner, $offset);

            if (($inner[$offset] ?? null) === ',') {
                $offset++;
            }
        }

        return $entries;
    }

    private function skipIgnorableCharacters(string $source, int $offset): int
    {
        $length = strlen($source);

        while ($offset < $length) {
            $char = $source[$offset];

            if ($char === ' ' || $char === "\n" || $char === "\r" || $char === "\t" || $char === ',') {
                $offset++;

                continue;
            }

            break;
        }

        return $offset;
    }

    private function readQuotedString(string $source, int &$offset): ?string
    {
        $quote = $source[$offset] ?? null;

        if ($quote !== '\'' && $quote !== '"') {
            return null;
        }

        $offset++;
        $length = strlen($source);
        $value = '';

        while ($offset < $length) {
            $char = $source[$offset];

            if ($char === '\\') {
                $next = $source[$offset + 1] ?? '';
                $value .= $next;
                $offset += 2;

                continue;
            }

            if ($char === $quote) {
                $offset++;

                return $value;
            }

            $value .= $char;
            $offset++;
        }

        return null;
    }

    /**
     * @return array{0: string, 1: int}
     */
    private function readValueExpression(string $source, int $offset): array
    {
        $length = strlen($source);
        $start = $offset;
        $quote = null;
        $squareDepth = 0;
        $parenDepth = 0;
        $braceDepth = 0;

        while ($offset < $length) {
            $char = $source[$offset];

            if ($quote !== null) {
                if ($char === '\\') {
                    $offset += 2;

                    continue;
                }

                if ($char === $quote) {
                    $quote = null;
                }

                $offset++;

                continue;
            }

            if ($char === '\'' || $char === '"') {
                $quote = $char;
                $offset++;

                continue;
            }

            if ($char === '[') {
                $squareDepth++;
                $offset++;

                continue;
            }

            if ($char === ']') {
                $squareDepth--;
                $offset++;

                continue;
            }

            if ($char === '(') {
                $parenDepth++;
                $offset++;

                continue;
            }

            if ($char === ')') {
                $parenDepth--;
                $offset++;

                continue;
            }

            if ($char === '{') {
                $braceDepth++;
                $offset++;

                continue;
            }

            if ($char === '}') {
                $braceDepth--;
                $offset++;

                continue;
            }

            if ($char === ',' && $squareDepth === 0 && $parenDepth === 0 && $braceDepth === 0) {
                break;
            }

            $offset++;
        }

        return [substr($source, $start, $offset - $start), $offset];
    }

    /**
     * @return array<int, string>
     */
    private function extractQuotedStrings(string $source): array
    {
        $values = [];
        $length = strlen($source);
        $offset = 0;

        while ($offset < $length) {
            $char = $source[$offset];

            if ($char !== '\'' && $char !== '"') {
                $offset++;

                continue;
            }

            $quote = $char;
            $offset++;
            $value = '';

            while ($offset < $length) {
                $current = $source[$offset];

                if ($current === '\\') {
                    $next = $source[$offset + 1] ?? '';
                    $value .= $next;
                    $offset += 2;

                    continue;
                }

                if ($current === $quote) {
                    $offset++;
                    $values[] = $value;

                    break;
                }

                $value .= $current;
                $offset++;
            }
        }

        return $values;
    }
}
