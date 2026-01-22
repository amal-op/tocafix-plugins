import template from "./index.html.twig";

Shopware.Component.register("sw-cms-el-preview-cmsbundle-breadcrumbs", {
  template,

  computed: {
    assetFilter() {
      return Shopware.Filter.getByName("asset");
    },
  },
});
