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
import bank from "./modules/bank";
import expense_source from "./modules/expense_source";
import expense from "./modules/expense";
import employee from "./modules/employee";
import salary from "./modules/salary";
import company from "./modules/company";
import customer from "./modules/customer";
import purchase_item from "./modules/purchase_item";
import purchase from "./modules/purchase";
import payment from "./modules/payment";
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

        bank: {
            ...bank,
            namespaced: true,
        },

        expense_source: {
            ...expense_source,
            namespaced: true,
        },

        expense: {
            ...expense,
            namespaced: true,
        },

        employee: {
            ...employee,
            namespaced: true,
        },

        salary: {
            ...salary,
            namespaced: true,
        },

        company: {
            ...company,
            namespaced: true,
        },

        customer: {
            ...customer,
            namespaced: true,
        },

        purchase_item: {
            ...purchase_item,
            namespaced: true,
        },

        purchase: {
            ...purchase,
            namespaced: true,
        },

        payment: {
            ...payment,
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
