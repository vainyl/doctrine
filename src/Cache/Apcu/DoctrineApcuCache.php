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

namespace Vainyl\Doctrine\ORM\Cache\Apcu;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\CacheProvider as DoctrineCacheProvider;

/**
 * Class DoctrineApcuCache
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineApcuCache extends DoctrineCacheProvider
{
    /**
     * @inheritDoc
     */
    protected function doFetch($id)
    {
        return apcu_fetch($id);
    }

    /**
     * @inheritDoc
     */
    protected function doContains($id)
    {
        return apcu_exists($id);
    }

    /**
     * @inheritDoc
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        return apcu_store($id, $data, $lifeTime);
    }

    /**
     * @inheritDoc
     */
    protected function doDelete($id)
    {
        return apcu_delete($id) || ! apcu_exists($id);
    }

    /**
     * @inheritDoc
     */
    protected function doFlush()
    {
        return apcu_clear_cache();
    }

    /**
     * @inheritDoc
     */
    protected function doGetStats()
    {
        $info = apcu_sma_info();

        return [
            Cache::STATS_HITS   => $info['keyspace_hits'],
            Cache::STATS_MISSES => $info['keyspace_misses'],
            Cache::STATS_UPTIME => $info['uptime_in_seconds'],
            Cache::STATS_MEMORY_USAGE      => $info['used_memory'],
            Cache::STATS_MEMORY_AVAILABLE  => false
        ];
    }
}
