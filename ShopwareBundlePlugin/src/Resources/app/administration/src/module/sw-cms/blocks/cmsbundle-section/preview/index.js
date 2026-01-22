import template from "./sw-cms-preview-cmsbundle-section.html.twig";
import "./sw-cms-preview-cmsbundle-section.scss";

const { Component } = Shopware;

Component.register("sw-cms-preview-cmsbundle-section", {
  template,

  computed: {
    assetFilter() {
      return Shopware.Filter.getByName("asset");
    },
  },
});
