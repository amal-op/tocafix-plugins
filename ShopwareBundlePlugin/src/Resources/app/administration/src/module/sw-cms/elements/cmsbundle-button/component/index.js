import template from "./sw-cms-el-cmsbundle-button.html.twig";
import "./sw-cms-el-cmsbundle-button.scss";

const { Component, Mixin } = Shopware;

Component.register("sw-cms-el-cmsbundle-button", {
  template,

  mixins: [Mixin.getByName("cms-element")],

  created() {
    this.createdComponent();
  },

  computed: {
    buttonText1() {
      return this.element.config.buttonText1.value;
    },
    buttonText2() {
      return this.element.config.buttonText2.value;
    },
    buttonText3() {
      return this.element.config.buttonText3.value;
    },
  },

  methods: {
    createdComponent() {
      this.initElementConfig("cmsbundle-button");
      this.initElementData("cmsbundle-button");
    },
  },
});
