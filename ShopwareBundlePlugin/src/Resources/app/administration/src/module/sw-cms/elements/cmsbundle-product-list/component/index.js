import template from "./sw-cms-el-cmsbundle-product-list.html.twig";
import "./sw-cms-el-cmsbundle-product-list.scss";

const { Component, Mixin } = Shopware;

Component.register("sw-cms-el-cmsbundle-product-list", {
  template,

  inject: ["feature"],

  mixins: [Mixin.getByName("cms-element")],

  data() {
    return {
      sliderBoxLimit: 3,
    };
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
    getDummyBoxes() {
      return function (limit) {
        const dummyBoxes = [];
        for (let i = 0; i < limit; i++) {
          dummyBoxes.push(i);
        }
        return dummyBoxes;
      };
    },
  },

  created() {
    this.createdComponent();
  },

  methods: {
    createdComponent() {
      this.initElementConfig("cmsbundle-product-slider");
      this.initElementData("cmsbundle-product-slider");
    },

    getProductEl(product) {
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
        data: {
          product,
        },
      };
    },
  },
});
