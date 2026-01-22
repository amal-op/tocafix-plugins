import "./component";
import "./preview";

Shopware.Service("cmsService").registerCmsBlock({
  name: "cmsbundle-subcategory-slider",
  label: "cmsbundle.block.subcategory-slider.label",
  category: "cmsbundleSliders",
  component: "sw-cms-block-cmsbundle-subcategory-slider",
  previewComponent: "sw-cms-preview-cmsbundle-subcategory-slider",
  defaultConfig: {
    marginBottom: "20px",
    marginTop: "20px",
    marginLeft: "20px",
    marginRight: "20px",
    sizingMode: "boxed",
  },
  slots: {
    background: {
      type: "cmsbundle-background-image",
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
    subcategory: {
      type: "cmsbundle-subcategory-slider",
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
