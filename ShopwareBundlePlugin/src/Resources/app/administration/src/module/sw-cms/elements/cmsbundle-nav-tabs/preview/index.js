import template from "./index.html.twig";
import "./index.scss";

Shopware.Component.register("sw-cms-el-preview-cmsbundle-nav-tabs", {
  template,

  computed: {
    assetFilter() {
      return Shopware.Filter.getByName("asset");
    },
  },
});
