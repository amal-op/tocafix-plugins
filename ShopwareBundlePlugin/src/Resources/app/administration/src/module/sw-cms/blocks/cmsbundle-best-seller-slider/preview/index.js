import template from "./sw-cms-preview-cmsbundle-best-seller-slider.html.twig";
import "./sw-cms-preview-cmsbundle-best-seller-slider.scss";

const { Component } = Shopware;

Component.register("sw-cms-preview-cmsbundle-best-seller-slider", {
  template,

  computed: {
    assetFilter() {
      return Shopware.Filter.getByName("asset");
    },
  },
});
