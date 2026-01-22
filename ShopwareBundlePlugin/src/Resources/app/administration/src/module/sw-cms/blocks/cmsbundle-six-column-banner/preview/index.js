import template from "./sw-cms-preview-cmsbundle-six-column-banner.html.twig";
import "./sw-cms-preview-cmsbundle-six-column-banner.scss";

const { Component } = Shopware;

Component.register("sw-cms-preview-cmsbundle-six-column-banner", {
  template,

  computed: {
    assetFilter() {
      return Shopware.Filter.getByName("asset");
    },
  },
});
