import template from "./sw-cms-el-preview-cmsbundle-background-image.html.twig";
import "./sw-cms-el-preview-cmsbundle-background-image.scss";

const { Component } = Shopware;

Component.register("sw-cms-el-preview-cmsbundle-background-image", {
  template,

  computed: {
    assetFilter() {
      return Shopware.Filter.getByName("asset");
    },
  },
});
