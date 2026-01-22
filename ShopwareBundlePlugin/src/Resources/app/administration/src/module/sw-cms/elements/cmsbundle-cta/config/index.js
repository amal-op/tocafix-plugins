import template from "./sw-cms-el-config-cmsbundle-cta.html.twig";
import "./sw-cms-el-config-cmsbundle-cta.scss";

const { Component, Mixin } = Shopware;

Component.register("sw-cms-el-config-cmsbundle-cta", {
  template,

  inject: ["repositoryFactory"],

  mixins: [Mixin.getByName("cms-element")],

  data() {
    return {
      mediaModalIsOpenDesktop: false,
      mediaModalIsOpenTablet: false,
      mediaModalIsOpenMobile: false,
      mediaModalIsOpenBackgroundDesktop: false,
      mediaModalIsOpenBackgroundTablet: false,
      mediaModalIsOpenBackgroundMobile: false,
      initialFolderId: null,
    };
  },

  computed: {
    mediaRepository() {
      return this.repositoryFactory.create("media");
    },

    uploadTagDesktop() {
      return `cms-element-cmsbundle-image-desktop-config-${this.element.id}`;
    },

    uploadTagTablet() {
      return `cms-element-cmsbundle-image-tablet-config-${this.element.id}`;
    },

    uploadTagMobile() {
      return `cms-element-cmsbundle-image-mobile-config-${this.element.id}`;
    },

    uploadTagBackgroundDesktop() {
      return `cms-element-cmsbundle-background-desktop-config-${this.element.id}`;
    },

    uploadTagBackgroundTablet() {
      return `cms-element-cmsbundle-background-tablet-config-${this.element.id}`;
    },

    uploadTagBackgroundMobile() {
      return `cms-element-cmsbundle-background-mobile-config-${this.element.id}`;
    },

    previewSourceDesktop() {
      if (
        this.element.dataDesktop &&
        this.element.dataDesktop.media &&
        this.element.dataDesktop.media.id
      ) {
        return this.element.dataDesktop.media;
      }

      return this.element.config.mediaDesktop.value;
    },
    previewSourceTablet() {
      if (
        this.element.dataTablet &&
        this.element.dataTablet.media &&
        this.element.dataTablet.media.id
      ) {
        return this.element.dataTablet.media;
      }

      return this.element.config.mediaTablet.value;
    },
    previewSourceMobile() {
      if (
        this.element.dataMobile &&
        this.element.dataMobile.media &&
        this.element.dataMobile.media.id
      ) {
        return this.element.dataMobile.media;
      }

      return this.element.config.mediaMobile.value;
    },

    previewSourceBackgroundDesktop() {
      if (
        this.element.dataBackgroundDesktop &&
        this.element.dataBackgroundDesktop.media &&
        this.element.dataBackgroundDesktop.media.id
      ) {
        return this.element.dataBackgroundDesktop.media;
      }

      return this.element.config.backgroundDesktop.value;
    },
    previewSourceBackgroundTablet() {
      if (
        this.element.dataBackgroundTablet &&
        this.element.dataBackgroundTablet.media &&
        this.element.dataBackgroundTablet.media.id
      ) {
        return this.element.dataBackgroundTablet.media;
      }

      return this.element.config.backgroundTablet.value;
    },
    previewSourceBackgroundMobile() {
      if (
        this.element.dataBackgroundMobile &&
        this.element.dataBackgroundMobile.media &&
        this.element.dataBackgroundMobile.media.id
      ) {
        return this.element.dataBackgroundMobile.media;
      }

      return this.element.config.backgroundMobile.value;
    },

    contentUpdate: {
      get() {
        return this.element.config.content.value;
      },

      set(value) {
        this.element.config.content.value = value;
      },
    },
  },

  created() {
    this.createdComponent();
  },

  methods: {
    createdComponent() {
      this.initElementConfig("cmsbundle-cta");
    },

    onElementUpdate(value) {
      this.element.config.content.value = value;

      this.$emit("element-update", this.element);
    },

    async onImageUploadDesktop({ targetId }) {
      const mediaDesktopEntity = await this.mediaRepository.get(targetId);

      this.element.config.mediaDesktop.value = mediaDesktopEntity.id;

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
      const mediaTabletEntity = await this.mediaRepository.get(targetId);

      this.element.config.mediaTablet.value = mediaTabletEntity.id;

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
      const mediaMobileEntity = await this.mediaRepository.get(targetId);

      this.element.config.mediaMobile.value = mediaMobileEntity.id;

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

    async onImageUploadBackgroundDesktop({ targetId }) {
      const mediaDesktopEntity = await this.mediaRepository.get(targetId);

      this.element.config.backgroundDesktop.value = mediaDesktopEntity.id;

      this.updateElementDataBackgroundDesktop(mediaDesktopEntity);

      this.$emit("element-update", this.element);
    },

    onImageRemoveBackgroundDesktop() {
      this.element.config.backgroundDesktop.value = null;

      this.updateElementDataBackgroundDesktop();

      this.$emit("element-update", this.element);
    },

    onCloseModalBackgroundDesktop() {
      this.mediaModalIsOpenBackgroundDesktop = false;
    },

    onSelectionChangesBackgroundDesktop(mediaEntity) {
      const media = mediaEntity[0];
      this.element.config.backgroundDesktop.value = media.id;

      this.updateElementDataBackgroundDesktop(media);

      this.$emit("element-update", this.element);
    },

    updateElementDataBackgroundDesktop(media = null) {
      const mediaId = media === null ? null : media.id;

      if (!this.element.dataBackgroundDesktop) {
        this.$set(this.element, "dataBackgroundDesktop", { mediaId });
        this.$set(this.element, "dataBackgroundDesktop", { media });
      } else {
        this.$set(this.element.dataBackgroundDesktop, "mediaId", mediaId);
        this.$set(this.element.dataBackgroundDesktop, "media", media);
      }
    },

    onOpenMediaModalBackgroundDesktop() {
      this.mediaModalIsOpenBackgroundDesktop = true;
    },

    async onImageUploadBackgroundTablet({ targetId }) {
      const mediaTabletEntity = await this.mediaRepository.get(targetId);

      this.element.config.backgroundTablet.value = mediaTabletEntity.id;

      this.updateElementDataBackgroundTablet(mediaTabletEntity);

      this.$emit("element-update", this.element);
    },

    onImageRemoveBackgroundTablet() {
      this.element.config.mediaTablet.value = null;

      this.updateElementDataBackgroundTablet();

      this.$emit("element-update", this.element);
    },

    onCloseModalBackgroundTablet() {
      this.mediaModalIsOpenBackgroundTablet = false;
    },

    onSelectionChangesBackgroundTablet(mediaEntity) {
      const media = mediaEntity[0];
      this.element.config.backgroundTablet.value = media.id;

      this.updateElementDataBackgroundTablet(media);

      this.$emit("element-update", this.element);
    },

    updateElementDataBackgroundTablet(media = null) {
      const mediaId = media === null ? null : media.id;

      if (!this.element.dataBackgroundTablet) {
        this.$set(this.element, "dataBackgroundTablet", { mediaId });
        this.$set(this.element, "dataBackgroundTablet", { media });
      } else {
        this.$set(this.element.dataBackgroundTablet, "mediaId", mediaId);
        this.$set(this.element.dataBackgroundTablet, "media", media);
      }
    },

    onOpenMediaModalBackgroundTablet() {
      this.mediaModalIsOpenBackgroundTablet = true;
    },

    async onImageUploadBackgroundMobile({ targetId }) {
      const mediaMobileEntity = await this.mediaRepository.get(targetId);

      this.element.config.backgroundMobile.value = mediaMobileEntity.id;

      this.updateElementDataBackgroundMobile(mediaMobileEntity);

      this.$emit("element-update", this.element);
    },

    onImageRemoveBackgroundMobile() {
      this.element.config.backgroundMobile.value = null;

      this.updateElementDataBackgroundMobile();

      this.$emit("element-update", this.element);
    },

    onCloseModalBackgroundMobile() {
      this.mediaModalIsOpenBackgroundMobile = false;
    },

    onSelectionChangesBackgroundMobile(mediaEntity) {
      const media = mediaEntity[0];
      this.element.config.backgroundMobile.value = media.id;

      this.updateElementDataBackgroundMobile(media);

      this.$emit("element-update", this.element);
    },

    updateElementDataBackgroundMobile(media = null) {
      const mediaId = media === null ? null : media.id;

      if (!this.element.dataBackgroundMobile) {
        this.$set(this.element, "dataBackgroundMobile", { mediaId });
        this.$set(this.element, "dataBackgroundMobile", { media });
      } else {
        this.$set(this.element.dataBackgroundMobile, "mediaId", mediaId);
        this.$set(this.element.dataBackgroundMobile, "media", media);
      }
    },

    onOpenMediaModalBackgroundMobile() {
      this.mediaModalIsOpenBackgroundMobile = true;
    },
  },
});
