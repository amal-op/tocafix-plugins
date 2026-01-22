import "./component";
import "./config";
import "./preview";

Shopware.Service("cmsService").registerCmsElement({
  name: "cmsbundle-nav-tabs",
  label: "cmsbundle.element.nav-tabs.label",
  component: "sw-cms-el-cmsbundle-nav-tabs",
  configComponent: "sw-cms-el-config-cmsbundle-nav-tabs",
  previewComponent: "sw-cms-el-preview-cmsbundle-nav-tabs",
  defaultConfig: {
    data: {
      source: "static",
      value: [
        {
          name: "First Tab",
          content:
            '<p>Lorem ipsum dolor sit amet, <a href="#">consetetur</a> sadipscing elitr, <b>sed diam nonumy eirmod tempor</b> invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua</p>',
        },
      ],
    },
    verticalAlign: {
      source: "static",
      value: null,
    },
    displayMode: {
      source: "static",
      value: "single",
    },
  },
});
