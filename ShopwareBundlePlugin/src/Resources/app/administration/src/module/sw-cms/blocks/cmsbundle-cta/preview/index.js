import template from "./sw-cms-preview-cmsbundle-cta.html.twig";
import "./sw-cms-preview-cmsbundle-cta.scss";

const { Component } = Shopware;

Component.register("sw-cms-preview-cmsbundle-cta", {
  template,

  computed: {
    assetFilter() {
      return Shopware.Filter.getByName("asset");
    },
  },
});
