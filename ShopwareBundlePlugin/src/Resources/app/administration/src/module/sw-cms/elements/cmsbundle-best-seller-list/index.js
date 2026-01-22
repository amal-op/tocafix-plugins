import "./component";
import "./config";
import "./preview";

const Criteria = Shopware.Data.Criteria;
const criteria = new Criteria(1, 25);

Shopware.Service("cmsService").registerCmsElement({
  name: "cmsbundle-best-seller-list",
  label: "cmsbundle.element.best-seller-list.label",
  component: "sw-cms-el-cmsbundle-best-seller-list",
  configComponent: "sw-cms-el-config-cmsbundle-best-seller-list",
  previewComponent: "sw-cms-el-preview-cmsbundle-best-seller-list",
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
    productsPerPage:{
      source: "static",
      value: 4,
    }
  },
});
