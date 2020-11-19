/**
 * 项目生产环境配置文件
 */
//testeteteteteteteteteetetetet
export default {
  API_BASE_URL: `//${window.location.host}`,
  LOGIN_URL: `//${window.location.host}/#login`,
  IMG_BASE_URL: '//mores.toponad.com',
  API: {
    'LOGIN': 'login',
    'LOGOUT': 'logout',
    'REGISTER': 'register',
    'USERINFO': 'account',
    'UPDATE_USERINFO': 'update-info',
    'UPDATE_PASSWORD': 'update-password',
    'PASSWORD_EMAIL': 'password/email',
    'PASSWORD_RESET': 'password/reset',
    'NOTICE_LIST': 'notice',
    'NOTICE_HIDE': 'notice-hide',
    'APP': 'app',
    'APP_LIST': 'app/list',
    'ENABLE_APP_LIST': 'app/network-active-app',
    'PLACEMENT': 'placement',
    'TRAFFIC_GROUP': 'traffic-group',
    'APP_PLACEMENT_LIST': 'app-placement-list',
    'PLACEMENT_LIST': 'placement/list',

    'SEGMENT_LIST': 'segment/list',
    'SEGMENT_LIST_FLAT': 'segment/flat-list',
    'SEGMENT_ALL_LIST': 'segment/all-list',
    'SEGMENT_SAVE_ONE': 'segment/save-one',
    'SEGMENT_UPDATE_ONE': 'segment/update-one',
    'SEGMENT_DELETE_ONE': 'segment/delete-one',
    'SEGMENT': 'v2/segment',
    'SEGMENT_BY_PLACEMENT': 'segment/by-placement',

    'APP_RING': 'app/ring',
    'PLACEMENT_RING': 'placement/ring',
    'ALL_NETWORK': 'networkfirm',
    'NETWORK': 'network',
    'NETWORK_APP': 'networkapp',
    'UNIT': 'unit',
    'UNIT_SHOW_ALL': 'unit/show-out',
    'UNIT_SAVE_ALL': 'unit/save',
    'ADD_UNIT': 'unit-bat',
    'APP_CATEGORY': 'app/get-category',
    'APP_SEARCH': 'app/search-app',
    'SDK_VERSION': 'sdk-version',
    'DEVICE_BRAND': 'brand',
    'SEGMENT_SORT': 'segment/sort',
    'NETWORKCHECK': 'networkcheck',
    'MEDIATION': 'relationship',
    'WATERFALL_REPORT': 'waterfall-report',
    'WATERFALL_SAVE_ONE': 'relationship/save-one',
    'WATERFALL_UPDATE_ONE': 'relationship/update-one',
    'COUNTRY': 'country',
    'ALL_COUNTRY': 'geolist',
    'PLACEMENT_LIST_FLAT': 'placement/flat-list',

    // Meta
    'META_APP': 'meta/app',
    'META_PLACEMENT': 'meta/placement',
    'META_UNIT': 'meta/unit',
    'META_NETWORK': 'meta/network',
    'META_AREA': 'meta/area',
    'META_METRICS': 'meta/metrics',
    'META_CHANNEL': 'meta/channel',
    'META_CITY': 'meta/city',
    'META_SEGMENT': 'meta/segment',
    'META_PRODUCT': 'v2/my-offer-product/meta',
    'META_OFFER': 'v2/my-offer-offer/meta',

    // LTV
    'LTV_REPORT_EXPORT': 'v2/report-ltv/export',
    'LTV_REPORT': 'v2/report-ltv',
    'LTV_REPORT_AVERAGE': 'v2/report-ltv/average',
    'LTV_METRICS': 'v2/report-ltv/metrics',

    // Retention
    'RETENTION_REPORT_EXPORT': 'v2/report-retention/export',
    'RETENTION_REPORT': 'v2/report-retention',
    'RETENTION_REPORT_AVERAGE': 'v2/report-retention/average',
    'RETENTION_METRICS': 'v2/report-retention/metrics',

    'CHART_METRICS': 'trend-metrics',
    // 'CHART_REPORT': 'trend-report',
    'CHART_REPORT_SWITCH': 'trend-report-switch',

    'REPORT_METRICS': 'report-metrics',
    'REPORT': 'report',
    'V2_REPORT': 'v2/report',
    'V2_REPORT_EXPORT': 'v2/report/export',
    'REPORT_EXPORT': 'report/export',
    // My Offer 预估收益开关
    'REPORT_MO_ESTIMATE_REVENUE_SWITCH': 'estimate-revenue-switch',
    // My Offer 广告统计数据开关
    'REPORT_MO_AD_DATA_SWITCH': 'my-offer-metric-switch',
    'CHECK_OFFER_NAME': 'v2/my-offer-offer/check-name',

    // My Offer Report
    'MY_OFFER_METRICS': 'my-offer-metrics',
    'MY_OFFER_REPORT': 'my-offer-report',
    'MY_OFFER_REPORT_EXPORT': 'my-offer-report/export',

    'RELATIONSHIPS_SEGMENT_SORT': 'relationships/segment-sort',
    'RELATIONSHIPS_SEGMENT_DATA': 'relationships/segment-data',
    'RELATIONSHIPS_SEGMENT_DEL': 'relationships/segment-del',
    'RELATIONSHIPS_UNIT_RANK': 'relationships/unit-rank',
    'RELATIONSHIP_ABTEST': 'relationship/abtest',
    'TODAY_EXCHANGE_RATE': 'meta/today-exchange-rate',
    'REPORT_IMPORT': 'report-import',
    'FULL_REPORT_FILTERS': 'full-report-filters',

    // V2 API
    'WATERFALL_ALL': 'v2/waterfall/all',
    'WATERFALL_SINGLE': 'v2/waterfall',
    'SET_WATERFALL_UNIT_STATUS': 'v2/waterfall/switch-status',
    'SET_WATERFALL_UNIT_OPTIMIZE': 'v2/waterfall/auto-optimize',
    'SET_MEDIATION': 'v2/mediation',
    'V2_SEGMENT': 'v2/segment',
    'COPY_SEGMENT': 'v2/waterfall/copy-segment',
    'COPY_UNIT': 'v2/waterfall/copy-unit',
    'MEDIATION_SAVE': 'v2/mediation/save',
    'SET_PARALLEL_REQUEST': 'v2/traffic/set-parallel-req',
    'SET_OFFER_STATUS': 'v2/my-offer-offer/update-status',
    'UNBIND_SEGMENT': 'v2/traffic/unbind-segment',
    'UNBIND_SEGMENTS': 'v2/traffic/unbind-segments',
    'BIND_SEGMENT': 'v2/traffic/bind-segment',
    'META_PLACEMENT_V2': 'v2/placement/meta',
    'UNIT_V2': 'v2/unit',
    'PLACEMENT_NETWORK': 'v2/placement/network',
    'UNIT_IS_USE': 'v2/unit/segment-in-use',
    'NETWORK_IN_USE': 'v2/network/meta-in-use',
    'NETWORK_ALL': 'v2/network/all',
    'MY_OFFER_PRODUCT': 'v2/my-offer-product',
    'MY_OFFER_OFFER': 'v2/my-offer-offer',
    'PLACEMENT_LIST_FOR_OFFER': 'v2/placement/meta-for-offer',
    'UPLOAD_OFFER_IMG': 'v2/my-offer-offer/upload-image',
    'SUB_ACCOUNT': 'v2/sub-account',
    'V2_NETWORK_APP': 'v2/network-app',
    'NETWORK_APP_AUTH_CONTENT': 'v2/network-app/auth-content',
    'WATERFALL_BATCH_ADD_UNIT': 'v2/unit/batch-save',
    'NETWORK_BATCH_ADD_UNIT': 'v2/unit/batch-save-for-network',
    'NETWORK_FOR_REPORT_IMPORT': 'v2/network/meta-for-report-import',
    'APP_META': 'v2/app/meta',
    'APP_META_PAGING': 'v2/app/meta-for-paging',
    'PLACEMENT_META_PAGING': 'v2/placement/meta-for-paging',
    'UNIT_META': 'v2/unit/meta',
    'SEGMENT_META': 'v2/segment/meta',
    'AREA_META': 'v2/area/grouping',
    'V2_APP': 'v2/app',
    'V2_PLACEMENT': 'v2/placement',
    'MY_OFFER_STATUS': 'v2/waterfall/my-offer-status',
    'PLACEMENT_META': 'v2/placement/meta',
    'MEDIATION_TEST': 'v2/mediation-test',
    'MEDIATION_TEST_UNIT': 'v2/mediation-test/unit',

    'FULL_REPORT_METRICS_OPTION': 'v2/report/metrics-option',
    'FULL_REPORT_METRICS_OPTION_SELF': 'v2/report/metrics',
    'OFFER_REPORT_METRICS_OPTION': 'v2/report-my-offer/metrics-option',
    'OFFER_REPORT_METRICS_OPTION_SELF': 'v2/report-my-offer/metrics',
    'WATERFALL_METRICS_OPTION': 'v2/waterfall/metrics-option',
    'WATERFALL_METRICS_OPTION_SELF': 'v2/waterfall/metrics',
    'APP_METRICS_OPTION': 'v2/app/metrics-option',
    'APP_METRICS_OPTION_SELF': 'v2/app/metrics',
    'PLACEMENT_METRICS_OPTION': 'v2/placement/metrics-option',
    'PLACEMENT_METRICS_OPTION_SELF': 'v2/placement/metrics',
    'SEGMENT_FOR_QUICK_ADD': 'v2/segment/for-quick-add',
    'EXPORT_NETWORK': 'v2/network/export',

    // Scenario
    'SCENARIO': 'v2/scenario',
    'SCENARIO_META': 'v2/scenario/meta',

    // Dashboard
    'DASHBOARD_OVERVIEW': 'v2/dashboard/overview',
    'DASHBOARD_TREND': 'v2/dashboard/trend',
    'DASHBOARD_REPORT': 'v2/dashboard/report',

    'DEU_NEW_USERS_REPORT': 'v2/report-deu-new-user',
    'DEU_NEW_USERS_TREND': 'v2/report-deu-new-user/average',
    'DEU_RETENTION_USERS_REPORT': 'v2/report-deu-retention',
    'DEU_RETENTION_USERS_TREND': 'v2/report-deu-retention/average',
    'IMP_NEW_USERS_REPORT': 'v2/report-impression-new-user',
    'IMP_NEW_USERS_TREND': 'v2/report-impression-new-user/average',
    'IMP_RETENTION_USERS_REPORT': 'v2/report-impression-retention',
    'IMP_RETENTION_USERS_TREND': 'v2/report-impression-retention/average',
    'AIPU_REPORT': 'v2/report-aipu',
    'AIPU_TREND': 'v2/report-aipu/trend',
    'IMP_FREQUENCY_REPORT': 'v2/report-impression-frequency',
    'IMP_FREQUENCY_TREND': 'v2/report-impression-frequency/trend',
    'CHART_REPORT': 'v2/report-hourly',
    'CHART_TREND': 'v2/report-hourly/trend',

    'SEGMENT_SORT_V2': 'v2/traffic/sort-segment',
    'WATERFALL_UPDATE_ECPM': 'v2/waterfall/update-ecpm',
  },
  DEFAULT_LANG: 'en-US',
  SUPPORT_LANG: [
    'en-US',
    'zh-CN',
  ],
  SHORT_LANG_MAP: {
    'en': 'en-US',
    'zh': 'zh-CN',
  },

  // 工具类应用开发者
  TOOLS_PUBLISHER_IDS: [14],

  // radio: 必须配置 DEFAULT
  // select: 可以不配置 DEFAULT, 不配置的话 DEFAULT 就是 0
  NETWORK_FORM_CONFIG: {
    0: {
      NAME: 'Custom',
      PUBLISHER: {},
      APP: {},
      UNIT: {},
    },
    1: {
      NAME: 'Facebook',
      PUBLISHER: {},
      APP: {
        app_id: {
          LABEL: 'fbAppID',
          MULTI_LANG: true,
        },
        property_id: {
          LABEL: 'fbPropertyID',
          MULTI_LANG: true,
        },
        app_token: {
          LABEL: 'fbAccessToken',
          MULTI_LANG: true,
        },
      },
      UNIT: {
        unit_id: {
          LABEL: 'fbPlacementID',
          MULTI_LANG: true,
        },
        unit_type: {
          0: {
            LABEL: 'fbUnitType0',
            TIPS: 'fbUnitTypeTips',
            TYPE: 'select',
            DEFAULT: '0',
            MULTI_LANG: true,
            OPTION: [
              {
                LABEL: 'fbUnitType0Native',
                VALUE: '0',
                MULTI_LANG: true,
              },
              {
                LABEL: 'fbUnitType0NativeBanner',
                VALUE: '1',
                MULTI_LANG: true,
              },
            ],
          },
        },
        height: {
          0: {
            LABEL: 'fbHeight',
            MULTI_LANG: true,
            TIPS: 'fbHeightTips',
            DEFAULT: '50',
            TYPE: 'select',
            OPTION: [
              {
                LABEL: '50',
                VALUE: '50',
              },
              {
                LABEL: '100',
                VALUE: '100',
              },
              {
                LABEL: '120',
                VALUE: '120',
              },
            ],
            WHERE: [
              {
                FIELD_KEY: 'unit_type',
                VALUES: ['1'],
              },
            ],
          },
        },
        size: {
          2: {
            LABEL: 'fbSize2',
            TYPE: 'select',
            MULTI_LANG: true,
            OPTION: [
              {
                LABEL: 'fbSize2Banner',
                VALUE: '320x50',
                MULTI_LANG: true,
              },
              {
                LABEL: 'fbSize2LargeBanner',
                VALUE: '320x90',
                MULTI_LANG: true,
              },
              {
                LABEL: 'fbSize2MediumRectangle',
                VALUE: '320x250',
                MULTI_LANG: true,
              },
            ],
          },
        }
        // header_bidding_switch: {
        //   0: {
        //     LABEL: 'Header Bidding',
        //     MULTI_LANG: true,
        //     TYPE: 'radio',
        //     POSITION: 'parent',
        //     MODE: 'create',
        //     SHOW: 2,
        //     DEFAULT: 1,
        //     OPTION: [
        //       {
        //         LABEL: 'Yes',
        //         VALUE: 2,
        //       },
        //       {
        //         LABEL: 'NO',
        //         VALUE: 1,
        //       }
        //     ]
        //   },
        //   1: {
        //     LABEL: 'Header Bidding',
        //     MULTI_LANG: true,
        //     TYPE: 'radio',
        //     POSITION: 'parent',
        //     MODE: 'create',
        //     SHOW: 2,
        //     DEFAULT: 1,
        //     OPTION: [
        //       {
        //         LABEL: 'Yes',
        //         VALUE: 2,
        //       },
        //       {
        //         LABEL: 'NO',
        //         VALUE: 1,
        //       }
        //     ]
        //   },
        //   2: {
        //     LABEL: 'Header Bidding',
        //     MULTI_LANG: true,
        //     TYPE: 'radio',
        //     POSITION: 'parent',
        //     MODE: 'create',
        //     SHOW: 2,
        //     DEFAULT: 1,
        //     OPTION: [
        //       {
        //         LABEL: 'Yes',
        //         VALUE: 2,
        //       },
        //       {
        //         LABEL: 'NO',
        //         VALUE: 1,
        //       }
        //     ]
        //   },
        //   3: {
        //     LABEL: 'Header Bidding',
        //     MULTI_LANG: true,
        //     TYPE: 'radio',
        //     POSITION: 'parent',
        //     MODE: 'create',
        //     SHOW: 2,
        //     DEFAULT: 1,
        //     OPTION: [
        //       {
        //         LABEL: 'Yes',
        //         VALUE: 2,
        //       },
        //       {
        //         LABEL: 'NO',
        //         VALUE: 1,
        //       }
        //     ]
        //   }
        // }
      },
    },
    2: {
      NAME: 'Admob',
      PUBLISHER: {
        account_id: {
          LABEL: 'admobPublisherID',
          MULTI_LANG: true,
        },
        oauth_key: {
          LABEL: 'admobAccessToken',
          MULTI_LANG: true,
        },
      },
      APP: {
        app_id: {
          LABEL: 'admobAppID',
          MULTI_LANG: true,
        },
      },
      UNIT: {
        unit_id: {
          LABEL: 'admobUnitID',
          MULTI_LANG: true,
        },
        size: {
          2: {
            LABEL: 'admobSize2',
            TYPE: 'select',
            MULTI_LANG: true,
            OPTION: [
              {
                LABEL: 'admobSize2Banner',
                VALUE: '320x50',
                MULTI_LANG: true,
              },
              {
                LABEL: 'admobSize2LargeBanner',
                VALUE: '320x100',
                MULTI_LANG: true,
              },
              {
                LABEL: 'admobSize2MediumRectangle',
                VALUE: '300x250',
                MULTI_LANG: true,
              },
              {
                LABEL: 'admobSize2FullSizeBanner',
                VALUE: '468x60',
                MULTI_LANG: true,
              },
              {
                LABEL: 'admobSize2LeaderBoard',
                VALUE: '728x90',
                MULTI_LANG: true,
              },
            ],
          },
        },
        media_ratio: {
          0: {
            LABEL: 'admobMediaAspectRatio',
            TYPE: 'select',
            TIPS: 'tips4AdmobMediaAspectRatio',
            MULTI_LANG: true,
            DEFAULT: 0,
            OPTION: [
              {
                LABEL: 'admobMediaAspectRatioUnknown',
                VALUE: '0',
              },
              {
                LABEL: 'admobMediaAspectRatioAny',
                VALUE: '1',
              },
              {
                LABEL: 'admobMediaAspectRatioLandscape',
                VALUE: '2',
              },
              {
                LABEL: 'admobMediaAspectRatioPortrait',
                VALUE: '3',
              },
              {
                LABEL: 'admobMediaAspectRatioSquare',
                VALUE: '4',
              }
            ],
          },
        },
        orientation: {
          4: {
            LABEL: 'admobSplashOrientation',
            TIPS: 'admobSplashOrientationTips',
            TYPE: 'select',
            MULTI_LANG: true,
            DEFAULT: 1,
            OPTION: [
              {
                LABEL: 'admobSplashOrientationPortrait',
                VALUE: 1,
                MULTI_LANG: true,
              },
              {
                LABEL: 'admobSplashOrientationLandscape',
                VALUE: 2,
                MULTI_LANG: true,
              },
            ],
          },
        }
      },
    },
    3: {
      NAME: 'Inmobi',
      PUBLISHER: {
        username: {
          LABEL: 'inmobiEmailID',
          MULTI_LANG: true,
        },
        app_id: {
          LABEL: 'Account ID',
          UNIT: true,
        },
        password: {
          LABEL: 'inmobiPassword',
          MULTI_LANG: true,
          INPUT_TYPE: 'password',
        },
        apikey: {
          LABEL: 'API Key',
        },
      },
      APP: {},
      UNIT: {
        unit_id: {
          LABEL: 'Placement ID',
        },
        size: {
          2: {
            LABEL: 'inmobiSize2',
            TYPE: 'select',
            MULTI_LANG: true,
            OPTION: [
              {
                LABEL: 'inmobiSize2Banner',
                VALUE: '320x50',
                MULTI_LANG: true,
              },
            ],
          },
        },
      },
    },
    4: {
      NAME: 'Flurry',
      PUBLISHER: {
        token: {
          LABEL: 'Token',
        },
      },
      APP: {
        sdk_key: {
          LABEL: 'API Key',
        },
      },
      UNIT: {
        ad_space: {
          LABEL: 'AD Unit Name',
        },
        size: {
          2: {
            LABEL: 'flurrySize2',
            TYPE: 'select',
            MULTI_LANG: true,
            OPTION: [
              {
                LABEL: 'flurrySize2Banner',
                VALUE: '320x50',
                MULTI_LANG: true,
              },
            ],
          },
        },
      },
    },
    5: {
      NAME: 'Applovin',
      PUBLISHER: {
        sdkkey: {
          LABEL: 'SDK Key',
          UNIT: true,
        },
        apikey: {
          LABEL: 'Report Key',
        },
      },
      APP: {},
      UNIT: {
        zone_id: {
          LABEL: 'Zone ID',
        },
        size: {
          2: {
            LABEL: 'applovinSize2',
            TYPE: 'select',
            MULTI_LANG: true,
            OPTION: [
              {
                LABEL: 'applovinSize2Banner',
                VALUE: '320x50',
                MULTI_LANG: true,
              },
              {
                LABEL: 'applovinSize2MediumRectangle',
                VALUE: '300x250',
                MULTI_LANG: true,
              },
            ],
          },
        },
      },
    },
    6: {
      NAME: 'Mintegral',
      PUBLISHER: {
        appkey: {
          LABEL: 'App Key',
          UNIT: true,
        },
        skey: {
          LABEL: 'Skey',
        },
        secret: {
          LABEL: 'mtgSecret',
          MULTI_LANG: true,
        },
      },
      APP: {
        appid: {
          LABEL: 'mtgAppID',
          MULTI_LANG: true,
        },
      },
      UNIT: {
        placement_id: {
          LABEL: 'mtgPlacementID',
          OPTIONAL: true,
          TIPS: 'mtgPlacementIDTips',
          MULTI_LANG: true,
        },
        unitid: {
          LABEL: 'mtgUnitID',
          TIPS: 'mtgUnitIDTips',
          MULTI_LANG: true,
        },
        is_video: {
          3: {
            LABEL: 'mtgIsVideo3',
            TYPE: 'select',
            MULTI_LANG: true,
            EDIT_DISABLE: true,
            OPTION: [{
              LABEL: 'mtgIsVideo3InterstitialImage',
              VALUE: '0',
              MULTI_LANG: true,
            },
              {
                LABEL: 'mtgIsVideo3InterstitialVideo',
                VALUE: '1',
                MULTI_LANG: true,
              },
            ]
          },
        },
        unit_type: {
          0: {
            LABEL: 'mtgUnitType0',
            TIPS: 'mtgUnitTypeTips',
            TYPE: 'select',
            DEFAULT: '0',
            MULTI_LANG: true,
            OPTION: [
              {
                LABEL: 'mtgUnitType0CustomRendering',
                VALUE: '0',
                MULTI_LANG: true,
              },
              {
                LABEL: 'mtgUnitType0AutoRendering',
                VALUE: '1',
                MULTI_LANG: true,
              },
            ],
          },
        },
        video_autoplay: {
          0: {
            LABEL: 'mtgVideoAutoplay',
            MULTI_LANG: true,
            TIPS: 'mtgVideoAutoplayTips',
            TYPE: 'radio',
            DEFAULT: '3',
            OPTION: [
              {
                LABEL: 'always',
                VALUE: '3',
              },
              {
                LABEL: 'WiFi',
                VALUE: '1',
              },
              {
                LABEL: 'manualClick',
                VALUE: '2',
              },
            ],
            WHERE: [
              {
                FIELD_KEY: 'unit_type',
                VALUES: ['1'],
              },
            ],
          },
        },
        video_muted: {
          0: {
            LABEL: 'mtgVideoMuted',
            MULTI_LANG: true,
            TIPS: 'mtgVideoMutedTips',
            TYPE: 'radio',
            DEFAULT: '0',
            OPTION: [
              {
                LABEL: 'Yes',
                VALUE: '0',
              },
              {
                LABEL: 'NO',
                VALUE: '1',
              },
            ],
            WHERE: [
              {
                FIELD_KEY: 'unit_type',
                VALUES: ['1'],
              },
            ],
          },
        },
        close_button: {
          0: {
            LABEL: 'mtgCloseBtn',
            MULTI_LANG: true,
            TIPS: 'mtgCloseBtnTips',
            TYPE: 'radio',
            DEFAULT: '0',
            OPTION: [
              {
                LABEL: 'Show',
                VALUE: '0',
              },
              {
                LABEL: 'Hide',
                VALUE: '1',
              },
            ],
            WHERE: [
              {
                FIELD_KEY: 'unit_type',
                VALUES: ['1'],
              },
            ],
          },
        },
        header_bidding_switch: {
          0: {
            LABEL: 'Header Bidding',
            MULTI_LANG: true,
            TYPE: 'radio',
            POSITION: 'parent',
            MODE: 'create',
            SHOW: 2,
            DEFAULT: 1,
            OPTION: [
              {
                LABEL: 'Yes',
                VALUE: 2,
              },
              {
                LABEL: 'NO',
                VALUE: 1,
              },
            ],
            WHERE: [
              {
                FIELD_KEY: 'unit_type',
                VALUES: ['0'],
              },
            ],
          },
          1: {
            LABEL: 'Header Bidding',
            MULTI_LANG: true,
            TYPE: 'radio',
            POSITION: 'parent',
            MODE: 'create',
            SHOW: 2,
            DEFAULT: 1,
            OPTION: [
              {
                LABEL: 'Yes',
                VALUE: 2,
              },
              {
                LABEL: 'NO',
                VALUE: 1,
              },
            ],
          },
          2: {
            LABEL: 'Header Bidding',
            MULTI_LANG: true,
            TYPE: 'radio',
            POSITION: 'parent',
            MODE: 'create',
            SHOW: 2,
            DEFAULT: 1,
            OPTION: [
              {
                LABEL: 'Yes',
                VALUE: 2,
              },
              {
                LABEL: 'NO',
                VALUE: 1,
              },
            ],
          },
          3: {
            LABEL: 'Header Bidding',
            MULTI_LANG: true,
            TYPE: 'radio',
            POSITION: 'parent',
            MODE: 'create',
            SHOW: 2,
            DEFAULT: 1,
            OPTION: [
              {
                LABEL: 'Yes',
                VALUE: 2,
              },
              {
                LABEL: 'NO',
                VALUE: 1,
              },
            ],
            WHERE: [
              {
                FIELD_KEY: 'is_video',
                VALUES: ['1'],
              },
            ],
          },
        },
        size: {
          2: {
            LABEL: 'mtgSize2',
            TYPE: 'select',
            MULTI_LANG: true,
            OPTION: [
              {
                LABEL: 'mtgSize2Banner',
                VALUE: '320x50',
                MULTI_LANG: true,
              },
              {
                LABEL: 'mtgSize2MediumRectangle',
                VALUE: '320x90',
                MULTI_LANG: true,
              },
              {
                LABEL: 'mtgSize2LargeBanner',
                VALUE: '300x250',
                MULTI_LANG: true,
              },
              {
                LABEL: 'mtgSize2SmartBanner',
                VALUE: 'smart',
                MULTI_LANG: true,
              },
            ],
          },
        },
        countdown: {
          4: {
            LABEL: 'mtgCountDown',
            TIPS: 'mtgCountDownTips',
            MULTI_LANG: true,
            DEFAULT: '5',
          },
        },
        allows_skip: {
          4: {
            LABEL: 'mtgAllowSkip',
            TIPS: 'mtgAllowSkipTips',
            MULTI_LANG: true,
            DEFAULT: '0',
            TYPE: 'radio',
            OPTION: [
              {
                LABEL: 'Yes',
                VALUE: '1',
              },
              {
                LABEL: 'NO',
                VALUE: '0',
              },
            ],
          },
        },
        orientation: {
          4: {
            LABEL: 'mtgOrientation4',
            MULTI_LANG: true,
            DEFAULT: '1',
            TYPE: 'select',
            OPTION: [
              {
                LABEL: 'mtgOrientation4Portrait',
                VALUE: '1',
                MULTI_LANG: true,
              },
              {
                LABEL: 'mtgOrientation4Landscape',
                VALUE: '2',
                MULTI_LANG: true,
              },
            ],
          },
        },
      },
    },
    7: {
      NAME: 'Mopub',
      PUBLISHER: {
        repkey: {
          LABEL: 'Inventory Report ID',
        },
        apikey: {
          LABEL: 'API Key',
        },
      },
      APP: {},
      UNIT: {
        unitid: {
          LABEL: 'Unit ID'
        },
        size: {
          2: {
            LABEL: 'mopubSize2',
            TYPE: 'select',
            MULTI_LANG: true,
            OPTION: [
              {
                LABEL: 'mopubSize2Banner',
                VALUE: '320x50',
                MULTI_LANG: true,
              },
              {
                LABEL: 'mopubSize2MediumRectangle',
                VALUE: '300x250',
                MULTI_LANG: true,
              },
              {
                LABEL: 'mopubSize2Leaderboard',
                VALUE: '728x90',
                MULTI_LANG: true,
              },
            ],
          },
        },
      },
    },
    8: {
      NAME: '广点通',
      PUBLISHER: {
        api_version: {
          LABEL: 'gdtAPIVersion',
          MULTI_LANG: true,
          TYPE: 'radio',
          IS_PARENT: true,
          TIPS: 'gdtAPIVersionTips',
          DEFAULT: 2,
          OPTION: [
            {
              LABEL: 'gdtAPIVersionOld',
              VALUE: 1,
            },
            {
              LABEL: 'gdtAPIVersionNew',
              VALUE: 2,
            },
          ],
        },
        qq: {
          LABEL: 'gdtQQ',
          MULTI_LANG: true,
          WHERE: [{ FIELD_KEY: 'api_version', VALUES: [1] }],
        },
        agid: {
          LABEL: 'AGID',
          WHERE: [{ FIELD_KEY: 'api_version', VALUES: [1] }],
        },
        publisher_id: {
          LABEL: 'App ID',
          WHERE: [{ FIELD_KEY: 'api_version', VALUES: [1] }],
        },
        app_key: {
          LABEL: 'App Key',
          WHERE: [{ FIELD_KEY: 'api_version', VALUES: [1] }],
        },
        account_id: {
          LABEL: 'gdtAccountID',
          MULTI_LANG: true,
          WHERE: [{ FIELD_KEY: 'api_version', VALUES: [2] }],
        },
        secret_key: {
          LABEL: 'gdtSecretKey',
          MULTI_LANG: true,
          WHERE: [{ FIELD_KEY: 'api_version', VALUES: [2] }],
        },
      },
      APP: {
        app_id: {
          LABEL: 'gdtAppID',
          MULTI_LANG: true,
        },
      },
      UNIT: {
        unit_id: {
          LABEL: 'gdtUnitID',
          MULTI_LANG: true,
        },
        unit_type: {
          0: {
            LABEL: 'gdtUnitType0',
            TYPE: 'select',
            DEFAULT: '1',
            MULTI_LANG: true,
            TIPS: 'tips4GdtUnitType0',
            OPTION: [
              {
                LABEL: 'Setup By SDK API',
                VALUE: '0',
                CREATE_HIDE: true,
                EDIT_REMOVE: [{
                  FIELD_KEY: 'unit_type',
                  VALUES: ['1', '2'],
                }],
              },
              {
                LABEL: 'gdtUnitType0Temple',
                VALUE: '1',
                MULTI_LANG: true,
              },
              {
                LABEL: 'gdtUnitType0SelfRendering',
                VALUE: '2',
                MULTI_LANG: true,
              }
            ]
          },
        },
        unit_version: {
          0: {
            LABEL: 'gdtUnitVersion0',
            TYPE: 'select',
            DEFAULT: '1',
            MULTI_LANG: true,
            TIPS: 'tips4GdtUnitVersion0',
            OPTION: [
              {
                LABEL: 'gdtUnitVersion0v1_0',
                VALUE: '1',
                MULTI_LANG: true,
              },
              {
                LABEL: 'gdtUnitVersion0v2_0',
                VALUE: '2',
                MULTI_LANG: true,
              },
            ],
            CREATE_WHERE: [
              {
                FIELD_KEY: 'unit_type',
                VALUES: ['1'],
              },
            ],
          },
          2: {
            LABEL: 'gdtUnitVersion2',
            TYPE: 'select',
            DEFAULT: '2',
            MULTI_LANG: true,
            OPTION: [
              {
                LABEL: 'gdtUnitVersion2v1_0',
                VALUE: '1',
                CREATE_HIDE: true,
                MULTI_LANG: true,
                EDIT_REMOVE: [{
                  FIELD_KEY: 'unit_version',
                  VALUES: ['2'],
                }],
              },
              {
                LABEL: 'gdtUnitVersion2v2_0',
                VALUE: '2',
                MULTI_LANG: true,
              },
            ]
          },
          3: {
            LABEL: 'gdtUnitVersion3',
            TYPE: 'select',
            DEFAULT: '2',
            MULTI_LANG: true,
            OPTION: [
              {
                LABEL: 'gdtUnitVersion3v1_0',
                VALUE: '1',
                CREATE_HIDE: true,
                MULTI_LANG: true,
                EDIT_REMOVE: [{
                  FIELD_KEY: 'unit_version',
                  VALUES: ['2'],
                }],
              },
              {
                LABEL: 'gdtUnitVersion3v2_0',
                VALUE: '2',
                MULTI_LANG: true,
              },
            ]
          },
        },
        size: {
          2: {
            LABEL: 'gdtSize2',
            TYPE: 'select',
            DEFAULT: '320x50',
            MULTI_LANG: true,
            OPTION: [
              {
                LABEL: 'gdtSize2Banner',
                VALUE: '320x50',
                MULTI_LANG: true,
              },
            ],
          },
        },
        video_muted: {
          0: {
            LABEL: 'videoMuted',
            MULTI_LANG: true,
            TYPE: 'radio',
            DEFAULT: 1,
            TIPS: 'tips4GdtVideoMuted0',
            OPTION: [
              {
                LABEL: 'Yes',
                VALUE: '1',
              },
              {
                LABEL: 'NO',
                VALUE: '0',
              },
            ],
          },
          3: {
            LABEL: 'videoMuted',
            MULTI_LANG: true,
            TYPE: 'radio',
            DEFAULT: 1,
            TIPS: 'tips4VideoMuted',
            WHERE: [
              {
                FIELD_KEY: 'unit_version',
                VALUES: ['2'],
              },
            ],
            OPTION: [
              {
                LABEL: 'Yes',
                VALUE: '1',
              },
              {
                LABEL: 'NO',
                VALUE: '0',
              },
            ],
          },
        },
        video_autoplay: {
          0: {
            LABEL: 'videoAutoplay',
            MULTI_LANG: true,
            TYPE: 'radio',
            DEFAULT: 1,
            TIPS: 'tips4GdtVideoAutoplay0',
            OPTION: [
              {
                LABEL: 'always',
                VALUE: '1',
              },
              {
                LABEL: 'WiFi',
                VALUE: '0',
              },
              {
                LABEL: 'gdtVideoAutoplay0Never',
                VALUE: '2',
              },
            ],
          },
          3: {
            LABEL: 'videoAutoplay',
            MULTI_LANG: true,
            TYPE: 'radio',
            DEFAULT: 1,
            TIPS: 'tips4VideoAutoplay',
            WHERE: [
              {
                FIELD_KEY: 'unit_version',
                VALUES: ['2'],
              },
            ],
            OPTION: [
              {
                LABEL: 'always',
                VALUE: '1',
              },
              {
                LABEL: 'WiFi',
                VALUE: '0',
              },
              {
                LABEL: 'gdtVideoAutoplay0Never',
                VALUE: '2',
              },
            ],
          },
        },
        video_duration_switch: {
          0: {
            LABEL: 'videoDuration',
            MULTI_LANG: true,
            TYPE: 'radio',
            DEFAULT: '1',
            TIPS: 'tips4GdtVideoDuration0',
            OPTION: [
              {
                LABEL: 'notLimitedTime',
                VALUE: '1',
              },
              {
                LABEL: 'specifiedTime',
                VALUE: '2',
              },
            ],
          },
          3: {
            LABEL: 'videoDuration',
            MULTI_LANG: true,
            TYPE: 'radio',
            DEFAULT: '1',
            TIPS: 'tips4VideoDuration',
            WHERE: [
              {
                FIELD_KEY: 'unit_version',
                VALUES: ['2'],
              },
            ],
            OPTION: [
              {
                LABEL: 'notLimitedTime',
                VALUE: '1',
              },
              {
                LABEL: 'specifiedTime',
                VALUE: '2',
              },
            ],
          },
        },
        video_duration: {
          0: {
            LABEL: 'availableTime',
            MULTI_LANG: true,
            TYPE: 'text',
            DEFAULT: '60',
            WHERE: [
              {
                FIELD_KEY: 'video_duration_switch',
                VALUES: ['2'],
              },
            ],
          },
          3: {
            LABEL: 'availableTime',
            MULTI_LANG: true,
            TYPE: 'text',
            DEFAULT: '60',
            WHERE: [
              {
                FIELD_KEY: 'video_duration_switch',
                VALUES: ['2'],
              },
            ],
          },
        },
        is_fullscreen: {
          3: {
            LABEL: 'gdtIsFullScreen',
            MULTI_LANG: true,
            TYPE: 'radio',
            DEFAULT: '',
            TIPS: 'tips4IsFullScreenVideo',
            OPTION: [
              {
                LABEL: 'Yes',
                VALUE: '1',
              },
              {
                LABEL: 'NO',
                VALUE: '0',
              },
            ],
          },
        },
      },
    },
    9: {
      NAME: 'Chartboost',
      PUBLISHER: {
        user_id: {
          LABEL: 'chartboostUserID',
          MULTI_LANG: true,
        },
        user_signature: {
          LABEL: 'chartboostUserSignature',
          MULTI_LANG: true,
        },
      },
      APP: {
        app_id: {
          LABEL: 'chartboostUserAppID',
          MULTI_LANG: true,
        },
        app_signature: {
          LABEL: 'chartboostAppSignature',
          MULTI_LANG: true,
        },
      },
      UNIT: {
        location: {
          1: {
            LABEL: 'chartboostLocation',
            MULTI_LANG: true,
          },
          2: {
            LABEL: 'chartboostLocation',
            MULTI_LANG: true,
            TYPE: 'select',
            DEFAULT: 'Default',
            OPTION: [
              {
                LABEL: 'Default',
                VALUE: 'Default',
              },
              {
                LABEL: 'Startup',
                VALUE: 'Startup',
              },
              {
                LABEL: 'Home Screen',
                VALUE: 'Home Screen',
              },
              {
                LABEL: 'Main Menu',
                VALUE: 'Main Menu',
              },
              {
                LABEL: 'Game Screen',
                VALUE: 'Game Screen',
              },
              {
                LABEL: 'Achievements',
                VALUE: 'Achievements',
              },
              {
                LABEL: 'Quests',
                VALUE: 'Quests',
              },
              {
                LABEL: 'Pause',
                VALUE: 'Pause',
              },
              {
                LABEL: 'Level Start',
                VALUE: 'Level Start',
              },
              {
                LABEL: 'Level Complete',
                VALUE: 'Level Complete',
              },
              {
                LABEL: 'Turn Complete',
                VALUE: 'Turn Complete',
              },
              {
                LABEL: 'IAP Store',
                VALUE: 'IAP Store',
              },
              {
                LABEL: 'Item Store',
                VALUE: 'Item Store',
              },
              {
                LABEL: 'Game Over',
                VALUE: 'Game Over',
              },
              {
                LABEL: 'Leaderboard',
                VALUE: 'Leaderboard',
              },
              {
                LABEL: 'Settings',
                VALUE: 'Settings',
              },
              {
                LABEL: 'Quit',
                VALUE: 'Quit',
              },
            ],
          },
          3: {
            LABEL: 'chartboostLocation',
            MULTI_LANG: true,
          },
        },
        size: {
          2: {
            LABEL: 'chartboostSize2',
            TYPE: 'select',
            TIPS: 'chartboostSizeTips',
            DEFAULT: '320x50',
            MULTI_LANG: true,
            OPTION: [
              {
                LABEL: 'chartboostSize2Banner320_50',
                VALUE: '320x50',
                MULTI_LANG: true,
              },
              {
                LABEL: 'chartboostSize2Banner300_250',
                VALUE: '300x250',
                MULTI_LANG: true,
              },
              {
                LABEL: 'chartboostSize2Banner728_90',
                VALUE: '728x90',
                MULTI_LANG: true,
              },
            ],
          },
        },
      },
    },
    10: {
      NAME: 'Tapjoy',
      PUBLISHER: {
        apikey: {
          LABEL: 'tapjoyAPIKey',
          MULTI_LANG: true,
        },
      },
      APP: {
        sdk_key: {
          LABEL: 'tapjoySDKKey',
          MULTI_LANG: true,
        },
      },
      UNIT: {
        placement_name: {
          LABEL: 'tapjoyPlacementName',
          MULTI_LANG: true,
        },
      },
    },
    11: {
      NAME: 'Ironsource',
      PUBLISHER: {
        username: {
          LABEL: 'Username',
        },
        secret_key: {
          LABEL: 'Secret Key',
        },
      },
      APP: {
        app_key: {
          LABEL: 'App Key',
        },
      },
      UNIT: {
        instance_id: {
          LABEL: 'Instance ID',
          TIPS: 'Tips4InstanceID',
        },
      },
    },
    12: {
      NAME: 'Unity AD',
      PUBLISHER: {
        apikey: {
          LABEL: 'API Key',
        },
        organization_core_id: {
          LABEL: 'Organization core ID',
        },
      },
      APP: {
        game_id: {
          LABEL: 'Game ID',
        },
      },
      UNIT: {
        placement_id: {
          LABEL: 'Placement ID',
        },
        size: {
          2: {
            LABEL: 'unitySizeBanner',
            TIPS: 'unitySizeBannerTips',
            TYPE: 'select',
            MULTI_LANG: true,
            DEFAULT: '320x50',
            OPTION: [
              {
                LABEL: '320x50',
                VALUE: '320x50',
              },
              {
                LABEL: '468x60',
                VALUE: '468x60',
              },
              {
                LABEL: '728x90',
                VALUE: '728x90',
              },
            ],
          },
        },
      },
    },
    13: {
      NAME: 'Vungle',
      PUBLISHER: {
        apikey: {
          LABEL: 'Reporting API Key',
        },
      },
      APP: {
        app_id: {
          LABEL: 'App ID',
        },
      },
      UNIT: {
        placement_id: {
          LABEL: 'vunglePlacementID',
          MULTI_LANG: true,
        },
        unit_type: {
          2: {
            LABEL: 'vungleUnitType2',
            TIPS: 'vungleUnitTypeTips',
            TYPE: 'select',
            MULTI_LANG: true,
            DEFAULT: '0',
            OPTION: [
              {
                LABEL: 'vungleUnitType2Banner',
                VALUE: '0',
                MULTI_LANG: true,
              },
              {
                LABEL: 'vungleUnitType2MREC',
                VALUE: '1',
                MULTI_LANG: true,
              },
            ],
          },
        },
        size_type: {
          2: {
            LABEL: 'vungleSizeType2',
            TYPE: 'select',
            MULTI_LANG: true,
            OPTION: [
              {
                LABEL: '320x50',
                VALUE: '2',
              },
              {
                LABEL: '300x50',
                VALUE: '3',
              },
              {
                LABEL: '728x90',
                VALUE: '4',
              },
            ],
            WHERE: [
              {
                FIELD_KEY: 'unit_type',
                VALUES: ['0'],
              },
            ],
          },
        },
      },
    },
    14: {
      NAME: 'Adcolony',
      PUBLISHER: {
        user_credentials: {
          LABEL: 'Read-Only API key',
        },
      },
      APP: {
        app_id: {
          LABEL: 'App ID',
        },
      },
      UNIT: {
        zone_id: {
          LABEL: 'Zone ID',
        },
        size: {
          2: {
            LABEL: 'adcolonySize2',
            TIPS: 'adcolonySizeTips',
            DEFAULT: '320x50',
            TYPE: 'select',
            MULTI_LANG: true,
            OPTION: [
              {
                LABEL: 'adcolonySize2Banner320_50',
                VALUE: '320x50',
                MULTI_LANG: true,
              },
              {
                LABEL: 'adcolonySize2Banner300_250',
                VALUE: '300x250',
                MULTI_LANG: true,
              },
              {
                LABEL: 'adcolonySize2Banner728_90',
                VALUE: '728x90',
                MULTI_LANG: true,
              },
              {
                LABEL: 'adcolonySize2Banner160_600',
                VALUE: '160x600',
                MULTI_LANG: true,
              },
            ],
          },
        },
      },
    },
    15: {
      NAME: '穿山甲',
      PUBLISHER: {
        user_id: {
          LABEL: 'ttUserID',
          MULTI_LANG: true,
        },
        secure_key: {
          LABEL: 'Secure Key',
        },
      },
      APP: {
        app_id: {
          LABEL: 'ttAppID',
          MULTI_LANG: true,
        },
      },
      UNIT: {
        slot_id: {
          LABEL: 'ttSlotID',
          MULTI_LANG: true,
        },
        is_video: {
          0: {
            LABEL: 'ttIsVideo0',
            TIPS: 'tips4UnitType',
            TYPE: 'select',
            DEFAULT: '0',
            MULTI_LANG: true,
            OPTION: [
              {
                LABEL: 'Native Feeds',
                VALUE: '0',
              },
              {
                LABEL: 'ttNativeDrawVideo',
                VALUE: '1',
              },
              {
                LABEL: 'Banner Native (Self-rendering)',
                VALUE: '2',
              },
              {
                LABEL: 'Interstitial Native (Self-rendering)',
                VALUE: '3',
              },
            ]
          },
          3: {
            LABEL: 'ttIsVideo3',
            TYPE: 'select',
            MULTI_LANG: true,
            SELECT_DISABLE: [
              {
                FIELD_KEY: 'layout_type',
                VALUE: '0',
              }
            ],
            OPTION: [{
              LABEL: 'ttIsVideo3InterstitialImage',
              VALUE: '0',
              MULTI_LANG: true,
            },
            {
              LABEL: 'ttIsVideo3InterstitialVideo',
              VALUE: '1',
              MULTI_LANG: true,
            },
            ]
          },
        },
        layout_type: {
          0: {
            LABEL: 'ttLayoutType0',
            TIPS: 'tips4NativeType',
            MULTI_LANG: true,
            TYPE: 'select',
            DEFAULT: '0',
            OPTION: [
              {
                LABEL: 'ttLayoutType0Template',
                VALUE: '0',
                MULTI_LANG: true,
              },
              {
                LABEL: 'ttLayoutType0Self',
                VALUE: '1',
                MULTI_LANG: true,
              },
            ],
            WHERE: [
              {
                FIELD_KEY: 'is_video',
                VALUES: ['0'],
              },
            ],
          },
          2: {
            LABEL: 'ttLayoutType2',
            TIPS: 'tips4LayoutType',
            MULTI_LANG: true,
            TYPE: 'select',
            DEFAULT: '1',
            SELECT_DISABLE: [
              {
                FIELD_KEY: 'layout_type',
                VALUE: '0',
              }
            ],
            OPTION: [{
                LABEL: 'ttLayoutType2Native',
                VALUE: '1',
                MULTI_LANG: true,
              },
              {
                LABEL: 'Template',
                VALUE: '0',
                CREATE_HIDE: true,
                EDIT_REMOVE: [{
                  FIELD_KEY: 'layout_type',
                  VALUES: ['1'],
                }],
              },
            ]
          },
          3: {
            LABEL: 'ttLayoutType3',
            TIPS: 'tips4LayoutType',
            MULTI_LANG: true,
            TYPE: 'select',
            DEFAULT: '1',
            SELECT_DISABLE: [
              {
                FIELD_KEY: 'layout_type',
                VALUE: '0',
              }
            ],
            OPTION: [
              {
                LABEL: 'ttLayoutType3Native',
                VALUE: '1',
                MULTI_LANG: true,
              },
              {
                LABEL: 'Template',
                VALUE: '0',
                CREATE_HIDE: true,
                EDIT_REMOVE: [{
                  FIELD_KEY: 'layout_type',
                  VALUES: ['1'],
                }],
              },
            ],
            WHERE: [
              {
                FIELD_KEY: 'is_video',
                VALUES: ['0'],
              },
            ],
          },
        },
        personalized_template: {
          // 0: {
          //   LABEL: 'ttPersonalizedTemplate',
          //   MULTI_LANG: true,
          //   TIPS: 'tips4IVPersonalizedTemplate',
          //   TYPE: 'radio',
          //   DEFAULT: '',
          //   WHERE: [
          //     {
          //       FIELD_KEY: 'is_video',
          //       VALUES: ['1'],
          //     },
          //   ],
          //   OPTION: [
          //     {
          //       LABEL: 'Yes',
          //       VALUE: '1',
          //     },
          //     {
          //       LABEL: 'NO',
          //       VALUE: '0',
          //     },
          //   ],
          // },
          1: {
            LABEL: 'ttPersonalizedTemplate',
            MULTI_LANG: true,
            TYPE: 'radio',
            DEFAULT: '',
            TIPS: 'tips4RVPersonalizedTemplate',
            OPTION: [
              {
                LABEL: 'Yes',
                VALUE: '1',
              },
              {
                LABEL: 'NO',
                VALUE: '0',
              },
            ],
          },
          3: {
            LABEL: 'ttPersonalizedTemplate',
            MULTI_LANG: true,
            TIPS: 'tips4IVPersonalizedTemplate',
            TYPE: 'radio',
            DEFAULT: '',
            WHERE: [
              {
                FIELD_KEY: 'is_video',
                VALUES: ['1'],
              },
            ],
            OPTION: [
              {
                LABEL: 'Yes',
                VALUE: '1',
              },
              {
                LABEL: 'NO',
                VALUE: '0',
              },
            ],
          },
          4: {
            LABEL: 'ttPersonalizedTemplate',
            MULTI_LANG: true,
            TYPE: 'radio',
            DEFAULT: '',
            TIPS: 'tips4SplashPersonalizedTemplate',
            OPTION: [
              {
                LABEL: 'Yes',
                VALUE: '1',
              },
              {
                LABEL: 'NO',
                VALUE: '0',
              },
            ],
          },
        },
        zoomoutad_sw: {
          4: {
            LABEL: 'openingTheScreen',
            MULTI_LANG: true,
            TYPE: 'radio',
            DEFAULT: '1',
            TIPS: 'tips4OpeningTheScreen',
            OPTION: [
              {
                LABEL: 'Yes',
                VALUE: '2',
              },
              {
                LABEL: 'NO',
                VALUE: '1',
              },
            ],
          }
        },
        media_size: {
          0: {
            LABEL: 'ttMediaSize0',
            TYPE: 'select',
            DEFAULT: '1',
            MULTI_LANG: true,
            OPTION: [
              {
                LABEL: 'Setup By SDK API',
                VALUE: '0',
                CREATE_HIDE: true,
                EDIT_REMOVE: [{
                  FIELD_KEY: 'media_size',
                  VALUES: ['1', '2'],
                }],
              },
              {
                LABEL: '690*388px',
                VALUE: '1',
              },
              {
                LABEL: '228*150px',
                VALUE: '2',
              }
            ],
            WHERE: [
              {
                FIELD_KEY: 'is_video',
                VALUES: ['2', '3'],
              },
              {
                FIELD_KEY: 'layout_type',
                VALUES: ['1'],
              },
              {
                FIELD_KEY: 'personalized_template',
                VALUES: ['0'],
              },
            ],
          },
          // 2: {
          //   LABEL: 'Media Size',
          //   TYPE: 'select',
          //   DEFAULT: '0',
          //   OPTION: [
          //     {
          //       LABEL: '600x90',
          //       VALUE: '0',
          //     },
          //     {
          //       LABEL: '600x100',
          //       VALUE: '1',
          //     },
          //     {
          //       LABEL: '600x150',
          //       VALUE: '2',
          //     },
          //     {
          //       LABEL: '600x260',
          //       VALUE: '3',
          //     },
          //     {
          //       LABEL: '600x286',
          //       VALUE: '4',
          //     },
          //     {
          //       LABEL: '600x300',
          //       VALUE: '5',
          //     },
          //     {
          //       LABEL: '600x388',
          //       VALUE: '6',
          //     },
          //     {
          //       LABEL: '600x400',
          //       VALUE: '7',
          //     },
          //     {
          //       LABEL: '600x500',
          //       VALUE: '8',
          //     },
          //   ],
          // },
          // 3: {
          //   LABEL: 'Media Size',
          //   TYPE: 'select',
          //   DEFAULT: '13',
          //   OPTION: [
          //     {
          //       LABEL: '600x400',
          //       VALUE: '11',
          //     },
          //     {
          //       LABEL: '600x600',
          //       VALUE: '12',
          //     },
          //     {
          //       LABEL: '600x900',
          //       VALUE: '13',
          //     }
          //   ],
          //   WHERE: [
          //     {
          //       FIELD_KEY: 'is_video',
          //       VALUES: ['0'],
          //     },
          //   ],
          // },
        },
        size: {
          2: {
            LABEL: 'ttSize2',
            TYPE: 'select',
            MULTI_LANG: true,
            DEFAULT: '600x90',
            OPTION: [
              {
                LABEL: '640x100',
                VALUE: '640x100',
              },
              {
                LABEL: '600x90',
                VALUE: '600x90',
              },
              {
                LABEL: '600x150',
                VALUE: '600x150',
              },
              {
                LABEL: '600x500',
                VALUE: '600x500',
              },
              {
                LABEL: '600x400',
                VALUE: '600x400',
              },
              {
                LABEL: '600x300',
                VALUE: '600x300',
              },
              {
                LABEL: '600x260',
                VALUE: '600x260',
              },
              {
                LABEL: '690x388',
                VALUE: '690x388',
              },
            ],
            // WHERE: [
            //   {
            //     FIELD_KEY: 'layout_type',
            //     VALUES: ['0'],
            //   },
            // ],
          },
          3: {
            LABEL: 'AD Source Size',
            TYPE: 'select',
            MULTI_LANG: true,
            DEFAULT: '2:3',
            OPTION: [
              {
                LABEL: '1:1',
                VALUE: '1:1',
              },
              {
                LABEL: '3:2',
                VALUE: '3:2',
              },
              {
                LABEL: '2:3',
                VALUE: '2:3',
              },
            ],
            WHERE: [
              {
                FIELD_KEY: 'is_video',
                VALUES: ['0'],
              },
            ],
          },
        },
      },
    },
    16: {
      NAME: '玩转互联',
      PUBLISHER: {},
      APP: {},
      UNIT: {
        app_id: {
          LABEL: 'App ID',
        },
        size: {
          2: {
            LABEL: 'uniplaySize2',
            TYPE: 'select',
            MULTI_LANG: true,
            OPTION: [
              {
                LABEL: 'uniplaySize2Banner320_50',
                VALUE: '320x50',
                MULTI_LANG: true,
              },
              {
                LABEL: 'uniplaySize2Banner480_75',
                VALUE: '480x75',
                MULTI_LANG: true,
              },
              {
                LABEL: 'uniplaySize2Banner640_100',
                VALUE: '640x100',
                MULTI_LANG: true,
              },
              {
                LABEL: 'uniplaySize2Banner960_150',
                VALUE: '960x150',
                MULTI_LANG: true,
              },
              {
                LABEL: 'uniplaySize2Leaderboard728_90',
                VALUE: '728x90',
                MULTI_LANG: true,
              },
            ],
          },
        },
      },
    },
    17: {
      NAME: 'Oneway',
      PUBLISHER: {
        access_key: {
          LABEL: 'Access Key',
        },
      },
      APP: {
        publisher_id: {
          LABEL: 'Publisher ID',
        },
      },
      UNIT: {
        slot_id: {
          LABEL: 'Placement ID',
        },
        is_video: {
          3: {
            LABEL: 'onewayIsVideo3',
            TIPS: 'tips4OnewayUnitType',
            TYPE: 'select',
            MULTI_LANG: true,
            DEFAULT: 1,
            OPTION: [
              {
                LABEL: 'onewayIsVideo3InterstitialImage',
                VALUE: '0',
                MULTI_LANG: true,
              },
              {
                LABEL: 'onewayIsVideo3InterstitialVideo',
                VALUE: '1',
                MULTI_LANG: true,
              },
            ],
          },
        }
      },
    },
    18: {
      NAME: 'Mobpower',
      PUBLISHER: {
        api_key: {
          LABEL: 'API Key',
          UNIT: true,
        },
        publisher_id: {
          LABEL: 'Publisher ID',
        },
      },
      APP: {
        app_id: {
          LABEL: 'App ID',
        },
      },
      UNIT: {
        placement_id: {
          LABEL: 'Placement ID',
        },
        size: {
          2: {
            LABEL: 'mobpowerSize2',
            TYPE: 'select',
            MULTI_LANG: true,
            OPTION: [
              {
                LABEL: 'mobpowerSize2Banner',
                VALUE: '320x50',
                MULTI_LANG: true,
              },
            ],
          },
        },
      },
    },
    19: {
      NAME: '金山云',
      PUBLISHER: {},
      APP: {
        media_id: {
          LABEL: 'Media ID',
        },
      },
      UNIT: {
        slot_id: {
          LABEL: 'Slot ID',
        },
      },
    },
    20: {
      NAME: 'Yeahmobi',
      PUBLISHER: {
        token: {
          LABEL: 'Token',
        },
      },
      APP: {},
      UNIT: {
        slot_id: {
          LABEL: 'Slot ID',
        },
      },
    },
    21: {
      NAME: 'Appnext',
      PUBLISHER: {
        email: {
          LABEL: 'Email',
        },
        password: {
          LABEL: 'Password',
          INPUT_TYPE: 'password',
        },
        key: {
          LABEL: 'Key',
        },
      },
      APP: {
        // app_id: {
        //   LABEL: 'App ID',
        // },
      },
      UNIT: {
        placement_id: {
          LABEL: 'Placement ID',
        },
        size: {
          2: {
            LABEL: 'appnextSize2',
            TYPE: 'select',
            MULTI_LANG: true,
            OPTION: [
              {
                LABEL: 'appnextSize2Banner',
                VALUE: '320x50',
                MULTI_LANG: true,
              },
              {
                LABEL: 'appnextSize2LargeBanner',
                VALUE: '320x100',
                MULTI_LANG: true,
              },
              {
                LABEL: 'appnextSize2MediumRectangle',
                VALUE: '300x250',
                MULTI_LANG: true,
              },
            ],
          },
        },
      },
    },
    22: {
      NAME: 'Baidu',
      PUBLISHER: {
        access_key: {
          LABEL: 'Access Key',
        },
      },
      APP: {
        app_id: {
          LABEL: 'baiduAppID',
          MULTI_LANG: true,
        },
      },
      UNIT: {
        ad_place_id: {
          LABEL: 'baiduADPlaceID',
          MULTI_LANG: true,
        },
        size: {
          2: {
            LABEL: 'baiduSize2',
            TYPE: 'select',
            MULTI_LANG: true,
            OPTION: [
              {
                LABEL: '20:3 (375x56)',
                VALUE: '375x56',
              },
              {
                LABEL: '20:3 (200x30)',
                VALUE: '200x30',
              },
              {
                LABEL: '3:2 (375x250)',
                VALUE: '375x250',
              },
              {
                LABEL: '3:2 (200x133)',
                VALUE: '200x133',
              },
              {
                LABEL: '7:3 (375x160)',
                VALUE: '375x160',
              },
              {
                LABEL: '7:3 (200x85)',
                VALUE: '200x85',
              },
              {
                LABEL: '2:1 (375x187)',
                VALUE: '375x187',
              },
              {
                LABEL: '2:1 (200x100)',
                VALUE: '200x100',
              },
            ],
          },
        },
      },
    },
    23: {
      NAME: 'Nend',
      PUBLISHER: {
        api_key: {
          LABEL: 'APIKey',
        },
      },
      APP: {},
      UNIT: {
        api_key: {
          LABEL: 'apiKey',
        },
        spot_id: {
          LABEL: 'spotID',
        },
        is_video: {
          0: {
            LABEL: 'nendIsVideo0',
            TYPE: 'select',
            MULTI_LANG: true,
            DEFAULT: '0',
            OPTION: [{
              LABEL: 'nendIsVideo0Native',
              VALUE: '0',
              MULTI_LANG: true,
            },
            {
              LABEL: 'nendIsVideo0NativeVideo',
              VALUE: '1',
              MULTI_LANG: true,
            },
            ]
          },
          3: {
            LABEL: 'nendIsVideo3',
            TYPE: 'select',
            DEFAULT: '0',
            MULTI_LANG: true,
            OPTION: [{
              LABEL: 'nendIsVideo3Interstitial',
              VALUE: '0',
              MULTI_LANG: true,
            },
            {
              LABEL: 'nendIsVideo3InterstitialVideo',
              VALUE: '1',
              MULTI_LANG: true,
            },
            {
              LABEL: 'nendIsVideo3FullScreen',
              VALUE: '2',
              MULTI_LANG: true,
            },
            ]
          },
        },
        size: {
          2: {
            LABEL: 'nendSize2',
            TYPE: 'select',
            MULTI_LANG: true,
            OPTION: [
              {
                LABEL: 'nendSize2banner320_50',
                VALUE: '320x50',
                MULTI_LANG: true,
              },
              {
                LABEL: 'nendSize2banner320_100',
                VALUE: '320x100',
                MULTI_LANG: true,
              },
              {
                LABEL: 'nendSize2banner300_100',
                VALUE: '300x100',
                MULTI_LANG: true,
              },
              {
                LABEL: 'nendSize2banner300_250',
                VALUE: '300x250',
                MULTI_LANG: true,
              },
              {
                LABEL: 'nendSize2banner728_90',
                VALUE: '728x90',
                MULTI_LANG: true,
              },
            ],
          },
        },
      },
    },
    24: {
      NAME: 'Maio',
      PUBLISHER: {
        api_id: {
          LABEL: 'API ID',
        },
        api_key: {
          LABEL: 'API Key',
        },
      },
      APP: {
        media_id: {
          LABEL: 'Media ID',
        },
      },
      UNIT: {
        zone_id: {
          LABEL: 'Zone ID',
        },
      },
    },
    25: {
      NAME: 'StartApp',
      PUBLISHER: {
        partner_id: {
          LABEL: 'Partner ID',
        },
        token: {
          LABEL: 'Token',
        },
      },
      APP: {
        app_id: {
          LABEL: 'APP ID',
        },
      },
      UNIT: {
        ad_tag: {
          LABEL: 'AD Tag',
          TIPS: 'tips4StartAppAdTag',
        },
        is_video: {
          3: {
            LABEL: 'startappIsVideo3',
            TYPE: 'select',
            MULTI_LANG: true,
            DEFAULT: 1,
            OPTION: [
              {
                LABEL: 'startappIsVideo3InterstitialImage',
                VALUE: '0',
                MULTI_LANG: true,
              },
              {
                LABEL: 'startappIsVideo3InterstitialVideo',
                VALUE: '1',
                MULTI_LANG: true,
              },
            ],
          },
        },
        size: {
          2: {
            LABEL: 'startappSize2',
            TYPE: 'select',
            MULTI_LANG: true,
            OPTION: [
              {
                LABEL: 'startappSize2Banner',
                VALUE: '320x50',
                MULTI_LANG: true,
              },
              {
                LABEL: 'startappSize2MediumRectangle',
                VALUE: '300x250',
                MULTI_LANG: true,
              },
              // {
              //   LABEL: 'Cover (1200x628)',
              //   VALUE: '1200x628',
              // },
            ],
          },
        },
      },
    },
    26: {
      NAME: 'SuperAwesome',
      PUBLISHER: {},
      APP: {
        property_id: {
          LABEL: 'Property ID',
        },
      },
      UNIT: {
        placement_id: {
          LABEL: 'Placement ID',
        },
      },
    },
    27: {
      NAME: 'Luomi',
      PUBLISHER: {},
      APP: {
        app_key: {
          LABEL: 'AppKey',
        },
      },
      UNIT: {
        size: {
          0: {
            LABEL: 'Image Size',
            TYPE: 'select',
            OPTION: [
              {
                LABEL: '800x1200',
                VALUE: '800x1200',
              },
              {
                LABEL: '640x400',
                VALUE: '640x400',
              },
              {
                LABEL: '720x1280',
                VALUE: '720x1280',
              },
              {
                LABEL: '150x150',
                VALUE: '150x150',
              },
              {
                LABEL: '214x140',
                VALUE: '214x140',
              },
              {
                LABEL: '640x100',
                VALUE: '640x100',
              },
            ],
          },
        },
      },
    },
    28: {
      NAME: 'KuaiShou',
      PUBLISHER: {
        access_key: {
          LABEL: 'Access Key',
        },
        security_key: {
          LABEL: 'Security Key',
        },
      },
      APP: {
        app_id: {
          LABEL: 'kuaishouAppID',
          MULTI_LANG: true,
        },
        app_name: {
          LABEL: 'kuaishouAppName',
          MULTI_LANG: true,
        }
        // app_key: {
        //   LABEL: 'App Key',
        //   TIPS: 'tips4kuaishouAppKeyAndWbindex'
        // },
        // wb_index: {
        //   LABEL: 'wbindex',
        //   TIPS: 'tips4kuaishouAppKeyAndWbindex'
        // }
      },
      UNIT: {
        position_id: {
          LABEL: 'kuaishouPosID',
          MULTI_LANG: true,
        },
        orientation: {
          1: {
            LABEL: 'kuaishouOrientation1',
            TYPE: 'select',
            MULTI_LANG: true,
            DEFAULT: 1,
            OPTION: [
              {
                LABEL: 'kuaishouOrientation1Portrait',
                VALUE: 1,
                MULTI_LANG: true,
              },
              {
                LABEL: 'kuaishouOrientation1Landscape',
                VALUE: 2,
                MULTI_LANG: true,
              },
            ],
          },
          3: {
            LABEL: 'kuaishouOrientation3',
            TYPE: 'select',
            DEFAULT: 1,
            MULTI_LANG: true,
            OPTION: [
              {
                LABEL: 'kuaishouOrientation3Portrait',
                VALUE: 1,
                MULTI_LANG: true,
              },
              {
                LABEL: 'kuaishouOrientation3Landscape',
                VALUE: 2,
                MULTI_LANG: true,
              },
            ],
          },
        },
        unit_type: {
          0: {
            LABEL: 'kuaishouUnitType',
            TIPS: 'tips4KuaishouUnitType',
            TYPE: 'radio',
            DEFAULT: '',
            MULTI_LANG: true,
            OPTION: [
              {
                LABEL: 'kuaishouNativeFeeds',
                VALUE: '0',
              },
              {
                LABEL: 'kuaishouDrawVideo',
                VALUE: '1',
              }
            ]
          },
        },
        layout_type: {
          0: {
            LABEL: 'Native Type',
            MULTI_LANG: true,
            TYPE: 'radio',
            DEFAULT: '0',
            WHERE: [{
              FIELD_KEY: 'unit_type',
              VALUES: ['0'],
            }],
            OPTION: [
              {
                LABEL: 'template',
                VALUE: '1',
              },
              {
                LABEL: 'Self Rendering',
                VALUE: '0',
              },
            ],
          },
        },
        is_video: {
          // 0: {
          //   LABEL: 'kuaishouMaterialType',
          //   TYPE: 'radio',
          //   MULTI_LANG: true,
          //   WHERE: [
          //     // {
          //     //   FIELD_KEY: 'layout_type',
          //     //   VALUES: ['0'],
          //     // },
          //     {
          //       FIELD_KEY: 'unit_type',
          //       VALUES: ['0'],
          //     }
          //   ],
          //   OPTION: [{
          //     LABEL: 'kuaishouMaterialTypeVideo',
          //     VALUE: '1',
          //   }, {
          //     LABEL: 'kuaishouMaterialTypeImage',
          //     VALUE: '0',
          //   }],
          // },
        },
        video_sound: {
          0: {
            LABEL: 'kuaishouVideoSound',
            MULTI_LANG: true,
            TYPE: 'radio',
            DEFAULT: '',
            WHERE: [{
              FIELD_KEY: 'unit_type',
              VALUES: ['0'],
            }],
            OPTION: [
              {
                LABEL: 'Yes',
                VALUE: '1',
              },
              {
                LABEL: 'NO',
                VALUE: '0',
              },
            ],
          },
        },
      }
    },
    29: {
      NAME: 'Sigmob',
      PUBLISHER: {
        public_key: {
          LABEL: 'Public Key',
        },
        secret_key: {
          LABEL: 'Secret Key',
        },
      },
      APP: {
        app_id: {
          LABEL: 'sigmobAppID',
          MULTI_LANG: true,
        },
        app_key: {
          LABEL: 'App Key',
        },
      },
      UNIT: {
        placement_id: {
          LABEL: 'sigmobPlacementID',
          MULTI_LANG: true,
        },
      },
    },
    30: {
      NAME: 'Smaato',
      PUBLISHER: {
        publisher_id: {
          LABEL: 'Publisher ID',
          UNIT: true,
        },
        client_id: {
          LABEL: 'Client ID',
        },
        client_secret: {
          LABEL: 'Client Secret',
        },
        token: {
          LABEL: 'Token',
        },
      },
      APP: {},
      UNIT: {
        adspace_id: {
          LABEL: 'Adspace ID',
        },
        size: {
          2: {
            LABEL: 'AD Source Size',
            TYPE: 'select',
            OPTION: [
              {
                LABEL: 'DEFAULT (320x50)',
                VALUE: '320x50',
              },
              {
                LABEL: 'LEADERBOARD (728x90)',
                VALUE: '728x90',
              },
              {
                LABEL: 'MEDIUM RECTANGLE (300x250)',
                VALUE: '300x250',
              },
              {
                LABEL: 'SKYSCRAPER (120x600)',
                VALUE: '120x600',
              },
            ],
          },
        },
        is_video: {
          3: {
            LABEL: 'Interstitial Type',
            TYPE: 'select',
            DEFAULT: '0',
            OPTION: [{
              LABEL: 'Interstitial',
              VALUE: '0',
            },
            {
              LABEL: 'Interstitial Video',
              VALUE: '1',
            }]
          },
        },
      },
    },
    31: {
      NAME: 'Five(Line)',
      PUBLISHER: {},
      APP: {
        app_id: {
          LABEL: 'App ID',
        },
      },
      UNIT: {
        slot_id: {
          LABEL: 'Slot ID',
        },
        size: {
          2: {
            LABEL: 'AD Source Size',
            TYPE: 'select',
            OPTION: [
              {
                LABEL: '300x250',
                VALUE: '300x250',
              },
              {
                LABEL: '320x180',
                VALUE: '320x180',
              },
              {
                LABEL: '320x320',
                VALUE: '320x320',
              },
              {
                LABEL: '320x100',
                VALUE: '320x100',
              },
              {
                LABEL: '320x80',
                VALUE: '320x80',
              },
              {
                LABEL: '320x70',
                VALUE: '320x70',
              },
            ],
          },
        },
      },
    },
    32: {
      NAME: 'MyTarget',
      PUBLISHER: {
        permanent_access_token: {
          LABEL: 'Permanent Access Token',
        },
      },
      APP: {},
      UNIT: {
        slot_id: {
          LABEL: 'Slot ID',
        },
        size: {
          2: {
            LABEL: 'AD Source Size',
            TYPE: 'select',
            OPTION: [
              {
                LABEL: '320x50',
                VALUE: '320x50',
              },
              {
                LABEL: '300x250',
                VALUE: '300x250',
              },
              {
                LABEL: '728x90',
                VALUE: '728x90',
              },
            ],
          },
        },
      },
    },
    33: {
      NAME: 'Google Ad Manager',
      PUBLISHER: {
        // publisher_id: {
        //   LABEL: 'Publisher ID',
        // },
        // access_token: {
        //   LABEL: 'Access Token',
        // },
      },
      APP: {
        // app_id: {
        //   LABEL: 'adManagerAppID',
        //   MULTI_LANG: true,
        // },
      },
      UNIT: {
        unit_id: {
          LABEL: 'adManagerUnitID',
          MULTI_LANG: true,
        },
        size: {
          2: {
            LABEL: 'adManagerSize2',
            TYPE: 'select',
            MULTI_LANG: true,
            OPTION: [
              {
                LABEL: 'adManagerSize2Banner',
                VALUE: '320x50',
              },
              {
                LABEL: 'adManagerSize2LargeBanner',
                VALUE: '320x100',
              },
              {
                LABEL: 'adManagerSize2MediumRectangle',
                VALUE: '300x250',
              },
              {
                LABEL: 'adManagerSize2FullSizeBanner',
                VALUE: '468x60',
              },
              {
                LABEL: 'adManagerSize2LeaderBoard',
                VALUE: '728x90',
              },
            ],
          },
        },
        media_ratio: {
          0: {
            LABEL: 'adManagerMediaAspectRatio',
            TYPE: 'select',
            // TIPS: 'tips4AdmobMediaAspectRatio',
            MULTI_LANG: true,
            DEFAULT: 0,
            OPTION: [
              {
                LABEL: 'adManagerMediaAspectRatioUnknown',
                VALUE: '0',
              },
              {
                LABEL: 'adManagerMediaAspectRatioAny',
                VALUE: '1',
              },
              {
                LABEL: 'adManagerMediaAspectRatioLandscape',
                VALUE: '2',
              },
              {
                LABEL: 'adManagerMediaAspectRatioPortrait',
                VALUE: '3',
              },
              {
                LABEL: 'adManagerMediaAspectRatioSquare',
                VALUE: '4',
              }
            ],
          },
        },
      },
    },
    34: {
      NAME: 'Yandex',
      PUBLISHER: {},
      APP: {},
      UNIT: {
        block_id: {
          LABEL: 'Block ID',
        },
        size: {
          2: {
            LABEL: 'AD Source Size',
            TYPE: 'select',
            OPTION: [
              {
                LABEL: '320x50',
                VALUE: '320x50',
              },
              {
                LABEL: '320x100',
                VALUE: '320x100',
              },
              {
                LABEL: '300x250',
                VALUE: '300x250',
              },
              {
                LABEL: '300x300',
                VALUE: '300x300',
              },
              {
                LABEL: '240x400',
                VALUE: '240x400',
              },
              {
                LABEL: '400x240',
                VALUE: '400x240',
              },
              {
                LABEL: '728x90',
                VALUE: '728x90',
              },
            ],
          },
        },
      },
    },
    36: {
      NAME: 'Ogury',
      PUBLISHER: {
        api_key: {
          LABEL: 'API KEY',
        },
        api_secret: {
          LABEL: 'API SECRET',
        },
      },
      APP: {
        key: {
          LABEL: 'KEY',
        },
      },
      UNIT: {
        unit_id: {
          LABEL: 'AD Unit ID',
        },
      },
    },
    37: {
      NAME: 'Fyber',
      PUBLISHER: {
        publisher_id: {
          LABEL: 'Publisher ID',
        },
        consumer_key: {
          LABEL: 'Consumer Key',
        },
        consumer_secret: {
          LABEL: 'Consumer Secret',
        },
      },
      APP: {
        app_id: {
          LABEL: 'App ID',
        },
      },
      UNIT: {
        spot_id: {
          LABEL: 'Ad Spot ID',
        },
        video_muted: {
          3: {
            LABEL: 'videoMuted',
            MULTI_LANG: true,
            TYPE: 'radio',
            DEFAULT: 0,
            OPTION: [
              {
                LABEL: 'Yes',
                VALUE: '1',
              },
              {
                LABEL: 'NO',
                VALUE: '0',
              },
            ],
          },
        },
      },
    },
    38: {
      NAME: 'Vplay',
      PUBLISHER: {
        api_key: {
          LABEL: 'API Key',
          UNIT: true,
        },
        publisher_id: {
          LABEL: 'Publisher ID',
        },
      },
      APP: {
        app_id: {
          LABEL: 'App ID',
        },
      },
      UNIT: {
        placement_id: {
          LABEL: 'Placement ID',
        },
        size: {
          2: {
            LABEL: 'vplaySize2',
            TYPE: 'select',
            MULTI_LANG: true,
            OPTION: [
              {
                LABEL: 'vplaySize2Banner',
                VALUE: '320x50',
                MULTI_LANG: true,
              },
            ],
          },
        },
        is_video: {
          3: {
            LABEL: 'vplayIsVideo3',
            TYPE: 'select',
            MULTI_LANG: true,
            DEFAULT: '0',
            OPTION: [{
              LABEL: 'vplayIsVideo3Interstitial',
              VALUE: '0',
              MULTI_LANG: true,
            },
            {
              LABEL: 'vplayIsVideo3InterstitialVideo',
              VALUE: '1',
              MULTI_LANG: true,
            }]
          },
        },
      },
    },
    39: {
      NAME: 'Huawei Ads',
      PUBLISHER: {
        currency: {
          LABEL: 'currencyForAccount',
          MULTI_LANG: true,
          TIPS: 'huaweiCurrencyTips',
          TYPE: 'radio',
          DEFAULT: 'USD',
          OPTION: [
            {
              LABEL: 'currency.USD',
              VALUE: 'USD',
              MULTI_LANG: true,
            },
            {
              LABEL: 'currency.CNY',
              VALUE: 'CNY',
              MULTI_LANG: true,
            },
          ],
        },
      },
      APP: {
        client_id: {
          LABEL: 'huaweiClientId',
          MULTI_LANG: true,
          TIPS: 'huaweiClientIdTips',
        },
        client_secret: {
          LABEL: 'huaweiClientSecret',
          MULTI_LANG: true,
          TIPS: 'huaweiClientSecretTips',
        },
      },
      UNIT: {
        ad_id: {
          LABEL: 'huaweiAdId',
          MULTI_LANG: true,
        },
        size: {
          2: {
            LABEL: 'huaweiSizeBanner',
            TYPE: 'select',
            MULTI_LANG: true,
            DEFAULT: '320x50',
            OPTION: [
              {
                LABEL: 'huaweiSizeBanner320x50',
                VALUE: '320x50',
                MULTI_LANG: true,
              },
              {
                LABEL: 'huaweiSizeBanner320x100',
                VALUE: '320x100',
                MULTI_LANG: true,
              },
              {
                LABEL: 'huaweiSizeBanner300x250',
                VALUE: '300x250',
                MULTI_LANG: true,
              },
              {
                LABEL: 'huaweiSizeBanner360x57',
                VALUE: '360x57',
                MULTI_LANG: true,
              },
              {
                LABEL: 'huaweiSizeBanner360x144',
                VALUE: '360x144',
                MULTI_LANG: true,
              },
              {
                LABEL: 'huaweiSizeBannerSmart',
                VALUE: 'smart',
                MULTI_LANG: true,
              },
            ],
          },
        },
        media_ratio: {
          0: {
            LABEL: 'huaweiMediaRatioNative',
            TYPE: 'select',
            // TIPS: 'tips4AdmobMediaAspectRatio',
            MULTI_LANG: true,
            DEFAULT: 0,
            OPTION: [
              {
                LABEL: 'huaweiMediaRatioNativeUnknown',
                VALUE: '0',
              },
              {
                LABEL: 'huaweiMediaRatioNativeAny',
                VALUE: '1',
              },
              {
                LABEL: 'huaweiMediaRatioNativeLandscape',
                VALUE: '2',
              },
              {
                LABEL: 'huaweiMediaRatioNativePortrait',
                VALUE: '3',
              },
              {
                LABEL: 'huaweiMediaRatioNativeSquare',
                VALUE: '4',
              }
            ],
          },
        },
        video_muted: {
          0: {
            LABEL: 'huaweiVideoMutedNative',
            MULTI_LANG: true,
            // TIPS: 'huaweiVideoMutedTips',
            TYPE: 'radio',
            DEFAULT: '0',
            OPTION: [
              {
                LABEL: 'Yes',
                VALUE: '0',
              },
              {
                LABEL: 'NO',
                VALUE: '1',
              },
            ],
          },
        },
        orientation: {
          0: {
            LABEL: 'huaweiOrientationNative',
            MULTI_LANG: true,
            DEFAULT: '0',
            TYPE: 'select',
            OPTION: [
              {
                LABEL: 'huaweiOrientationNativeAny',
                VALUE: '0',
                MULTI_LANG: true,
              },
              {
                LABEL: 'huaweiOrientationNativePortrait',
                VALUE: '1',
                MULTI_LANG: true,
              },
              {
                LABEL: 'huaweiOrientationNativeLandscape',
                VALUE: '2',
                MULTI_LANG: true,
              },
            ],
          },
          4: {
            LABEL: 'huaweiOrientationSplash',
            MULTI_LANG: true,
            DEFAULT: '1',
            TYPE: 'select',
            OPTION: [
              {
                LABEL: 'huaweiOrientationSplashPortrait',
                VALUE: '1',
                MULTI_LANG: true,
              },
              {
                LABEL: 'huaweiOrientationSplashLandscape',
                VALUE: '2',
                MULTI_LANG: true,
              },
            ],
          },
        },
      },
    },
    40: {
      NAME: 'Helium',
      PUBLISHER: {
        user_id: {
          LABEL: 'chartboostUserID',
          MULTI_LANG: true,
        },
        user_signature: {
          LABEL: 'chartboostUserSignature',
          MULTI_LANG: true,
        }
      },
      APP: {
        app_id: {
          LABEL: 'heliumAppID',
          MULTI_LANG: true,
        },
        app_signature: {
          LABEL: 'chartboostAppSignature',
          MULTI_LANG: true,
        }
      },
      UNIT: {
        placement_name: {
          LABEL: 'heliumMtgPlacementID',
          MULTI_LANG: true
        }
      },
    },
    41: {
      NAME: 'Mintegral OnlineAPI',
      PUBLISHER: {
        appkey: {
          LABEL: 'App Key',
          UNIT: true,
        },
        publisher_id: {
          LABEL: 'publisherId',
          UNIT: true,
          MULTI_LANG: true
        },
        skey: {
          LABEL: 'Skey',
        },
        secret: {
          LABEL: 'mtgSecret',
          MULTI_LANG: true,
        },
      },
      APP: {
        appid: {
          LABEL: 'mtgAppID',
          MULTI_LANG: true,
        },
      },
      UNIT: {
        placement_id: {
          LABEL: 'mtgPlacementID',
          OPTIONAL: true,
          // TIPS: 'mtgPlacementIDTips',
          MULTI_LANG: true,
        },
        unitid: {
          LABEL: 'mtgUnitID',
          // TIPS: 'mtgUnitIDTips',
          MULTI_LANG: true,
        },
        unit_type: {
          3: {
            LABEL: 'mtgIsVideo3',
            TYPE: 'select',
            MULTI_LANG: true,
            EDIT_DISABLE: true,
            OPTION: [{
              LABEL: 'mtgIsVideo3InterstitialImage',
              VALUE: '0',
              MULTI_LANG: true,
            },
              {
                LABEL: 'mtgIsVideo3InterstitialVideo',
                VALUE: '1',
                MULTI_LANG: true,
              },
            ]
          },
        },
        v_m: {
          1: {
            LABEL: 'mtgVideoMuted',
            MULTI_LANG: true,
            // TIPS: 'mtgVideoMutedTips',
            TYPE: 'radio',
            DEFAULT: '1',
            OPTION: [
              {
                LABEL: 'Yes',
                VALUE: '0',
              },
              {
                LABEL: 'NO',
                VALUE: '1',
              },
            ],
          },
          3: {
            LABEL: 'mtgVideoMuted',
            MULTI_LANG: true,
            // TIPS: 'mtgVideoMutedTips',
            TYPE: 'radio',
            DEFAULT: '1',
            OPTION: [
              {
                LABEL: 'Yes',
                VALUE: '0',
              },
              {
                LABEL: 'NO',
                VALUE: '1',
              },
            ],
            WHERE: [
              {
                FIELD_KEY: 'unit_type',
                VALUES: ['1'],
              },
            ],
          },
        },
        close_button: {
          2: {
            LABEL: 'mtgCloseBtn',
            MULTI_LANG: true,
            // TIPS: 'mtgCloseBtnTips',
            TYPE: 'radio',
            DEFAULT: '0',
            OPTION: [
              {
                LABEL: 'Show',
                VALUE: '0',
              },
              {
                LABEL: 'Hide',
                VALUE: '1',
              },
            ],
          },
        },
        size: {
          2: {
            LABEL: 'mtgSize2',
            TYPE: 'select',
            MULTI_LANG: true,
            DEFAULT: '320x50',
            OPTION: [
              {
                LABEL: 'mtgOlSizeBanner320x50',
                VALUE: '320x50',
                MULTI_LANG: true,
              },
              {
                LABEL: 'mtgOlSizeBanner320x90',
                VALUE: '320x90',
                MULTI_LANG: true,
              },
              {
                LABEL: 'mtgOlSizeBanner300x250',
                VALUE: '300x250',
                MULTI_LANG: true,
              },
              {
                LABEL: 'mtgOlSizeBanner728x90',
                VALUE: '728x90',
                MULTI_LANG: true,
              },
            ],
          },
        },
        countdown: {
          4: {
            LABEL: 'mtgCountDown',
            // TIPS: 'mtgCountDownTips',
            MULTI_LANG: true,
            DEFAULT: '5',
          },
        },
        allows_skip: {
          4: {
            LABEL: 'mtgAllowSkip',
            // TIPS: 'mtgAllowSkipTips',
            MULTI_LANG: true,
            DEFAULT: '1',
            TYPE: 'radio',
            OPTION: [
              {
                LABEL: 'Yes',
                VALUE: '1',
              },
              {
                LABEL: 'NO',
                VALUE: '0',
              },
            ],
          },
        },
        orientation: {
          4: {
            LABEL: 'mtgOrientation4',
            MULTI_LANG: true,
            DEFAULT: '1',
            TYPE: 'select',
            OPTION: [
              {
                LABEL: 'mtgOrientation4Portrait',
                VALUE: '1',
                MULTI_LANG: true,
              },
              {
                LABEL: 'mtgOrientation4Landscape',
                VALUE: '2',
                MULTI_LANG: true,
              },
            ],
          },
        },
      },
    },
    42: {
      NAME: '腾讯广告 OnlineAPI',
      PUBLISHER: {
        // api_version: {
        //   LABEL: 'gdtAPIVersion',
        //   MULTI_LANG: true,
        //   TYPE: 'radio',
        //   IS_PARENT: true,
        //   // TIPS: 'gdtAPIVersionTips',
        //   DEFAULT: 2,
        //   OPTION: [
        //     {
        //       LABEL: 'gdtAPIVersionNew',
        //       VALUE: 2,
        //     },
        //   ],
        // },
        account_id: {
          LABEL: 'gdtAccountID',
          MULTI_LANG: true,
        },
        secret_key: {
          LABEL: 'gdtSecretKey',
          MULTI_LANG: true,
        },
      },
      APP: {
        app_id: {
          LABEL: 'gdtAppID',
          MULTI_LANG: true,
        },
      },
      UNIT: {
        unit_id: {
          LABEL: 'gdtUnitID',
          MULTI_LANG: true,
        },
        size: {
          2: {
            LABEL: 'gdtSize2',
            TYPE: 'select',
            DEFAULT: '320x50',
            MULTI_LANG: true,
            OPTION: [
              {
                LABEL: 'mtgOlSizeBanner320x50',
                VALUE: '320x50',
                MULTI_LANG: true,
              },
              {
                LABEL: 'mtgOlSizeBanner320x90',
                VALUE: '320x90',
                MULTI_LANG: true,
              },
              {
                LABEL: 'mtgOlSizeBanner300x250',
                VALUE: '300x250',
                MULTI_LANG: true,
              },
              {
                LABEL: 'mtgOlSizeBanner728x90',
                VALUE: '728x90',
                MULTI_LANG: true,
              },
            ],
          },
        },
        unit_type: {
          3: {
            LABEL: 'mtgIsVideo3',
            TYPE: 'select',
            MULTI_LANG: true,
            EDIT_DISABLE: true,
            OPTION: [{
              LABEL: 'mtgIsVideo3InterstitialImage',
              VALUE: '0',
              MULTI_LANG: true,
            },
              {
                LABEL: 'mtgIsVideo3InterstitialVideo',
                VALUE: '1',
                MULTI_LANG: true,
              },
            ]
          },
        },
        v_m: {
          1: {
            LABEL: 'videoMuted',
            MULTI_LANG: true,
            TYPE: 'radio',
            DEFAULT: 1,
            // TIPS: 'tips4GdtVideoMuted0',
            OPTION: [
              {
                LABEL: 'Yes',
                VALUE: '0',
              },
              {
                LABEL: 'NO',
                VALUE: '1',
              },
            ],
          },
          3: {
            LABEL: 'videoMuted',
            MULTI_LANG: true,
            TYPE: 'radio',
            DEFAULT: 1,
            // TIPS: 'tips4VideoMuted',
            WHERE: [
              {
                FIELD_KEY: 'unit_type',
                VALUES: ['1'],
              },
            ],
            OPTION: [
              {
                LABEL: 'Yes',
                VALUE: '0',
              },
              {
                LABEL: 'NO',
                VALUE: '1',
              },
            ],
          },
        },
        close_button: {
          2: {
            LABEL: 'mtgCloseBtn',
            MULTI_LANG: true,
            // TIPS: 'mtgCloseBtnTips',
            TYPE: 'radio',
            DEFAULT: '0',
            OPTION: [
              {
                LABEL: 'Show',
                VALUE: '0',
              },
              {
                LABEL: 'Hide',
                VALUE: '1',
              },
            ],
          },
        },
        countdown: {
          4: {
            LABEL: 'mtgCountDown',
            // TIPS: 'mtgCountDownTips',
            MULTI_LANG: true,
            DEFAULT: '5',
          },
        },
        allows_skip: {
          4: {
            LABEL: 'mtgAllowSkip',
            // TIPS: 'mtgAllowSkipTips',
            MULTI_LANG: true,
            DEFAULT: '1',
            TYPE: 'radio',
            OPTION: [
              {
                LABEL: 'Yes',
                VALUE: '1',
              },
              {
                LABEL: 'NO',
                VALUE: '0',
              },
            ],
          },
        },
        orientation: {
          4: {
            LABEL: 'mtgOrientation4',
            MULTI_LANG: true,
            DEFAULT: '1',
            TYPE: 'select',
            OPTION: [
              {
                LABEL: 'mtgOrientation4Portrait',
                VALUE: '1',
                MULTI_LANG: true,
              },
              // {
              //   LABEL: 'mtgOrientation4Landscape',
              //   VALUE: '2',
              //   MULTI_LANG: true,
              // },
            ],
          },
        },
      },
    },
    66: {
      NAME: 'TopOn ADX',
      PUBLISHER: {},
      APP: {},
      UNIT: {}
    }
  },
};
