import template from "./sw-cms-preview-cmsbundle-best-seller-list.html.twig";
import "./sw-cms-preview-cmsbundle-best-seller-list.scss";

const { Component } = Shopware;

Component.register("sw-cms-preview-cmsbundle-best-seller-list", {
  template,

  computed: {
    assetFilter() {
      return Shopware.Filter.getByName("asset");
    },
  },
});
