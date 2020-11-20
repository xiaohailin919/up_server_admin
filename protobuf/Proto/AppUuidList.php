<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: proto/meta.proto

namespace Proto;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>proto.AppUuidList</code>
 */
class AppUuidList extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>repeated string uuids = 1;</code>
     */
    private $uuids;
    /**
     * Generated from protobuf field <code>int32 publisherId = 2;</code>
     */
    private $publisherId = 0;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string[]|\Google\Protobuf\Internal\RepeatedField $uuids
     *     @type int $publisherId
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Proto\Meta::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>repeated string uuids = 1;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getUuids()
    {
        return $this->uuids;
    }

    /**
     * Generated from protobuf field <code>repeated string uuids = 1;</code>
     * @param string[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setUuids($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::STRING);
        $this->uuids = $arr;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 publisherId = 2;</code>
     * @return int
     */
    public function getPublisherId()
    {
        return $this->publisherId;
    }

    /**
     * Generated from protobuf field <code>int32 publisherId = 2;</code>
     * @param int $var
     * @return $this
     */
    public function setPublisherId($var)
    {
        GPBUtil::checkInt32($var);
        $this->publisherId = $var;

        return $this;
    }

}

