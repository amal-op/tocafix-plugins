import template from "./sw-cms-el-preview-cmsbundle-image.html.twig";
import "./sw-cms-el-preview-cmsbundle-image.scss";

const { Component } = Shopware;

Component.register("sw-cms-el-preview-cmsbundle-image", {
  template,

  computed: {
    assetFilter() {
      return Shopware.Filter.getByName("asset");
    },
  },
});
