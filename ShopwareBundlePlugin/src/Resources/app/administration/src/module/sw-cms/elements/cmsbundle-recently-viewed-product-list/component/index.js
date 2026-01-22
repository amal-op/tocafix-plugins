import template from "./sw-cms-el-cmsbundle-recently-viewed-product-list.html.twig";
import "./sw-cms-el-cmsbundle-recently-viewed-product-list.scss";

const { Component, Mixin } = Shopware;

Component.register("sw-cms-el-cmsbundle-recently-viewed-product-list", {
  template,

  inject: ["feature"],

  mixins: [Mixin.getByName("cms-element")],

  data() {
    return {
      sliderBoxLimit: 3,
    };
  },
  watch: {
    currentDeviceView() {
      setTimeout(() => {
        this.setSliderRowLimit();
      }, 400);
    },
  },
  computed: {
    demoProductElement() {
      return {
        config: {
          boxLayout: {
            source: "static",
            value: this.element.config.boxLayout.value,
          },
          displayMode: {
            source: "static",
            value: this.element.config.displayMode.value,
          },
        },
        data: null,
      };
    },
    classes() {
      return {
        "has--border": !!this.element.config.border.value,
      };
    },

    currentDeviceView() {
      return this.cmsPageState.currentCmsDeviceView;
    },
    verticalAlignStyle() {
      if (!this.element.config.verticalAlign.value) {
        return null;
      }
      return `align-self: ${this.element.config.verticalAlign.value};`;
    },
  },

  created() {
    this.createdComponent();
  },

  methods: {
    createdComponent() {
      this.initElementConfig("cmsbundle-recently-viewed-product-list");
      this.initElementData("cmsbundle-recently-viewed-product-list");
    },
  },
});
