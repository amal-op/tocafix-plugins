import CMS from "../../../constant/sw-cms.constant";
import template from "./sw-cms-el-cmsbundle-hero-slider-background.html.twig";
import "./sw-cms-el-cmsbundle-hero-slider-background.scss";

const { Mixin, Filter } = Shopware;

Shopware.Component.register("sw-cms-el-cmsbundle-hero-slider-background", {
  template,

  mixins: [Mixin.getByName("cms-element")],

  computed: {
    currentDeviceView() {
      return this.cmsPageState.currentCmsDeviceView;
    },
    backgroundStyle() {
      if (!this.mediaUrlDesktop)
        return { "background-color": this.element.config.colour.value };
      return {};
    },
    imageStyle() {
      const deviceViewHeight = {
        mobile: "400px",
        "tablet-landscape": "600px",
        desktop: "800px",
      };

      const height = deviceViewHeight[this.currentDeviceView];

      return { height };
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
      const fallbackImageFileName = CMS.MEDIA.previewMountain.slice(
        CMS.MEDIA.previewMountain.lastIndexOf("/") + 1
      );
      
      const staticFallbackImage = this.assetFilter(
        `administration/static/img/cms/${fallbackImageFileName}`
      );

      const elemDataDesktop = this.element.dataDesktop?.media;
      const elemConfigDesktop = this.element.config.mediaDesktop;
      if (elemConfigDesktop.source === "default") {
        const fileNameDesktop =
          elemConfigDesktop.value?.slice(
            elemConfigDesktop.value.lastIndexOf("/") + 1
          ) || "";
        return this.assetFilter(
          `/administration/static/img/cms/${fileNameDesktop}`
        );
      }

      if (elemDataDesktop?.id) {
        return elemDataDesktop.url;
      }

      if (elemConfigDesktop?.url) {
        return this.assetFilter(elemConfigDesktop.url);
      }
      
      return staticFallbackImage;
    },

    mediaUrlTablet() {
      const fallBackImageFileNameTablet = CMS.MEDIA.previewMountain.slice(
        CMS.MEDIA.previewMountain.lastIndexOf("/") + 1
      );
      const staticFallBackImageTablet = this.assetFilter(
        `administration/static/img/cms/${fallBackImageFileNameTablet}`
      );
      const elemDataTablet = this.element.dataTablet?.media;
      const elemConfigTablet = this.element.config.mediaTablet;

      if (elemConfigTablet.source === "mapped") {
        const demoMediaTablet = this.getDemoValue(elemConfigTablet.value);

        if (demoMediaTablet?.url) {
          return demoMediaTablet.url;
        }

        return staticFallBackImageTablet;
      }

      if (elemConfigTablet.source === "default") {
        // use only the filename
        const fileNameTablet =
          elemConfigTablet.value?.slice(
            elemConfigTablet.value.lastIndexOf("/") + 1
          ) ?? "";
        return this.assetFilter(
          `/administration/static/img/cms/${fileNameTablet}`
        );
      }

      if (elemDataTablet?.id) {
        return this.element.dataTablet.media.url;
      }

      if (elemDataTablet?.url) {
        return this.assetFilter(elemConfigTablet.url);
      }

      return staticFallBackImageTablet;
    },

    mediaUrlMobile() {
      const fallBackImageFileNameMobile = CMS.MEDIA.previewMountain.slice(
        CMS.MEDIA.previewMountain.lastIndexOf("/") + 1
      );
      const staticFallBackImageMobile = this.assetFilter(
        `administration/static/img/cms/${fallBackImageFileNameMobile}`
      );
      const elemDataMobile = this.element.dataMobile?.media;
      const elemConfigMobile = this.element.config.mediaMobile;

      if (elemConfigMobile.source === "mapped") {
        const demoMediaMobile = this.getDemoValue(elemConfigMobile.value);

        if (demoMediaMobile?.url) {
          return demoMediaMobile.url;
        }

        return staticFallBackImageMobile;
      }

      if (elemConfigMobile.source === "default") {
        // use only the filename
        const fileNameMobile =
          elemConfigMobile.value?.slice(
            elemConfigMobile.value.lastIndexOf("/") + 1
          ) ?? "";
        return this.assetFilter(
          `/administration/static/img/cms/${fileNameMobile}`
        );
      }

      if (elemDataMobile?.id) {
        return this.element.dataMobile.media.url;
      }

      if (elemDataMobile?.url) {
        return this.assetFilter(elemConfigMobile.url);
      }

      return staticFallBackImageMobile;
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

      if (isSourceStaticTablet && mediaIdTablet && value !== mediaIdTablet) {
        this.element.config.mediaTablet.value = mediaIdTablet;
      }
    },

    mediaConfigValueMobile(value) {
      const mediaIdMobile = this.element?.dataMobile?.media?.id;
      const isSourceStaticMobile =
        this.element?.config?.mediaMobile?.source === "static";

      if (isSourceStaticMobile && mediaIdMobile && value !== mediaIdMobile) {
        this.element.config.mediaMobile.value = mediaIdMobile;
      }
    },
  },

  created() {
    this.createdComponent();
  },

  methods: {
    createdComponent() {
      this.initElementConfig("cmsbundle-hero-slider-background");
      this.initElementData("cmsbundle-hero-slider-background");
    },
  },
});
