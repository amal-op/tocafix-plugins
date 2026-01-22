import "./component";
import "./config";
import "./preview";

Shopware.Service("cmsService").registerCmsElement({
  name: "cmsbundle-manufacturer-detail",
  label: "cmsbundle.element.manufacturer-detail.label",
  component: "sw-cms-el-cmsbundle-manufacturer-detail",
  configComponent: "sw-cms-el-config-cmsbundle-manufacturer-detail",
  previewComponent: "sw-cms-el-preview-cmsbundle-manufacturer-detail",
  defaultConfig: {
    manufacturerId: {
      source: "static",
      value: null,
      required: true,
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
  },
});
