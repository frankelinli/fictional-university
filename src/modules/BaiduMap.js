class BaiduMap {
  constructor() {
    document.querySelectorAll(".acf-map").forEach(el => {
      this.new_map(el)
    })
  }

  new_map($el) {
    var $markers = $el.querySelectorAll(".marker")

    var map = new BMapGL.Map($el)
    map.enableScrollWheelZoom() // 开启滚轮缩放
    map.disableInertialDragging() // 禁用惯性拖拽
    map.markerPonits = []
    var that = this

    // add markers
    $markers.forEach(function (x) {
      that.add_marker(x, map)
    })

    // center map
    this.center_map(map)
  } // end new_map

  add_marker($marker, map) {
    var latlng = new BMapGL.Point($marker.getAttribute("data-lng"), $marker.getAttribute("data-lat"));

    var marker = new BMapGL.Marker(latlng);

    map.markerPonits.push(latlng)

    // if marker contains HTML, add it to an infoWindow
    if ($marker.innerHTML) {
      // create info window
      var infowindow = new BMapGL.InfoWindow($marker.innerHTML, {
        title: $marker.getAttribute('data-title')
      })

      // show info window when marker is clicked
      marker.addEventListener('click', () => {
        map.openInfoWindow(infowindow, latlng);
      })
    }

    map.addOverlay(marker)
  } // end add_marker

  center_map(map) {
    // only 1 marker?
    if (map.markerPonits.length == 1) {
      // set center of map
      map.centerAndZoom(map.markerPonits[0], 16)
    } else {
      // fit to bounds
      map.setViewport(map.markerPonits)
    }
  } // end center_map
}

export default BaiduMap
