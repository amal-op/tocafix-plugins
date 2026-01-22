import "./component";
import "./preview";

Shopware.Service("cmsService").registerCmsBlock({
  name: "cmsbundle-recently-viewed-product-list",
  label: "cmsbundle.block.recently-viewed-product-list.label",
  category: "cmsbundleListing",
  component: "sw-cms-block-cmsbundle-recently-viewed-product-list",
  previewComponent: "sw-cms-preview-cmsbundle-recently-viewed-product-list",
  defaultConfig: {
    marginBottom: '20px',
    marginTop: '20px',
    marginLeft: '20px',
    marginRight: '20px',
    sizingMode: 'boxed'
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
    product: {
      type: "cmsbundle-recently-viewed-product-list",
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
