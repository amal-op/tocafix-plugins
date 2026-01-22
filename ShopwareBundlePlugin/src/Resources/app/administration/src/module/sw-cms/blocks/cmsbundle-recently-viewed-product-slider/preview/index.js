import template from "./sw-cms-preview-cmsbundle-recently-viewed-product-slider.html.twig";
import "./sw-cms-preview-cmsbundle-recently-viewed-product-slider.scss";

const { Component } = Shopware;

Component.register("sw-cms-preview-cmsbundle-recently-viewed-product-slider", {
  template,

  computed: {
    assetFilter() {
      return Shopware.Filter.getByName("asset");
    },
  },
});
