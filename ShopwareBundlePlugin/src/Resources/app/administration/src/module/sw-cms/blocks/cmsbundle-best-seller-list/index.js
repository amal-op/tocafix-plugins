import "./component";
import "./preview";

Shopware.Service("cmsService").registerCmsBlock({
  name: "cmsbundle-best-seller-list",
  label: "cmsbundle.block.best-seller-list.label",
  category: "cmsbundleListing",
  component: "sw-cms-block-cmsbundle-best-seller-list",
  previewComponent: "sw-cms-preview-cmsbundle-best-seller-list",
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
    bestSeller: {
      type: "cmsbundle-best-seller-list",
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
