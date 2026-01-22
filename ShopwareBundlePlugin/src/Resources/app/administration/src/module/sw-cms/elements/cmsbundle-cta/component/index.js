import template from "./sw-cms-el-cmsbundle-cta.html.twig";
import "./sw-cms-el-cmsbundle-cta.scss";

const { Component, Mixin, Filter } = Shopware;

Component.register("sw-cms-el-cmsbundle-cta", {
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
      const subTitle  = this.element.config.subTitle.value;
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
    backgroundStyle() {
      if (!this.backgroundMediaUrl)
        return {
          "background-color":
            this.element.config.backgroundColor.value ?? "#ddd",
          "min-height": "300px",
        };
      return {};
    },
    backgroundMediaUrl() {
      if (this.cmsPageState.currentCmsDeviceView === "desktop") {
        return this.backgroundMediaUrlDesktop;
      }
      if (this.cmsPageState.currentCmsDeviceView === "tablet-landscape") {
        return this.backgroundMediaUrlTablet;
      }
      return this.backgroundMediaUrlMobile;
    },
    backgroundMediaUrlDesktop() {
      //const elemDataDesktop = this.element.dataBackgroundDesktop?.media;
      const elemConfigDesktop = this.element.config.backgroundDesktop;

      let elemDataDesktop = this.element.dataDesktop?.media;
      if(!elemDataDesktop) {
        elemDataDesktop = this.element.data?.mediaDesktop;
      }

    console.log(elemDataDesktop);

      const staticFallBackImageDesktop = this.assetFilter(
        `shopwarebundleplugin/static/img/cta.jpg`
      );

      if (elemDataDesktop?.id) { 
        if(this.element.dataDesktop && this.element.dataDesktop.media && this.element.dataDesktop.media.url) {
            return this.element.dataDesktop.media.url;
        }

        if(this.element.data.mediaDesktop && this.element.data.mediaDesktop.url) {
            return this.element.data.mediaDesktop.url;
        }
      }

      if (elemDataDesktop?.url) { console.log(2);
        return this.assetFilter(elemConfigDesktop.url);
      }

      if (this.element.data.backgroundDesktop?.url) { console.log(3);
        return this.element.data.backgroundDesktop.url;
      }

      return staticFallBackImageDesktop;
    },

    backgroundMediaUrlTablet() {
      const elemDataTablet = this.element.dataBackgroundTablet?.media;
      const elemConfigTablet = this.element.config.backgroundTablet;

      const staticFallBackImageTablet = this.assetFilter(
        `shopwarebundleplugin/static/img/cta.jpg`
      );

      if (elemDataTablet?.id) {
        return this.element.dataBackgroundTablet.media.url;
      }

      if (elemDataTablet?.url) {
        return this.assetFilter(elemConfigTablet.url);
      }

      if (this.element.data.backgroundTablet?.url) {
        return this.element.data.backgroundTablet.url;
      }

      return staticFallBackImageTablet;
    },

    backgroundMediaUrlMobile() {
      const elemDataMobile = this.element.dataBackgroundMobile?.media;
      const elemConfigMobile = this.element.config.backgroundMobile;

      const staticFallBackImageMobile = this.assetFilter(
        `shopwarebundleplugin/static/img/cta.jpg`
      );

      if (elemDataMobile?.id) {
        return this.element.dataBackgroundMobile.media.url;
      }

      if (elemDataMobile?.url) {
        return this.assetFilter(elemConfigMobile.url);
      }

      if (this.element.data.backgroundMobile?.url) {
        return this.element.data.backgroundMobile.url;
      }

      return staticFallBackImageMobile;
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

    assetFilter() {
      return Filter.getByName("asset");
    },

    staticFallBackImageDesktop() {
      const staticFallBackImageDesktop = this.assetFilter(
        `shopwarebundleplugin/static/img/cta.jpg`
      );
      return staticFallBackImageDesktop;
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

    mediaConfigValueTablet(value) {
      const mediaIdTablet = this.element?.dataTablet?.media?.id;
      const isSourceStaticTablet =
        this.element?.config?.mediaTablet?.source === "static";

      if (isSourceStaticTablet && mediaIdTablet && value !== mediaId) {
        this.element.config.mediaTablet.value = mediaId;
      }
    },

    mediaConfigValueMobile(value) {
      const mediaIdMobile = this.element?.dataMobile?.media?.id;
      const isSourceStaticMobile =
        this.element?.config?.mediaMobile?.source === "static";

      if (isSourceStaticMobile && mediaIdMobile && value !== mediaId) {
        this.element.config.mediaMobile.value = mediaId;
      }
    },
  },

  created() {
    this.createdComponent();
  },

  methods: {
    createdComponent() {
      this.initElementConfig("cmsbundle-cta");
      this.initElementData("cmsbundle-cta");
    },
  },
});
