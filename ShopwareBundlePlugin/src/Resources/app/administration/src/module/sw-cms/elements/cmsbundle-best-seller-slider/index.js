import "./component";
import "./config";
import "./preview";

Shopware.Service("cmsService").registerCmsElement({
  name: "cmsbundle-best-seller-slider",
  label: "cmsbundle.element.best-seller-slider.label",
  component: "sw-cms-el-cmsbundle-best-seller-slider",
  configComponent: "sw-cms-el-config-cmsbundle-best-seller-slider",
  previewComponent: "sw-cms-el-preview-cmsbundle-best-seller-slider",
  defaultConfig: {
    displayMode: {
      source: "static",
      value: "standard",
    },
    boxLayout: {
      source: "static",
      value: "standard",
    },
    navigation: {
      source: "static",
      value: true,
    },
    rotate: {
      source: "static",
      value: false,
    },
    border: {
      source: "static",
      value: false,
    },
    elMinWidth: {
      source: "static",
      value: "300px",
    },
    verticalAlign: {
      source: "static",
      value: null,
    },
    countDesktop: {
      source: "static",
      value: 3,
    },
    countTablet: {
      source: "static",
      value: 2,
    },
    countMobile: {
      source: "static",
      value: 1,
    },
    autoPlay: {
      source: "static",
      value: false,
    },
    speed: {
      source: "static",
      value: 800,
    },
    dots: {
      source: "static",
      value: true,
    },
    loop: {
      source: "static",
      value: false,
    },
    controlls: {
      source: "static",
      value: false,
    },
    productsPerPage:{
      source: "static",
      value: 4,
    },
    productColorProperty:{
      source: "static",
      value: null,
    }
  },
});
