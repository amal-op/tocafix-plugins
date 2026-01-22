import template from "./sw-cms-el-config-cmsbundle-hero-slider-background.html.twig";
import "./sw-cms-el-config-cmsbundle-hero-slider-background.scss";

const { Component, Mixin } = Shopware;

Component.register("sw-cms-el-config-cmsbundle-hero-slider-background", {
  template,

  inject: ["repositoryFactory"],

  mixins: [Mixin.getByName("cms-element")],

  data() {
    return {
      mediaModalIsOpenDesktop: false,
      mediaModalIsOpenTablet: false,
      mediaModalIsOpenMobile: false,
      initialFolderId: null,
    };
  },

  computed: {
    mediaRepositoryDesktop() {
      return this.repositoryFactory.create("media");
    },
    mediaRepositoryTablet() {
      return this.repositoryFactory.create("media");
    },
    mediaRepositoryMobile() {
      return this.repositoryFactory.create("media");
    },

    uploadTagDesktop() {
      return `cms-element-cmsbundle-hero-slider-background-desktop-config-${this.element.id}`;
    },

    uploadTagTablet() {
      return `cms-element-cmsbundle-hero-slider-background-tablet-config-${this.element.id}`;
    },

    uploadTagMobile() {
      return `cms-element-cmsbundle-hero-slider-background-mobile-config-${this.element.id}`;
    },

    previewSourceDesktop() {
      if (this.element?.dataDesktop?.media?.id) {
        return this.element.dataDesktop.media;
      }

      return this.element.config.mediaDesktop.value;
    },
    previewSourceTablet() {
      if (this.element?.dataTablet?.media?.id) {
        return this.element.dataTablet.media;
      }

      return this.element.config.mediaTablet.value;
    },
    previewSourceMobile() {
      if (this.element?.dataTablet?.media?.id) {
        return this.element.dataMobile.media;
      }

      return this.element.config.mediaMobile.value;
    },
  },

  created() {
    this.createdComponent();
  },

  methods: {
    createdComponent() {
      this.initElementConfig("cmsbundle-hero-slider-background");
    },

    async onImageUploadDesktop({ targetId }) {
      const mediaDesktopEntity = await this.mediaRepositoryDesktop.get(
        targetId
      );

      this.element.config.mediaDesktop.value = mediaDesktopEntity.id;
      this.element.config.mediaDesktop.source = "static";

      this.updateElementDataDesktop(mediaDesktopEntity);

      this.$emit("element-update", this.element);
    },

    onImageRemoveDesktop() {
      this.element.config.mediaDesktop.value = null;

      this.updateElementDataDesktop();

      this.$emit("element-update", this.element);
    },

    onCloseModalDesktop() {
      this.mediaModalIsOpenDesktop = false;
    },

    onSelectionChangesDesktop(mediaEntity) {
      const media = mediaEntity[0];
      this.element.config.mediaDesktop.value = media.id;

      this.updateElementDataDesktop(media);

      this.$emit("element-update", this.element);
    },

    updateElementDataDesktop(media = null) {
      const mediaId = media === null ? null : media.id;

      if (!this.element.dataDesktop) {
        this.$set(this.element, "dataDesktop", { mediaId });
        this.$set(this.element, "dataDesktop", { media });
      } else {
        this.$set(this.element.dataDesktop, "mediaId", mediaId);
        this.$set(this.element.dataDesktop, "media", media);
      }
    },

    onOpenMediaModalDesktop() {
      this.mediaModalIsOpenDesktop = true;
    },

    async onImageUploadTablet({ targetId }) {
      const mediaTabletEntity = await this.mediaRepositoryTablet.get(targetId);

      this.element.config.mediaTablet.value = mediaTabletEntity.id;
      this.element.config.mediaTablet.source = "static";

      this.updateElementDataTablet(mediaTabletEntity);

      this.$emit("element-update", this.element);
    },

    onImageRemoveTablet() {
      this.element.config.mediaTablet.value = null;

      this.updateElementDataTablet();

      this.$emit("element-update", this.element);
    },

    onCloseModalTablet() {
      this.mediaModalIsOpenTablet = false;
    },

    onSelectionChangesTablet(mediaEntity) {
      const media = mediaEntity[0];
      this.element.config.mediaTablet.value = media.id;

      this.updateElementDataTablet(media);

      this.$emit("element-update", this.element);
    },

    updateElementDataTablet(media = null) {
      const mediaId = media === null ? null : media.id;

      if (!this.element.dataTablet) {
        this.$set(this.element, "dataTablet", { mediaId });
        this.$set(this.element, "dataTablet", { media });
      } else {
        this.$set(this.element.dataTablet, "mediaId", mediaId);
        this.$set(this.element.dataTablet, "media", media);
      }
    },

    onOpenMediaModalTablet() {
      this.mediaModalIsOpenTablet = true;
    },

    async onImageUploadMobile({ targetId }) {
      const mediaMobileEntity = await this.mediaRepositoryMobile.get(targetId);

      this.element.config.mediaMobile.value = mediaMobileEntity.id;
      this.element.config.mediaMobile.source = "static";

      this.updateElementDataMobile(mediaMobileEntity);

      this.$emit("element-update", this.element);
    },

    onImageRemoveMobile() {
      this.element.config.mediaMobile.value = null;

      this.updateElementDataMobile();

      this.$emit("element-update", this.element);
    },

    onCloseModalMobile() {
      this.mediaModalIsOpenMobile = false;
    },

    onSelectionChangesMobile(mediaEntity) {
      const media = mediaEntity[0];
      this.element.config.mediaMobile.value = media.id;

      this.updateElementDataMobile(media);

      this.$emit("element-update", this.element);
    },

    updateElementDataMobile(media = null) {
      const mediaId = media === null ? null : media.id;

      if (!this.element.dataMobile) {
        this.$set(this.element, "dataMobile", { mediaId });
        this.$set(this.element, "dataMobile", { media });
      } else {
        this.$set(this.element.dataMobile, "mediaId", mediaId);
        this.$set(this.element.dataMobile, "media", media);
      }
    },

    onOpenMediaModalMobile() {
      this.mediaModalIsOpenMobile = true;
    },
  },
});
