import "./component";
import "./config";
import "./preview";

Shopware.Service("cmsService").registerCmsElement({
  name: "cmsbundle-title",
  label: "cmsbundle.element.title.label",
  hidden: true,
  component: "sw-cms-el-cmsbundle-title",
  configComponent: "sw-cms-el-config-cmsbundle-title",
  previewComponent: "sw-cms-el-preview-cmsbundle-title",
  defaultConfig: {
    title: {
      source: "static",
      value: null,
    },
    subTitle: {
      source: "static",
      value: null,
    },
    titleHeading: {
      source: "static",
      value: null,
    },
    subTitleHeading: {
      source: "static",
      value: null,
    },
    shortDescription: {
      source: "static",
      value: null,
    },
    ctaButton: {
      source: "static",
      value: false,
    },
    titlePosition: {
      source: "static",
      value: "left",
    },
    url: {
      source: "static",
      value: null,
    },
    newTab: {
      source: "static",
      value: false,
    },
    buttonText: {
      source: "static",
      value: null,
    },
    buttonType: {
      source: "static",
      value: null,
    },
  },
});
