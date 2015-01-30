TcomPayWayPayumBundle
=====================

## Prerequisites

For TcomPayPayumBundle to work you have to install Payum. You can read more about payum at their official website
[payum](http://payum.org/)

## Installation

For installation of TcomPaywayPayumBundle just include it in your composer.json file and run composer's update
command.

Afterwards register the new bundle in your AppKernel

    // app/AppKernel.php
    public function registerBundles()
    {
        $bundles = array(
            ...
            new \Locastic\TcomPaywayPayumBundle\LocasticTcomPaywayPayumBundle(),
            ...
        );


Next step is to fill your shop details. Parameters secure3d_template, prepare_template & done_template are exposed
so you can replace them with your own templates.

    # app/config/config.yml
    payum:
        contexts:
            tcompayway:
                tcompayway:
                    shop_id:              YOUR_SHOP_ID
                    shop_username:        YOUR_SHOP_USERNAME
                    shop_password:        YOUR_SHOP_PASSWORD
                    shop_secret_key:      YOUR_SHOP_SECRET
                    secure3d_template:     LocasticWebBundle:Frontend/Payum:secure3d.html.twig
                    prepare_template:      LocasticWebBundle:Frontend/Payum:prepare.html.twig
                    done_template:      LocasticWebBundle:Frontend/Payum:done.html.twig
                    actions:
                        - locastic.tcompayway_payum.action.capture
                        - locastic.tcompayway_payum.action.status


    # app/config/config.yml
    twig:
        paths:
            %kernel.root_dir%/../vendor/payum/core/Payum/Core/Resources/views: PayumCore

## Sylius installation

Apart from basic installation for integration with sylius you have to add this piece of code.

    # app/config/config.yml
    services:
        payum.tcompayway.action.capture_payment:
            class: Locastic\TcomPaywayPayumBundle\Bridge\Sylius\CapturePaymentAction
            tags:
                - { name: payum.action, factory: tcompayway, prepend: true }

