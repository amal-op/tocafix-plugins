import "./component";
import "./preview";

Shopware.Service("cmsService").registerCmsBlock({
  name: "cmsbundle-content-block",
  label: "cmsbundle.block.content-block.label",
  category: "cmsbundleContents",
  component: "sw-cms-block-cmsbundle-content-block",
  previewComponent: "sw-cms-preview-cmsbundle-content-block",
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
    left: {
      type: "cmsbundle-cta",
      default: {
        config: {
          displayMode: { source: "static", value: "cover" },
        },
        data: {
          media: {
            url: "/shopwarebundleplugin/static/img/column-banner.png",
          },
        },
      },
    },
    right: {
      type: "cmsbundle-image",
      default: {
        config: {
          displayMode: { source: "static", value: "cover" },
        },
        data: {
          media: {
            url: "/shopwarebundleplugin/static/img/column-banner.png",
          },
        },
      },
    },
  },
});
