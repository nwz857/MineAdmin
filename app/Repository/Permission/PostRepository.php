<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

namespace App\Repository\Permission;

use App\Kernel\IRepository\AbstractRepository;
use App\Model\Permission\Post;
use Hyperf\Database\Model\Builder;

class PostRepository extends AbstractRepository
{
    /**
     * @var Post
     */
    public $model;

    public function assignModel()
    {
        $this->model = Post::class;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['name']) && filled($params['name'])) {
            $query->where('name', 'like', '%' . $params['name'] . '%');
        }
        if (isset($params['code']) && filled($params['code'])) {
            $query->where('code', $params['code']);
        }
        if (isset($params['status']) && filled($params['status'])) {
            $query->where('status', $params['status']);
        }
        if (isset($params['created_at']) && filled($params['created_at']) && is_array($params['created_at']) && count($params['created_at']) == 2) {
            $query->whereBetween(
                'created_at',
                [$params['created_at'][0] . ' 00:00:00', $params['created_at'][1] . ' 23:59:59']
            );
        }
        return $query;
    }
}