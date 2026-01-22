import template from "./sw-cms-preview-cmsbundle-hero-slider.html.twig";
import "./sw-cms-preview-cmsbundle-hero-slider.scss";

const { Component } = Shopware;

Component.register("sw-cms-preview-cmsbundle-hero-slider", {
  template,

  computed: {
    assetFilter() {
      return Shopware.Filter.getByName("asset");
    },
  },
});
