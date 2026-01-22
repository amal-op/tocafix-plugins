import template from "./sw-cms-preview-cmsbundle-product-list.html.twig";
import "./sw-cms-preview-cmsbundle-product-list.scss";

const { Component } = Shopware;

Component.register("sw-cms-preview-cmsbundle-product-list", {
  template,

  computed: {
    assetFilter() {
      return Shopware.Filter.getByName("asset");
    },
  },
});
