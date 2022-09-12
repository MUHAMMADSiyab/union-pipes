import Vue from "vue";
import Vuex from "vuex";

// Modules
import common from "./modules/common";
import print from "./modules/print";
import auth from "./modules/auth";
import alert from "./modules/alert";
import permission from "./modules/permission";
import role from "./modules/role";
import user from "./modules/user";
import setting from "./modules/setting";
import product from "./modules/product";
import tank from "./modules/tank";
import dispenser from "./modules/dispenser";
import nozzle from "./modules/nozzle";
import rate from "./modules/rate";
import dashboard from "./modules/dashboard";

// Subscriber
import subscriber from "./subscriber";

Vue.use(Vuex);

const store = new Vuex.Store({
    modules: {
        common,
        print,
        auth: {
            ...auth,
            namespaced: true,
        },

        alert: {
            ...alert,
            namespaced: true,
        },

        permission: {
            ...permission,
            namespaced: true,
        },

        role: {
            ...role,
            namespaced: true,
        },

        user: {
            ...user,
            namespaced: true,
        },

        product: {
            ...product,
            namespaced: true,
        },

        tank: {
            ...tank,
            namespaced: true,
        },

        dispenser: {
            ...dispenser,
            namespaced: true,
        },

        nozzle: {
            ...nozzle,
            namespaced: true,
        },

        rate: {
            ...rate,
            namespaced: true,
        },

        setting: {
            ...setting,
            namespaced: true,
        },

        dashboard: {
            ...dashboard,
            namespaced: true,
        },
    },
});

// Attach a subscriber which will take care of attaching the token to every auth request
subscriber(store);

export default store;
