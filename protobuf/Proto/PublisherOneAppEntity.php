<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: proto/openapi.proto

namespace Proto;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>proto.PublisherOneAppEntity</code>
 */
class PublisherOneAppEntity extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string appName = 1;</code>
     */
    private $appName = '';
    /**
     * Generated from protobuf field <code>int32 platform = 2;</code>
     */
    private $platform = 0;
    /**
     * Generated from protobuf field <code>string marketUrl = 3;</code>
     */
    private $marketUrl = '';
    /**
     * Generated from protobuf field <code>string packageName = 4;</code>
     */
    private $packageName = '';
    /**
     * Generated from protobuf field <code>string category = 5;</code>
     */
    private $category = '';
    /**
     * Generated from protobuf field <code>string subCategory = 6;</code>
     */
    private $subCategory = '';
    /**
     * Generated from protobuf field <code>string appId = 7;</code>
     */
    private $appId = '';
    /**
     * Generated from protobuf field <code>int32 publisherId = 8;</code>
     */
    private $publisherId = 0;
    /**
     * Generated from protobuf field <code>string msg = 9;</code>
     */
    private $msg = '';
    /**
     * Generated from protobuf field <code>string iconUrl = 10;</code>
     */
    private $iconUrl = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $appName
     *     @type int $platform
     *     @type string $marketUrl
     *     @type string $packageName
     *     @type string $category
     *     @type string $subCategory
     *     @type string $appId
     *     @type int $publisherId
     *     @type string $msg
     *     @type string $iconUrl
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Proto\Openapi::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>string appName = 1;</code>
     * @return string
     */
    public function getAppName()
    {
        return $this->appName;
    }

    /**
     * Generated from protobuf field <code>string appName = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setAppName($var)
    {
        GPBUtil::checkString($var, True);
        $this->appName = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 platform = 2;</code>
     * @return int
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * Generated from protobuf field <code>int32 platform = 2;</code>
     * @param int $var
     * @return $this
     */
    public function setPlatform($var)
    {
        GPBUtil::checkInt32($var);
        $this->platform = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string marketUrl = 3;</code>
     * @return string
     */
    public function getMarketUrl()
    {
        return $this->marketUrl;
    }

    /**
     * Generated from protobuf field <code>string marketUrl = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setMarketUrl($var)
    {
        GPBUtil::checkString($var, True);
        $this->marketUrl = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string packageName = 4;</code>
     * @return string
     */
    public function getPackageName()
    {
        return $this->packageName;
    }

    /**
     * Generated from protobuf field <code>string packageName = 4;</code>
     * @param string $var
     * @return $this
     */
    public function setPackageName($var)
    {
        GPBUtil::checkString($var, True);
        $this->packageName = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string category = 5;</code>
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Generated from protobuf field <code>string category = 5;</code>
     * @param string $var
     * @return $this
     */
    public function setCategory($var)
    {
        GPBUtil::checkString($var, True);
        $this->category = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string subCategory = 6;</code>
     * @return string
     */
    public function getSubCategory()
    {
        return $this->subCategory;
    }

    /**
     * Generated from protobuf field <code>string subCategory = 6;</code>
     * @param string $var
     * @return $this
     */
    public function setSubCategory($var)
    {
        GPBUtil::checkString($var, True);
        $this->subCategory = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string appId = 7;</code>
     * @return string
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * Generated from protobuf field <code>string appId = 7;</code>
     * @param string $var
     * @return $this
     */
    public function setAppId($var)
    {
        GPBUtil::checkString($var, True);
        $this->appId = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 publisherId = 8;</code>
     * @return int
     */
    public function getPublisherId()
    {
        return $this->publisherId;
    }

    /**
     * Generated from protobuf field <code>int32 publisherId = 8;</code>
     * @param int $var
     * @return $this
     */
    public function setPublisherId($var)
    {
        GPBUtil::checkInt32($var);
        $this->publisherId = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string msg = 9;</code>
     * @return string
     */
    public function getMsg()
    {
        return $this->msg;
    }

    /**
     * Generated from protobuf field <code>string msg = 9;</code>
     * @param string $var
     * @return $this
     */
    public function setMsg($var)
    {
        GPBUtil::checkString($var, True);
        $this->msg = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string iconUrl = 10;</code>
     * @return string
     */
    public function getIconUrl()
    {
        return $this->iconUrl;
    }

    /**
     * Generated from protobuf field <code>string iconUrl = 10;</code>
     * @param string $var
     * @return $this
     */
    public function setIconUrl($var)
    {
        GPBUtil::checkString($var, True);
        $this->iconUrl = $var;

        return $this;
    }

}

