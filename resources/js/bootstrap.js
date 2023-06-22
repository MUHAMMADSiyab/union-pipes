window._ = require("lodash");

window.Vue = require("vue").default;

// Globals
Vue.prototype.$appName = "MyShowroom";
Vue.prototype.$datatablePerPage = "MyShowroom";

window.axios = require("axios");
axios.defaults.withCredentials = true;
Vue.config.productionTip = false;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
