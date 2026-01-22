import template from "./sw-cms-el-cmsbundle-category-list.html.twig";
import "./sw-cms-el-cmsbundle-category-list.scss";

const { Component, Mixin, Filter } = Shopware;

Component.register("sw-cms-el-cmsbundle-category-list", {
  template,

  inject: ["feature"],

  mixins: [Mixin.getByName("cms-element")],

  data() {
    return {
      sliderBoxLimit: 4,
    };
  },

  computed: {
    demoCategoryElement() {
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
    mediaUrl: (app) => (category) => {
      if (category && category.media) {
        if (category.media.id) {
          return category.media.url;
        }

        return app.assetFilter(category.media.url);
      }

      return app.assetFilter(
        "administration/static/img/cms/preview_glasses_large.jpg"
      );
    },
    assetFilter() {
      return Filter.getByName("asset");
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

  created() {
    this.createdComponent();
  },

  mounted() {
    this.mountedComponent();
  },

  methods: {
    createdComponent() {
      this.initElementConfig("cmsbundle-category-slider");
      this.initElementData("cmsbundle-category-slider");
    },
    mountedComponent() {
      this.setSliderRowLimit();
    },
    setSliderRowLimit() {
      const { currentDeviceView, element } = this;
      const { countMobile, countDesktop, countTablet } = element.config;

      if (currentDeviceView === "mobile") {
        this.sliderBoxLimit = countMobile.value;
        return;
      }

      if (currentDeviceView === "tablet-landscape") {
        this.sliderBoxLimit = countTablet.value;
        return;
      }

      if (currentDeviceView === "desktop") {
        this.sliderBoxLimit = countDesktop.value;
        return;
      }
    },
    getCategoryEl(category) {
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
          category,
        },
      };
    },
  },
});
