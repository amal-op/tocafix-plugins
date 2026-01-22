import CMS from "../../../constant/sw-cms.constant";
import template from "./sw-cms-el-cmsbundle-background-image.html.twig";
import "./sw-cms-el-cmsbundle-background-image.scss";

const { Mixin, Filter } = Shopware;

Shopware.Component.register("sw-cms-el-cmsbundle-background-image", {
  template,

  mixins: [Mixin.getByName("cms-element")],

  computed: {
    backgroundStyle() {
      if (!this.mediaUrl)
        return {
          "background-color": this.element.config.colour.value,
        };
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

      return this.assetFilter(
        "administration/static/img/cms/preview_mountain_large.jpg"
      );
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

      return this.assetFilter(
        "administration/static/img/cms/preview_mountain_large.jpg"
      );
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

      return this.assetFilter(
        "administration/static/img/cms/preview_mountain_large.jpg"
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
      this.initElementConfig("cmsbundle-background-image");
      this.initElementData("cmsbundle-background-image");
    },
  },
});
