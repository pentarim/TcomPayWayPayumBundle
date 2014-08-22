<?php

namespace Locastic\TcomPaywayPayumBundle;

use Locastic\TcomPayWayPayum\Bridge\Symfony\TcomPayWayPaymentFactory;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Payum\Bundle\PayumBundle\DependencyInjection\PayumExtension;

class LocasticTcomPaywayPayumBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        /** @var $extension PayumExtension */
        $extension = $container->getExtension('payum');

        $extension->addPaymentFactory(new TcomPayWayPaymentFactory());
    }
}