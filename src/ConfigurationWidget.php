<?php

declare(strict_types=1);

namespace BobdenOtter\ConfigurationNotices;

use Bolt\Extension\BaseExtension;
use Bolt\Widget\BaseWidget;
use Bolt\Widget\Injector\AdditionalTarget;
use Bolt\Widget\Injector\RequestZone;
use Bolt\Widget\RequestAwareInterface;
use Bolt\Widget\StopwatchAwareInterface;
use Bolt\Widget\StopwatchTrait;
use Bolt\Widget\TwigAwareInterface;

class ConfigurationWidget extends BaseWidget implements TwigAwareInterface, RequestAwareInterface, StopwatchAwareInterface
{
    use StopwatchTrait;

    protected ?string $name = 'Configuration Notices Widget';
    protected string $target = AdditionalTarget::WIDGET_BACK_DASHBOARD_ASIDE_TOP;
    protected ?int $priority = 100;
    protected ?string $template = '@configuration-notices-widget/configuration.html.twig';
    protected ?string $zone = RequestZone::BACKEND;

    protected function run(array $params = []): ?string
    {
        /** @var BaseExtension $extension */
        $extension = $this->getExtension();

        $checks = new Checks($extension);
        $results = $checks->getResults();

        // This is the case when getResults failed executing
        if ($results === null) {
            return null;
        }

        // This is the case when getResults has no outstanding issues
        if (empty($results['notices'])) {
            return '';
        }

        $context = [
            'results' => $results,
        ];

        return parent::run($context);
    }
}
