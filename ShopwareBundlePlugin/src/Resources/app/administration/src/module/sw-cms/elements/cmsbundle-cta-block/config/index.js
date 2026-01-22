import template from "./sw-cms-el-config-cmsbundle-cta-block.html.twig";
import "./sw-cms-el-config-cmsbundle-cta-block.scss";

const { Component, Mixin } = Shopware;

Component.register("sw-cms-el-config-cmsbundle-cta-block", {
  template,

  inject: ["repositoryFactory"],

  mixins: [Mixin.getByName("cms-element")],

  computed: {
    contentUpdate: {
      get() {
        return this.element.config.content.value;
      },

      set(value) {
        this.element.config.content.value = value;
      },
    },
  },

  created() {
    this.createdComponent();
  },

  methods: {
    createdComponent() {
      this.initElementConfig("cmsbundle-cta-block");
    },

    onElementUpdate(value) {
      this.element.config.content.value = value;

      this.$emit("element-update", this.element);
    },
  },
});
