import template from "./sw-cms-el-preview-cmsbundle-product-cta-block.html.twig";
import "./sw-cms-el-preview-cmsbundle-product-cta-block.scss";

const { Component } = Shopware;

Component.register("sw-cms-el-preview-cmsbundle-product-cta-block", {
  template,

  computed: {
    assetFilter() {
      return Shopware.Filter.getByName("asset");
    },
  },
});
