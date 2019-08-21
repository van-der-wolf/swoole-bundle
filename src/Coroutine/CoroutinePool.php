<?php

declare(strict_types=1);

namespace K911\Swoole\Coroutine;

use Assert\Assertion;
use Swoole\Coroutine\Channel;
use Swoole\Coroutine\Scheduler;
use Throwable;

/**
 * @internal
 */
final class CoroutinePool
{
    private $scheduler;
    private $coroutines;
    private $coroutinesCount;
    private $results = [];
    private $exceptions = [];
    private $resultsChannel;
    private $started = false;

    public function __construct(Scheduler $scheduler, Channel $resultsChannel, callable ...$coroutines)
    {
        $this->coroutines = $coroutines;
        $this->coroutinesCount = \count($coroutines);
        $this->resultsChannel = $resultsChannel;
        $this->scheduler = $scheduler;
    }

    public static function fromCoroutines(callable ...$coroutines): self
    {
        $count = \count($coroutines);

        return new self(new Scheduler(), new Channel($count), ...$coroutines);
    }

    /**
     * Blocks until all coroutines have been finished.
     */
    public function run(): array
    {
        Assertion::false($this->started, 'Single PoolExecutor cannot be run twice.');
        $this->started = true;

        foreach ($this->coroutines as $coroutine) {
            $this->scheduler->add($this->wrapCoroutine($this->resultsChannel, $coroutine));
        }

        $this->scheduler->add($this->makeGatherResults());
        $this->scheduler->start();

        // TODO: Create parent exception containing all child exceptions and throw it instead
        if (\count($this->exceptions) > 0) {
            throw $this->exceptions[0];
        }

        return $this->results;
    }

    private function makeGatherResults(): \Closure
    {
        return function (): void {
            while ($this->coroutinesCount > 0) {
                $result = $this->resultsChannel->pop();
                $outputName = $result instanceof Throwable ? 'exceptions' : 'results';
                $this->{$outputName}[] = $result;
                --$this->coroutinesCount;
            }
        };
    }

    private function wrapCoroutine(Channel $resultChannel, callable $coroutine): \Closure
    {
        return static function () use ($resultChannel, $coroutine): void {
            $result = null;

            try {
                $result = $coroutine() ?? true;
            } catch (\Throwable $exception) {
                $result = $exception;
            }
            $resultChannel->push($result);
        };
    }
}
