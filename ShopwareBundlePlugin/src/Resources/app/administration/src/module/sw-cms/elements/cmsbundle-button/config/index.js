import template from "./sw-cms-el-config-cmsbundle-button.html.twig";
import "./sw-cms-el-config-cmsbundle-button.scss";

const { Component, Mixin } = Shopware;

Component.register("sw-cms-el-config-cmsbundle-button", {
  template,

  inject: ["repositoryFactory"],

  mixins: [Mixin.getByName("cms-element")],

  created() {
    this.createdComponent();
  },

  methods: {
    createdComponent() {
      this.initElementConfig("cmsbundle-button");
    },
  },
});
