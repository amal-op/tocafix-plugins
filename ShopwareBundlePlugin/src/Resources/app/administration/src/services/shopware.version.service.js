const { Application } = Shopware;

export default class ShopwareVersionService {
  getVersion() {
    const shopwareVersion = Shopware.Context.app.config.version;
    return shopwareVersion;
  }

  compareVersions(version1, version2) {
    const v1 = version1.split(".").map(Number);
    const v2 = version2.split(".").map(Number);

    for (let i = 0; i < Math.max(v1.length, v2.length); i++) {
      if ((v1[i] || 0) > (v2[i] || 0)) {
        return 1;
      } else if ((v1[i] || 0) < (v2[i] || 0)) {
        return -1;
      }
    }

    return 0;
  }
}

Application.addServiceProvider("ShopwareVersionService", (container) => {
  return new ShopwareVersionService();
});
