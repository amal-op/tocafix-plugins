import template from "./sw-cms-el-cmsbundle-title.html.twig";
import "./sw-cms-el-cmsbundle-title.scss";

const { Component, Mixin } = Shopware;

Component.register("sw-cms-el-cmsbundle-title", {
  template,

  mixins: [Mixin.getByName("cms-element")],

  created() {
    this.createdComponent();
  },

  methods: {
    createdComponent() {
      this.initElementConfig("cmsbundle-title");
      this.initElementData("cmsbundle-title");
    },
  },

  computed: {
    mainTitle() {
      if (
        this.element.config.title.value &&
        this.element.config.title.value !== ""
      ) {
        return this.element.config.title.value;
      }
      return this.$tc("cmsbundle.element.title.mainTitle");
    },
    subTitle() {
      if (
        this.element.config.subTitle.value &&
        this.element.config.subTitle.value !== ""
      ) {
        return this.element.config.subTitle.value;
      }
      return this.$tc("cmsbundle.element.title.subTitle");
    },
    shortDescription() {
      if (
        this.element.config.shortDescription.value &&
        this.element.config.shortDescription.value !== ""
      ) {
        return this.element.config.shortDescription.value;
      }
      return this.$tc("cmsbundle.element.title.shortDescription");
    },
  },
});
