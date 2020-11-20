<?php

namespace App\Services;

use App\Models\MySql\DictNetwork as DictNetworkMyModel;
use App\Models\Mongo\DictNetwork as DictNetworkMoModel;

class DictNetwork
{
    public static function syncToMo()
    {
        $fields = [
            'code',
            'name',
        ];

        $mym = new DictNetworkMyModel();
        $myDict = self::getDict($mym->get($fields), 'code', 'name');

        $mom = new DictNetworkMoModel();
        $moDict = self::getDict($mom->get($fields), 'code', 'name');

        $insert = $update = $delete = [];

        //build insert  update
        if ($myDict) {
            foreach ($myDict as $mk => $md) {
                if (!isset($moDict[$mk])) {
                    $insert[$mk] = $md;
                } else {
                    if ($md != $moDict[$mk]) {
                        $update[$mk] = $md;
                    }
                    unset($moDict[$mk]);
                }
            }
        }

        //build delete
        if ($moDict) {
            foreach ($moDict as $mk => $md) {
                $delete[$mk] = $md;
            }
        }

        //insert
        if ($insert) {
            foreach ($insert as $ik => $iv) {
                $tmpI = [
                    'code' => (string)$ik,
                    'name' => $iv,
                ];
                $mom->queryBuilder()->insert($tmpI);
            }
        }

        if ($update) {
            //update
            foreach ($update as $ik => $iv) {
                $mom->queryBuilder()->where('code', (string)$ik)->update(['name' => $iv]);
            }
        }

        //delete
        if ($delete) {
            foreach ($delete as $ik => $iv) {
                $mom->queryBuilder()->where('code', (string)$ik)->delete();
            }
        }
    }

    private static function getDict($list, $key ,$value)
    {
        if (empty($list)) {
            return [];
        }

        $dict = [];
        foreach ($list as $v) {
            $dict[$v[$key]] = $v[$value];
        }

        return $dict;
    }
}