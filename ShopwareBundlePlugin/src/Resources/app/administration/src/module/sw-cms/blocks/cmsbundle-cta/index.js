import "./component";
import "./preview";

Shopware.Service("cmsService").registerCmsBlock({
  name: "cmsbundle-cta",
  label: "cmsbundle.block.cta.label",
  category: "cmsbundleContents",
  component: "sw-cms-block-cmsbundle-cta",
  previewComponent: "sw-cms-preview-cmsbundle-cta",
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
    content: {
      type: "cmsbundle-cta-block",
      default: {
        config: {
          displayMode: { source: "static", value: "cover" },
        },
        data: {
          media: {
            url: "/shopwarebundleplugin/static/img/cta-element.jpg",
          },
        },
      },
    },
  },
});
