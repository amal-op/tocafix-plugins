import template from "./sw-cms-preview-cmsbundle-content-block.html.twig";
import "./sw-cms-preview-cmsbundle-content-block.scss";

const { Component } = Shopware;

Component.register("sw-cms-preview-cmsbundle-content-block", {
  template,

  computed: {
    assetFilter() {
      return Shopware.Filter.getByName("asset");
    },
  },
});
