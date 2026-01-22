import "./component";
import "./config";
import "./preview";

Shopware.Service("cmsService").registerCmsElement({
  name: "cmsbundle-recently-viewed-product-list",
  label: "cmsbundle.block.recently-viewed-product-list.label",
  component: "sw-cms-el-cmsbundle-recently-viewed-product-list",
  configComponent: "sw-cms-el-config-cmsbundle-recently-viewed-product-list",
  previewComponent: "sw-cms-el-preview-cmsbundle-recently-viewed-product-list",
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
    productsPerPage:{
      source: "static",
      value: 4,
    }
  },
});
