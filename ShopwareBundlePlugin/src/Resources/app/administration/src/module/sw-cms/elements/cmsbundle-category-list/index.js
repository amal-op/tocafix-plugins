import "./component";
import "./config";
import "./preview";

const Criteria = Shopware.Data.Criteria;
const criteria = new Criteria(1, 25);

Shopware.Service("cmsService").registerCmsElement({
  name: "cmsbundle-category-list",
  label: "cmsbundle.block.category-list.label",
  component: "sw-cms-el-cmsbundle-category-list",
  configComponent: "sw-cms-el-config-cmsbundle-category-list",
  previewComponent: "sw-cms-el-preview-cmsbundle-category-list",
  defaultConfig: {
    categories: {
      source: "static",
      value: [],
      required: false,
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
      value: 6,
    },
    countTablet: {
      source: "static",
      value: 4,
    },
    countMobile: {
      source: "static",
      value: 2,
    },
    showDescription: {
      source: "static",
      value: false,
    },
    showImage: {
      source: "static",
      value: false,
    },
  },
});
