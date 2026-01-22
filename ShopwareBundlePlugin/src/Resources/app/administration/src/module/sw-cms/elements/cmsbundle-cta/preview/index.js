import template from "./sw-cms-el-preview-cmsbundle-cta.html.twig";
import "./sw-cms-el-preview-cmsbundle-cta.scss";

const { Component } = Shopware;

Component.register("sw-cms-el-preview-cmsbundle-cta", {
  template,

  computed: {
    assetFilter() {
      return Shopware.Filter.getByName("asset");
    },
  },
});
