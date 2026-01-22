import template from "./sw-cms-preview-cmsbundle-category-slider.html.twig";
import "./sw-cms-preview-cmsbundle-category-slider.scss";

const { Component } = Shopware;

Component.register("sw-cms-preview-cmsbundle-category-slider", {
  template,

  computed: {
    assetFilter() {
      return Shopware.Filter.getByName("asset");
    },
  },
});
