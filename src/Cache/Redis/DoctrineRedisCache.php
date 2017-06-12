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

namespace Vainyl\Doctrine\ORM\Cache\Redis;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\CacheProvider as DoctrineCacheProvider;
use Vainyl\Redis\RedisInterface;

/**
 * Class DoctrineRedisCache
 *
 * @author Taras P. Girnyk <taras.p.gyrnik@gmail.com>
 */
class DoctrineRedisCache extends DoctrineCacheProvider
{
    private $redis;

    /**
     * DoctrineRedisCache constructor.
     *
     * @param RedisInterface $redis
     */
    public function __construct(RedisInterface $redis)
    {
        $this->redis = $redis;
    }

    /**
     * @inheritDoc
     */
    protected function doFetch($id)
    {
        if (null === ($result = $this->redis->get($id))) {
            return false;
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    protected function doContains($id)
    {
        return $this->redis->has($id);
    }

    /**
     * @inheritDoc
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        if ($lifeTime > 0) {
            return $this->redis->setEx($id, $data, $lifeTime);
        }

        return $this->redis->setNx($id, $data);
    }

    /**
     * @inheritDoc
     */
    protected function doDelete($id)
    {
        return $this->redis->delete($id);
    }

    /**
     * @inheritDoc
     */
    protected function doFlush()
    {
        return $this->redis->flush();
    }

    /**
     * @inheritDoc
     */
    protected function doGetStats()
    {
        $info = $this->redis->info();

        return [
            Cache::STATS_HITS             => $info['keyspace_hits'],
            Cache::STATS_MISSES           => $info['keyspace_misses'],
            Cache::STATS_UPTIME           => $info['uptime_in_seconds'],
            Cache::STATS_MEMORY_USAGE     => $info['used_memory'],
            Cache::STATS_MEMORY_AVAILABLE => false,
        ];
    }
}
