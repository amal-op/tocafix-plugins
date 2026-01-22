import template from "./sw-cms-el-preview-cmsbundle-cta-block.html.twig";
import "./sw-cms-el-preview-cmsbundle-cta-block.scss";

const { Component } = Shopware;

Component.register("sw-cms-el-preview-cmsbundle-cta-block", {
  template,

  computed: {
    assetFilter() {
      return Shopware.Filter.getByName("asset");
    },
  },
});
