<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: proto/meta.proto

namespace Proto;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>proto.OnePlacementGroup</code>
 */
class OnePlacementGroup extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>repeated .proto.OnePlacementGroupSegmentData one_group_segment_data = 1;</code>
     */
    private $one_group_segment_data;
    /**
     * Generated from protobuf field <code>.proto.OnePlacementGroupConfig group_config = 2;</code>
     */
    private $group_config = null;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type \Proto\OnePlacementGroupSegmentData[]|\Google\Protobuf\Internal\RepeatedField $one_group_segment_data
     *     @type \Proto\OnePlacementGroupConfig $group_config
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Proto\Meta::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>repeated .proto.OnePlacementGroupSegmentData one_group_segment_data = 1;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getOneGroupSegmentData()
    {
        return $this->one_group_segment_data;
    }

    /**
     * Generated from protobuf field <code>repeated .proto.OnePlacementGroupSegmentData one_group_segment_data = 1;</code>
     * @param \Proto\OnePlacementGroupSegmentData[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setOneGroupSegmentData($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Proto\OnePlacementGroupSegmentData::class);
        $this->one_group_segment_data = $arr;

        return $this;
    }

    /**
     * Generated from protobuf field <code>.proto.OnePlacementGroupConfig group_config = 2;</code>
     * @return \Proto\OnePlacementGroupConfig
     */
    public function getGroupConfig()
    {
        return $this->group_config;
    }

    /**
     * Generated from protobuf field <code>.proto.OnePlacementGroupConfig group_config = 2;</code>
     * @param \Proto\OnePlacementGroupConfig $var
     * @return $this
     */
    public function setGroupConfig($var)
    {
        GPBUtil::checkMessage($var, \Proto\OnePlacementGroupConfig::class);
        $this->group_config = $var;

        return $this;
    }

}

