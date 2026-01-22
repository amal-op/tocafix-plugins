import template from "./index.html.twig";
import "./index.scss";

Shopware.Component.register("sw-cms-preview-cmsbundle-manufacturer-detail", {
  template,

  computed: {
    assetFilter() {
      return Shopware.Filter.getByName("asset");
    },
  },
});
