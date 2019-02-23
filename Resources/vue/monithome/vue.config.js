// vue.config.js
const path = require("path");

module.exports = {
  devServer: {
    proxy: {
      "/api/*": {
        target: "http://blog.meduse.space.florian/",
        secure: false
      }
    },
    disableHostCheck: true,
  },
  outputDir: path.resolve(__dirname, "../../public/build")
};
