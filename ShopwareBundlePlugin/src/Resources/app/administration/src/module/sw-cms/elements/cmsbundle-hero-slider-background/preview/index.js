import template from "./sw-cms-el-preview-cmsbundle-hero-slider-background.html.twig";
import "./sw-cms-el-preview-cmsbundle-hero-slider-background.scss";

const { Component } = Shopware;

Component.register("sw-cms-el-preview-cmsbundle-hero-slider-background", {
  template,

  computed: {
    assetFilter() {
      return Shopware.Filter.getByName("asset");
    },
  },
});
