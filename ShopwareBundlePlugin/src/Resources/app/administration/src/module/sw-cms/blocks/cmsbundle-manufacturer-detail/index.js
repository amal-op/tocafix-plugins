import "./preview";
import "./component";

const shopwareVersionService = Shopware.Service("ShopwareVersionService");

if (
  shopwareVersionService.compareVersions(
    shopwareVersionService.getVersion(),
    "6.5.3.0"
  ) >= 0
) {
  Shopware.Service("cmsService").registerCmsBlock({
    name: "cmsbundle-manufacturer-detail",
    label: "cmsbundle.block.manufacturer-detail.label",
    category: "cmsbundleListing",
    component: "sw-cms-block-cmsbundle-manufacturer-detail",
    previewComponent: "sw-cms-preview-cmsbundle-manufacturer-detail",
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
            title: { source: "static", value: "Manufacturers" },
          },
          data: {
            title: { source: "static", value: "Manufacturers" },
          },
        },
      },
      manufacturer: {
        type: "cmsbundle-manufacturer-detail",
      },
      button: {
        type: "cmsbundle-button",
      },
    },
  });
}
