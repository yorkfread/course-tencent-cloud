<?php

namespace App\Caches;

use App\Models\ImGroupMessage as ImGroupMessageModel;
use App\Models\User as UserModel;
use App\Repos\User as UserRepo;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class ImGroupActiveUserList extends Cache
{

    protected $lifetime = 1 * 86400;

    public function getLifetime()
    {
        return $this->lifetime;
    }

    public function getKey($id = null)
    {
        return "im_group_active_user_list:{$id}";
    }

    public function getContent($id = null)
    {
        $users = $this->findUsers($id);

        if (!$users) return [];

        $result = [];

        foreach ($users as $user) {
            $result[] = [
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => $user->avatar,
                'title' => $user->title,
                'about' => $user->about,
                'vip' => $user->vip,
            ];
        }

        return $result;
    }

    /**
     * @param int $groupId
     * @param int $days
     * @param int $limit
     * @return ResultsetInterface|Resultset|UserModel[]
     */
    protected function findUsers($groupId, $days = 7, $limit = 5)
    {
        $result = [];

        $startTime = strtotime("-{$days} days");
        $endTime = time();

        $rows = ImGroupMessageModel::query()
            ->columns(['sender_id', 'total_count' => 'count(sender_id)'])
            ->groupBy('sender_id')
            ->orderBy('total_count DESC')
            ->where('group_id = :group_id:', ['group_id' => $groupId])
            ->betweenWhere('create_time', $startTime, $endTime)
            ->limit($limit)
            ->execute();

        if ($rows->count() > 0) {

            $ids = kg_array_column($rows->toArray(), 'sender_id');

            $userRepo = new UserRepo();

            $result = $userRepo->findByIds($ids);
        }

        return $result;
    }

}
