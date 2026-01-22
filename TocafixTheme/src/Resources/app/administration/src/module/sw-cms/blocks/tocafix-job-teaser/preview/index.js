import template from "./sw-cms-preview-tocafix-job-teaser.html.twig";
import "./sw-cms-preview-tocafix-job-teaser.scss";

Shopware.Component.register("sw-cms-preview-tocafix-job-teaser", {
  template,
  computed: {
    assetFilter() {
      return Shopware.Filter.getByName("asset");
    },
  }
});
