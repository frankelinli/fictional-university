(function ($, undefined) {
  var Field = acf.Field.extend({
    type: 'baidu_map',
    map: null,
    geocoder: null,
    wait: 'load',
    events: {
      showField: 'onShow'
    },
    // 用于acf获取默认属性
    $control: function () {
      return this.$('.acf-baidu-map');
    },
    $address: function () {
      return this.$('.acf-baidu-map .address-info');
    },
    $container: function () {
      return this.$('.acf-baidu-map #baiduMapContainer');
    },
    setState: function (state) {
      this.$address().html(state);
    },
    getValue: function () {
      var val = this.$input().val();
      if (val) {
        return JSON.parse(val);
      } else {
        return false;
      }
    },
    setValue: function (val, silent) {
      // Convert input value.
      var valAttr = '';
      if (val) {
        valAttr = JSON.stringify(val);
      }

      // Update input (with change).
      acf.val(this.$input(), valAttr);

      // Bail early if silent update.
      if (silent) {
        return;
      }

      // 设置图钉的坐标位置
      if (val) {
        this.setPosition(val.lng, val.lat);
      }

      /**
       * Fires immediately after the value has changed.
       *
       * @date	12/02/2014
       * @since	5.0.0
       *
       * @param	object|string val The new value.
       * @param	object map The Google Map isntance.
       * @param	object field The field instance.
       */
      acf.doAction('baidu_map_change', val, this.map, this);
    },
    newPoint: function (lng, lat) {
      return new BMapGL.Point(parseFloat(lng), parseFloat(lat));
    },
    setPosition: function (lng, lat) {
      // 更新图钉位置
      this.map.marker.setPosition({
        lng: parseFloat(lng),
        lat: parseFloat(lat)
      });

      // 根据图钉位置查找地址
      this.searchPosition(lng, lat);

      // 居中地图
      this.center();
    },
    center: function () {
      // 寻找图钉位置
      var position = this.map.marker.getPosition();
      if (position) {
        var lng = position.lng;
        var lat = position.lat;

        // 或者寻找默认设置
      } else {
        var lng = this.get('lng');
        var lat = this.get('lat');
      }

      // 居中地图
      this.map.panTo({
        lng: parseFloat(lng),
        lat: parseFloat(lat)
      });
    },
    initialize: function () {
      // Ensure Baidu API is loaded and then initialize map.
      withAPI(this.initializeMap.bind(this));
    },
    initializeMap: function () {
      // Get value ignoring conditional logic status.
      var val = this.getValue();

      // 构造默认参数
      var args = acf.parseArgs(val, {
        zoom: this.get('zoom'),
        lng: this.get('lng'),
        lat: this.get('lat')
      });

      // 创建地图
      var map = new BMapGL.Map(this.$container()[0]);
      var geocoder = new BMapGL.Geocoder();
      map.centerAndZoom({
        lng: args.lng,
        lat: args.lat
      }, parseFloat(args.zoom));
      map.enableScrollWheelZoom(); // 开启滚轮缩放
      map.disableInertialDragging(); // 禁用惯性拖拽

      // 创建图钉
      var marker = new BMapGL.Marker({
        lat: args.lat,
        lng: args.lng
      }, {
        enableDragging: true, // 启用拖拽
      });
      map.addOverlay(marker);


      // 添加地图事件
      this.addMapEvents(this, map, marker);

      // 添加引用
      map.acf = this;
      map.marker = marker;
      this.map = map;
      this.geocoder = geocoder;

      // 设置图钉位置，居中地图
      if (val) {
        this.setPosition(val.lng, val.lat);
      } else {
        // 定位到当前城市
        var localCity = new BMapGL.LocalCity();
        localCity.get(e => {
          this.setPosition(e.center.lng, e.center.lat);
        });
      }

      /**
       * Fires immediately after the Google Map has been initialized.
       *
       * @date	12/02/2014
       * @since	5.0.0
       *
       * @param	object map The Google Map isntance.
       * @param	object marker The Google Map marker isntance.
       * @param	object field The field instance.
       */
      acf.doAction('baidu_map_init', map, marker, this);
    },
    addMapEvents: function (field, map, marker) {
      // 拖拽图钉结束
      marker.addEventListener('dragend', () => {
        var position = marker.getPosition();
        field.setPosition(position.lng, position.lat);
      });

      // 地图缩放结束
      map.addEventListener('zoomend', () => {
        var val = field.val();
        if (val) {
          val.zoom = map.getZoom();
          field.setValue(val, true);
        }
      });
    },
    searchPosition: function (lng, lat) {
      // 开始查找
      this.setState('查找中...');

      // 查询地理信息
      // var lngLat = {
      //   lng: lng,
      //   lat: lat
      // };
      var lngLat = new BMapGL.Point(lng, lat);
      this.geocoder.getLocation(lngLat, function(results) {
        // 查找失败
        if (results === null) {
          this.setState('未找到位置信息...');
        } else { // 查找成功
          var val = this.parseResult(results);

          // 覆盖经度/纬度以匹配用户定义的标记位置。
          // 避免“捕捉”到标记附近结果的问题。
          val.lng = lng;
          val.lat = lat;
          this.setValue(val, true);
          this.setState(val.address);
        }
      }.bind(this));
    },
    /**
     * parseResult
     *
     * Returns location data for the given GeocoderResult object.
     *
     * @date	15/10/19
     * @since	5.8.6
     *
     * @param	object obj A GeocoderResult object.
     * @return	object
     */
    parseResult: function (obj) {
      // 构建基础数据
      var result = {
        address: obj.address,
        lng: obj.point.lng,
        lat: obj.point.lat
      };

      if (obj.content.poi_region.length) {
        result.address += `, ${obj.content.poi_region[0].name}`;
      }

      // 添加缩放等级
      result.zoom = this.map.getZoom();

      // 创建结构化和数据地图
      var map = [
        "adcode",
        "city",
        "city_code",
        "country",
        "country_code",
        "direction",
        "distance",
        "district",
        "province",
        "street",
        "street_number",
        "town",
        "town_code"
      ];

      map.forEach(item => {
        const value = obj.content.address_detail[item];
        result[item] = value;
      });

      /**
       * Filters the parsed result.
       *
       * @date	18/10/19
       * @since	5.8.6
       *
       * @param	object result The parsed result value.
       * @param	object obj The GeocoderResult object.
       */
      return acf.applyFilters('baidu_map_result', result, obj, this.map, this);
    },
    // Center map once made visible.
    onShow: function () {
      if (this.map) {
        this.setTimeout(this.center);
      }
    }
  });
  acf.registerFieldType(Field);

  // Vars.
  var loading = false;

  /**
   * withAPI
   *
   * Loads the Google Maps API library and troggers callback.
   *
   * @date	28/3/19
   * @since	5.7.14
   *
   * @param	function callback The callback to excecute.
   * @return	void
   */

  function withAPI(callback) {
    // Geocoder will need to be loaded. Hook callback to action.
    acf.addAction('baidu_map_api_loaded', callback);

    // Bail early if already loading API.
    if (loading) {
      return;
    }

    // load api
    var url = acf.get('baidu_map_api');
    if (url) {
      // Set loading status.
      loading = true;

      // Load API
      $.ajax({
        url: url,
        dataType: 'script',
        cache: true,
        success: function () {
          window.initBaiduMapFieldInput = function () {
            acf.doAction('baidu_map_api_loaded');
          };
        }
      });
    }
  }
})(jQuery);