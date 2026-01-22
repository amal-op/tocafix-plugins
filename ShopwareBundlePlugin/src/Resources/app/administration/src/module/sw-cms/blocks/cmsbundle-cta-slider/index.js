import "./component";
import "./preview";

Shopware.Service("cmsService").registerCmsBlock({
  name: "cmsbundle-cta-slider",
  label: "cmsbundle.block.cta-slider.label",
  category: "cmsbundleSliders",
  component: "sw-cms-block-cmsbundle-cta-slider",
  previewComponent: "sw-cms-preview-cmsbundle-cta-slider",
  defaultConfig: {
    marginBottom: "20px",
    marginTop: "20px",
    marginLeft: "20px",
    marginRight: "20px",
    sizingMode: "boxed",
  },
  slots: {
    background: {
      type: "cmsbundle-hero-slider-background",
      default: {
        config: {
          displayMode: { source: "static", value: "cover" },
        },
        data: {
          media: {
            url: "/shopwarebundleplugin/static/img/section-title.png",
          },
        },
      },
    },
    title: {
      type: "cmsbundle-title",
      default: {
        config: {
          displayMode: { source: "static", value: "cover" },
        },
        data: {
          media: {
            url: "/shopwarebundleplugin/static/img/section-title.png",
          },
        },
      },
    },
    cta1: {
      type: "cmsbundle-cta",
      default: null,
    },
    cta2: {
      type: "cmsbundle-cta",
      default: null,
    },
    cta3: {
      type: "cmsbundle-cta",
      default: null,
    },
    cta4: {
      type: "cmsbundle-cta",
      default: null,
    },
    cta5: {
      type: "cmsbundle-cta",
      default: null,
    },
    cta6: {
      type: "cmsbundle-cta",
      default: null,
    },
    button: {
      type: "cmsbundle-button",
      default: {
        config: {
          displayMode: { source: "static", value: "cover" },
        },
        data: {
          media: {
            url: "/shopwarebundleplugin/static/img/section-cta.png",
          },
        },
      },
    },
  },
});
