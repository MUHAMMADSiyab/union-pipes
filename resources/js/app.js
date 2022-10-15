require("./bootstrap");

// Global Components
import MainComponent from "./components/MainComponent";
import Alert from "./components/globals/Alert";
import PrintButton from "./components/globals/PrintButton";
Vue.component("main-component", MainComponent);
Vue.component("alert", Alert);
Vue.component("print-button", PrintButton);

// Plugins
import vuetify from "./plugins/vuetify";
import store from "./store";
import router from "./router";
import DatetimePicker from "vuetify-datetime-picker";
Vue.use(DatetimePicker);

// vue-apex-charts
import VueApexCharts from "vue-apexcharts";
Vue.use(VueApexCharts);

Vue.component("apexchart", VueApexCharts);

Vue.config.productionTip = false;

// CSS
import "@mdi/font/css/materialdesignicons.css";

// Mixins
import AuthMixin from "./mixins/AuthMixin";
import PrintMixin from "./mixins/PrintMixin";

store.dispatch("auth/getCurrentUser", localStorage.token).then(() => {
    //auth mixin to be used globally for checking current user's abilities
    Vue.mixin(AuthMixin);
    Vue.mixin(PrintMixin);

    new Vue({
        el: "#app",
        store,
        vuetify,
        router,
    });
});
