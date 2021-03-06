<?php
namespace Locastic\TcomPayWayPayumBundle\Tests\DependencyInjection\Factory\Gateway;

use Locastic\TcomPayWayPayumBundle\DependencyInjection\Factory\Gateway\TcomOnsiteGatewayFactory;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class TcomOnsiteGatewayFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldBeSubClassOfAbstractGatewayFactory()
    {
        $rc = new \ReflectionClass(
            'Locastic\TcomPayWayPayumBundle\DependencyInjection\Factory\Gateway\TcomOnsiteGatewayFactory'
        );

        $this->assertTrue(
            $rc->isSubclassOf('Payum\Bundle\PayumBundle\DependencyInjection\Factory\Gateway\AbstractGatewayFactory')
        );
    }

    /**
     * @test
     */
    public function shouldBeSubClassOfTcomOffsiteGatewayFactory()
    {
        $rc = new \ReflectionClass(
            'Locastic\TcomPayWayPayumBundle\DependencyInjection\Factory\Gateway\TcomOnsiteGatewayFactory'
        );

        $this->assertTrue(
            $rc->isSubclassOf(
                'Locastic\TcomPayWayPayumBundle\DependencyInjection\Factory\Gateway\TcomOffsiteGatewayFactory'
            )
        );
    }

    /**
     * @test
     */
    public function couldBeConstructedWithoutAnyArguments()
    {
        new TcomOnsiteGatewayFactory;
    }

    /**
     * @test
     */
    public function shouldAllowGetName()
    {
        $factory = new TcomOnsiteGatewayFactory;

        $this->assertEquals('tcompayway_onsite', $factory->getName());
    }


    /**
     * @test
     */
    public function shouldAllowAddConfigurationWithoutAuthorizationTypeAndSandbox()
    {
        $factory = new TcomOnsiteGatewayFactory;

        $tb = new TreeBuilder();
        $rootNode = $tb->root('foo');
        $factory->addConfiguration($rootNode);
        $processor = new Processor();
        $config = $processor->process(
            $tb->buildTree(),
            array(
                array(
                    'shop_id' => 'theShopId',
                    'secret_key' => 'aSecretKey',
                ),
            )
        );

        $this->assertArrayHasKey('shop_id', $config);
        $this->assertEquals('theShopId', $config['shop_id']);
        $this->assertArrayHasKey('secret_key', $config);
        $this->assertEquals('aSecretKey', $config['secret_key']);
        $this->assertArrayHasKey('sandbox', $config);
        $this->assertTrue($config['sandbox']);

        //come from abstract gateway factory
        $this->assertArrayHasKey('actions', $config);
        $this->assertArrayHasKey('apis', $config);
        $this->assertArrayHasKey('extensions', $config);
    }

    /**
     * @test
     */
    public function shouldAllowAddConfigurationWithAuthorizationTypeAndSandbox()
    {
        $factory = new TcomOnsiteGatewayFactory;

        $tb = new TreeBuilder();
        $rootNode = $tb->root('foo');
        $factory->addConfiguration($rootNode);
        $processor = new Processor();
        $config = $processor->process(
            $tb->buildTree(),
            array(
                array(
                    'shop_id' => 'theShopId',
                    'secret_key' => 'aSecretKey',
                    'authorization_type' => 1,
                    'sandbox' => false,
                ),
            )
        );

        $this->assertArrayHasKey('shop_id', $config);
        $this->assertEquals('theShopId', $config['shop_id']);

        $this->assertArrayHasKey('secret_key', $config);
        $this->assertEquals('aSecretKey', $config['secret_key']);

        $this->assertArrayHasKey('authorization_type', $config);
        $this->assertEquals(1, $config['authorization_type']);

        $this->assertArrayHasKey('sandbox', $config);
        $this->assertFalse($config['sandbox']);

        //come from abstract gateway factory
        $this->assertArrayHasKey('actions', $config);
        $this->assertArrayHasKey('apis', $config);
        $this->assertArrayHasKey('extensions', $config);
    }

    /**
     * @test
     *
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage The child node "shop_id" at path "foo" must be configured.
     */
    public function thrownIfShopIdOptionNotSet()
    {
        $factory = new TcomOnsiteGatewayFactory;

        $tb = new TreeBuilder();
        $rootNode = $tb->root('foo');
        $factory->addConfiguration($rootNode);

        $processor = new Processor();
        $processor->process($tb->buildTree(), array(array()));
    }

    /**
     * @test
     *
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage The child node "secret_key" at path "foo" must be configured.
     */
    public function thrownIfSecretKeyOptionNotSet()
    {
        $factory = new TcomOnsiteGatewayFactory;

        $tb = new TreeBuilder();
        $rootNode = $tb->root('foo');
        $factory->addConfiguration($rootNode);

        $processor = new Processor();
        $processor->process(
            $tb->buildTree(),
            array(
                array(
                    'shop_id' => 123,
                ),
            )
        );
    }

    /**
     * @test
     */
    public function shouldAllowCreatePaymentAndReturnItsId()
    {
        $factory = new TcomOnsiteGatewayFactory;

        $container = new ContainerBuilder();
        $paymentId = $factory->create(
            $container,
            'aPaymentName',
            array(
                'shop_id' => 123,
                'secret_key' => 'aSecretKey',
                'sandbox' => true,
                'actions' => array(),
                'apis' => array(),
                'extensions' => array(),
            )
        );

        $this->assertEquals('payum.tcompayway_onsite.aPaymentName.payment', $paymentId);
        $this->assertTrue($container->hasDefinition($paymentId));
        $payment = $container->getDefinition($paymentId);

        //guard
        $this->assertNotEmpty($payment->getFactoryMethod());
        $this->assertNotEmpty($payment->getFactoryService());
        $this->assertNotEmpty($payment->getArguments());
        $config = $payment->getArgument(0);

        $this->assertEquals('aPaymentName', $config['payum.payment_name']);
    }

    /**
     * @test
     */
    public function shouldLoadFactoryAndTemplateSettings()
    {
        $factory = new TcomOnsiteGatewayFactory;

        $container = new ContainerBuilder;
        $factory->load($container);

        $this->assertTrue($container->hasDefinition('payum.tcompayway_onsite.factory'));
        $factoryService = $container->getDefinition('payum.tcompayway_onsite.factory');

        $this->assertEquals('Locastic\TcomPayWayPayumBundle\OnsiteGatewayFactory', $factoryService->getClass());
        $this->assertEquals(
            array(array('name' => 'tcompayway_onsite', 'human_name' => 'Tcompayway Onsite',)),
            $factoryService->getTag('payum.gateway_factory')
        );

        $factoryConfig = $factoryService->getArgument(0);

        $this->assertEquals('tcompayway_onsite', $factoryConfig['payum.factory_name']);
        $this->assertArrayHasKey('buzz.client', $factoryConfig);
        $this->assertArrayHasKey('twig.env', $factoryConfig);
        $this->assertArrayHasKey('payum.template.layout', $factoryConfig);
        $this->assertArrayHasKey('payum.template.obtain_credit_card', $factoryConfig);
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $factoryService->getArgument(1));
        $this->assertEquals('payum.gateway_factory', (string)$factoryService->getArgument(1));

        $this->assertEquals('LocasticTcomPayWayPayumBundle:TcomPayWay/Onsite:capture.html.twig', $container->getParameter('payum.tcompayway_onsite.template.capture'));
        $this->assertEquals('LocasticTcomPayWayPayumBundle:TcomPayWay/Onsite:obtainCreditCard.html.twig', $container->getParameter('payum.tcompayway_onsite.template.obtain_credit_card'));
    }

    /**
     * @test
     */
    public function shouldCallParentsCreateMethod()
    {
        $factory = new TcomOnsiteGatewayFactory;

        $container = new ContainerBuilder;
        $paymentId = $factory->create(
            $container,
            'aPaymentName',
            array(
                'shop_id' => 123,
                'secret_key' => 'aSecretKey',
                'sandbox' => true,
                'actions' => array('payum.action.foo'),
                'apis' => array('payum.api.bar'),
                'extensions' => array('payum.extension.ololo'),
            )
        );

        $this->assertDefinitionContainsMethodCall(
            $container->getDefinition($paymentId),
            'addAction',
            new Reference('payum.action.foo')
        );

        $this->assertDefinitionContainsMethodCall(
            $container->getDefinition($paymentId),
            'addApi',
            new Reference('payum.api.bar')
        );

        $this->assertDefinitionContainsMethodCall(
            $container->getDefinition($paymentId),
            'addExtension',
            new Reference('payum.extension.ololo')
        );
    }

    protected function assertDefinitionContainsMethodCall(
        Definition $serviceDefinition,
        $expectedMethod,
        $expectedFirstArgument
    ) {
        foreach ($serviceDefinition->getMethodCalls() as $methodCall) {
            if ($expectedMethod == $methodCall[0] && $expectedFirstArgument == $methodCall[1][0]) {
                return;
            }
        }
        $this->fail(
            sprintf(
                'Failed assert that service (Class: %s) has method %s been called with first argument %s',
                $serviceDefinition->getClass(),
                $expectedMethod,
                $expectedFirstArgument
            )
        );
    }
}
