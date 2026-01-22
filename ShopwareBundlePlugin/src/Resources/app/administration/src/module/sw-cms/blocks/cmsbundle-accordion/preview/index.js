import template from "./index.html.twig";
import "./index.scss";

const { Component } = Shopware;

Component.register("sw-cms-preview-cmsbundle-accordion", {
  template,

  computed: {
    assetFilter() {
      return Shopware.Filter.getByName("asset");
    },
  },
});
