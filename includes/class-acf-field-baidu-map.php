<?php

if ( ! class_exists( 'acf_field_baidu_map' ) ) :
  #[AllowDynamicProperties]
  class acf_field_baidu_map extends acf_field {


    /**
     * This function will setup the field type data
     *
     * @type    function
     * @date    5/03/2014
     * @since   5.0.0
     *
     * @param   n/a
     * @return  n/a
     */

    function initialize() {

      // vars
      $this->name           = 'baidu_map';
      $this->label          = '百度地图';
      $this->category       = 'advanced';
      $this->description    = '用于使用百度地图选择位置的交互式UI。需要百度地图 API 密钥和其他配置才能正确显示。';
      $this->preview_image  = fic_uni_get_url() . '/images/field-preview-baidu-map.png';
      // $this->doc_url        = acf_add_url_utm_tags( 'https://www.advancedcustomfields.com/resources/google-map/', 'docs', 'field-type-selection' );
      $this->defaults       = array(
        'height'     => '',
        'center_lat' => '',
        'center_lng' => '',
        'zoom'       => '',
      );
      $this->default_values = array(
        'height'     => '400',
        'center_lng' => '116.404',
        'center_lat' => '39.915',
        'zoom'       => '15',
      );
    }


    /**
     * description
     *
     * @type    function
     * @date    16/12/2015
     * @since   5.3.2
     *
     * @param   $post_id (int)
     * @return  $post_id (int)
     */

    function input_admin_enqueue_scripts() {

      // vars
      $api = array(
        'ak' => acf_get_setting( 'baidu_api_key' ),
        'v' => '1.0',
        'type' => 'webgl',
        'callback' => 'initBaiduMapFieldInput'
      );

      // filter
      $api = apply_filters( 'acf/fields/baidu_map/api', $api );

      // remove empty
      if ( empty( $api['ak'] ) ) {
        unset( $api['ak'] );
      }

      // construct url
      $url = add_query_arg( $api, 'https://api.map.baidu.com/api' );

      // localize
      acf_localize_data(
        array(
          'baidu_map_api' => $url,
        )
      );

      // 引入custom JavaScript文件
      wp_enqueue_script('acf-baidu-map', fic_uni_get_url() . "/includes/assets/acf-baidu-map.js", array('jquery'), null, true);

      // 引入对应的CSS样式文件
      wp_enqueue_style('acf-baidu-map', fic_uni_get_url() . "/includes/assets/acf-baidu-map.css", array(), null);
    }


    /**
     * Create the HTML interface for your field
     *
     * @param   $field - an array holding all the field's data
     *
     * @type    action
     * @since   3.6
     * @date    23/01/13
     */

    function render_field( $field ) {

      // Apply defaults.
      foreach ( $this->default_values as $k => $v ) {
        if ( ! $field[ $k ] ) {
          $field[ $k ] = $v;
        }
      }

      // Attrs.
      $attrs = array(
        'id'        => $field['id'],
        'class'     => "acf-baidu-map {$field['class']}",
        'data-lat'  => $field['center_lat'],
        'data-lng'  => $field['center_lng'],
        'data-zoom' => $field['zoom'],
      );

      if ( $field['value'] ) {
        $attrs['class'] .= ' -value';
      } else {
        $field['value'] = '';
      }

      ?>
<div <?php echo acf_esc_attrs( $attrs ); ?>>
      <?php
      acf_hidden_input(
        array(
          'name'  => $field['name'],
          'value' => $field['value'],
        )
      );
      ?>
  <div class="address-info"></div>
  <div id="baiduMapContainer" style="width: 100%; <?php echo esc_attr( 'height: ' . $field['height'] . 'px' ); ?>"></div>
</div>
      <?php
    }


    /**
     * Create extra options for your field. This is rendered when editing a field.
     * The value of $field['name'] can be used (like bellow) to save extra data to the $field
     *
     * @type    action
     * @since   3.6
     * @date    23/01/13
     *
     * @param   $field  - an array holding all the field's data
     */

    function render_field_settings( $field ) {

      // center_lat
      acf_render_field_setting(
        $field,
        array(
          'label'       => '中心点',
          'hint'        => '初始地图中心点',
          'type'        => 'text',
          'name'        => 'center_lat',
          'prepend'     => '纬度',
          'placeholder' => $this->default_values['center_lat'],
        )
      );

      // center_lng
      acf_render_field_setting(
        $field,
        array(
          'label'       => '中心点',
          'hint'        => '初始地图中心点',
          'type'        => 'text',
          'name'        => 'center_lng',
          'prepend'     => '经度',
          'placeholder' => $this->default_values['center_lng'],
          '_append'     => 'center_lat',
        )
      );

      // zoom
      acf_render_field_setting(
        $field,
        array(
          'label'        => '缩放',
          'instructions' => '设置初始缩放级别',
          'type'         => 'text',
          'name'         => 'zoom',
          'placeholder'  => $this->default_values['zoom'],
        )
      );

      // allow_null
      acf_render_field_setting(
        $field,
        array(
          'label'        => '高度',
          'instructions' => '自定义地图高度',
          'type'         => 'text',
          'name'         => 'height',
          'append'       => 'px',
          'placeholder'  => $this->default_values['height'],
        )
      );
    }

    /**
     * load_value
     *
     * Filters the value loaded from the database.
     *
     * @date    16/10/19
     * @since   5.8.1
     *
     * @param   mixed $value   The value loaded from the database.
     * @param   mixed $post_id The post ID where the value is saved.
     * @param   array $field   The field settings array.
     * @return  (array|false)
     */
    function load_value( $value, $post_id, $field ) {

      // Ensure value is an array.
      if ( $value ) {
        return wp_parse_args(
          $value,
          array(
            'address' => '',
            'lat'     => 0,
            'lng'     => 0,
          )
        );
      }

      // Return default.
      return false;
    }


    /**
     * This filter is appied to the $value before it is updated in the db
     *
     * @type    filter
     * @since   3.6
     * @date    23/01/13
     *
     * @param   $value - the value which will be saved in the database
     * @param   $post_id - the post_id of which the value will be saved
     * @param   $field - the field array holding all the field options
     *
     * @return  $value - the modified value
     */
    function update_value( $value, $post_id, $field ) {

      // decode JSON string.
      if ( is_string( $value ) ) {
        $value = json_decode( wp_unslash( $value ), true );
      }

      // Ensure value is an array.
      if ( $value ) {
        return (array) $value;
      }

      // Return default.
      return false;
    }

    /**
     * Return the schema array for the REST API.
     *
     * @param array $field
     * @return array
     */
    public function get_rest_schema( array $field ) {
      return array(
        'type'       => array( 'object', 'null' ),
        'required'   => ! empty( $field['required'] ),
        'properties' => array(
          'address'           => array(
            'type' => 'string',
          ),
          'lat'               => array(
            'type' => array( 'string', 'float' ),
          ),
          'lng'               => array(
            'type' => array( 'string', 'float' ),
          ),
          'zoom'              => array(
            'type' => array( 'string', 'float', 'int' ),
          ),
          'adcode'            => array(
            'type' => array( 'string', 'int' ),
          ),
          'city'              => array(
            'type' => 'string',
          ),
          'city_code'         => array(
            'type' => array( 'string', 'int' ),
          ),
          'country'           => array(
            'type' => 'string',
          ),
          'country_code'      => array(
            'type' => array( 'string', 'int' ),
          ),
          'direction'         => array(
            'type' => 'string',
          ),
          'distance'          => array(
            'type' => 'string',
          ),
          'district'          => array(
            'type' => 'string',
          ),
          'province'          => array(
            'type' => 'string',
          ),
          'street'            => array(
            'type' => 'string',
          ),
          'street_number'     => array(
            'type' => array( 'string', 'int' ),
          ),
          'town'              => array(
            'type' => 'string',
          ),
          'town_code'         => array(
            'type' => array( 'string', 'int' ),
          ),
        ),
      );
    }

    /**
     * Apply basic formatting to prepare the value for default REST output.
     *
     * @param mixed          $value
     * @param string|integer $post_id
     * @param array          $field
     * @return mixed
     */
    public function format_value_for_rest( $value, $post_id, array $field ) {
      if ( ! $value ) {
        return null;
      }

      return acf_format_numerics( $value );
    }
  }


  // initialize
  acf_register_field_type( 'acf_field_baidu_map' );
endif; // class_exists check

?>
