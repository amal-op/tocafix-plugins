import "./component";
import "./config";
import "./preview";

Shopware.Service("cmsService").registerCmsElement({
  name: "cmsbundle-background-image",
  label: "cmsbundle.element.background-image.label",
  component: "sw-cms-el-cmsbundle-background-image",
  configComponent: "sw-cms-el-config-cmsbundle-background-image",
  previewComponent: "sw-cms-el-preview-cmsbundle-background-image",
  defaultConfig: {
    mediaDesktop: {
      source: "static",
      value: null,
      required: false,
      entity: {
        name: "media",
      },
    },
    mediaTablet: {
      source: "static",
      value: null,
      required: false,
      entity: {
        name: "media",
      },
    },
    mediaMobile: {
      source: "static",
      value: null,
      required: false,
      entity: {
        name: "media",
      },
    },
    lazyLoad: {
      source: "static",
      value: false,
    },
    alignment: {
      source: "static",
      value: "",
    },
    customClass: {
      source: "static",
      value: "",
    },
    colour: {
      source: "static",
      value: null,
    },
  },
});
