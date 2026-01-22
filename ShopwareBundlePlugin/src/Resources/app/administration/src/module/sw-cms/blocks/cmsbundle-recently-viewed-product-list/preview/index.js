import template from "./sw-cms-preview-cmsbundle-recently-viewed-product-list.html.twig";
import "./sw-cms-preview-cmsbundle-recently-viewed-product-list.scss";

const { Component } = Shopware;

Component.register("sw-cms-preview-cmsbundle-recently-viewed-product-list", {
  template,

  computed: {
    assetFilter() {
      return Shopware.Filter.getByName("asset");
    },
  },
});
