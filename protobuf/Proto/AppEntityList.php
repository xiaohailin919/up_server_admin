<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: proto/meta.proto

namespace Proto;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>proto.AppEntityList</code>
 */
class AppEntityList extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>repeated .proto.AppEntity apps = 1;</code>
     */
    private $apps;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type \Proto\AppEntity[]|\Google\Protobuf\Internal\RepeatedField $apps
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Proto\Meta::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>repeated .proto.AppEntity apps = 1;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getApps()
    {
        return $this->apps;
    }

    /**
     * Generated from protobuf field <code>repeated .proto.AppEntity apps = 1;</code>
     * @param \Proto\AppEntity[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setApps($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Proto\AppEntity::class);
        $this->apps = $arr;

        return $this;
    }

}

