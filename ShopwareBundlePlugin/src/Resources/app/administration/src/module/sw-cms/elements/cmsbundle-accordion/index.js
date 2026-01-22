import "./component";
import "./config";
import "./preview";

Shopware.Service("cmsService").registerCmsElement({
  name: "cmsbundle-accordion",
  label: "cmsbundle.element.accordion.label",
  component: "sw-cms-el-cmsbundle-accordion",
  configComponent: "sw-cms-el-config-cmsbundle-accordion",
  previewComponent: "sw-cms-el-preview-cmsbundle-accordion",
  defaultConfig: {
    data: {
      source: "static",
      value: [
        {
          name: "First Data",
          content:
            '<p>Lorem ipsum dolor sit amet, <a href="#">consetetur</a> sadipscing elitr, <b>sed diam nonumy eirmod tempor</b> invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua</p>',
        },
        {
          name: "Second Data",
          content:
            '<p>Lorem ipsum dolor sit amet, <a href="#">consetetur</a> sadipscing elitr, <b>sed diam nonumy eirmod tempor</b> invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua</p>',
        },
      ],
    },
    autoClose: {
      source: "static",
      value: true,
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
