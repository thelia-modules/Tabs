<?php

declare(strict_types=1);

/*
 * This file is part of the Thelia package.
 * http://www.thelia.net
 *
 * (c) OpenStudio <info@thelia.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tabs\Twig;

use Symfony\Contracts\Translation\TranslatorInterface;
use Thelia\Api\Service\DataAccess\DataAccessService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class TabsExtension extends AbstractExtension
{
    public function __construct(
        private readonly DataAccessService $dataAccessService,
        protected ?TranslatorInterface $translator,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_tabs', [$this, 'getTabs']),
        ];
    }

    public function getTabs(array $params = []): array|object|null
    {
        return $this->dataAccessService->resources('/api/front/tabs', $params);
    }
}
