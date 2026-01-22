import "./component";
import "./preview";

Shopware.Service("cmsService").registerCmsBlock({
  name: "cmsbundle-nav-tabs",
  label: "cmsbundle.block.nav-tabs.label",
  category: "cmsbundleContents",
  component: "sw-cms-block-cmsbundle-nav-tabs",
  previewComponent: "sw-cms-preview-cmsbundle-nav-tabs",
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
    tabs: {
      type: "cmsbundle-nav-tabs",
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
  },
});
