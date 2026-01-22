import "./component";
import "./config";
import "./preview";

Shopware.Service("cmsService").registerCmsElement({
  name: "cmsbundle-subcategory-list",
  label: "element.subcategory-list.label",
  component: "sw-cms-el-sub-category-list",
  configComponent: "sw-cms-el-config-sub-category-list",
  previewComponent: "sw-cms-el-preview-sub-category-list",
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
    }
  },
});
