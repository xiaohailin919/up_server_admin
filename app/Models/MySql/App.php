<?php

namespace App\Models\MySql;

use App\Helpers\Format;
use App\Models\MySqlBase;

class App extends MySqlBase
{
    protected $table = 'app';
    const TABLE = 'app';

    const STATUS_DELETED = 0;
    const STATUS_LOCKED = 1;
    const STATUS_PENDING = 2;
    const STATUS_RUNNING = 3;

    const PRIVATE_STATUS_DELETED = 0;
    const PRIVATE_STATUS_LOCKED  = 1;
    const PRIVATE_STATUS_PENDING = 2;
    const PRIVATE_STATUS_RUNNING = 3;

    public function getCreateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    public function getUpdateTimeAttribute($value) {
        return Format::millisecondTimestamp($value);
    }

    private static $idNameMap;
    private static $idUuidMap;
    
    /**
     * 通过app表的主键ID获取UUID
     * @param $id
     * @return string
     */
    public static function getUuid($id): string
    {
        if (self::$idUuidMap == null) {
            $data = self::query()->get(['id', 'uuid'])->toArray();
            self::$idUuidMap = array_column($data, 'uuid', 'id');
        }
        return self::$idUuidMap[$id] ?? '';
    }
    
    public static function getName($id)
    {
        if (self::$idNameMap == null) {
            $data = self::query()->get(['id', 'name'])->toArray();
            self::$idNameMap = array_column($data, 'name', 'id');
        }
        return self::$idNameMap[$id] ?? '';
    }
    
    /**
     * 获取状态码映射的名称
     * @param int $status
     * @return string
     */
    public static function getStatusName($status)
    {
        $map = self::getStatusMap(true);
        return $map[$status];
    }
    
    /**
     * 获取所有状态码映射配置
     * @param boolean $deleted
     * @return array
     */
    public static function getStatusMap($deleted = false)
    {
        $map = [
            self::STATUS_DELETED => 'Deleted',
            self::STATUS_LOCKED  => 'Locked',
            self::STATUS_PENDING => 'Pending',
            self::STATUS_RUNNING => 'Running',
        ];
        if(!$deleted){
            unset($map[self::STATUS_DELETED]);
        }
        return $map;
    }

    /**
     * 根据id修改 private status
     * @param $id
     * @param $status
     */
    public function updatePrivateStatus($id, $status)
    {
        $query = $this->queryBuilder();

        $data = [
            'private_status' => $status,
            'update_time' => time(),
        ];

        $query->where('id', $id)->update($data);
    }

    /**
     * 获取全平台的分类 Map
     */
    public static function getAllPlatformCategoryMap(): array {
        $androidCategoryList = self::getAndroidCategoryList();
        $iosCategoryList = self::getIosCategoryList();
        $allCategoryKeys = array_keys($androidCategoryList);
        foreach (array_keys($iosCategoryList) as $key) {
            if (!in_array($key, $allCategoryKeys, true)) {
                $allCategoryKeys[] = $key;
            }
        }
        /* 合并二者 */
        $categoryMap = [];
        foreach ($allCategoryKeys as $key) {
            /* 只有其中一个系统平台有的分类，如安卓下的 Family */
            if (!array_key_exists($key, $androidCategoryList)) {
                $categoryMap[$key] = $iosCategoryList[$key];
                continue;
            }
            if (!array_key_exists($key, $iosCategoryList)) {
                $categoryMap[$key] = $androidCategoryList[$key];
                continue;
            }
            /* 两个系统平台都有的分类，对二者进行合并 */
            $newCategory = $androidCategoryList[$key];
            foreach ($iosCategoryList[$key] as $categoryName) {
                if (!in_array($categoryName, $newCategory, true)) {
                    $newCategory[] = $categoryName;
                }
            }
            /* 合并完之后排序 */
            sort($newCategory);
            $categoryMap[$key] = $newCategory;
        }
        /* 生成结果 */
        $res = [];
        foreach ($categoryMap as $parent => $children) {
            $tmp = [
                'value' => base64_encode($parent),
                'label' => array_key_exists($parent, $androidCategoryList) ?
                    __('category_android.' . $parent) : __('category_ios.' . $parent),
                'children' => [],
            ];
            foreach ($children as $child) {
                $tmp['children'][] = [
                    'value' => base64_encode($parent . '___' .$child),
                    'label' => in_array($child, $androidCategoryList[$parent], true) ?
                        __('category_android.' . $child) : __('category_ios.' . $child),
                ];
            }
            $res[] = $tmp;
        }
        return $res;
    }

    /**
     * Android类别列表
     * @return array
     */
    public static function getAndroidCategoryList(): array
    {
        return [
            'App' =>
                [
                    'Daydream',
                    'Android Wear',
                    'Art & Design',
                    'Auto & Vehicles',
                    'Beauty',
                    'Books & Reference',
                    'Business',
                    'Comics',
                    'Communication',
                    'Dating',
                    'Education',
                    'Entertainment',
                    'Events',
                    'Finance',
                    'Food & Drink',
                    'Health & Fitness',
                    'House & Home',
                    'Libraries & Demo',
                    'Lifestyle',
                    'Maps & Navigation',
                    'Medical',
                    'Music & Audio',
                    'News & Magazines',
                    'Parenting',
                    'Personalisation',
                    'Photography',
                    'Productivity',
                    'Shopping',
                    'Social',
                    'Sports',
                    'Tools',
                    'Travel & Local',
                    'Video Players & Editors',
                    'Weather',
                ],
            'Game'=>
                [
                    'Action',
                    'Adventure',
                    'Arcade',
                    'Board',
                    'Card',
                    'Casino',
                    'Casual',
                    'Educational',
                    'Music',
                    'Puzzle',
                    'Racing',
                    'Role Playing',
                    'Simulation',
                    'Sports',
                    'Strategy',
                    'Trivia',
                    'Word',
                ],
            'Family' =>
                [
                    'Ages 5 & Under',
                    'Ages 6-8',
                    'Ages 9 & Over',
                    'Action & Adventure',
                    'Brain Games',
                    'Creativity',
                    'Education',
                    'Music and video',
                    'Pretend play',
                ]
        ];
    }

    /**
     * iOS类别列表
     * @return array
     */
    public static function getIosCategoryList(): array
    {
        return [
            'Game'=>
                [
                    'Action',
                    'Adventure',
                    'Arcade',
                    'Board',
                    'Card',
                    'Casino',
                    'Dice',
                    'Educational',
                    'Family',
                    'Music',
                    'Puzzle',
                    'Racing',
                    'Role Playing',
                    'Simulation',
                    'Sports',
                    'Strategy',
                    'Trivia',
                    'Word',
                ],
            'App'=>
                [
                    'Books',
                    'Business',
                    'Catalogs',
                    'Education',
                    'Entertainment',
                    'Finance',
                    'Food & Drink',
                    'Health & Fitness',
                    'Lifestyle',
                    'Magazines & Newspapers',
                    'Medical',
                    'Music',
                    'Navigation',
                    'News',
                    'Photo & Video',
                    'Productivity',
                    'Reference',
                    'Shopping',
                    'Social Networking',
                    'Sports',
                    'Stickers',
                    'Travel',
                    'Utilities',
                    'Weather',
                ]
        ];
    }
}

