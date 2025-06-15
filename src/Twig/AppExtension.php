<?php

declare(strict_types=1);

namespace App\Twig;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class AppExtension extends AbstractExtension
{
    /**
     * @param TranslatorInterface $translator
     * @param RequestStack $requestStack
     * @param UrlGeneratorInterface $router
     */
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly RequestStack $requestStack,
        private readonly UrlGeneratorInterface $router,
    ) {}

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('append_param', [$this, 'appendParamToRoute']),
        ];
    }

    /**
     * @param string $paramName
     * @param mixed $paramValue
     * @param array $paramsFromRequest
     * @return string
     * @throws \RuntimeException
     */
    public function appendParamToRoute(string $paramName, mixed $paramValue, array $paramsFromRequest = []): string
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request instanceof Request) {
            throw new \RuntimeException($this->translator->trans('error.request_not_found'));
        }

        $params = [];
        foreach ($paramsFromRequest as $key) {
            $value = $request->get($key);
            if ($value !== null && $value !== '') {
                $params[$key] = $value;
            }
        }

        $params[$paramName] = $paramValue;

        return $this->router->generate($request->get('_route'), $params, UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
