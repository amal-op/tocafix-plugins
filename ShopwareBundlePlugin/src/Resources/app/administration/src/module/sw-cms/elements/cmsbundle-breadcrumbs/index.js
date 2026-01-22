import "./component";
import "./config";
import "./preview";

Shopware.Service("cmsService").registerCmsElement({
  name: "cmsbundle-breadcrumbs",
  label: "cmsbundle.element.breadcrumbs.label",
  component: "sw-cms-el-cmsbundle-breadcrumbs",
  configComponent: "sw-cms-el-config-cmsbundle-breadcrumbs",
  previewComponent: "sw-cms-el-preview-cmsbundle-breadcrumbs",
  defaultConfig: {
    horizontalAlign: {
      source: "static",
      value: "flex-start",
    },
  },
});
