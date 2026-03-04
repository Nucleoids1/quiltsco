<?php

declare(strict_types=1);

$failures = [];

function assertSameValue($expected, $actual, string $message): void
{
    global $failures;

    if ($expected !== $actual) {
        $failures[] = sprintf(
            "%s\nExpected: %s\nActual:   %s",
            $message,
            var_export($expected, true),
            var_export($actual, true)
        );
    }
}

function assertTrue(bool $condition, string $message): void
{
    assertSameValue(true, $condition, $message);
}

function assertContains(string $needle, string $haystack, string $message): void
{
    assertTrue(strpos($haystack, $needle) !== false, $message);
}

function assertFalse(bool $condition, string $message): void
{
    assertSameValue(false, $condition, $message);
}

function assertThrows(callable $callable, string $expectedException, string $message): void
{
    global $failures;

    try {
        $callable();
    } catch (\Throwable $throwable) {
        if ($throwable instanceof $expectedException) {
            return;
        }

        $failures[] = sprintf(
            "%s\nExpected exception: %s\nActual exception:   %s (%s)",
            $message,
            $expectedException,
            get_class($throwable),
            $throwable->getMessage()
        );
        return;
    }

    $failures[] = sprintf(
        "%s\nExpected exception: %s\nActual exception:   none",
        $message,
        $expectedException
    );
}

function declaredFunctionNames(string $phpCode): array
{
    $tokens = token_get_all($phpCode);
    $names = [];
    $count = count($tokens);

    for ($i = 0; $i < $count; $i++) {
        $token = $tokens[$i];
        if (!is_array($token) || $token[0] !== T_FUNCTION) {
            continue;
        }

        for ($j = $i + 1; $j < $count; $j++) {
            $next = $tokens[$j];

            if (is_array($next) && $next[0] === T_STRING) {
                $names[] = $next[1];
                break;
            }

            if ($next === '(') {
                break;
            }
        }
    }

    return $names;
}

function assertFunctionFileContract(string $fileName, array $expectedFunctionNames): void
{
    $path = dirname(__DIR__) . '/functions/' . $fileName;

    assertTrue(is_file($path), "$fileName exists.");
    if (!is_file($path)) {
        return;
    }

    $lintOutput = [];
    $lintExitCode = 0;
    exec('php -l ' . escapeshellarg($path), $lintOutput, $lintExitCode);
    assertSameValue(0, $lintExitCode, "$fileName passes php -l syntax check.");

    $contents = file_get_contents($path);
    assertTrue($contents !== false, "$fileName is readable.");
    if ($contents === false) {
        return;
    }

    $declaredFunctions = declaredFunctionNames($contents);
    assertSameValue($expectedFunctionNames, $declaredFunctions, "$fileName declares expected named functions.");
}

function finishTest(string $label): void
{
    global $failures;

    if (!empty($failures)) {
        fwrite(STDERR, "$label failures:\n\n" . implode("\n\n", $failures) . "\n");
        exit(1);
    }

    echo "$label passed.\n";
}
