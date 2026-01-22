import template from "./sw-cms-el-config-cmsbundle-best-seller-list.html.twig";
import "./sw-cms-el-config-cmsbundle-best-seller-list.scss";

const { Component, Mixin } = Shopware;
const { Criteria, EntityCollection } = Shopware.Data;

Component.register("sw-cms-el-config-cmsbundle-best-seller-list", {
  template,

  inject: ["repositoryFactory", "feature"],

  mixins: [Mixin.getByName("cms-element")],

  data() {
    return {
      productCollection: null,
      showProductStreamPreview: false,

      // Temporary values to store the previous selection in case the user changes the assignment type.
      tempProductIds: [],
      tempStreamId: null,
    };
  },

  created() {
    this.createdComponent();
  },

  methods: {
    createdComponent() {
      this.initElementConfig("cmsbundle-best-seller-list");
    },
  },
});
