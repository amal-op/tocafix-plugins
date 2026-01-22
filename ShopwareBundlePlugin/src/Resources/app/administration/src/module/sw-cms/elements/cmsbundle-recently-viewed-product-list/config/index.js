import template from "./sw-cms-el-config-cmsbundle-recently-viewed-product-list.html.twig";
import "./sw-cms-el-config-cmsbundle-recently-viewed-product-list.scss";

const { Component, Mixin } = Shopware;

Component.register("sw-cms-el-config-cmsbundle-recently-viewed-product-list", {
  template,

  inject: ["feature"],

  mixins: [Mixin.getByName("cms-element")],

  created() {
    this.createdComponent();
  },

  methods: {
    createdComponent() {
      this.initElementConfig("cmsbundle-recently-viewed-product-list");
    },
  },
});
