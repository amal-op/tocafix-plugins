import "./component";
import "./config";
import "./preview";

const Criteria = Shopware.Data.Criteria;
const criteria = new Criteria(1, 25);

Shopware.Service("cmsService").registerCmsElement({
  name: "cmsbundle-category-slider",
  label: "Category Slider",
  component: "sw-cms-el-cmsbundle-category-slider",
  configComponent: "sw-cms-el-config-cmsbundle-category-slider",
  previewComponent: "sw-cms-el-preview-cmsbundle-category-slider",
  defaultConfig: {
    categories: {
      source: "static",
      value: [],
      required: true,
      entity: {
        name: "category",
        criteria: criteria,
      },
    },
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
    showDescription: {
      source: "static",
      value: false,
    },
    showImage:{
      source: "static",
      value: true,
    }
  },
});
