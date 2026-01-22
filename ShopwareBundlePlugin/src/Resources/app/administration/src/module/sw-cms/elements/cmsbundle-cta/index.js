import "./component";
import "./config";
import "./preview";

Shopware.Service("cmsService").registerCmsElement({
  name: "cmsbundle-cta",
  label: "cmsbundle.element.cta.label",
  component: "sw-cms-el-cmsbundle-cta",
  configComponent: "sw-cms-el-config-cmsbundle-cta",
  previewComponent: "sw-cms-el-preview-cmsbundle-cta",
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
    content: {
      source: "static",
      value: null,
    },
    customClass: {
      source: "static",
      value: "",
    },
    url1: {
      source: "static",
      value: null,
    },
    newTab1: {
      source: "static",
      value: false,
    },
    buttonText1: {
      source: "static",
      value: null,
    },
    buttonType1: {
      source: "static",
      value: null,
    },
    buttomLevel1: {
      source: "static",
      value: false,
    },
    url2: {
      source: "static",
      value: null,
    },
    newTab2: {
      source: "static",
      value: false,
    },
    buttonText2: {
      source: "static",
      value: null,
    },
    buttonType2: {
      source: "static",
      value: null,
    },
    buttomLevel2: {
      source: "static",
      value: false,
    },
    url3: {
      source: "static",
      value: null,
    },
    newTab3: {
      source: "static",
      value: false,
    },
    buttonText3: {
      source: "static",
      value: null,
    },
    buttonType3: {
      source: "static",
      value: null,
    },
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
    titlePosition: {
      source: "static",
      value: "start",
    },
    alignment: {
      source: "static",
      value: "center",
    },
    backgroundDesktop: {
      source: "static",
      value: null,
      required: false,
      entity: {
        name: "media",
      },
    },
    backgroundTablet: {
      source: "static",
      value: null,
      required: false,
      entity: {
        name: "media",
      },
    },
    backgroundMobile: {
      source: "static",
      value: null,
      required: false,
      entity: {
        name: "media",
      },
    },
    backgroundColor: {
      source: "static",
      value: null,
    },
  },
});
