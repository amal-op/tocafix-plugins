import template from "./sw-cms-preview-cmsbundle-category-list.html.twig";
import "./sw-cms-preview-cmsbundle-category-list.scss";

const { Component } = Shopware;

Component.register("sw-cms-preview-cmsbundle-category-list", {
  template,

  computed: {
    assetFilter() {
      return Shopware.Filter.getByName("asset");
    },
  },
});
