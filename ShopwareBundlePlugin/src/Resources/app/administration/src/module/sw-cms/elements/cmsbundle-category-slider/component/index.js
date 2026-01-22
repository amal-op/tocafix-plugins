import template from "./sw-cms-el-cmsbundle-category-slider.html.twig";
import "./sw-cms-el-cmsbundle-category-slider.scss";

const { Component, Mixin, Filter } = Shopware;

Component.register("sw-cms-el-cmsbundle-category-slider", {
  template,

  inject: ["feature"],

  mixins: [Mixin.getByName("cms-element")],

  data() {
    return {
      sliderBoxLimit: 3,
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
    hasNavigation() {
      return !!this.element.config.navigation.value;
    },
    classes() {
      return {
        "has--navigation": this.hasNavigation,
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

  created() {
    this.createdComponent();
  },


  methods: {
    createdComponent() {
      this.initElementConfig("cmsbundle-category-slider");
      this.initElementData("cmsbundle-category-slider");
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
