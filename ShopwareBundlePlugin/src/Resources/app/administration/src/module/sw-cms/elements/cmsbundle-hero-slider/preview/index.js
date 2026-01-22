import template from "./sw-cms-el-preview-cmsbundle-hero-slider.html.twig";
import "./sw-cms-el-preview-cmsbundle-hero-slider.scss";

const { Component } = Shopware;

Component.register("sw-cms-el-preview-cmsbundle-hero-slider", {
  template,

  computed: {
    assetFilter() {
      return Shopware.Filter.getByName("asset");
    },
  },
});
