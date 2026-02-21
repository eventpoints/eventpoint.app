<?php

declare(strict_types=1);

use App\Security\User\UserProvider;
use App\Service\EventStatusService;
use App\Service\Ticketing\StripeCheckoutService;
use App\Service\Ticketing\StripeConnectService;
use App\Service\Ticketing\RefundService;
use App\Service\Ticketing\WebhookHandler;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Workflow\WorkflowInterface;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $parameters = $containerConfigurator->parameters();
    $parameters->set('CLOUDFLARE_TURNSTILE_PRIVATE_KEY', '%env(CLOUDFLARE_TURNSTILE_PRIVATE_KEY)%');
    $parameters->set('MAPBOX_TOKEN', '%env(MAPBOX_TOKEN)%');

    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->bind('$stripeSecretKey', '%env(STRIPE_SECRET_KEY)%')
        ->bind('$stripePublishableKey', '%env(STRIPE_PUBLISHABLE_KEY)%')
        ->bind('$stripeWebhookSecret', '%env(STRIPE_WEBHOOK_SECRET)%');

    $services->load('App\\', __DIR__ . '/../src/')
        ->exclude([
            __DIR__ . '/../src/DependencyInjection/',
            __DIR__ . '/../src/Entity/',
            __DIR__ . '/../src/Kernel.php',
        ]);

    $services->set(UserProvider::class)
        ->class(UserProvider::class);

    $services->set(EventStatusService::class)
        ->arg('$eventStatusStateMachine', service('state_machine.event_status'));
};
