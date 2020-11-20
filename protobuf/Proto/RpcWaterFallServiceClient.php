<?php
// GENERATED CODE -- DO NOT EDIT!

namespace Proto;

/**
 */
class RpcWaterFallServiceClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * @param \Proto\PublisherAppEntityPage $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function GetAppDatas(\Proto\PublisherAppEntityPage $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/GetAppDatas',
        $argument,
        ['\Proto\PublisherAppEntitys', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\PublisherAppEntitys $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function CreateAppData(\Proto\PublisherAppEntitys $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/CreateAppData',
        $argument,
        ['\Proto\PublisherAppEntitys', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\PublisherId $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function GetAllPublisherApp(\Proto\PublisherId $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/GetAllPublisherApp',
        $argument,
        ['\Proto\PublisherAppEntitiesList', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\PublisherOpenApiEntity $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function GetPublisherOpenApiData(\Proto\PublisherOpenApiEntity $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/GetPublisherOpenApiData',
        $argument,
        ['\Proto\PublisherOpenApiResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\PublisherPlacementList $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function GetPlacementDatas(\Proto\PublisherPlacementList $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/GetPlacementDatas',
        $argument,
        ['\Proto\PublisherPlacementEntitys', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\PublisherPlacementEntitys $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function CreatePlacementData(\Proto\PublisherPlacementEntitys $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/CreatePlacementData',
        $argument,
        ['\Proto\PublisherPlacementEntitys', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\PlacementIdList $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function GetPlacementWithId(\Proto\PlacementIdList $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/GetPlacementWithId',
        $argument,
        ['\Proto\PmEntityList', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\PlacementUuidList $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function GetPlacementWithUuids(\Proto\PlacementUuidList $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/GetPlacementWithUuids',
        $argument,
        ['\Proto\PmEntityList', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\PlacementIdList $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function GetNormalPlacementWithId(\Proto\PlacementIdList $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/GetNormalPlacementWithId',
        $argument,
        ['\Proto\PmEntityList', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\PublisherId $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function GetPlacementWithPublisherId(\Proto\PublisherId $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/GetPlacementWithPublisherId',
        $argument,
        ['\Proto\PmEntityList', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\PublisherId $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function GetAllPlacement(\Proto\PublisherId $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/GetAllPlacement',
        $argument,
        ['\Proto\PmEntityList', 'decode'],
        $metadata, $options);
    }

    /**
     * placement属性修改
     * @param \Proto\PlacementEntity $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function UpdatePlacement(\Proto\PlacementEntity $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/UpdatePlacement',
        $argument,
        ['\Proto\CommonEmptyMsg', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\PublisherId $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function GetPublisherWithId(\Proto\PublisherId $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/GetPublisherWithId',
        $argument,
        ['\Proto\PublisherEntity', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\PublisherId $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function GetAppWithPublisherId(\Proto\PublisherId $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/GetAppWithPublisherId',
        $argument,
        ['\Proto\AppEntityList', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\AppUuidList $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function GetAppWithUuids(\Proto\AppUuidList $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/GetAppWithUuids',
        $argument,
        ['\Proto\AppEntityList', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\AppIdList $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function GetAppWithIds(\Proto\AppIdList $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/GetAppWithIds',
        $argument,
        ['\Proto\AppEntityList', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\AppIdRanks $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function SetAppRanks(\Proto\AppIdRanks $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/SetAppRanks',
        $argument,
        ['\Proto\AppIdRanksResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\SegmentIdList $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function GetSegmentWithId(\Proto\SegmentIdList $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/GetSegmentWithId',
        $argument,
        ['\Proto\SegmentEntityList', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\UnitRemote $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function GetUnitWithRemote(\Proto\UnitRemote $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/GetUnitWithRemote',
        $argument,
        ['\Proto\UnitEntity', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\UnitIdList $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function GetUnitWithIds(\Proto\UnitIdList $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/GetUnitWithIds',
        $argument,
        ['\Proto\UnitEntityList', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\UnitIdList $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function GetUnitUpInfo(\Proto\UnitIdList $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/GetUnitUpInfo',
        $argument,
        ['\Proto\UnitUpInfoList', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\GeoShort $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function GetGeoNameWithShort(\Proto\GeoShort $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/GetGeoNameWithShort',
        $argument,
        ['\Proto\GeoName', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\PagePmPub $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function PagePlacements(\Proto\PagePmPub $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/PagePlacements',
        $argument,
        ['\Proto\PmEntityList', 'decode'],
        $metadata, $options);
    }

    /**
     * publisher
     * @param \Proto\PagePublisher $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function PagePublishers(\Proto\PagePublisher $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/PagePublishers',
        $argument,
        ['\Proto\PublisherEntityList', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\PagePmPub $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function GetPublisherEcpmOptimization(\Proto\PagePmPub $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/GetPublisherEcpmOptimization',
        $argument,
        ['\Proto\UnitSegmentPlacementTrafficGroups', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\NetWorkCountRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function QueryNetworkCount(\Proto\NetWorkCountRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/QueryNetworkCount',
        $argument,
        ['\Proto\NetWorkCountReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\NetWorkLimitRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function QueryNetworkLimit(\Proto\NetWorkLimitRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/QueryNetworkLimit',
        $argument,
        ['\Proto\NetWorkLimitReply', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\NwfId $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function GetNetworkFirmWithId(\Proto\NwfId $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/GetNetworkFirmWithId',
        $argument,
        ['\Proto\NwfEntity', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\PublisherId $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function GetSegmentUnit(\Proto\PublisherId $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/GetSegmentUnit',
        $argument,
        ['\Proto\SegmentUnitList', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\PlatformSegmentSet $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function GetUnitByPlatformSegment(\Proto\PlatformSegmentSet $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/GetUnitByPlatformSegment',
        $argument,
        ['\Proto\SegmentUnitList', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\PlatformSet $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function GetUnitByPlatform(\Proto\PlatformSet $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/GetUnitByPlatform',
        $argument,
        ['\Proto\PlatformUnitList', 'decode'],
        $metadata, $options);
    }

    /**
     * 自动优化设置表
     * @param \Proto\UnitEcpmOptimization $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function GetUnitEcpmOptimization(\Proto\UnitEcpmOptimization $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/GetUnitEcpmOptimization',
        $argument,
        ['\Proto\UnitEcpmOptimization', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\UnitEcpmOptimization $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function SetUnitEcpmOptimization(\Proto\UnitEcpmOptimization $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/SetUnitEcpmOptimization',
        $argument,
        ['\Proto\CommonEmptyMsg', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\UnitEcpmOptimizationLog $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function SetUnitEcpmOptimizationLog(\Proto\UnitEcpmOptimizationLog $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/SetUnitEcpmOptimizationLog',
        $argument,
        ['\Proto\CommonEmptyMsg', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\UnitEcpmOptimizationLog $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function GetUnitEcpmOptimizationLog(\Proto\UnitEcpmOptimizationLog $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/GetUnitEcpmOptimizationLog',
        $argument,
        ['\Proto\UnitEcpmOptimizationLog', 'decode'],
        $metadata, $options);
    }

    /**
     * 开发者后台读取数据接口列表
     * waterfall列表接口
     * @param \Proto\PlacementUuidList $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function GetPlacementWaterFallList(\Proto\PlacementUuidList $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/GetPlacementWaterFallList',
        $argument,
        ['\Proto\PlacementWaterfallData', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\PlacementGroupConfig $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function SetPlacementGroupConfig(\Proto\PlacementGroupConfig $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/SetPlacementGroupConfig',
        $argument,
        ['\Proto\CommonEmptyMsg', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\PlacementSegmentRank $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function SetPlacementSegmentRank(\Proto\PlacementSegmentRank $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/SetPlacementSegmentRank',
        $argument,
        ['\Proto\CommonEmptyMsg', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\PlacementSegmentRemove $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function RemovePlacementSegmentRank(\Proto\PlacementSegmentRemove $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/RemovePlacementSegmentRank',
        $argument,
        ['\Proto\CommonEmptyMsg', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\PlacementTrafficGroupSegmentConfig $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function SetPlacementGroupSegmentConfig(\Proto\PlacementTrafficGroupSegmentConfig $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/SetPlacementGroupSegmentConfig',
        $argument,
        ['\Proto\CommonEmptyMsg', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\PlacementTrafficGroupSegmentUnitOptimization $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function SetPlacementGroupSegmentUnitOptimization(\Proto\PlacementTrafficGroupSegmentUnitOptimization $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/SetPlacementGroupSegmentUnitOptimization',
        $argument,
        ['\Proto\CommonEmptyMsg', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\PlacementTrafficGroupSegmentUnitRank $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function SetPlacementGroupSegmentUnitRank(\Proto\PlacementTrafficGroupSegmentUnitRank $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/SetPlacementGroupSegmentUnitRank',
        $argument,
        ['\Proto\OnePlacementGroupSegmentData', 'decode'],
        $metadata, $options);
    }

    /**
     * network页面选择adsourcelist的列表
     * @param \Proto\PlacementTrafficGroupUnit $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function GetPlacementTrafficGroupList(\Proto\PlacementTrafficGroupUnit $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/GetPlacementTrafficGroupList',
        $argument,
        ['\Proto\PlacementSegmentList', 'decode'],
        $metadata, $options);
    }

    /**
     * 添加unit
     * @param \Proto\OnePlacementUnit $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function AddPlacementGroupSegmentUnit(\Proto\OnePlacementUnit $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/AddPlacementGroupSegmentUnit',
        $argument,
        ['\Proto\OnePlacementGroupSegmentData', 'decode'],
        $metadata, $options);
    }

    /**
     * 删除unit
     * @param \Proto\UnitIdLists $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function DeleteUnit(\Proto\UnitIdLists $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/DeleteUnit',
        $argument,
        ['\Proto\CommonEmptyMsg', 'decode'],
        $metadata, $options);
    }

    /**
     * 后台迁移数据
     * @param \Proto\PublisherId $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function AdminMigratePublisherData(\Proto\PublisherId $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/AdminMigratePublisherData',
        $argument,
        ['\Proto\CommonEmptyMsg', 'decode'],
        $metadata, $options);
    }

    /**
     * waterfall上面的搜索
     * @param \Proto\PlacementTrafficGroupSegmentConfig $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function PlacementGroupSegmentSearch(\Proto\PlacementTrafficGroupSegmentConfig $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/PlacementGroupSegmentSearch',
        $argument,
        ['\Proto\OnePlacementGroupSegmentData', 'decode'],
        $metadata, $options);
    }

    /**
     * 关闭所有的network的unit
     * @param \Proto\CloseNetwork $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function CloseOneNetworkUnits(\Proto\CloseNetwork $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/CloseOneNetworkUnits',
        $argument,
        ['\Proto\CommonEmptyMsg', 'decode'],
        $metadata, $options);
    }

    /**
     * offer队列服务
     * @param \Proto\OfferQueueParam $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function DealOfferQueue(\Proto\OfferQueueParam $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/DealOfferQueue',
        $argument,
        ['\Proto\CommonEmptyMsg', 'decode'],
        $metadata, $options);
    }

    /**
     * sqs的队列服务
     * @param \Proto\SqsQueueParam $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function PlacementSqsQueue(\Proto\SqsQueueParam $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/PlacementSqsQueue',
        $argument,
        ['\Proto\CommonEmptyMsg', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Proto\SqsQueueParam $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function OfferSqsQueue(\Proto\SqsQueueParam $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/proto.RpcWaterFallService/OfferSqsQueue',
        $argument,
        ['\Proto\CommonEmptyMsg', 'decode'],
        $metadata, $options);
    }

}
