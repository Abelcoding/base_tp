<?php


namespace app\base;


use app\traits\InstanceMulti;
use think\Collection;
use think\Model;

class BaseModel extends Model
{
    use InstanceMulti;

    /**
     * @按条件获取一条数据
     * @param array $where
     * @access   public
     * @return   array
     * @throws
     */
    public function getRow($where = [])
    {
        $row = $this->_dealWhere($where)->find();

        if (empty($row)) {
            return [];
        }

        return $row->toArray();
    }

    /**
     * @通过主键获取数据
     * @param int $pk
     * @access   public
     * @return   array
     * @throws
     */
    public function pkRow($pk)
    {
        $row = $this->get($pk);

        if (empty($row)) {
            return [];
        }

        return $row->toArray();
    }

    /**
     * @获取多条数据
     * @param array $where
     * @access   public
     * @return   array
     * @throws
     */
    public function getRows($where = [])
    {
        $rows = $this->_dealWhere($where)->select();

        if (empty($rows)) {
            return [];
        }

        return Collection::make($rows)->toArray();
    }

    /**
     * @按页获取数据
     * @param array $where
     * @param int $page
     * @param int $size
     * @access public
     * @return array
     * @throws
     */
    public function getRowPages($where = [], $page = 1, $size = 15)
    {
        $db = $this->_dealWhere($where);

        $page <= 0 && $page = 1;
        ($size <= 0 || $size > 100) && $size = 20;

        $db->page($page, $size);

        $rows = $db->select();

        if (empty($rows)) {
            return [];
        }

        return Collection::make($rows)->toArray();
    }

    /**
     * @计算数据行数
     * @param array $where
     * @access   public
     * @return   int
     */
    public function countRow($where = [])
    {
        return $this->_dealWhere($where)->count();
    }

    /**
     * @添加
     * @param array $data
     * @access   public
     * @return   int
     */
    public function add($data)
    {
        return $this->insertGetId($data);
    }

    /**
     * @删除
     * @param array $where
     * @access   public
     * @return   int
     * @throws
     */
    public function del($where = [])
    {
        return $this->_dealWhere($where)->delete();
    }

    /**
     * @des      修改数据
     * @param array $where 条件
     * @param array $data 数据
     * @access   public
     * @return   mixed
     * @throws
     */
    public function edit($data, $where = [])
    {
        return $this->_dealWhere($where)->update($data);
    }

    /**
     * @des      条件生成
     * @param array $params //查询条件
     * @return   \think\Db\Query
     */
    private function _dealWhere($params = [])
    {
        $db = $this->db();
        if (isset($params['_fields_'])) {
            $db->field($params['_fields_']);
            unset($params['_fields_']);
        }

        if (isset($params['_limit_'])) {
            if (is_array($params['_limit_']) && isset($params['_limit_'][0]) && $params['_limit_'][1]) {
                $db->limit($params['_limit_'][0], $params['_limit_'][1]);
            } else {
                $db->limit($params['_limit_']);
            }
            unset($params['_limit_']);
        }

        if (isset($params['_order_'])) {
            $db->order($params['_order_']);
            unset($params['_order_']);
        }

        if (isset($params['_orderRaw_'])) {
            $db->orderRaw($params['_orderRaw_']);
            unset($params['_orderRaw_']);
        }

        if (isset($params['_between_']) && !empty($params['_between_'][0]) && !empty($params['_between_'][1])) {
            $db->whereBetween($params['_between_'][0], $params['_between_'][1]);
            unset($params['_between_']);
        }

        if (isset($params['_readMaster_'])) {
            $db->master();
            unset($params['_readMaster_']);
        }

        if (isset($params['_lock_'])) {
            $db->lock($params['_lock_']);
            unset($params['_lock_']);
        }

        if (isset($params['_group_'])) {
            $db->group($params['_group_']);
            unset($params['_group_']);
        }

        if (isset($params['_or_'])) {
            $or = $params['_or_'];
            $callback = function ($query) use ($or) {
                $query->whereOr($or);
            };
            $db->where($callback);
            unset($params['_or_']);
        }

        if (!empty($params)) {
            $db->where($params);
        }

        return $db;
    }
}