import "./component";
import "./preview";

Shopware.Service("cmsService").registerCmsBlock({
  name: "cmsbundle-product-cta",
  label: "cmsbundle.block.product-cta.label",
  category: "cmsbundleContents",
  component: "sw-cms-block-cmsbundle-product-cta",
  previewComponent: "sw-cms-preview-cmsbundle-product-cta",
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
      type: "cmsbundle-product-cta",
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
