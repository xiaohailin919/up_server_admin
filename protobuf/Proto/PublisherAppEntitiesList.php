<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: proto/meta.proto

namespace Proto;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>proto.PublisherAppEntitiesList</code>
 */
class PublisherAppEntitiesList extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>repeated .proto.PublisherAppEntities publisherAppEntities = 1;</code>
     */
    private $publisherAppEntities;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type \Proto\PublisherAppEntities[]|\Google\Protobuf\Internal\RepeatedField $publisherAppEntities
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Proto\Meta::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>repeated .proto.PublisherAppEntities publisherAppEntities = 1;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getPublisherAppEntities()
    {
        return $this->publisherAppEntities;
    }

    /**
     * Generated from protobuf field <code>repeated .proto.PublisherAppEntities publisherAppEntities = 1;</code>
     * @param \Proto\PublisherAppEntities[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setPublisherAppEntities($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Proto\PublisherAppEntities::class);
        $this->publisherAppEntities = $arr;

        return $this;
    }

}

