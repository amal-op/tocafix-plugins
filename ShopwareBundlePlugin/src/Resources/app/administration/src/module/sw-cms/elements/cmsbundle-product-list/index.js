import "./component";
import "./config";
import "./preview";

const Criteria = Shopware.Data.Criteria;
const criteria = new Criteria(1, 25);
criteria.addAssociation('cover');

Shopware.Service("cmsService").registerCmsElement({
  name: "cmsbundle-product-list",
  label: "Product List",
  component: "sw-cms-el-cmsbundle-product-list",
  configComponent: "sw-cms-el-config-cmsbundle-product-list",
  previewComponent: "sw-cms-el-preview-cmsbundle-product-list",
  defaultConfig: {
    products: {
      source: "static",
      value: [],
      required: false,
      entity: {
        name: "product",
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
