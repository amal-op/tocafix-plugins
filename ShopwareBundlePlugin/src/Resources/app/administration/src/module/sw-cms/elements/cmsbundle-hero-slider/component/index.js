import template from "./sw-cms-el-cmsbundle-hero-slider.html.twig";
import "./sw-cms-el-cmsbundle-hero-slider.scss";

const { Component, Mixin, Filter } = Shopware;

Component.register("sw-cms-el-cmsbundle-hero-slider", {
  template,

  mixins: [Mixin.getByName("cms-element")],

  computed: {
    backgroundStyle() {
      if (!this.backgroundMediaUrl)
        return {
          "background-color":
            this.element.config.backgroundColor.value ?? "#ddd",
          height: "100%",
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
      const elemDataDesktop = this.element.dataBackgroundDesktop?.media;
      const elemConfigDesktop = this.element.config.backgroundDesktop;

      if (elemDataDesktop?.id) {
        return this.element.dataBackgroundDesktop.media.url;
      }

      if (elemDataDesktop?.url) {
        return this.assetFilter(elemConfigDesktop.url);
      }

      if (this.element.data.backgroundDesktop?.url) {
        return this.element.data.backgroundDesktop.url;
      }

      if (this.element.data.media) {
        return this.assetFilter(this.element.data.media.url);
      }

      return null
    },

    backgroundMediaUrlTablet() {
      const elemDataTablet = this.element.dataBackgroundTablet?.media;
      const elemConfigTablet = this.element.config.backgroundTablet;

      if (elemDataTablet?.id) {
        return this.element.dataBackgroundTablet.media.url;
      }

      if (elemDataTablet?.url) {
        return this.assetFilter(elemConfigTablet.url);
      }

      if (this.element.data.backgroundTablet?.url) {
        return this.element.data.backgroundTablet.url;
      }

      return null;
    },

    backgroundMediaUrlMobile() {
      const elemDataMobile = this.element.dataBackgroundMobile?.media;
      const elemConfigMobile = this.element.config.backgroundMobile;

      if (elemDataMobile?.id) {
        return this.element.dataBackgroundMobile.media.url;
      }

      if (elemDataMobile?.url) {
        return this.assetFilter(elemConfigMobile.url);
      }

      if (this.element.data.backgroundMobile?.url) {
        return this.element.data.backgroundMobile.url;
      }

      return null;
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
      
      let elemDataDesktop = this.element.dataDesktop?.media;
      if(!elemDataDesktop) {
        elemDataDesktop = this.element.data?.mediaDesktop;
      }

      const mediaSourceDesktop = this.element.config.mediaDesktop?.source;
      if (mediaSourceDesktop === "mapped") {
        const demoMediaDesktop = this.getDemoValue(
          this.element.config.mediaDesktop.value
        );

        if (demoMediaDesktop?.url) {
          return demoMediaDesktop.url;
        }

        return this.assetFilter(
          "administration/static/img/cms/preview_mountain_large.jpg"
        );
      }

      if (elemDataDesktop?.id) { 

        if(this.element.dataDesktop && this.element.dataDesktop.media && this.element.dataDesktop.media.url) {
            return this.element.dataDesktop.media.url;
        }

        if(this.element.data.mediaDesktop && this.element.data.mediaDesktop.url) {
            return this.element.data.mediaDesktop.url;
        }
        
      }

      if (elemDataDesktop?.url) { 
        return this.assetFilter(elemDataDesktop.url);
      }

      return this.assetFilter(
        "administration/static/img/cms/preview_mountain_large.jpg"
      );
    },

    mediaUrlTablet() {
      const elemDataTablet = this.element.dataTablet.media;
      const mediaSourceTablet = this.element.config.mediaTablet.source;

      if (mediaSourceTablet === "mapped") {
        const demoMediaTablet = this.getDemoValue(
          this.element.config.mediaTablet.value
        );

        if (demoMediaTablet?.url) {
          return demoMediaTablet.url;
        }

        return this.assetFilter(
          "administration/static/img/cms/preview_mountain_small.jpg"
        );
      }

      if (elemDataTablet?.id) {
        return this.element.dataTablet.media.url;
      }

      if (elemDataTablet?.url) {
        return this.assetFilter(elemDataTablet.url);
      }

      return this.assetFilter(
        "administration/static/img/cms/preview_mountain_small.jpg"
      );
    },

    mediaUrlMobile() {
      const elemDataMobile = this.element.dataMobile.media;
      const mediaSourceMobile = this.element.config.mediaMobile.source;

      if (mediaSourceMobile === "mapped") {
        const demoMediaMobile = this.getDemoValue(
          this.element.config.mediaMobile.value
        );

        if (demoMediaMobile?.url) {
          return demoMediaMobile.url;
        }

        return this.assetFilter(
          "administration/static/img/cms/preview_mountain_small.jpg"
        );
      }

      if (elemDataMobile?.id) {
        return this.element.dataMobile.media.url;
      }

      if (elemDataMobile?.url) {
        return this.assetFilter(elemDataMobile.url);
      }

      return this.assetFilter(
        "administration/static/img/cms/preview_mountain_small.jpg"
      );
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

      if (isSourceStaticDesktop && mediaIdDesktop && value !== mediaIdDesktop) {
        this.element.config.mediaDesktop.value = mediaIdDesktop;
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
      this.initElementConfig("cmsbundle-hero-slider");
      this.initElementData("cmsbundle-hero-slider");
    },
  },
});
