<?php
/**
 * Vainyl
 *
 * PHP Version 7
 *
 * @package   Doctrine-orm-bridge
 * @license   https://opensource.org/licenses/MIT MIT License
 * @link      https://vainyl.com
 */
declare(strict_types=1);

namespace Vainyl\Doctrine\ORM\Extension;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class DoctrineORMConfiguration
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineORMConfiguration implements ConfigurationInterface
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('doctrine');

        $rootNode
            ->children()
                ->arrayNode('orm')
                    ->children()
                        ->scalarNode('config')->defaultValue('yaml')->end()
                        ->scalarNode('file')->defaultValue('entitymap')->end()
                        ->scalarNode('extension')->defaultValue('.orm.yml')->end()
                        ->scalarNode('proxy')->defaultValue('Proxy')->end()
                        ->scalarNode('tmp_dir')->defaultValue('doctrine')->end()
                    ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}