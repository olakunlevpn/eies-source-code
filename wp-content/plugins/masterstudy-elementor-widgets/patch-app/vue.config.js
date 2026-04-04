module.exports = {
    filenameHashing: false,
    productionSourceMap: false,
    publicPath: "/hybrid/wp-content/plugins/masterstudy-elementor-widgets/patch-app/dist",
    devServer: {
        proxy: 'http://masterstudy.loc/',
    }
};