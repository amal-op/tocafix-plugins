import "./component";
import "./config";
import "./preview";

Shopware.Service("cmsService").registerCmsElement({
  name: "cmsbundle-hero-slider-background",
  label: "cmsbundle.element.hero-slider-background.label",
  hidden: true,
  component: "sw-cms-el-cmsbundle-hero-slider-background",
  configComponent: "sw-cms-el-config-cmsbundle-hero-slider-background",
  previewComponent: "sw-cms-el-preview-cmsbundle-hero-slider-background",
  defaultConfig: {
    autoPlay: {
      source: "static",
      value: false,
    },
    speed: {
      source: "static",
      value: 800,
    },
    controlls: {
      source: "static",
      value: false,
    },
    dots: {
      source: "static",
      value: true,
    },
    loop: {
      source: "static",
      value: false,
    },
    colour: {
      source: "static",
      value: null,
    },
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
    customClass: {
      source: "static",
      value: "",
    },
    displayMode: {
      source: "static",
      value: "standard",
    },
    boxLayout: {
      source: "static",
      value: "standard",
    },
    navigation: {
      source: "static",
      value: true,
    },
    rotate: {
      source: "static",
      value: false,
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
      value: 1,
    },
    countTablet: {
      source: "static",
      value: 1,
    },
    countMobile: {
      source: "static",
      value: 1,
    },
    overlay: {
      source: "static",
      value: false,
    },
  },
});
