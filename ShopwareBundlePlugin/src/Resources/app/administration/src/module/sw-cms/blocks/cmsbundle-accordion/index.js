import "./component";
import "./preview";

Shopware.Service("cmsService").registerCmsBlock({
  name: "cmsbundle-accordion",
  label: "cmsbundle.block.accordion.label",
  category: "cmsbundleContents",
  component: "sw-cms-block-cmsbundle-accordion",
  previewComponent: "sw-cms-preview-cmsbundle-accordion",
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
    accordion: {
      type: "cmsbundle-accordion",
      default: null,
    },
  },
});
