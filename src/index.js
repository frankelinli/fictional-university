import "../css/style.scss"

// Our modules / classes
import MobileMenu from "./modules/MobileMenu"
import HeroSlider from "./modules/HeroSlider"
import BaiduMap from "./modules/BaiduMap"
import Search from "./modules/Search"

// Instantiate a new object using our modules/classes
const mobileMenu = new MobileMenu()
const heroSlider = new HeroSlider()
window.initBaiduMapFunction = function () {
  const baiduMap = new BaiduMap()
}

const search = new Search()
