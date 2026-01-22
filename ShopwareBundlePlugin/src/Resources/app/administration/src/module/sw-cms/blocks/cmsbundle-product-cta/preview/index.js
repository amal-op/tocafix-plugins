import template from "./sw-cms-preview-cmsbundle-product-cta.html.twig";
import "./sw-cms-preview-cmsbundle-product-cta.scss";

const { Component } = Shopware;

Component.register("sw-cms-preview-cmsbundle-product-cta", {
  template,

  computed: {
    assetFilter() {
      return Shopware.Filter.getByName("asset");
    },
  },
});
