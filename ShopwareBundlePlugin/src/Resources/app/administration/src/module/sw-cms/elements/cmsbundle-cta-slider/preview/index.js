import template from "./sw-cms-el-preview-cmsbundle-cta-slider.html.twig";
import "./sw-cms-el-preview-cmsbundle-cta-slider.scss";

const { Component } = Shopware;

Component.register("sw-cms-el-preview-cmsbundle-cta-slider", {
  template,
  computed: {
    assetFilter() {
      return Shopware.Filter.getByName("asset");
    },
  },

});
