<?php

namespace Behat\MageExtension\ServiceContainer;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\MinkExtension\ServiceContainer\Driver\BrowserStackFactory;
use Behat\MinkExtension\ServiceContainer\Driver\DriverFactory;
use Behat\MinkExtension\ServiceContainer\Driver\GoutteFactory;
use Behat\MinkExtension\ServiceContainer\Driver\SahiFactory;
use Behat\MinkExtension\ServiceContainer\Driver\SauceLabsFactory;
use Behat\MinkExtension\ServiceContainer\Driver\Selenium2Factory;
use Behat\MinkExtension\ServiceContainer\Driver\SeleniumFactory;
use Behat\MinkExtension\ServiceContainer\Driver\ZombieFactory;
use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\ServiceContainer\Exception\ProcessingException;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class MageExtension implements ExtensionInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        // TODO: Implement process() method.
    }

    /**
     * Returns the extension config key.
     *
     * @return string
     */
    public function getConfigKey()
    {
        return "mage";
    }

    /**
     * Initializes other extensions.
     *
     * This method is called immediately after all extensions are activated but
     * before any extension `configure()` method is called. This allows extensions
     * to hook into the configuration of other extensions providing such an
     * extension point.
     *
     * @param ExtensionManager $extensionManager
     */
    public function initialize(ExtensionManager $extensionManager)
    {
        // TODO: Implement initialize() method.
    }

    /**
     * Setups configuration for the extension.
     *
     * @param ArrayNodeDefinition $builder
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $default_fixtures = array(
            'admin_user'             => '\Behat\MageExtension\Fixture\AdminUserFixtureFactory',
            'cart_product'           => '\Behat\MageExtension\Fixture\CartProductFixtureFactory',
            'customer'               => '\Behat\MageExtension\Fixture\CustomerFixtureFactory',
            'product'                => '\Behat\MageExtension\Fixture\ProductFixtureFactory',
            'configurable_product'   => '\Behat\MageExtension\Fixture\ConfigurableProductFixtureFactory',
            'attribute_set'          => '\Behat\MageExtension\Fixture\AttributeSetFixtureFactory',
            'attribute'              => '\Behat\MageExtension\Fixture\AttributeFixtureFactory',
            'attribute_with_options' => '\Behat\MageExtension\Fixture\AttributeWithOptionsFixtureFactory',
        );

        $builder
            ->beforeNormalization()
                ->always()
                ->then(function($v) use ($default_fixtures) {
                    if(array_key_exists('fixture_factories', $v)) {
                        $v['fixture_factories'] = array_merge($default_fixtures, $v['fixture_factories']);
                    } else {
                        $v['fixture_factories'] = $default_fixtures;
                    }
                    return $v;
                })
            ->end()
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('default_page_class')->defaultValue('\Behat\MageExtension\Page\MagePage')->end()
            ->scalarNode('category_page_class')->defaultValue('\Behat\MageExtension\Page\Category')->end()
            ->scalarNode('product_page_class')->defaultValue('\Behat\MageExtension\Page\Product')->end()
            ->arrayNode('custom_page_classes')
                ->prototype('scalar')
                ->end()
            ->end()
            ->arrayNode('fixture_factories')
            ->prototype('scalar');


    }

    /**
     * Loads extension services into temporary container.
     *
     * @param ContainerBuilder $container
     * @param array $config
     */
    public function load(ContainerBuilder $container, array $config)
    {

        $definition = new Definition('Behat\MageExtension\Context\Initializer\MageAwareInitializer', array(
            $config
        ));
        $definition->addTag(ContextExtension::INITIALIZER_TAG, array('priority' => 0));
        $container->setDefinition('mage.context_initializer', $definition);
    }
}