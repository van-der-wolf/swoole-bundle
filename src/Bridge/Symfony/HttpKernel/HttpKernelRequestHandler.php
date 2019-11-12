<?php

declare(strict_types=1);

namespace K911\Swoole\Bridge\Symfony\HttpKernel;

use K911\Swoole\Bridge\Symfony\HttpFoundation\RequestFactoryInterface;
use K911\Swoole\Bridge\Symfony\HttpFoundation\ResponseProcessorInterface;
use K911\Swoole\Server\RequestHandler\RequestHandlerInterface;
use K911\Swoole\Server\Runtime\BootableInterface;
use Psr\Log\LoggerInterface;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;
use Throwable;

final class HttpKernelRequestHandler implements RequestHandlerInterface, BootableInterface
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * @var ResponseProcessorInterface
     */
    private $responseProcessor;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        KernelInterface $kernel,
        RequestFactoryInterface $requestFactory,
        ResponseProcessorInterface $responseProcessor,
        LoggerInterface $logger
    ) {
        $this->kernel = $kernel;
        $this->requestFactory = $requestFactory;
        $this->responseProcessor = $responseProcessor;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function boot(array $runtimeConfiguration = []): void
    {
        $this->kernel->boot();
    }

    /**
     * {@inheritdoc}
     *
     * @param SwooleRequest  $request
     * @param SwooleResponse $response
     *
     * @throws Throwable
     */
    public function handle(SwooleRequest $request, SwooleResponse $response): void
    {
        try {
            $httpFoundationRequest = $this->requestFactory->make($request);
            $httpFoundationResponse = $this->kernel->handle($httpFoundationRequest);
            $this->responseProcessor->process($httpFoundationResponse, $response);

            if ($this->kernel instanceof TerminableInterface) {
                $this->kernel->terminate($httpFoundationRequest, $httpFoundationResponse);
            }
        } catch (Throwable $e) {
            $this->logger->critical(
                sprintf('Unable to process request: %s', $e->getMessage()),
                [
                    'error' => $e,
                ]
            );

            $this->kernel->shutdown();
            $this->kernel->boot();

            return;
        }
    }
}
