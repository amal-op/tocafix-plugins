import template from "./sw-cms-el-preview-cmsbundle-button.html.twig";
import "./sw-cms-el-preview-cmsbundle-button.scss";

const { Component } = Shopware;

Component.register("sw-cms-el-preview-cmsbundle-button", {
  template,

  computed: {
    assetFilter() {
      return Shopware.Filter.getByName("asset");
    },
  },
});
