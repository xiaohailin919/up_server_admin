<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: proto/meta.proto

namespace Proto;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>proto.CommonMsg</code>
 */
class CommonMsg extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>bool flag = 1;</code>
     */
    private $flag = false;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type bool $flag
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Proto\Meta::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>bool flag = 1;</code>
     * @return bool
     */
    public function getFlag()
    {
        return $this->flag;
    }

    /**
     * Generated from protobuf field <code>bool flag = 1;</code>
     * @param bool $var
     * @return $this
     */
    public function setFlag($var)
    {
        GPBUtil::checkBool($var);
        $this->flag = $var;

        return $this;
    }

}

