import template from "./sw-cms-el-cmsbundle-cta-block.html.twig";
import "./sw-cms-el-cmsbundle-cta-block.scss";

const { Component, Mixin, Filter } = Shopware;

Component.register("sw-cms-el-cmsbundle-cta-block", {
  template,

  mixins: [Mixin.getByName("cms-element")],

  computed: {
    title() {
      const titleValue = this.element.config.title.value;
      if (!titleValue) return null;

      const titleHeading = this.element.config.titleHeading.value || "h6";

      return `<${titleHeading}>${titleValue}</${titleHeading}>`;
    },
    subTitle() {
      const subTitle = this.element.config.subTitle.value;
      if (!subTitle) {
        return null;
      }
      const subTitleHeading = this.element.config.subTitleHeading.value || "h6";

      return `<${subTitleHeading}>${subTitle}</${subTitleHeading}>`;
    },
    shortDescription() {
      const { value } = this.element.config.shortDescription;
      return value || null;
    },
    content() {
      return this.element.config.content.value
        ? this.element.config.content.value
        : null;
    },
    buttonText1() {
      return this.element.config.buttonText1.value;
    },
    buttonText2() {
      return this.element.config.buttonText2.value;
    },
    buttonText3() {
      return this.element.config.buttonText3.value;
    },
    contentStyle() {
      return {
        "align-items": this.element.config.titlePosition.value,
        "justify-content": this.element.config.alignment.value,
      };
    },
    mediaUrl() {
      if (this.cmsPageState.currentCmsDeviceView === "desktop") {
        return this.mediaUrlDesktop;
      }
      if (this.cmsPageState.currentCmsDeviceView === "tablet-landscape") {
        return this.mediaUrlTablet;
      }
      return this.mediaUrlMobile;
    },
    mediaUrlDesktop() {
      const elemDataDesktop = this.element.dataDesktop?.media;
      const elemConfigDesktop = this.element.config.mediaDesktop;

      if (elemDataDesktop?.id) {
        return this.element.dataDesktop.media.url;
      }

      if (elemDataDesktop?.url) {
        return this.assetFilter(elemConfigDesktop.url);
      }

      if (this.element.data.mediaDesktop?.url) {
        return this.element.data.mediaDesktop.url;
      }

      return null;
    },

    mediaUrlTablet() {
      const elemDataTablet = this.element.dataTablet?.media;
      const elemConfigTablet = this.element.config.mediaTablet;

      if (elemDataTablet?.id) {
        return this.element.dataTablet.media.url;
      }

      if (elemDataTablet?.url) {
        return this.assetFilter(elemConfigTablet.url);
      }

      if (this.element.data.mediaTablet?.url) {
        return this.element.data.mediaTablet.url;
      }

      return null;
    },

    mediaUrlMobile() {
      const elemDataMobile = this.element.dataMobile?.media;
      const elemConfigMobile = this.element.config.mediaMobile;

      if (elemDataMobile?.id) {
        return this.element.dataMobile.media.url;
      }

      if (elemDataMobile?.url) {
        return this.assetFilter(elemConfigMobile.url);
      }

      if (this.element.data.mediaMobile?.url) {
        return this.element.data.mediaMobile.url;
      }

      return null;
    },
    placeholder() {
      
    },

    assetFilter() {
      return Filter.getByName("asset");
    },
  },

  watch: {
    cmsPageState: {
      deep: true,
      handler() {
        this.$forceUpdate();
      },
    },

    mediaConfigValueDesktop(value) {
      const mediaIdDesktop = this.element?.dataDesktop?.media?.id;
      const isSourceStaticDesktop =
        this.element?.config?.mediaDesktop?.source === "static";

      if (isSourceStaticDesktop && mediaIdDesktop && value !== mediaId) {
        this.element.config.mediaDesktop.value = mediaId;
      }
    },
  },

  created() {
    this.createdComponent();
  },

  methods: {
    createdComponent() {
      this.initElementConfig("cmsbundle-cta-block");
      this.initElementData("cmsbundle-cta-block");
    },
  },
});
