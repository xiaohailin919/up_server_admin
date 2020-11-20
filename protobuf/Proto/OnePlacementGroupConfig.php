<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: proto/meta.proto

namespace Proto;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>proto.OnePlacementGroupConfig</code>
 */
class OnePlacementGroupConfig extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>int32 abtype = 1;</code>
     */
    private $abtype = 0;
    /**
     * Generated from protobuf field <code>string name = 2;</code>
     */
    private $name = '';
    /**
     * Generated from protobuf field <code>int32 traffic_variation = 3;</code>
     */
    private $traffic_variation = 0;
    /**
     * Generated from protobuf field <code>int32 status = 4;</code>
     */
    private $status = 0;
    /**
     * Generated from protobuf field <code>string create_time = 5;</code>
     */
    private $create_time = '';
    /**
     * Generated from protobuf field <code>string update_time = 6;</code>
     */
    private $update_time = '';
    /**
     * Generated from protobuf field <code>int32 cap_hour_switch = 7;</code>
     */
    private $cap_hour_switch = 0;
    /**
     * Generated from protobuf field <code>int32 cap_hour = 8;</code>
     */
    private $cap_hour = 0;
    /**
     * Generated from protobuf field <code>int32 cap_day_switch = 9;</code>
     */
    private $cap_day_switch = 0;
    /**
     * Generated from protobuf field <code>int32 cap_day = 10;</code>
     */
    private $cap_day = 0;
    /**
     * Generated from protobuf field <code>int32 pacing_switch = 11;</code>
     */
    private $pacing_switch = 0;
    /**
     * Generated from protobuf field <code>int32 pacing = 12;</code>
     */
    private $pacing = 0;
    /**
     * Generated from protobuf field <code>int32 auto_refresh = 13;</code>
     */
    private $auto_refresh = 0;
    /**
     * Generated from protobuf field <code>int32 auto_refresh_time = 14;</code>
     */
    private $auto_refresh_time = 0;
    /**
     * Generated from protobuf field <code>int32 traffic_group_id = 15;</code>
     */
    private $traffic_group_id = 0;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type int $abtype
     *     @type string $name
     *     @type int $traffic_variation
     *     @type int $status
     *     @type string $create_time
     *     @type string $update_time
     *     @type int $cap_hour_switch
     *     @type int $cap_hour
     *     @type int $cap_day_switch
     *     @type int $cap_day
     *     @type int $pacing_switch
     *     @type int $pacing
     *     @type int $auto_refresh
     *     @type int $auto_refresh_time
     *     @type int $traffic_group_id
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Proto\Meta::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>int32 abtype = 1;</code>
     * @return int
     */
    public function getAbtype()
    {
        return $this->abtype;
    }

    /**
     * Generated from protobuf field <code>int32 abtype = 1;</code>
     * @param int $var
     * @return $this
     */
    public function setAbtype($var)
    {
        GPBUtil::checkInt32($var);
        $this->abtype = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string name = 2;</code>
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Generated from protobuf field <code>string name = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setName($var)
    {
        GPBUtil::checkString($var, True);
        $this->name = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 traffic_variation = 3;</code>
     * @return int
     */
    public function getTrafficVariation()
    {
        return $this->traffic_variation;
    }

    /**
     * Generated from protobuf field <code>int32 traffic_variation = 3;</code>
     * @param int $var
     * @return $this
     */
    public function setTrafficVariation($var)
    {
        GPBUtil::checkInt32($var);
        $this->traffic_variation = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 status = 4;</code>
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Generated from protobuf field <code>int32 status = 4;</code>
     * @param int $var
     * @return $this
     */
    public function setStatus($var)
    {
        GPBUtil::checkInt32($var);
        $this->status = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string create_time = 5;</code>
     * @return string
     */
    public function getCreateTime()
    {
        return $this->create_time;
    }

    /**
     * Generated from protobuf field <code>string create_time = 5;</code>
     * @param string $var
     * @return $this
     */
    public function setCreateTime($var)
    {
        GPBUtil::checkString($var, True);
        $this->create_time = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string update_time = 6;</code>
     * @return string
     */
    public function getUpdateTime()
    {
        return $this->update_time;
    }

    /**
     * Generated from protobuf field <code>string update_time = 6;</code>
     * @param string $var
     * @return $this
     */
    public function setUpdateTime($var)
    {
        GPBUtil::checkString($var, True);
        $this->update_time = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 cap_hour_switch = 7;</code>
     * @return int
     */
    public function getCapHourSwitch()
    {
        return $this->cap_hour_switch;
    }

    /**
     * Generated from protobuf field <code>int32 cap_hour_switch = 7;</code>
     * @param int $var
     * @return $this
     */
    public function setCapHourSwitch($var)
    {
        GPBUtil::checkInt32($var);
        $this->cap_hour_switch = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 cap_hour = 8;</code>
     * @return int
     */
    public function getCapHour()
    {
        return $this->cap_hour;
    }

    /**
     * Generated from protobuf field <code>int32 cap_hour = 8;</code>
     * @param int $var
     * @return $this
     */
    public function setCapHour($var)
    {
        GPBUtil::checkInt32($var);
        $this->cap_hour = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 cap_day_switch = 9;</code>
     * @return int
     */
    public function getCapDaySwitch()
    {
        return $this->cap_day_switch;
    }

    /**
     * Generated from protobuf field <code>int32 cap_day_switch = 9;</code>
     * @param int $var
     * @return $this
     */
    public function setCapDaySwitch($var)
    {
        GPBUtil::checkInt32($var);
        $this->cap_day_switch = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 cap_day = 10;</code>
     * @return int
     */
    public function getCapDay()
    {
        return $this->cap_day;
    }

    /**
     * Generated from protobuf field <code>int32 cap_day = 10;</code>
     * @param int $var
     * @return $this
     */
    public function setCapDay($var)
    {
        GPBUtil::checkInt32($var);
        $this->cap_day = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 pacing_switch = 11;</code>
     * @return int
     */
    public function getPacingSwitch()
    {
        return $this->pacing_switch;
    }

    /**
     * Generated from protobuf field <code>int32 pacing_switch = 11;</code>
     * @param int $var
     * @return $this
     */
    public function setPacingSwitch($var)
    {
        GPBUtil::checkInt32($var);
        $this->pacing_switch = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 pacing = 12;</code>
     * @return int
     */
    public function getPacing()
    {
        return $this->pacing;
    }

    /**
     * Generated from protobuf field <code>int32 pacing = 12;</code>
     * @param int $var
     * @return $this
     */
    public function setPacing($var)
    {
        GPBUtil::checkInt32($var);
        $this->pacing = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 auto_refresh = 13;</code>
     * @return int
     */
    public function getAutoRefresh()
    {
        return $this->auto_refresh;
    }

    /**
     * Generated from protobuf field <code>int32 auto_refresh = 13;</code>
     * @param int $var
     * @return $this
     */
    public function setAutoRefresh($var)
    {
        GPBUtil::checkInt32($var);
        $this->auto_refresh = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 auto_refresh_time = 14;</code>
     * @return int
     */
    public function getAutoRefreshTime()
    {
        return $this->auto_refresh_time;
    }

    /**
     * Generated from protobuf field <code>int32 auto_refresh_time = 14;</code>
     * @param int $var
     * @return $this
     */
    public function setAutoRefreshTime($var)
    {
        GPBUtil::checkInt32($var);
        $this->auto_refresh_time = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 traffic_group_id = 15;</code>
     * @return int
     */
    public function getTrafficGroupId()
    {
        return $this->traffic_group_id;
    }

    /**
     * Generated from protobuf field <code>int32 traffic_group_id = 15;</code>
     * @param int $var
     * @return $this
     */
    public function setTrafficGroupId($var)
    {
        GPBUtil::checkInt32($var);
        $this->traffic_group_id = $var;

        return $this;
    }

}

