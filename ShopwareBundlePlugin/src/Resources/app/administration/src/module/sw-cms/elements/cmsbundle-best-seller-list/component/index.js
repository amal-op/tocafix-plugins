import template from "./sw-cms-el-cmsbundle-best-seller-list.html.twig";
import "./sw-cms-el-cmsbundle-best-seller-list.scss";

const { Component, Mixin } = Shopware;

Component.register("sw-cms-el-cmsbundle-best-seller-list", {
  template,

  inject: ["feature"],

  mixins: [Mixin.getByName("cms-element")],

  data() {
    return {
      sliderBoxLimit: 3,
    };
  },
  watch: {
    "element.config.elMinWidth.value": {
      handler() {
        this.setSliderRowLimit();
      },
    },

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

  mounted() {
    this.mountedComponent();
  },

  methods: {
    mountedComponent() {
      this.setSliderRowLimit();
    },
    setSliderRowLimit() {
      const { currentDeviceView, element } = this;
      const { countMobile, countDesktop, countTablet } = element.config;

      if (currentDeviceView === "mobile") {
        this.sliderBoxLimit = parseInt(countMobile.value);
        return;
      }

      if (currentDeviceView === "tablet-landscape") {
        this.sliderBoxLimit = parseInt(countTablet.value);
        return;
      }

      if (currentDeviceView === "desktop") {
        this.sliderBoxLimit = parseInt(countDesktop.value);
        return;
      }
    },
    createdComponent() {
      this.initElementConfig("cmsbundle-best-seller-list");
      this.initElementData("cmsbundle-best-seller-list");
    },
  },
});
