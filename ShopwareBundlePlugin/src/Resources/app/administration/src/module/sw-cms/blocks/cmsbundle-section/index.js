import "./component";
import "./preview";

Shopware.Service("cmsService").registerCmsBlock({
  name: "cmsbundle-section",
  label: "cmsbundle.block.section.label",
  category: "cmsbundleContents",
  component: "sw-cms-block-cmsbundle-section",
  previewComponent: "sw-cms-preview-cmsbundle-section",
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
    content1: {
      type: "cmsbundle-cta",
      default: {
        config: {
          displayMode: { source: "static", value: "cover" },
        },
        data: {
          media: {
            url: "/shopwarebundleplugin/static/img/vertical-column.jpg",
          },
        },
      },
    },
    content2: {
      type: "cmsbundle-cta",
      default: {
        config: {
          displayMode: { source: "static", value: "cover" },
        },
        data: {
          media: {
            url: "/shopwarebundleplugin/static/img/vertical-column.jpg",
          },
        },
      },
    },
    content3: {
      type: "cmsbundle-cta",
      default: {
        config: {
          displayMode: { source: "static", value: "cover" },
        },
        data: {
          media: {
            url: "/shopwarebundleplugin/static/img/vertical-column.jpg",
          },
        },
      },
    },
    content4: {
      type: "cmsbundle-cta",
      default: {
        config: {
          displayMode: { source: "static", value: "cover" },
        },
        data: {
          media: {
            url: "/shopwarebundleplugin/static/img/vertical-column.jpg",
          },
        },
      },
    },
    content5: {
      type: "cmsbundle-cta",
      default: {
        config: {
          displayMode: { source: "static", value: "cover" },
        },
        data: {
          media: {
            url: "/shopwarebundleplugin/static/img/vertical-column.jpg",
          },
        },
      },
    },
    content6: {
      type: "cmsbundle-cta",
      default: {
        config: {
          displayMode: { source: "static", value: "cover" },
        },
        data: {
          media: {
            url: "/shopwarebundleplugin/static/img/vertical-column.jpg",
          },
        },
      },
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
