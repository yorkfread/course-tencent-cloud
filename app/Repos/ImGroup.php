<?php

namespace App\Repos;

use App\Library\Paginator\Adapter\QueryBuilder as PagerQueryBuilder;
use App\Models\ImGroup as ImGroupModel;
use App\Models\ImGroupMessage as ImGroupMessageModel;
use App\Models\ImGroupUser as ImGroupUserModel;
use App\Models\ImUser as ImUserModel;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class ImGroup extends Repository
{

    public function paginate($where = [], $sort = 'latest', $page = 1, $limit = 15)
    {
        $builder = $this->modelsManager->createBuilder();

        $builder->from(ImGroupModel::class);

        $builder->where('1 = 1');

        if (!empty($where['id'])) {
            $builder->andWhere('id = :id:', ['id' => $where['id']]);
        }

        if (!empty($where['name'])) {
            $builder->andWhere('name LIKE :name:', ['name' => "%{$where['name']}%"]);
        }

        if (!empty($where['course_id'])) {
            $builder->andWhere('course_id = :course_id:', ['course_id' => $where['course_id']]);
        }

        if (!empty($where['owner_id'])) {
            $builder->andWhere('owner_id = :owner_id:', ['owner_id' => $where['owner_id']]);
        }

        if (isset($where['published'])) {
            $builder->andWhere('published = :published:', ['published' => $where['published']]);
        }

        if (isset($where['deleted'])) {
            $builder->andWhere('deleted = :deleted:', ['deleted' => $where['deleted']]);
        }

        switch ($sort) {
            case 'popular':
                $orderBy = 'user_count DESC';
                break;
            default:
                $orderBy = 'id DESC';
                break;
        }

        $builder->orderBy($orderBy);

        $pager = new PagerQueryBuilder([
            'builder' => $builder,
            'page' => $page,
            'limit' => $limit,
        ]);

        return $pager->paginate();
    }

    /**
     * @param int $id
     * @return ImGroupModel|Model|bool
     */
    public function findById($id)
    {
        return ImGroupModel::findFirst($id);
    }

    /**
     * @param array $ids
     * @param string|array $columns
     * @return ResultsetInterface|Resultset|ImGroupModel[]
     */
    public function findByIds($ids, $columns = '*')
    {
        return ImGroupModel::query()
            ->columns($columns)
            ->inWhere('id', $ids)
            ->execute();
    }

    /**
     * @param int $groupId
     * @return ResultsetInterface|Resultset|ImUserModel[]
     */
    public function findUsers($groupId)
    {
        return $this->modelsManager->createBuilder()
            ->columns('u.*')
            ->addFrom(ImUserModel::class, 'u')
            ->join(ImGroupUserModel::class, 'u.id = gu.user_id', 'gu')
            ->where('gu.group_id = :group_id:', ['group_id' => $groupId])
            ->getQuery()->execute();
    }

    public function countGroups()
    {
        return (int)ImGroupModel::count(['conditions' => 'published = 1']);
    }

    public function countUsers($groupId)
    {
        return (int)ImGroupUserModel::count([
            'conditions' => 'group_id = :group_id:',
            'bind' => ['group_id' => $groupId],
        ]);
    }

    public function countMessages($groupId)
    {
        return (int)ImGroupMessageModel::count([
            'conditions' => 'group_id = :group_id: AND published = 1',
            'bind' => ['group_id' => $groupId],
        ]);
    }

}
