import template from "./sw-cms-el-sub-category-slider.html.twig";
import "./sw-cms-el-sub-category-slider.scss";

const { Component, Mixin } = Shopware;

Component.register("sw-cms-el-subcategory-slider", {
  template,

  inject: ["feature"],

  mixins: [Mixin.getByName("cms-element")],

  computed: {
    assetFilter() {
      return Shopware.Filter.getByName("asset");
    },
  },
  created() {
    this.createdComponent();
  },

  methods: {
    createdComponent() {
      this.initElementConfig("cmsbundle-subcategory-slider");
      this.initElementData("cmsbundle-subcategory-slider");
    },
  },
});
