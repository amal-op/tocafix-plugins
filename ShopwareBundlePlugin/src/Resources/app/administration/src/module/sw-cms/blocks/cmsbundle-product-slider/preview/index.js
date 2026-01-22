import template from "./sw-cms-preview-cmsbundle-product-slider.html.twig";
import "./sw-cms-preview-cmsbundle-product-slider.scss";

const { Component } = Shopware;

Component.register("sw-cms-preview-cmsbundle-product-slider", {
  template,

  computed: {
    assetFilter() {
      return Shopware.Filter.getByName("asset");
    },
  },
});
