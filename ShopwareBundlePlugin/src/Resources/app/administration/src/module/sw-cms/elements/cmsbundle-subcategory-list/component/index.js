import template from "./sw-cms-el-sub-category-list.html.twig";
import "./sw-cms-el-sub-category-list.scss";

const { Component, Mixin } = Shopware;

Component.register("sw-cms-el-sub-category-list", {
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
      this.initElementConfig("cmsbundle-subcategory-list");
      this.initElementData("cmsbundle-subcategory-list");
    },
  },
});
