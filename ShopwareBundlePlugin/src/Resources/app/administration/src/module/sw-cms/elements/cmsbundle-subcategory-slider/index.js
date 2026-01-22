import "./component";
import "./config";
import "./preview";

Shopware.Service("cmsService").registerCmsElement({
  name: "cmsbundle-subcategory-slider",
  label: "element.subcategory-slider.label",
  component: "sw-cms-el-subcategory-slider",
  configComponent: "sw-cms-el-config-subcategory-slider",
  previewComponent: "sw-cms-el-preview-subcategory-slider",
  defaultConfig: {
    displayMode: {
      source: "static",
      value: "standard",
    },
    boxLayout: {
      source: "static",
      value: "standard",
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
      value: 4,
    },
    countTablet: {
      source: "static",
      value: 2,
    },
    countMobile: {
      source: "static",
      value: 1,
    },
    category: {
      source: "static",
      value: [],
      required: false,
      entity: {
        name: "category",
       
      },
    },
    selectedOption:{
      source: 'static',
      value: 'category',
    },
    autoPlay: {
      source: "static",
      value: false,
    },
    speed: {
      source: "static",
      value: 800,
    },
    navigation: {
      source: "static",
      value: false,
    },
    dots: {
      source: "static",
      value: true,
    },
    loop: {
      source: "static",
      value: false,
    },
    controls: {
      source: "static",
      value: false,
    },
  },
});
