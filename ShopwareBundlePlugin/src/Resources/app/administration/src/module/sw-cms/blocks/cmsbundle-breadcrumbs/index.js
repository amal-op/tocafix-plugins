import "./component";
import "./preview";

Shopware.Service("cmsService").registerCmsBlock({
  name: "cmsbundle-breadcrumbs",
  label: "cmsbundle.block.breadcrumbs.label",
  category: "cmsbundleContents",
  component: "sw-cms-block-cmsbundle-breadcrumbs",
  previewComponent: "sw-cms-preview-cmsbundle-breadcrumbs",
  defaultConfig: {
    marginBottom: "20px",
    marginTop: "20px",
    marginLeft: "20px",
    marginRight: "20px",
    sizingMode: "boxed",
  },
  slots: {
    breadcrumbs: {
      type: "cmsbundle-breadcrumbs",
    },
  },
});
