import template from "./sw-cms-el-tocafix-job-teaser.html.twig";
import "./sw-cms-el-tocafix-job-teaser.scss";

const { Component, Mixin } = Shopware;

Component.register("sw-cms-el-tocafix-job-teaser", {
  template,

  mixins: [Mixin.getByName("cms-element")],

  created() {
    this.createdComponent();
  },
  computed: {
    assetFilter() {
      return Shopware.Filter.getByName("asset");
    },
  },
  methods: {
    createdComponent() {
      this.initElementConfig("tocafix-job-teaser");
    },
  },
});
